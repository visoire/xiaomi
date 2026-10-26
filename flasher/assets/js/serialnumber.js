// serialnumber.js
// LEQI dashboard scooter serial (command 0x97), web port of bwflasher/serial_number.py.
// Command 0x97 is handled by the dashboard BLE firmware (RTL8762C), not the motor
// controller. The serial is persisted in BLE NVM. Brightway and Ninebot do not
// support it.
//
// Packet (19200 8N1): [5A] [12] [97] [SN_LEN+1] [30] [SN...] [CRC_H] [CRC_L]
// CRC is CRC-16/XMODEM, same as every other LEQI packet.
//
// Protocol: ScooterTeam, CC BY-NC-SA 4.0. Web port by visoire.

'use strict'

const LEQI_SERIAL_LENGTH = 19
const LEQI_SERIAL_PREFIX_LENGTH = 5
const LEQI_SERIAL_SUFFIX_LENGTH = LEQI_SERIAL_LENGTH - LEQI_SERIAL_PREFIX_LENGTH // 14
const LEQI_SERIAL_PREFIX_EXAMPLE = '05393'

const LeqiSerialNumber = {
	/** Join prefix and serial parts into the full 19 character value. */
	compose(prefix, suffix) {
		return `${prefix || ''}${suffix || ''}`
	},

	/** Throws on anything but exactly 19 printable ASCII characters. */
	validate(serialNumber) {
		if (typeof serialNumber !== 'string') throw new Error('Serial number must be a string')
		if (serialNumber.length !== LEQI_SERIAL_LENGTH) {
			throw new Error(
				`Serial number must be exactly ${LEQI_SERIAL_LENGTH} characters ` +
					`(prefix ${LEQI_SERIAL_PREFIX_LENGTH} + serial ${LEQI_SERIAL_SUFFIX_LENGTH}), got ${serialNumber.length}`,
			)
		}
		if (!/^[\x20-\x7e]+$/.test(serialNumber)) {
			throw new Error('Serial number must contain only printable ASCII characters')
		}
		return serialNumber
	},

	buildPacket(serialNumber) {
		this.validate(serialNumber)
		const sn = strToBytes(serialNumber)
		const body = new Uint8Array(5 + sn.length)
		body.set([0x5a, 0x12, 0x97, sn.length + 1, 0x30])
		body.set(sn, 5)
		const crc = crc16(body)
		const out = new Uint8Array(body.length + 2)
		out.set(body)
		out[body.length] = (crc >> 8) & 0xff
		out[body.length + 1] = crc & 0xff
		return out
	},

	/** @returns {{ok: boolean, serial: string|null, message: string}} */
	parseResponse(data) {
		if (!data || data.length < 8) {
			return { ok: false, serial: null, message: `Response too short: ${data ? data.length : 0} bytes (expected at least 8)` }
		}
		if (data[0] !== 0x5a) return { ok: false, serial: null, message: `Invalid header: 0x${data[0].toString(16)} (expected 0x5A)` }
		if (data[1] !== 0x12) return { ok: false, serial: null, message: `Invalid response command: 0x${data[1].toString(16)} (expected 0x12)` }
		if (data[2] !== 0x97) return { ok: false, serial: null, message: `Invalid subcommand: 0x${data[2].toString(16)} (expected 0x97)` }

		const payloadLen = data[3]
		if (payloadLen < 2) return { ok: false, serial: null, message: `Invalid length: ${payloadLen} (must be >= 2)` }
		const snLen = payloadLen - 1
		const expected = 5 + snLen + 2
		if (data.length < expected) {
			return { ok: false, serial: null, message: `Response too short: ${data.length} bytes (expected ${expected})` }
		}
		if (data[4] !== 0x30) return { ok: false, serial: null, message: `Invalid field marker: 0x${data[4].toString(16)} (expected 0x30)` }

		const serial = bytesToAscii(data.subarray(5, 5 + snLen))
		const got = (data[5 + snLen] << 8) | data[5 + snLen + 1]
		const calc = crc16(data.subarray(0, 5 + snLen))
		if (got !== calc) {
			return {
				ok: false,
				serial,
				message: `CRC mismatch: got 0x${got.toString(16).toUpperCase().padStart(4, '0')}, expected 0x${calc.toString(16).toUpperCase().padStart(4, '0')}`,
			}
		}
		return { ok: true, serial, message: 'OK' }
	},

	/** Read one 0x97 response from the adapter's rx buffer (header scan, then fixed length). */
	async _readResponse(serial, timeout = 2000) {
		const deadline = Date.now() + timeout
		const out = []
		while (Date.now() < deadline && out.length === 0) {
			if (serial.rxBuf.length > 0) {
				const b = serial.rxBuf.shift()
				if (b === 0x5a) out.push(b)
			} else {
				await serial._waitRx(10)
			}
		}
		if (out.length === 0) return null

		let total = 5 // header, cmd, subcmd, len, 0x30 - refined once the length byte arrives
		while (out.length < total && Date.now() < deadline) {
			if (serial.rxBuf.length > 0) {
				out.push(serial.rxBuf.shift())
				if (out.length === 4) total = 5 + (out[3] - 1) + 2
			} else {
				await serial._waitRx(10)
			}
		}
		return new Uint8Array(out)
	},

	/**
	 * Write the scooter serial to the dashboard BLE module.
	 * @param {object} serial  FlasherSerial-like adapter (ignored when simulation is true)
	 * @param {string} serialNumber 19 character serial
	 * @param {{simulation?: boolean, debug?: boolean, onLog?: Function, abortSignal?: {aborted: boolean}, maxAttempts?: number}} opts
	 * @returns {Promise<{ok: boolean, serial: string|null, message: string}>}
	 */
	async write(serial, serialNumber, opts = {}) {
		const { simulation = false, debug = false, onLog = () => {}, abortSignal = { aborted: false }, maxAttempts = 10 } = opts
		let packet
		try {
			packet = this.buildPacket(serialNumber)
		} catch (e) {
			return { ok: false, serial: null, message: e.message }
		}

		const crc = (packet[packet.length - 2] << 8) | packet[packet.length - 1]
		// Simulation always shows the traffic so the dry run is visible without Debug.
		const verbose = debug || simulation
		if (verbose) {
			onLog(`TX [Set serial] (${packet.length} bytes): ${toHex(packet)}`, 'tx')
			onLog(`     Serial: ${serialNumber}   CRC: 0x${crc.toString(16).toUpperCase().padStart(4, '0')}`, 'dbg')
		}

		if (simulation) {
			onLog(`RX (simulated ACK, ${packet.length} bytes): ${toHex(packet)}`, 'rx')
			onLog(`[SIM] Scooter serial would be set to '${serialNumber}'`, 'dbg')
			return { ok: true, serial: serialNumber, message: 'OK (simulated)' }
		}

		if (!serial || !serial.connected) return { ok: false, serial: null, message: 'Not connected' }

		let firstError = null
		serial.clearBuffer()
		await serial.write(packet)
		for (let attempt = 1; attempt <= maxAttempts; attempt++) {
			if (abortSignal.aborted) return { ok: false, serial: null, message: 'Aborted' }
			const resp = await this._readResponse(serial, 2000)
			if (!resp) {
				const msg = `Timeout waiting for response ${attempt}`
				if (!firstError) firstError = { ok: false, serial: null, message: msg }
				if (verbose) onLog(msg, 'warn')
				continue
			}
			if (verbose) onLog(`RX [Set serial] (${resp.length} bytes): ${toHex(resp)}`, 'rx')
			const parsed = this.parseResponse(resp)
			if (parsed.ok && parsed.serial === serialNumber) return { ok: true, serial: parsed.serial, message: 'OK' }
			if (!firstError) firstError = parsed
			if (verbose) onLog(`Response ${attempt}: ${parsed.message}`, 'warn')
		}
		return firstError || { ok: false, serial: null, message: 'No valid response received' }
	},
}
