#!/usr/bin/env python3
# -*- coding: utf-8 -*-
#
# BW Flasher - Set Scooter Serial Number (LEQI)
# Copyright (C) 2024-2025 ScooterTeam
#
# This work is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/4.0/
#
# Standalone script for setting the scooter serial number on LEQI scooters.
# Based on bw-flasher 0.7.1 serial_number module.
#
# Command 0x97 is handled by the dashboard BLE firmware (RTL8762C), NOT the motor controller.
# The value is the scooter serial (exposed via Xiaomi MIoT); it is persisted in BLE NVM.
# Brightway and Ninebot are not supported via this command.
#
# Protocol (19200 8N1):
#   TX/RX: [0x5A] [0x12] [0x97] [SN_LEN+1] [0x30] [SN...] [CRC_H] [CRC_L]
#
# Usage:
#   python set_serialnumber.py /dev/ttyUSB0 "0539370000000ABCDEF"
#   python set_serialnumber.py --simulation "0539370000000ABCDEF"
#   python set_serialnumber.py --generate "0539370000000ABCDEF"
#   python set_serialnumber.py --list
#
# Prefer the integrated CLI if you have bw-flasher installed:
#   python -m bwflasher set-serial "0539370000000ABCDEF" --port /dev/ttyUSB0

from __future__ import annotations

import argparse
import sys
import time

SERIAL_NUMBER_LENGTH = 19
DEFAULT_BAUDRATE = 19200


# ---------------------------------------------------------------------------
# CRC
# ---------------------------------------------------------------------------

def calculate_crc16(data: bytes) -> int:
    """CRC-16/XMODEM (poly 0x1021, init 0x0000) used by LEQI UART packets."""
    crc = 0x0000
    for byte in data:
        crc ^= byte << 8
        for _ in range(8):
            if crc & 0x8000:
                crc = ((crc << 1) ^ 0x1021) & 0xFFFF
            else:
                crc = (crc << 1) & 0xFFFF
    return crc


# ---------------------------------------------------------------------------
# Packet building & validation
# ---------------------------------------------------------------------------

def validate_serial_number(serial_number: str) -> str:
    """Validate scooter serial number - must be exactly 19 ASCII characters."""
    if not isinstance(serial_number, str):
        raise ValueError("Serial number must be a string")
    if len(serial_number) != SERIAL_NUMBER_LENGTH:
        raise ValueError(
            f"Serial number must be exactly {SERIAL_NUMBER_LENGTH} characters "
            f"(got {len(serial_number)})"
        )
    try:
        serial_number.encode("ascii")
    except UnicodeEncodeError as exc:
        raise ValueError(f"Serial number must contain only ASCII characters: {exc}") from exc
    return serial_number


def build_serial_packet(serial_number: str) -> bytes:
    """Build command 0x97 packet: [5A][12][97][SN_LEN+1][30][SN...][CRC_H][CRC_L]."""
    serial_number = validate_serial_number(serial_number)
    serial_bytes = serial_number.encode("ascii")
    sn_len = len(serial_bytes)

    packet = bytearray([
        0x5A,       # Header
        0x12,       # Immediate processing
        0x97,       # Set product serial
        sn_len + 1, # Length includes 0x30 field
        0x30,
    ])
    packet.extend(serial_bytes)

    crc = calculate_crc16(bytes(packet))
    packet.append((crc >> 8) & 0xFF)
    packet.append(crc & 0xFF)
    return bytes(packet)


def generate_serial_packet_hex(serial_number: str):
    """Return (success, hex_string, message) without sending anything."""
    try:
        packet = build_serial_packet(serial_number)
        return True, " ".join(f"{b:02X}" for b in packet), "OK"
    except ValueError as exc:
        return False, None, str(exc)


# ---------------------------------------------------------------------------
# Response parsing
# ---------------------------------------------------------------------------

def parse_response(data: bytes):
    """
    Parse a command 0x97 response.

    Returns (success, serial_number_or_None, message).
    """
    if len(data) < 8:
        return False, None, f"Response too short: {len(data)} bytes (expected at least 8)"
    if data[0] != 0x5A:
        return False, None, f"Invalid header: 0x{data[0]:02X} (expected 0x5A)"
    if data[1] != 0x12:
        return False, None, f"Invalid response command: 0x{data[1]:02X} (expected 0x12)"
    if data[2] != 0x97:
        return False, None, f"Invalid subcommand: 0x{data[2]:02X} (expected 0x97)"

    payload_len = data[3]
    if payload_len < 2:
        return False, None, f"Invalid length: {payload_len} (must be >= 2)"

    sn_len = payload_len - 1
    expected_len = 5 + sn_len + 2  # hdr/cmd/sub/len/0x30 + SN + CRC
    if len(data) < expected_len:
        return False, None, f"Response too short: {len(data)} bytes (expected {expected_len})"

    if data[4] != 0x30:
        return False, None, f"Invalid field marker: 0x{data[4]:02X} (expected 0x30)"

    try:
        serial_number = data[5:5 + sn_len].decode("ascii")
    except UnicodeDecodeError:
        return False, None, "Response contains non-ASCII serial number"

    crc_received = (data[5 + sn_len] << 8) | data[5 + sn_len + 1]
    crc_calculated = calculate_crc16(data[:5 + sn_len])
    if crc_received != crc_calculated:
        return (
            False,
            serial_number,
            f"CRC mismatch: got 0x{crc_received:04X}, expected 0x{crc_calculated:04X}",
        )

    return True, serial_number, "OK"


# ---------------------------------------------------------------------------
# Serial I/O
# ---------------------------------------------------------------------------

def _log(callback, message: str) -> None:
    if callback:
        callback(message)


def read_response(ser, timeout: float = 2.0, debug_callback=None) -> bytes:
    """Read one 0x97-style response packet from the serial port."""
    response = bytearray()
    start_time = time.time()

    while time.time() - start_time < timeout:
        if ser.in_waiting > 0:
            byte = ser.read(1)
            if byte and byte[0] == 0x5A:
                response.append(byte[0])
                _log(debug_callback, "[RX] Found header byte 0x5A")
                break
        time.sleep(0.01)

    if not response:
        _log(debug_callback, "[RX] No response (timeout)")
        return bytes()

    # [cmd][subcmd][len][0x30]
    needed = 4
    while needed > 0 and time.time() - start_time < timeout:
        if ser.in_waiting > 0:
            chunk = ser.read(min(needed, ser.in_waiting))
            response.extend(chunk)
            needed -= len(chunk)
        time.sleep(0.01)

    if len(response) < 5:
        _log(debug_callback, "[RX] Incomplete response header")
        return bytes(response)

    sn_len = response[3] - 1
    total_len = 5 + sn_len + 2
    remaining = total_len - len(response)
    while remaining > 0 and time.time() - start_time < timeout:
        if ser.in_waiting > 0:
            chunk = ser.read(min(remaining, ser.in_waiting))
            response.extend(chunk)
            remaining -= len(chunk)
        time.sleep(0.01)

    _log(
        debug_callback,
        f"[RX] Response ({len(response)} bytes): {' '.join(f'{b:02X}' for b in response)}",
    )
    return bytes(response)


def send_serial_command(
    port: str,
    serial_number: str,
    baudrate: int = DEFAULT_BAUDRATE,
    timeout: float = 2.0,
    max_attempts: int = 10,
    debug_callback=None,
    simulation: bool = False,
):
    """
    Send command 0x97 to set the scooter serial number.

    Returns (success, response_serial_or_None, message).
    """
    try:
        packet = build_serial_packet(serial_number)
    except ValueError as exc:
        return False, None, str(exc)

    packet_hex = " ".join(f"{b:02X}" for b in packet)
    _log(debug_callback, f"[TX] Packet ({len(packet)} bytes): {packet_hex}")
    _log(debug_callback, f"     Serial: {serial_number}")
    _log(debug_callback, f"     CRC: 0x{packet[-2]:02X}{packet[-1]:02X}")

    if simulation:
        _log(debug_callback, f"[SIM] Port {port or '(none)'} @ {baudrate} baud")
        _log(debug_callback, f"[RX] Simulated ACK ({len(packet)} bytes): {packet_hex}")
        _log(debug_callback, f"[SIM] Scooter serial set to '{serial_number}'")
        return True, serial_number, "OK (simulated)"

    import serial as _serial
    errors = []
    try:
        ser = _serial.Serial(
            port=port,
            baudrate=baudrate,
            bytesize=_serial.EIGHTBITS,
            parity=_serial.PARITY_NONE,
            stopbits=_serial.STOPBITS_ONE,
            timeout=timeout,
        )
        try:
            _log(debug_callback, f"[INFO] Serial port opened: {port} @ {baudrate} baud")
            ser.reset_input_buffer()
            ser.reset_output_buffer()
            ser.write(packet)
            _log(debug_callback, f"[INFO] Sent {len(packet)} bytes")

            for attempt in range(max_attempts):
                response = read_response(ser, timeout, debug_callback)
                if not response:
                    msg = f"Timeout waiting for response {attempt + 1}"
                    errors.append((False, None, msg))
                    _log(debug_callback, f"[ERROR] {msg}")
                    continue

                success, resp_serial, message = parse_response(response)
                if success and resp_serial == serial_number:
                    _log(debug_callback, f"[OK] Response {attempt + 1}: Serial number set successfully")
                    return True, resp_serial, "OK"

                errors.append((success, resp_serial, message))
                _log(debug_callback, f"[ERROR] Response {attempt + 1}: {message}")
        finally:
            ser.close()

        if errors:
            return errors[0]
        return False, None, "No valid response received"

    except _serial.SerialException as exc:
        return False, None, f"Serial error: {exc}"
    except Exception as exc:
        return False, None, f"Unexpected error: {exc}"


def list_serial_ports():
    """List available serial ports as (device, description) pairs."""
    try:
        import serial.tools.list_ports as lp
        return [(p.device, p.description) for p in lp.comports()]
    except ImportError:
        return []


# ---------------------------------------------------------------------------
# CLI
# ---------------------------------------------------------------------------

def main():
    parser = argparse.ArgumentParser(
        description="Set scooter serial number (LEQI only; stored in dashboard BLE NVM)",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  %(prog)s /dev/ttyUSB0 "0539370000000ABCDEF"
  %(prog)s /dev/ttyUSB0 "0537770000000123456" -v
  %(prog)s --generate "0539370000000ABCDEF"
  %(prog)s --list
  %(prog)s --simulation "0539370000000ABCDEF"

Notes:
  - Serial number must be exactly 19 ASCII characters
  - LEQI only; Brightway/Ninebot are not supported via this command
  - Prefer: python -m bwflasher set-serial SERIAL --port PORT
        """,
    )

    parser.add_argument("port", nargs="?", help="Serial port (e.g., /dev/ttyUSB0 or COM3)")
    parser.add_argument("serial", nargs="?", help=f"{SERIAL_NUMBER_LENGTH}-character serial number")
    parser.add_argument("-b", "--baudrate", type=int, default=DEFAULT_BAUDRATE, help=f"Baud rate (default: {DEFAULT_BAUDRATE})")
    parser.add_argument("-t", "--timeout", type=float, default=2.0, help="Response timeout in seconds")
    parser.add_argument("-v", "--verbose", action="store_true", help="Enable verbose output")
    parser.add_argument("-l", "--list", action="store_true", help="List available serial ports")
    parser.add_argument(
        "--simulation",
        action="store_true",
        help="Simulate set-serial without opening the serial port",
    )
    parser.add_argument(
        "-g",
        "--generate",
        metavar="SERIAL",
        help="Generate packet hex without sending",
    )

    args = parser.parse_args()

    if args.list:
        ports = list_serial_ports()
        if not ports:
            print("No serial ports found.")
            return 1
        print("\nAvailable Serial Ports:")
        for device, description in ports:
            print(f"  {device}: {description}")
        return 0

    if args.generate:
        success, hex_string, message = generate_serial_packet_hex(args.generate)
        if success:
            print(f"\nPacket for serial '{args.generate}':")
            print(f"  {hex_string}")
            return 0
        print(f"\nERROR: {message}")
        return 1

    # With --simulation, allow a single positional as the serial:
    #   set_serialnumber.py --simulation "0539..."
    if args.simulation and args.port and not args.serial and len(args.port) == SERIAL_NUMBER_LENGTH:
        args.serial = args.port
        args.port = "SIM"

    if not args.serial or (not args.simulation and not args.port):
        parser.print_help()
        return 1

    if args.simulation and not args.port:
        args.port = "SIM"

    try:
        validate_serial_number(args.serial)
    except ValueError as exc:
        print(f"\nERROR: {exc}")
        return 1

    print(f"\nSetting scooter serial number to: {args.serial}")
    if args.simulation:
        print("Simulation mode: no serial I/O")
    else:
        print(f"Connecting to {args.port} @ {args.baudrate} baud...")

    success, resp_serial, message = send_serial_command(
        args.port,
        args.serial,
        baudrate=args.baudrate,
        timeout=args.timeout,
        debug_callback=print if (args.verbose or args.simulation) else None,
        simulation=args.simulation,
    )

    if success:
        print(f"\nSUCCESS: Scooter serial set to '{resp_serial}'")
        return 0

    print(f"\nFAILED: {message}")
    return 1


if __name__ == "__main__":
    sys.exit(main())
