// bw-flasher-web - crc.js
// CRC utilities - CRC-16/XMODEM and CRC-32 (matches Python binascii)
// ScooterTeam © 2024-2025 CC BY-NC-SA 4.0

'use strict'

// ═══════════════════════════════════════════════════════════════════════════
//  CRC UTILITIES
// ═══════════════════════════════════════════════════════════════════════════

/** CRC-16/XMODEM (poly=0x1021, init=0, no reflection) - same as Python's crc_hqx(data, 0x0) */
function crc16(data, init = 0) {
	let crc = init & 0xffff
	for (const b of data) {
		crc ^= b << 8
		for (let i = 0; i < 8; i++) {
			crc = crc & 0x8000 ? ((crc << 1) ^ 0x1021) & 0xffff : (crc << 1) & 0xffff
		}
	}
	return crc & 0xffff
}

/** CRC-32 (zlib) - same as Python's binascii.crc32 */
const _crc32Table = (() => {
	const t = new Uint32Array(256)
	for (let i = 0; i < 256; i++) {
		let c = i
		for (let j = 0; j < 8; j++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1
		t[i] = c
	}
	return t
})()

function crc32(data) {
	let crc = 0xffffffff
	for (const b of data) crc = _crc32Table[(crc ^ b) & 0xff] ^ (crc >>> 8)
	return (crc ^ 0xffffffff) >>> 0
}

/** Reverse the bit order of one byte. */
function bitReverse8(value) {
	let r = 0
	for (let i = 0; i < 8; i++) if (value & (1 << i)) r |= 1 << (7 - i)
	return r & 0xff
}

/** Reverse the bit order of a 16 bit word. */
function bitReverse16(value) {
	let r = 0
	for (let i = 0; i < 16; i++) if (value & (1 << i)) r |= 1 << (15 - i)
	return r & 0xffff
}

/**
 * CRC-16 poly 0x8005, init 0xFFFF, input bytes bit reversed, output word bit reversed.
 * Used only for the CRC embedded in LEQI firmware bodies.
 * Mirrors LeqiFlasher.crc16_firmware in bw-flasher 0.7.1.
 */
function crc16Firmware(data) {
	let crc = 0xffff
	for (const b of data) {
		crc = (crc ^ (bitReverse8(b) << 8)) & 0xffff
		for (let i = 0; i < 8; i++) {
			if (crc & 0x8000) crc = ((crc << 1) ^ 0x8005) & 0xffff
			else crc = ((crc & 0x7fff) << 1) & 0xffff
		}
	}
	return bitReverse16(crc)
}
