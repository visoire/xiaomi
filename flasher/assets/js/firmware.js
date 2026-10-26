// bw-flasher-web - firmware.js
// Firmware utilities - ZIP extraction, type detection, info parsing, validation
// ScooterTeam © 2024-2025 CC BY-NC-SA 4.0

'use strict'

// ── Client-side firmware file validation ──────────────────────────────────
// Mirrors the server-side validateFirmwareFile() in inc/functions.php.
// Provides multi-layer checks before any firmware data is processed.

const FW_VALIDATION = {
  maxSize: 4 * 1024 * 1024, // 4MB
  allowedExtensions: ['bin', 'zip', 'enc'],
  dangerousExtensions: ['exe', 'bat', 'sh', 'js', 'py', 'php', 'msi', 'elf', 'html', 'htm', 'svg', 'jsp', 'war', 'jar', 'rb', 'go', 'cgi', 'pl'],

  /**
   * Validate a File object on the client side.
   * Returns an array of error strings (empty array = valid).
   * @param {File} file
   * @returns {Promise<string[]>}
   */
  async validate(file) {
    const errors = []
    if (file.size > this.maxSize) {
      const maxMb = this.maxSize / (1024 * 1024)
      errors.push(`The file is too large. Maximum allowed size is ${maxMb}MB.`)
    }
    const name = file.name.toLowerCase()
    const ext = name.split('.').pop()
    if (!this.allowedExtensions.includes(ext)) {
      errors.push('Unsupported file type. Please upload a valid .bin firmware file.')
    }
    const parts = name.split('.')
    for (const part of parts) {
      if (this.dangerousExtensions.includes(part)) { errors.push('Invalid file extension detected. Please upload a standard .bin firmware file.'); break }
    }
    if (file.size > 0) {
      try {
        const header = await this._readHeaderBytes(file, 16)
        const mimeType = this._sniffMime(header)
        const expectedMimes = { bin: ['application/octet-stream', 'application/x-binary'], zip: ['application/zip', 'application/x-zip-compressed'], enc: ['application/octet-stream', 'application/x-binary'] }
        if (expectedMimes[ext] && !expectedMimes[ext].includes(mimeType)) errors.push(`File content does not match the expected format for .${ext} files.`)
        if (header.length >= 4 && header[0] === 0x7F && header[1] === 0x45 && header[2] === 0x4C && header[3] === 0x46) errors.push('Executable system file detected. Please upload an official .bin MCU firmware file.')
      } catch (e) { /* non-blocking */ }
    }
    return errors
  },

  async _readHeaderBytes(file, bytes) {
    const blob = file.slice(0, bytes)
    const buf = await blob.arrayBuffer()
    return new Uint8Array(buf)
  },

  _sniffMime(header) {
    if (header.length >= 4 && header[0] === 0x50 && header[1] === 0x4B && header[2] === 0x03 && header[3] === 0x04) return 'application/zip'
    return 'application/octet-stream'
  },
}

// Allow server-provided overrides (e.g., if PHP sends tighter limits from getFirmwareValidationRules())
if (window.FW_VALIDATION_OVERRIDES) Object.assign(FW_VALIDATION, window.FW_VALIDATION_OVERRIDES)

/**
 * Find all byte offsets of patternHex within data, starting at startOffset.
 */
function findPatternOffsets(patternHex, data, startOffset = 0) {
	const pattern = hexToBytes(patternHex)
	const offsets = []
	let pos = startOffset
	while (pos <= data.length - pattern.length) {
		let found = true
		for (let i = 0; i < pattern.length; i++) {
			if (data[pos + i] !== pattern[i]) {
				found = false
				break
			}
		}
		if (found) offsets.push(pos)
		pos++
	}
	return offsets
}

/**
 * Extract firmware from a ZIP archive if necessary, then trim the trailing
 * 2-byte checksum appended by some tools (mirrors Python process_firmware).
 *
 * @param {Uint8Array} rawData  Raw file bytes from the user's file input.
 * @param {Function}   [log]   Optional log callback(msg, type) - avoids
 *                             global dependency on UI module (FIX-5).
 * @returns {Promise<{data: Uint8Array, raw: Uint8Array}>} Processed firmware bytes.
 *   data: trimmed buffer (trailing 2 bytes removed when > 4096 B - mirrors Python process_firmware)
 *   raw:  untrimmed buffer (use this for LEQI flashing so header and CRC logic see the full file)
 */
async function processFirmware(rawData, log) {
	let data = new Uint8Array(rawData)
	const _log = typeof log === 'function' ? log : () => {}

	// Try ZIP extraction (JSZip must be loaded before this module)
	try {
		const zip = await JSZip.loadAsync(data)
		const names = Object.keys(zip.files).filter(n => !zip.files[n].dir)
		if (names.length > 0) {
			const preferred = names.find(n => n.startsWith('EC_ESC_Driver') || n.endsWith('.enc')) || names[0]
			const content = await zip.files[preferred].async('uint8array')
			data = content
			// FIX-5: log via callback instead of undefined global logMsg
			_log(`Extracted from ZIP: ${preferred}`, 'info')
		}
	} catch (e) {
		/* not a ZIP - use raw bytes */
	}

	// Return both the untrimmed buffer and the trimmed view.
	// The trim mirrors Python: processed_fw[:-2] and is used only for type detection and info.
	// LEQI flashing must use raw so the EU1 header and embedded CRC remain intact.
	const untrimmed = data
	let trimmed = data
	if (data.length > 4096) trimmed = data.slice(0, data.length - 2)
	return { data: trimmed, raw: untrimmed }
}

/**
 * Detect firmware type from binary data.
 * Mirrors Python base_flasher.detect_firmware_type + leqi_flasher.detect_firmware_type (0.7.1).
 */
function detectFirmwareType(data) {
	if (data.length < 0x10) return 'UNKNOWN'

	// Brightway: signature "DEPRD5C\x00" at offset 0x800
	if (data.length > 0x808) {
		const sig = String.fromCharCode(...data.slice(0x800, 0x808))
		if (sig === 'DEPRD5C\x00') return 'BRIGHTWAY'
	}

	// Brightway alternative: exactly one 637C pattern past 0x1000
	if (data.length > 0x1000) {
		const offsets = findPatternOffsets('637C', data)
		if (offsets.length === 1 && offsets[0] > 0x1000) return 'BRIGHTWAY'
	}

	// LEQI: EU1 header tag at offset 0x0A (bw-flasher 0.7.1)
	if (data.length >= 0x10) {
		const tag = String.fromCharCode(...data.slice(0x0a, 0x0e))
		if (tag === 'EU1\0') {
			const sz = data[0x0e] | (data[0x0f] << 8)
			if (sz > 0 && sz <= 0xffff) return 'LEQI'
		}
	}

	// LEQI fallback: dense 0xAA / 0xAA 0xA2 patterns in the header region 0x80–0x400
	if (data.length >= 0x400) {
		const slice = data.slice(0x80, 0x400)
		let aaCount = 0,
			aaA2Count = 0
		for (let i = 0; i < slice.length; i++) {
			if (slice[i] === 0xaa) {
				aaCount++
				if (i + 1 < slice.length && slice[i + 1] === 0xa2) aaA2Count++
			}
		}
		if (aaA2Count > 10 && aaCount > 50) return 'LEQI'
	}

	return 'UNKNOWN'
}

function getFirmwareInfo(data, raw = null) {
	const type = detectFirmwareType(data)
	const info = { type, size: data.length }

	if (type === 'BRIGHTWAY') {
		if (data.length > 0x808) info.signature = String.fromCharCode(...data.slice(0x800, 0x807))
		info.protocol = 'DFU (Device Firmware Update)'
		const offsets = findPatternOffsets('637C', data)
		if (offsets.length > 0) info.signingOffset = `0x${offsets[0].toString(16).toUpperCase()}`
	} else if (type === 'LEQI') {
		const src = raw || data
		const bodySize = typeof LeqiFlasher !== 'undefined' ? LeqiFlasher.parseHeaderFirmwareSize(src) : null
		if (bodySize !== null && bodySize !== undefined) {
			info.bodySize = `${bodySize} B (0x${bodySize.toString(16).toUpperCase()})`
			info.sizeSource = 'EU1 header'
		} else {
			info.sizeSource = 'AA padding'
		}
		const isPlaintext = typeof LeqiFlasher !== 'undefined' && LeqiFlasher.isPlaintextLeqiBody ? LeqiFlasher.isPlaintextLeqiBody(src) : false
		info.encryption = isPlaintext ? 'none (plaintext)' : 'XOR 0xAA'
		info.protocol = 'Binary packets (5A 12 header)'
	}

	return info
}
