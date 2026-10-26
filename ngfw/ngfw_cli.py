#!/usr/bin/env python3
# -*- coding: utf-8 -*-
#
# ngfw_cli.py
# JSON command line wrapper around the NGFW patcher (Xiaomi M365 family and
# Ninebot ESCs). Called by patch.php. All output is JSON on stdout.
#
# Options mirror the rest of the site:
#   --simulation  run every patch on a copy, report the result, write no file
#   --debug       include offsets, raw bytes and disassembly for each change
#
# NGFW patcher core: Daljeet Nandha (turbojeet), AGPL-3.0, based on BotoX'
# xiaomi-m365-firmware-patcher. This wrapper is part of the same AGPL work:
# source is available in this directory (ngfwpatcher/) and at
# https://github.com/scooterteam/ngfw-patcher.

import argparse
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), "ngfwpatcher"))

from util import SignatureException  # noqa: E402

MODELS = ["1s", "pro2", "lite", "mi3", "4pro", "4proita", "4plus", "4max", "f2pro", "f2plus", "f2", "g2"]
ITALIAN_MODELS = {"4proita"}


def fail(message, code=2):
    sys.stdout.write(json.dumps({"ok": False, "error": message}))
    sys.exit(code)


def mi_patches(vlt, model):
    ita = model in ITALIAN_MODELS
    mult = 10.0 / (10.0 if ita else 8.5)
    return {
        "dpc": lambda v: vlt.dpc(),
        "sdt": lambda v: vlt.shutdown_time(v if v is not None else 1),
        "mss": lambda v: vlt.motor_start_speed(v if v is not None else 3),
        "wsc": lambda v: vlt.wheel_speed_const(v if v is not None else mult),
        "sld": lambda v: vlt.speed_limit_drive(v if v is not None else 22),
        "sls": lambda v: vlt.speed_limit_sport(v if v is not None else 27),
        "slp": lambda v: vlt.speed_limit_ped(v if v is not None else 9),
        "amp": lambda v: vlt.ampere_ped(int(v) if v is not None else 10000),
        "amd": lambda v: vlt.ampere_drive(int(v) if v is not None else 20000),
        "ams": lambda v: vlt.ampere_sport(int(v) if v is not None else 30000),
        "alm": lambda v: vlt.ampere_max(10000, 35000 if ita else 30000, 55000),
        "rml": lambda v: vlt.remove_modellock(),
        "rks": lambda v: vlt.remove_kers(),
        "rab": lambda v: vlt.remove_autobrake(),
        "rcm": lambda v: vlt.remove_charging_mode(),
        "crc": lambda v: vlt.current_raising_coeff(int(v) if v is not None else 1000),
        "ccd": lambda v: vlt.cc_delay(v if v is not None else 2),
        "rfm": lambda v: vlt.region_free(),
        "llm": lambda v: vlt.lower_light(),
        "blm": lambda v: vlt.brake_light_static() if ita else vlt.brake_light(),
        "amm": lambda v: vlt.ampere_meter(shift=8),
        "lrb": lambda v: vlt.lever_resolution(brake=0x9C),
        "bud": lambda v: vlt.bms_baudrate(int(v) if v is not None else 76800),
        "vlt": lambda v: vlt.volt_limit(v if v is not None else (45.01 if ita else 56.01)),
        "pnb": lambda v: vlt.ped_noblink(),
        "bts": lambda v: vlt.button_swap(),
        "abr": lambda v: vlt.ampere_brake(min_=25000, max_=60000),
        "kml": lambda v: vlt.kers_multi(2, 5, 10),
    }


def nb_patches(vlt, model):
    return {
        "dmn": lambda v: vlt.disable_motor_ntc(),
        "asc": lambda v: vlt.allow_sn_change(),
        "skc": lambda v: vlt.skip_key_check(),
        "rfm": lambda v: vlt.region_free(),
        "kml": lambda v: vlt.kers_multi(2, 5, 10),
        "slp": lambda v: vlt.speed_params(23, 22, 21, 20),
        "dpc": lambda v: vlt.dpc(),
        "rab": lambda v: vlt.remove_autobrake(),
        "ccd": lambda v: vlt.cc_delay(v if v is not None else 3),
        "rcm": lambda v: vlt.remove_charging_mode(),
        "rks": lambda v: vlt.remove_kers(),
        "amp": lambda v: vlt.ampere_ped(int(v) if v is not None else 5000),
        "ame": lambda v: vlt.ampere_eco(int(v) if v is not None else 10000),
        "amd": lambda v: vlt.ampere_drive(int(v) if v is not None else 20000),
        "ams": lambda v: vlt.ampere_sport(int(v) if v is not None else 30000),
        "mme": lambda v: vlt.ampere_max_eco(int(v) if v is not None else 20000),
        "mmd": lambda v: vlt.ampere_max_drive(int(v) if v is not None else 40000),
        "mms": lambda v: vlt.ampere_max_sport(int(v) if v is not None else 60000),
        "bud": lambda v: vlt.bms_baudrate(int(v) if v is not None else 76800),
    }


def list_patches():
    """Patch codes per family, used by the web UI and for validation."""
    class _Dummy:
        def __getattr__(self, name):
            return lambda *a, **k: []
    return {
        "mi": sorted(mi_patches(_Dummy(), "pro2").keys()),
        "nb": sorted(nb_patches(_Dummy(), "4max").keys()),
    }


def parse_tokens(raw):
    tokens = []
    for item in raw.split(","):
        item = item.strip()
        if not item:
            continue
        code, _, val = item.partition("=")
        value = None
        if val != "":
            try:
                value = float(val)
            except ValueError:
                fail("Invalid value for patch %s: %s" % (code, val))
        tokens.append((code.strip(), value))
    return tokens


def main():
    parser = argparse.ArgumentParser(description="NGFW patcher CLI (JSON output)")
    parser.add_argument("type", choices=["mi", "nb", "list"])
    parser.add_argument("model", nargs="?", default="")
    parser.add_argument("infile", nargs="?", default="")
    parser.add_argument("outfile", nargs="?", default="")
    parser.add_argument("patches", nargs="?", default="")
    parser.add_argument("--debug", action="store_true")
    parser.add_argument("--simulation", action="store_true")
    args = parser.parse_args()

    if args.type == "list":
        sys.stdout.write(json.dumps({"ok": True, "patches": list_patches(), "models": MODELS}))
        return

    if args.model not in MODELS:
        fail("Unknown model: %s" % args.model)
    if not os.path.isfile(args.infile):
        fail("Input firmware not found.")
    with open(args.infile, "rb") as fh:
        data = fh.read()
    if len(data) < 4096:
        fail("Firmware file looks too small to be valid (%d bytes)." % len(data))

    tokens = parse_tokens(args.patches)
    if not tokens:
        fail("No patches selected.")

    from mi_patcher import MiPatcher
    from nb_patcher import NbPatcher

    try:
        vlt = MiPatcher(data, args.model) if args.type == "mi" else NbPatcher(data, args.model)
    except Exception as exc:  # noqa: BLE001
        fail("Could not initialise patcher for this model: %s" % (exc or exc.__class__.__name__), code=3)

    table = mi_patches(vlt, args.model) if args.type == "mi" else nb_patches(vlt, args.model)
    for code, _ in tokens:
        if code not in table:
            fail("Unknown patch for %s family: %s" % (args.type, code))

    report = []
    sigerr = []
    for code, value in tokens:
        try:
            items = []
            for desc, ofs, pre, post in table[code](value):
                item = {"desc": desc, "offset": str(ofs)}
                if args.debug:
                    item["pre"] = pre
                    item["post"] = post
                    item["pre_dis"] = [" ".join([x.bytes.hex(), x.mnemonic, x.op_str]) for x in vlt.cs.disasm(bytes.fromhex(pre), 0)]
                    item["post_dis"] = [" ".join([x.bytes.hex(), x.mnemonic, x.op_str]) for x in vlt.cs.disasm(bytes.fromhex(post), 0)]
                items.append(item)
            report.append({"patch": code, "status": "ok", "items": items})
        except SignatureException:
            sigerr.append(code)
            report.append({"patch": code, "status": "sigerr", "items": []})
        except Exception as exc:  # noqa: BLE001
            sigerr.append(code)
            report.append({"patch": code, "status": "error", "error": str(exc) or exc.__class__.__name__, "items": []})

    changed = sum(1 for a, b in zip(data, vlt.data) if a != b)
    if not any(r["status"] == "ok" for r in report):
        fail("No patch could be applied. The firmware does not match the selected model or version (signatures not found).", code=3)

    if not args.simulation:
        with open(args.outfile, "wb") as fh:
            fh.write(vlt.data)

    sys.stdout.write(json.dumps({
        "ok": True,
        "simulation": args.simulation,
        "type": args.type,
        "model": args.model,
        "input_size": len(data),
        "output_size": len(vlt.data),
        "changed_bytes": changed,
        "failed": sigerr,
        "report": report,
    }))


if __name__ == "__main__":
    main()
