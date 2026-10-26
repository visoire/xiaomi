# Xiaomi & Ninebot Scooter Firmware Suite

[![Live Deployment](https://img.shields.io/badge/Live-vivalet.xyz%2Fxiaomi-brightgreen?style=for-the-badge&logo=googlechrome)](https://vivalet.xyz/xiaomi)
[![bw-flasher](https://img.shields.io/badge/bw--flasher-v0.7.1-blue?style=for-the-badge)](bw/bw-flasher-0.7.1)
[![bw-patcher](https://img.shields.io/badge/bw--patcher-v1.1.6-purple?style=for-the-badge)](bw/bw-patcher)
[![License](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-orange?style=for-the-badge)](LICENSE)
[![Python](https://img.shields.io/badge/Python-3.9%2B-informational?style=for-the-badge&logo=python)](https://python.org)
[![Web Serial](https://img.shields.io/badge/Web_Serial_API-Supported-success?style=for-the-badge)](https://developer.mozilla.org/en-US/docs/Web/API/Web_Serial_API)

Open-source toolkit and web suite for analyzing, patching, and flashing MCU firmware on **Xiaomi** (Brightway, LEQI) and **Ninebot** electric scooters over UART.

> 🌐 **Live Web Application:**  
> The web suite is publicly deployed and accessible at **[vivalet.xyz/xiaomi](https://vivalet.xyz/xiaomi)**.  
> Users can patch firmware and flash directly in modern browsers (Chrome, Edge, Opera on Desktop & Android) via the Web Serial API without installing local software.

---

## 📋 Table of Contents

- [Overview & Principles](#-overview--principles)
- [Current Versions](#-current-versions)
- [Key Logic & Architecture](#-key-logic--architecture)
  - [1. Web Serial Flasher Engine](#1-web-serial-flasher-engine)
  - [2. Standalone Python Flasher (`bw-flasher 0.7.1`)](#2-standalone-python-flasher-bw-flasher-071)
  - [3. Firmware Patcher Engine (`bw-patcher 1.1.6`)](#3-firmware-patcher-engine-bw-patcher-116)
  - [4. Next-Gen Firmware Patcher (`ngfw`)](#4-next-gen-firmware-patcher-ngfw)
  - [5. Dashboard BLE Serial Number Tool](#5-dashboard-ble-serial-number-tool)
  - [6. Guided Setup Wizard & MCU Registry](#6-guided-setup-wizard--mcu-registry)
- [Supported Scooter Models](#-supported-scooter-models)
- [Hardware Requirements & Wiring](#-hardware-requirements--wiring)
- [Usage Guides](#-usage-guides)
  - [Web-Based Flasher & Patcher](#web-based-flasher--patcher)
  - [Python CLI Flasher](#python-cli-flasher)
  - [Python GUI Flasher](#python-gui-flasher)
  - [Firmware Patching via CLI](#firmware-patching-via-cli)
  - [Setting LEQI Scooter Serial](#setting-leqi-scooter-serial)
- [Testing & Verification](#-testing--verification)
- [Safety & Legal Disclaimer](#%EF%B8%8F-safety--legal-disclaimer)
- [License & Credits](#-license--credits)

---

## 🔓 Overview & Principles

- **Right to Repair & Ownership:** You own what you purchase. Manufacturers should not lock you out of hardware you own.
- **Knowledge in the Public Domain:** Reverse engineering findings and communication protocols belong to the open community to prevent commercial exploitation and black-market software gating.
- **Security through Transparency:** Obscurity, unauthenticated bootloaders, and weak encryption are flaws, not protection. Documenting them promotes better industry engineering.
- **Educational & Research Focus:** Intended solely for academic, educational, and personal research.

---

## 📌 Current Versions

| Component | Current Version | Release Highlights |
|---|---|---|
| **bw-flasher** | `0.7.1` | Modular UART engine, plaintext LEQI body support (Xiaomi 5 Plus), authoritative `EU1\0` header tag parsing, BLE dashboard serial number programming. |
| **bw-patcher** | `1.1.6` | Dynamic binary header detection, EU1 dynamic firmware sizing, speed limit patching (`sls`), experimental patch decorator support for Xiaomi 6, 6 Lite, 6 Pro, and reworked 4 Ultra. |
| **Web Serial Flasher** | `0.7.1` (JS Port) | Pure client-side Web Serial API implementation mirroring Python flasher logic with connection probing, setup mode, and real-time progress. |
| **Web Patcher & Setup** | Unified Backend | Python CLI wrapper integration (`bwpatcher_cli.py`, `prepare.php`), temp path fallbacks, and multi-language support (EN, DE, PL). |

---

## 🧠 Key Logic & Architecture

```
xiaomi/
├── flasher/                    # Web Serial browser flasher
│   ├── assets/js/
│   │   ├── brightway.js        # Brightway protocol (M7, XMODEM transfer, NAK retries)
│   │   ├── leqi.js             # LEQI protocol (M8, plaintext body check, EU1 header)
│   │   ├── crc.js              # Hardware CRC16 algorithms
│   │   ├── firmware.js         # Firmware header detection & container parsing
│   │   ├── serial.js           # Web Serial connection abstraction
│   │   ├── serialnumber.js     # LEQI BLE serial packet builder & UART transmitter
│   │   └── flasher-ui.js       # Web interface state management
│   └── scripts/
│       └── set_serialnumber.py # Standalone LEQI BLE serial programmer CLI
│
├── bw/
│   ├── bw-flasher-0.7.1/       # Reference standalone Python flasher package
│   │   ├── bwflasher/
│   │   │   ├── base_flasher.py     # Base communication abstraction
│   │   │   ├── brightway_flasher.py# Brightway UART protocol
│   │   │   ├── leqi_flasher.py     # LEQI UART protocol
│   │   │   ├── serial_number.py    # Serial number NVM write driver
│   │   │   ├── xmodem.py           # Robust XMODEM implementation
│   │   │   ├── keygen.py           # Dynamic key generation
│   │   │   └── gui.py              # PySide6 desktop interface
│   │   └── tests/                  # Flasher test suite
│   │
│   └── bw-patcher/             # Reference standalone Python patcher package
│       ├── bwpatcher/
│       │   ├── core.py             # Patch engine base & binary loader
│       │   ├── detect.py           # Binary model auto-detection heuristics
│       │   ├── modules/            # Model-specific patch definitions
│       │   │   ├── mi4.py, mi5.py, mi5plus.py, mi6.py, ultra4.py, etc.
│       │   │   └── leqi_speed.py   # LEQI speed limit patching logic
│       │   └── core_n32.py, core_lks32.py, core_es32.py # Architecture drivers
│       └── tests/                  # Patcher test suite
│
├── patcher/                    # Web patcher endpoint & Python bridge
│   ├── patch.php               # Server-side upload handler & patch dispatch
│   └── bwpatcher_cli.py        # CLI interface for automated web patching
│
├── setup/                      # Interactive guided setup wizard
│   ├── prepare.php             # Automated stock firmware retrieval & patch generation
│   ├── assets/js/setup.js      # Step-by-step UI, connection probe & Web Serial flashing
│   └── mi4lite2nd/             # Dedicated utility for 4 Lite 2nd Gen
│
├── ngfw/                       # Ninebot Next-Gen Firmware patcher
│   ├── ngfwpatcher/            # AGPL-3.0 patcher engine for Ninebot ESC
│   ├── ngfw_cli.py             # CLI wrapper
│   └── patch.php               # Web patcher endpoint
│
├── mcu/                        # Firmware metadata & binary catalog
│   ├── fw/data.json            # Central registry of 70+ scooter models & checksums
│   ├── fetch_info.php          # Firmware information API endpoint
│   └── version_check.php       # Update validation
│
└── inc/
    ├── functions.php           # Python interpreter resolution & system helpers
    └── lang.php                # Internationalization strings (EN, DE, PL)
```

### 1. Web Serial Flasher Engine
Runs entirely inside client browsers via Web Serial API:
- **Brightway Protocol (`brightway.js`):** 5-pin M7 interface, handshake negotiation, XMODEM binary block transfer, automatic NAK retry loop (up to 20 retries), and packet verification.
- **LEQI Protocol (`leqi.js`):** 5-pin M8 interface, plaintext body verification (`0xAA` check), authoritative `EU1\0` header tag parsing at offset `0x0A`, dynamic 16-bit body sizing, and CRC16 checksum calculation.
- **Simulation & Debug Modes:** Allows testing connection logic, loopbacks, and protocol state transitions without physical hardware risks.

### 2. Standalone Python Flasher (`bw-flasher 0.7.1`)
- Cross-platform CLI and PySide6 GUI supporting Windows, Linux, and macOS.
- Auto-detects firmware container types (Brightway vs. LEQI).
- Built-in chiptune audio player, live transfer rate metering, and port auto-discovery.

### 3. Firmware Patcher Engine (`bw-patcher 1.1.6`)
- Disassembles ARM Cortex-M binaries using Capstone/Keystone.
- Automatically detects scooter model from firmware header `[0x00, 0x80)`.
- Replaces hardcoded speed limit variables (`sls`) and patches checksum validation routines (`chk`).
- Architecture support across Nationstech N32, Linko LKS32, and Eastsoft ES32 microcontrollers.

### 4. Next-Gen Firmware Patcher (`ngfw`)
- Handles Next-Gen Ninebot controllers (Xiaomi 4 Pro, 4 Pro Max, 4 Pro Plus).
- Dispatches patching routines via `ngfw_cli.py` and provides ST-Link / ScooterFlasher flashing commands.

### 5. Dashboard BLE Serial Number Tool
- Writes 19-character ASCII product serial numbers to the dashboard BLE module's Non-Volatile Memory (NVM) over UART.
- Accessible via Web UI (`flasher/`), Python CLI (`flasher/scripts/set_serialnumber.py`), or `bw-flasher`.

### 6. Guided Setup Wizard & MCU Registry
- `setup/`: Guides non-technical users through selecting their model, choosing desired speed targets, verifying wiring, and flashing without manual command-line execution.
- `mcu/fw/data.json`: Authoritative firmware database matching `mi-fw-info`.

---

## 🛴 Supported Scooter Models

| Model | Manufacturer | Connector | Flashing Support | Status |
|---|---|---|---|---|
| **Xiaomi Scooter 4** | Brightway | Julet 5-Pin M7 Male | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 4 Lite** | Brightway | Julet 5-Pin M7 Male | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 4 Ultra** | Brightway | Julet 5-Pin M7 Male | Web Serial / Python | ⚠️ Experimental |
| **Xiaomi Scooter 4 Pro (2nd Gen)** | Brightway | Julet 5-Pin M7 Female | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 5** | Brightway | Julet 5-Pin M7 Female | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 5 Max** | Brightway | Julet 5-Pin M7 Male | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 5 Pro** | Brightway | Julet 5-Pin M7 Male | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 5 Elite** | LEQI | Julet 5-Pin M8 Male | Web Serial / Python | ✅ Verified |
| **Xiaomi Scooter 6** | LEQI | Julet 5-Pin M8 Male | Web Serial / Python | ⚠️ Experimental |
| **Xiaomi Scooter 6 Lite** | LEQI | Julet 5-Pin M8 Male | Web Serial / Python | ⚠️ Experimental |
| **Xiaomi Scooter 6 Pro** | LEQI | Julet 5-Pin M8 Male | Web Serial / Python | ⚠️ Experimental |
| **Xiaomi Scooter 42 / 43 (Navee)** | Brightway/Navee | Julet 5-Pin M7 Male | Web Serial / Python | ✅ Verified (Error 43 Fix) |
| **Xiaomi Scooter 4 Pro (1st Gen)** | Ninebot | Dash / ST-Link | NGFW Patcher | ✅ CFW Supported |
| **Xiaomi 4 Lite (2nd Gen)** | LEQI | Julet 5-Pin M8 | Setup script / ST-Link | ⚠️ Advanced |

---

## 🔌 Hardware Requirements & Wiring

1. **USB to UART Adapter:**  
   - Compatible chipsets: **CH340**, **FT232RL**, or **CP2102**.
   - Logic Level: **Set to 5V** (or 3.3V logic where specified by board revision).

2. **Dashboard Cable Connector:**  
   - **Brightway controllers:** Julet 5-Pin **M7** (Male or Female depending on model, see table above).
   - **LEQI controllers:** Julet 5-Pin **M8** Male (1:1 identical across 5 Elite and 6th Gen).

3. **Pinout Guide:**

```
   Julet 5-Pin Cable               USB-UART Adapter
   ┌────────────────┐              ┌────────────────┐
   │ Red            │ ───────────> │ 5V / VCC       │
   │ Black          │ ───────────> │ GND            │
   │ Blue / Yellow  │ ───────────> │ RXD            │
   │ Green / White  │ ───────────> │ TXD            │
   └────────────────┘              └────────────────┘
```
*(Always double-check pinouts with a digital multimeter before powering on).*

---

## 🚀 Usage Guides

### Web-Based Flasher & Patcher
1. Visit **[vivalet.xyz/xiaomi](https://vivalet.xyz/xiaomi)** (or host locally with Apache/Nginx + PHP 8+ and Python 3.9+).
2. Go to **Setup** or **Flasher**.
3. Connect your USB-UART adapter and click **Connect**.
4. Select your scooter model or upload your `.bin` firmware.
5. Click **Flash Firmware** to flash directly over Web Serial.

### Python CLI Flasher
```bash
cd bw/bw-flasher-0.7.1

# Install dependencies
pip install -r requirements.txt

# Flash firmware (auto-detects Brightway or LEQI)
python -m bwflasher --port COM3 /path/to/firmware.bin

# Test run in simulation mode
python -m bwflasher --simulation /path/to/firmware.bin
```

### Python GUI Flasher
```bash
cd bw/bw-flasher-0.7.1
python -m bwflasher.gui
```

### Firmware Patching via CLI
```bash
cd bw/bw-patcher

# Install requirements
pip install -r requirements.txt

# Auto-detect model and patch speed limit to 25 km/h with checksum recalculation
python -m bwpatcher auto stock_firmware.bin patched_firmware.bin sls=25,chk

# Explicit model patching (e.g. Mi 5 Pro)
python -m bwpatcher mi5pro stock_firmware.bin patched_firmware.bin sls=30,chk
```

### Setting LEQI Scooter Serial
```bash
# Using the standalone CLI script
python flasher/scripts/set_serialnumber.py COM3 "0539370000000123456"

# Or using bw-flasher 0.7.1
python -m bwflasher set-serial "0539370000000123456" --port COM3
```

---

## 🧪 Testing & Verification

Run the automated test suites using `pytest`:

```bash
# Test bw-flasher (9 tests covering detection, CRC, packet formation, simulation)
cd bw/bw-flasher-0.7.1
pytest tests/ -v

# Test bw-patcher (25 tests covering module disassemblers, header heuristics, patches)
cd bw/bw-patcher
pytest tests/ -v
```

---

## ⚠️ Safety & Legal Disclaimer

**Modifying scooter firmware alters vehicle behavior, can cause physical injury, and may be illegal in your jurisdiction.**

- Modifying vehicle speed or power parameters will void your manufacturer warranty.
- Operating modified electric scooters on public roads or bicycle infrastructure may violate local traffic regulations (e.g., German StVZO / eKFV, EU EN 17128).
- Flashing unverified or corrupted binaries carries risk of bricking the MCU controller.
- **This software is provided strictly for educational, security analysis, and research purposes.**
- The authors and contributors assume **no liability** for hardware damage, accidents, bodily harm, or legal consequences resulting from the use of these tools.

---

## 📄 License & Credits

- **Suite Web Integration & Architecture:** Copyright (c) 2026 **[visoire](https://github.com/visoire)**.  
  Licensed under **[Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](LICENSE)**.
- **Core bw-flasher & bw-patcher Engines:** Developed by **ScooterTeam** and community researchers (CC BY-NC-SA 4.0).
- **NGFW Patcher Engine:** Licensed under the **[GNU Affero General Public License v3.0 (AGPL-3.0)](ngfw/LICENSE.md)**.
- **Community Testers:** Special thanks to Ilkan0342567, Gianluxx, Hypersniper_de, encryptize, holow_rt, Lenny5292, Krypz0n, Turbojeet, and everyone contributing to open scooter research.
