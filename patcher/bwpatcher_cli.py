#!/usr/bin/env python3
# -*- coding: utf-8 -*-
#
# bwpatcher_cli.py
# Thin command line wrapper around the bwpatcher package.
# Called by patch.php. Reads a firmware file, applies a comma separated list
# of patches for a given model and writes the patched firmware to disk.
# All output is JSON on stdout so the PHP layer can parse it reliably.
#
# Patcher core: ScooterTeam, CC BY-NC-SA 4.0
# Web integration and CLI wrapper by visoire (open source).

import sys
import os
import io
import json
import argparse
import contextlib

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from bwpatcher.modules import ALL_MODULES
from bwpatcher.utils import patch_map, patch_firmware


# Web slugs that share a patch module with another model. Xiaomi Scooter 42 and
# 43 (navee1.scooter.t2209 / t2209g) use the Mi 4 firmware layout, but need their
# own stock image: flashing the plain Mi 4 (t2201) image on them shows Error 43.
MODEL_ALIASES = {
    "mi42": "mi4",
    "mi43": "mi4",
}


def fail(message, code=2):
    sys.stdout.write(json.dumps({"ok": False, "error": message}))
    sys.exit(code)


def main():
    parser = argparse.ArgumentParser(description="Brightway firmware patcher CLI")
    parser.add_argument("model", type=str.lower, help="Scooter dev name")
    parser.add_argument("infile", help="Input firmware (.bin)")
    parser.add_argument("outfile", help="Output firmware (.bin)")
    parser.add_argument("patches", help="Comma separated patch list")
    args = parser.parse_args()
    args.model = MODEL_ALIASES.get(args.model, args.model)

    if args.model not in ALL_MODULES:
        fail("Unknown model: %s. Known models: %s" % (args.model, ", ".join(ALL_MODULES)))

    if not os.path.isfile(args.infile):
        fail("Input firmware not found.")

    try:
        with open(args.infile, "rb") as fh:
            data = fh.read()
    except OSError as exc:
        fail("Could not read input firmware: %s" % exc)

    if len(data) < 4096:
        fail("Firmware file looks too small to be valid (%d bytes)." % len(data))

    patches = [p.strip() for p in args.patches.split(",") if p.strip()]
    if not patches:
        fail("No patches selected.")

    # Validate every patch token against the known patch map.
    for p in patches:
        token = p.split("=")[0]
        if token not in patch_map:
            fail("Unknown patch: %s" % token)

    try:
        # web=True makes patch_firmware raise on the first failing patch instead
        # of silently collecting errors, which is what we want for a clear UI.
        # The patcher prints progress to stdout, so we redirect it to keep our
        # own stdout clean for the JSON result that PHP parses.
        with contextlib.redirect_stdout(io.StringIO()):
            output = patch_firmware(args.model, data, list(patches), web=True, allow_experimental=True)
    except Exception as exc:  # noqa: BLE001 - surface a clean message to the UI
        msg = str(exc) or exc.__class__.__name__
        if "Pattern not found" in msg:
            msg = ("A required code pattern was not found. This firmware "
                   "version is not supported for the selected patch, or the "
                   "file does not match the chosen model.")
        fail(msg, code=3)

    try:
        with open(args.outfile, "wb") as fh:
            fh.write(output)
    except OSError as exc:
        fail("Could not write patched firmware: %s" % exc)

    sys.stdout.write(json.dumps({
        "ok": True,
        "model": args.model,
        "patches": patches,
        "input_size": len(data),
        "output_size": len(output),
        "changed_bytes": sum(1 for a, b in zip(data, output) if a != b),
    }))


if __name__ == "__main__":
    main()
