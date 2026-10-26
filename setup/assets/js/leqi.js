// bw-flasher-web - leqi.js
// LEQI binary flasher - 5A 12 packet protocol, 128-byte chunks
// ScooterTeam © 2024-2025 CC BY-NC-SA 4.0

'use strict'

// ═══════════════════════════════════════════════════════════════════════════
//  LEQI FLASHER (5A 12 Binary Protocol)
// ═══════════════════════════════════════════════════════════════════════════

const LEQI_FIRMWARE_OFFSET = 0x80
const LEQI_FIRMWARE_SIZE = 0x9880 // legacy fallback extraction window (39040 bytes)
const LEQI_CHUNK_SIZE = 128
const LEQI_HEADER_TAG = [0x45, 0x55, 0x31, 0x00] // "EU1\0"
const LEQI_HEADER_TAG_OFFSET = 0x0a
const LEQI_HEADER_SIZE_FIELD_OFFSET = 0x0e
const LEQI_CRC_START_OFFSET = 0x40
const LEQI_MIN_FIRMWARE_SIZE = 0x42 // 66 bytes
const LEQI_ENCRYPTION_KEY = 0xaa
const LEQI_PLAINTEXT_AA_DENSITY_MAX = 0.02
const LEQI_PLAINTEXT_AA_SAMPLE_START = 0x80
const LEQI_PLAINTEXT_AA_SAMPLE_END = 0x400

class LeqiFlasher {
	constructor(serial, { simulation = false, debug = false, onLog, onStatus, onProgress, abortSignal } = {}) {
		this.serial = serial
		this.simulation = simulation
		this.debug = debug
		this.log = onLog || (() => {})
		this.status = onStatus || (() => {})
		this.progress = onProgress || (() => {})
		this.abort = abortSignal || { aborted: false }

		this.fw = null
		this.encryptedFw = null
		this.fwSize = 0
		this.plaintextBody = false
	}

	/**
	 * Read the authoritative EU1 body size from a full image header.
	 * Returns the uint16 LE value at 0x0E, or null when the tag is missing,
	 * the value is implausible, or the file is too short.
	 */
	static parseHeaderFirmwareSize(data) {
		if (data.length < LEQI_HEADER_SIZE_FIELD_OFFSET + 2) return null
		for (let i = 0; i < LEQI_HEADER_TAG.length; i++) {
			if (data[LEQI_HEADER_TAG_OFFSET + i] !== LEQI_HEADER_TAG[i]) return null
		}
		const size = data[LEQI_HEADER_SIZE_FIELD_OFFSET] | (data[LEQI_HEADER_SIZE_FIELD_OFFSET + 1] << 8)
		if (size < LEQI_MIN_FIRMWARE_SIZE) return null
		if (data.length < LEQI_FIRMWARE_OFFSET + size) return null
		return size
	}

	/** Extract the firmware body from a full image. */
	static extractFirmwareFromImage(data, firmwareSize = null) {
		let size = firmwareSize
		if (size === null || size === undefined) size = LeqiFlasher.parseHeaderFirmwareSize(data)
		if (size === null || size === undefined) size = LEQI_FIRMWARE_SIZE
		if (data.length < LEQI_FIRMWARE_OFFSET + size) {
			throw new Error(
				`Image too small: ${data.length} bytes. Expected at least ${LEQI_FIRMWARE_OFFSET + size} bytes`,
			)
		}
		return data.slice(LEQI_FIRMWARE_OFFSET, LEQI_FIRMWARE_OFFSET + size)
	}

	/**
	 * True when EU1 body lacks XOR-0xAA density (5 Plus-style plaintext).
	 */
	static isPlaintextLeqiBody(firmwareData) {
		const data = firmwareData instanceof Uint8Array ? firmwareData : new Uint8Array(firmwareData)
		if (!data || data.length === 0) return false
		const start = Math.min(LEQI_PLAINTEXT_AA_SAMPLE_START, data.length)
		const end = Math.min(LEQI_PLAINTEXT_AA_SAMPLE_END, data.length)
		const sample = end > start ? data.subarray(start, end) : data
		if (sample.length === 0) return false
		let count = 0
		for (let i = 0; i < sample.length; i++) {
			if (sample[i] === LEQI_ENCRYPTION_KEY) count++
		}
		return (count / sample.length) < LEQI_PLAINTEXT_AA_DENSITY_MAX
	}

	/**
	 * True when Elite-style embedded BE CRC over [0x40:size-2] matches.
	 */
	embeddedCrcOk(fwData, fwSize) {
		if (fwSize < LEQI_MIN_FIRMWARE_SIZE || fwData.length < fwSize) return false
		const embedded = (fwData[fwSize - 2] << 8) | fwData[fwSize - 1]
		const calculated = crc16Firmware(fwData.subarray(LEQI_CRC_START_OFFSET, fwSize - 2))
		return embedded === calculated
	}

	/** Validate the CRC-16 embedded in the last two bytes of the firmware body. */
	validateEmbeddedCrc(fwData, fwSize) {
		if (fwSize < LEQI_MIN_FIRMWARE_SIZE) {
			throw new Error(
				`Firmware too small for CRC validation: 0x${fwSize.toString(16).toUpperCase()} bytes ` +
					`(minimum 0x${LEQI_MIN_FIRMWARE_SIZE.toString(16).toUpperCase()})`,
			)
		}
		if (fwData.length < fwSize) {
			throw new Error(
				`Firmware buffer too small: have ${fwData.length} bytes, ` +
					`need 0x${fwSize.toString(16).toUpperCase()} for declared size`,
			)
		}
		const embedded = (fwData[fwSize - 2] << 8) | fwData[fwSize - 1]
		const calculated = crc16Firmware(fwData.subarray(LEQI_CRC_START_OFFSET, fwSize - 2))
		if (embedded !== calculated) {
			throw new Error(
				`Embedded firmware CRC mismatch at size 0x${fwSize.toString(16).toUpperCase()}: ` +
					`expected 0x${embedded.toString(16).toUpperCase().padStart(4, '0')}, ` +
					`calculated 0x${calculated.toString(16).toUpperCase().padStart(4, '0')}. ` +
					`The file was probably edited without the "Fix checksum" patch, or is a truncated download. ` +
					`Re-create it with the web patcher (it adds the checksum automatically).`,
			)
		}
		if (this.debug) {
			this.log(
				`Embedded CRC validated: 0x${embedded.toString(16).toUpperCase().padStart(4, '0')}`,
				'dbg',
			)
		}
	}

	loadFirmware(data) {
		this.fw = data
		const headerSize = LeqiFlasher.parseHeaderFirmwareSize(data)
		let isFullImage =
			data.length > LEQI_FIRMWARE_OFFSET && (headerSize !== null || data.length > LEQI_FIRMWARE_SIZE)

		// CRC-fail guard: a headerless file larger than the legacy window is not
		// necessarily a full image. Bodies of newer models (Mi 6 = 0xA800, Mi 6 Lite
		// = 0xB000) exceed 0x9880, so a raw body patched with only "sls,chk" used to
		// be cut at offset 0x80 and then rejected with a bogus CRC mismatch. When the
		// whole file already carries a valid embedded CRC, treat it as a raw body.
		if (isFullImage && headerSize === null && this.embeddedCrcOk(data, data.length)) {
			isFullImage = false
			this.log('Headerless file with a valid embedded CRC: loading as raw firmware body', 'info')
		}

		this.encryptedFw = isFullImage ? LeqiFlasher.extractFirmwareFromImage(data, headerSize) : data

		let sizeSource
		if (headerSize !== null) {
			if (headerSize > 0xffff) throw new Error(`Firmware body size 0x${headerSize.toString(16).toUpperCase()} exceeds uint16 limit`)
			this.fwSize = headerSize
			sizeSource = 'header'
		} else if (!isFullImage && this.embeddedCrcOk(data, data.length)) {
			this.fwSize = data.length
			sizeSource = 'file length (CRC verified)'
		} else {
			this.fwSize = this._calcFirmwareSize(this.encryptedFw)
			sizeSource = 'AA padding'
		}

		this.plaintextBody = false
		if (this.embeddedCrcOk(this.encryptedFw, this.fwSize)) {
			this.validateEmbeddedCrc(this.encryptedFw, this.fwSize)
		} else if (LeqiFlasher.isPlaintextLeqiBody(this.encryptedFw)) {
			this.plaintextBody = true
			this.log('Plaintext LEQI body: skipping embedded CRC (5 Plus-style)', 'info')
		} else {
			this.validateEmbeddedCrc(this.encryptedFw, this.fwSize)
		}

		this.log(`Loaded LEQI firmware: ${data.length} bytes`, 'info')
		this.log(
			`Firmware size (${sizeSource}): 0x${this.fwSize.toString(16).toUpperCase()} (${this.fwSize} bytes)`,
			'info',
		)
	}

	_calcFirmwareSize(data) {
		let maxLen = 0,
			maxEnd = 0
		let i = 0
		while (i < data.length) {
			if (data[i] === 0xaa) {
				const start = i
				while (i < data.length && data[i] === 0xaa) i++
				const len = i - start
				if (len > maxLen && len > 500) {
					maxLen = len
					maxEnd = i
				}
			} else i++
		}
		if (maxEnd > 0) return Math.ceil(maxEnd / 128) * 128
		return data.length
	}

	_crc16(data) {
		return crc16(data)
	} // CRC-16/XMODEM - same poly/init

	_buildPacket(type, payload) {
		const hdr = new Uint8Array([0x5a, 0x12, type, payload.length])
		const full = new Uint8Array(hdr.length + payload.length)
		full.set(hdr)
		full.set(payload, hdr.length)
		const crcVal = this._crc16(full)
		const out = new Uint8Array(full.length + 2)
		out.set(full)
		out[full.length] = (crcVal >> 8) & 0xff
		out[full.length + 1] = crcVal & 0xff
		return out
	}

	async run() {
		if (!this.encryptedFw) throw new Error('No firmware loaded')
		if (this.simulation) {
			await this._runSimulation()
			return
		}

		await this._sendStartCommand()
		await this._sendFirmwareData()
		await this._sendEndCommand()

		this.log('✓ LEQI firmware update completed', 'ok')
		this.progress(100)
	}

	async _sendAndReceive(pkt, desc, timeout = 2000, expectedLen = 7) {
		const bytes = pkt instanceof Uint8Array ? pkt : new Uint8Array(pkt)
		if (this.debug) this.log(`TX [${desc}]: ${toHex(bytes)}`, 'tx')
		this.serial.clearBuffer()
		await this.serial.write(bytes)
		// Small delay for controller to process (matches Python's time.sleep(0.05))
		await sleep(50)
		// Read response: scan raw rxBuf for 0x5A header byte directly.
		// NOTE: do NOT use receiveResponse() here - it uses 0x5A as a "terminator"
		// which would CONSUME the start byte, breaking the response parsing.
		const full = await this._readLeqiResponse(timeout, expectedLen)
		if (this.debug && full) this.log(`RX [${desc}]: ${toHex(full)}`, 'rx')
		return full
	}

	async _readLeqiResponse(timeout = 2000, expectedLen = 7) {
		const deadline = Date.now() + timeout
		const out = []

		// Wait for the 0x5A header byte.
		while (Date.now() < deadline && out.length === 0) {
			if (this.serial.rxBuf.length > 0) {
				const b = this.serial.rxBuf.shift()
				if (b === 0x5a) out.push(b)
			} else {
				await this.serial._waitRx(10)
			}
		}
		if (out.length === 0) return null

		// Read the rest until expectedLen, or until the line goes quiet after 5 bytes.
		while (out.length < expectedLen && Date.now() < deadline) {
			if (this.serial.rxBuf.length > 0) {
				out.push(this.serial.rxBuf.shift())
				continue
			}
			if (out.length >= 5) {
				await sleep(20)
				if (this.serial.rxBuf.length === 0) break
				continue
			}
			await this.serial._waitRx(10)
		}

		return new Uint8Array(out)
	}

	async _sendStartCommand() {
		this.status('Sending firmware update start command…')
		const payload = new Uint8Array(8)
		payload[0] = 0x31
		payload[1] = 0x00
		payload[2] = this.fwSize & 0xff
		payload[3] = (this.fwSize >> 8) & 0xff
		payload[4] = 0x00
		payload[5] = 0x00
		const pkt = this._buildPacket(0x03, payload.slice(0, 6))

		const resp = await this._sendAndReceive(pkt, 'Start')
		if (!resp || resp.length < 5 || resp[1] !== 0x21 || resp[2] !== 0x03)
			throw new Error('Invalid start response from controller')
		this.log('✓ Start command acknowledged', 'ok')
	}

	async _sendFirmwareData() {
		this.status('Sending firmware data…')
		let offset = 0,
			chunkNum = 0,
			failedChunks = 0
		const totalChunks = Math.ceil(this.fwSize / LEQI_CHUNK_SIZE)

		while (offset < this.fwSize) {
			if (this.abort.aborted) throw new Error('Aborted')

			const end = Math.min(offset + LEQI_CHUNK_SIZE, this.fwSize)
			let chunk = this.encryptedFw.slice(offset, end)
			if (chunk.length < LEQI_CHUNK_SIZE) {
				const padded = new Uint8Array(LEQI_CHUNK_SIZE).fill(0xff)
				padded.set(chunk)
				chunk = padded
			}

			// Packet: [5A][12][04][0x84][offset_32LE][data_128][crc_16BE]
			const payload = new Uint8Array(4 + LEQI_CHUNK_SIZE)
			const view = new DataView(payload.buffer)
			view.setUint32(0, offset, true) // LE offset
			payload.set(chunk, 4)

			const hdr = new Uint8Array([0x5a, 0x12, 0x04, 0x84])
			const full = new Uint8Array(hdr.length + payload.length)
			full.set(hdr)
			full.set(payload, 4)
			const crcVal = this._crc16(full)
			const pkt = new Uint8Array(full.length + 2)
			pkt.set(full)
			pkt[full.length] = (crcVal >> 8) & 0xff
			pkt[full.length + 1] = crcVal & 0xff

			chunkNum++
			const resp = await this._sendAndReceive(pkt, `Chunk ${chunkNum} @ 0x${offset.toString(16)}`)

			if (!resp) {
				this.log(`WARNING: No response for chunk ${chunkNum}`, 'warn')
				failedChunks++
			} else if (resp.length < 5 || resp[1] !== 0x21 || resp[2] !== 0x04) {
				this.log(`WARNING: Invalid response for chunk ${chunkNum}`, 'warn')
				failedChunks++
			} else if (resp[4] !== 0x01) {
				this.log(`ERROR: Chunk ${chunkNum} REJECTED (status=0x${resp[4].toString(16)})`, 'err')
				failedChunks++
			}

			offset = end
			const pct = Math.floor((chunkNum / totalChunks) * 90)
			this.progress(pct)

			if (chunkNum % 10 === 0) {
				this.log(`Progress: ${chunkNum}/${totalChunks} chunks (${pct}%)`, 'info')
			}

			await sleep(44)
		}

		if (failedChunks > 0) throw new Error(`${failedChunks} chunks had invalid/missing responses`)
		this.log(`✓ Sent ${chunkNum} chunks`, 'ok')
		await sleep(690)
	}

	async _sendEndCommand() {
		this.status('Finalizing firmware update…')
		const pkt = this._buildPacket(0x05, new Uint8Array(0))

		for (let attempt = 1; attempt <= 10; attempt++) {
			if (attempt > 1) {
				this.log(`Retry ${attempt}/10…`, 'warn')
				await sleep(60)
			}
			const resp = await this._sendAndReceive(pkt, `End (attempt ${attempt})`, 400)
			if (resp && resp.length >= 5 && resp[1] === 0x21 && resp[2] === 0x05) {
				this.log('✓ End command acknowledged', 'ok')
				return
			}
		}
		throw new Error('No valid response to end command after 10 attempts')
	}

	async testConnection() {
		// DeviceInfo command 0x02, no payload.
		const head = new Uint8Array([0x5a, 0x12, 0x02, 0x00])
		const crcVal = this._crc16(head)
		const pkt = new Uint8Array(6)
		pkt.set(head)
		pkt[4] = (crcVal >> 8) & 0xff
		pkt[5] = crcVal & 0xff

		if (this.simulation) {
			const simData = new Uint8Array([
				0x5a, 0x21, 0x02, 0x0b, 0x00, 0x01, 0x02, 0x00, 0x00, 0x01, 0x00, 0x00, 0x45, 0x55, 0x31,
			])
			const simCrc = this._crc16(simData)
			const resp = new Uint8Array(simData.length + 2)
			resp.set(simData)
			resp[simData.length] = (simCrc >> 8) & 0xff
			resp[simData.length + 1] = simCrc & 0xff
			if (this.debug) {
				this.log(`TX (simulated): ${toHex(pkt)}`, 'tx')
				this.log(`RX (simulated): ${toHex(resp)}`, 'rx')
			}
			const info = LeqiFlasher.parseDeviceInfo(resp)
			this.log(
				`✓ Simulation successful. Controller firmware version: ${info.version}, Region: ${info.region}`,
				'ok',
			)
			this.progress(100)
			return
		}

		const resp = await this._sendAndReceive(pkt, 'DeviceInfo', 2000, 17)
		if (!resp || resp.length < 15 || resp[1] !== 0x21 || resp[2] !== 0x02) {
			throw new Error('Connection test failed: invalid or no response from controller')
		}

		const info = LeqiFlasher.parseDeviceInfo(resp)
		this.log(
			`✓ Connection successful. Controller firmware version: ${info.version}, Region: ${info.region}`,
			'ok',
		)
		this.progress(100)
	}

	/** Parse version (bytes 4 to 7) and region (bytes 12 to 14) from a DeviceInfo reply. */
	static parseDeviceInfo(resp) {
		const version = Array.from(resp.subarray(4, 8)).join('')
		const region = bytesToAscii(resp.subarray(12, 15)).replace(/[^\x20-\x7e]/g, '')
		return { version, region }
	}

	async _runSimulation() {
		this.status('SIMULATION: Starting LEQI firmware flash…')
		await sleep(100)
		this.log('[SIM] Start command → ACK', 'dbg')
		this.progress(5)

		const totalChunks = Math.ceil(this.fwSize / LEQI_CHUNK_SIZE)
		for (let i = 1; i <= totalChunks; i++) {
			if (this.abort.aborted) throw new Error('Aborted')
			await sleep(5)
			const pct = 5 + Math.floor((i / totalChunks) * 85)
			this.progress(pct)
			if (i % 50 === 0 || i === totalChunks) {
				this.log(`[SIM] Chunk ${i}/${totalChunks} (${pct}%)`, 'dbg')
			}
		}

		this.status('SIMULATION: Finalizing…')
		this.progress(95)
		await sleep(200)
		this.log('✓ SIMULATION: LEQI firmware update completed!', 'ok')
		this.progress(100)
	}
}
