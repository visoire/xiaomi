// flasher-ui.js
// UI controller for the visoire web flasher.
// Wires the redesigned DOM to the unchanged protocol classes
// (WebSerialAdapter, WebUSBAdapter, SimulationSerial, BrightwayFlasher, LeqiFlasher).
// Built by visoire. Core protocol logic by ScooterTeam, CC BY-NC-SA 4.0.

'use strict'

const T = (window.FLASHER_I18N || {})
const tr = (k) => T[k] || k

const UI = {
  $: (id) => document.getElementById(id),

  _logEl: null,
  _abortCtrl: null,
  _serial: null,
  _fwData: null,
  _fwRaw: null,
  _fwType: null,
  _flashing: false,

  init() {
    this._logEl = this.$('log')

    this.$('btnWebSerial').addEventListener('click', () => this.connectWebSerial())
    this.$('btnWebUSB').addEventListener('click', () => this.connectWebUSB())
    this.$('btnDisconnect').addEventListener('click', () => this.disconnect())
    this.$('btnTest').addEventListener('click', () => this.testConnection())
    this.$('btnFlash').addEventListener('click', () => this.startFlash())
    this.$('btnAbort').addEventListener('click', () => this.abortFlash())
    this.$('btnClear').addEventListener('click', () => { this._logEl.innerHTML = '' })
    this.$('btnExport').addEventListener('click', () => this.exportLog())
    this.$('fwFile').addEventListener('change', (e) => this.onFileSelect(e))
    this.$('chkSim').addEventListener('change', () => { this._savePrefs(); this._updateSimMode() })
    this.$('chkDebug').addEventListener('change', () => this._savePrefs())
    this.$('chkSetup').addEventListener('change', () => { this._savePrefs(); this._updateModePill() })
    this.$('btnDashWrite').addEventListener('click', () => this.writeDashboardSerial())
    ;['dashPrefix', 'dashSerial'].forEach((id) =>
      this.$(id).addEventListener('input', () => this._updateDashPreview())
    )

    this._loadPrefs()
    this._updateModePill()
    this._updateDashPreview()
    this._setupDropZone()
    this._setupGlobalDrop()
    this._setupTurnstile()

    // Warn before leaving or refreshing while connected or flashing.
    window.addEventListener('beforeunload', (e) => {
      if (this._flashing) {
        e.preventDefault(); e.returnValue = tr('leave_warn')
      }
    })

    // Report API availability without scaring the user when only one is missing.
    if (!navigator.serial && !navigator.usb) {
      const warn = this.$('browserWarn')
      if (warn) warn.style.display = 'flex'
    } else {
      if (navigator.serial) this.addLog(tr('api_serial'), 'ok')
      if (navigator.usb) this.addLog(tr('api_usb'), 'ok')
    }

    this._updateSimMode()
    this.setFlashState('idle')
  },

  // ── Drag and drop ────────────────────────────────────────────────────────
  _setupDropZone() {
    const zone = this.$('dropZone')
    if (!zone) return
    zone.addEventListener('click', () => this.$('fwFile').click())
    zone.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.$('fwFile').click() }
    })
    ;['dragenter', 'dragover'].forEach((ev) =>
      zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.add('drag') })
    )
    ;['dragleave', 'drop'].forEach((ev) =>
      zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.remove('drag') })
    )
    zone.addEventListener('drop', (e) => {
      const file = e.dataTransfer.files[0]
      if (file) this.loadFirmwareFile(file)
    })
  },

  _setupGlobalDrop() {
    const overlay = this.$('globalDrop')
    if (!overlay) return
    let counter = 0
    window.addEventListener('dragenter', (e) => { e.preventDefault(); counter++; overlay.classList.add('active') })
    window.addEventListener('dragover', (e) => e.preventDefault())
    window.addEventListener('dragleave', (e) => {
      e.preventDefault(); counter--
      if (counter <= 0) { overlay.classList.remove('active'); counter = 0 }
    })
    window.addEventListener('drop', (e) => {
      e.preventDefault(); counter = 0; overlay.classList.remove('active')
      const file = e.dataTransfer.files[0]
      if (file) this.loadFirmwareFile(file)
    })
  },

  // ── Cloudflare Turnstile (invisible mode) ──────────────────────────────
  _turnstileToken: null,
  _turnstileActive: false,
  _setupTurnstile() {
    const container = this.$('cfTurnstile')
    if (!container || typeof turnstile === 'undefined') {
      this._turnstileActive = false
      this.addLog(tr('turnstile_skip') || 'Captcha skipped (Turnstile unavailable)', 'warn')
      return
    }
    this._turnstileActive = true
    try {
      const self = this
      window.flasherTurnstileCallback = function (token) { self._turnstileToken = token }
      window.flasherTurnstileError = function () { self._turnstileToken = null; self.addLog(tr('turnstile_error') || 'Captcha error', 'err') }
      window.flasherTurnstileExpired = function () { self._turnstileToken = null }
    } catch (e) {
      this.addLog(tr('turnstile_fail') || 'Failed to initialise captcha', 'warn')
    }
  },

  // ── Connection ───────────────────────────────────────────────────────────
  _updateSimMode() {
    const sim = this.$('chkSim').checked
    this.$('btnWebSerial').disabled = sim
    this.$('btnWebUSB').disabled = sim
    if (sim) {
      this.setConnected(true, tr('conn_sim'))
    } else if (!this._serial || !this._serial.connected) {
      this.setConnected(false, tr('disconnected'))
    }
    this._updateActionButtons()
  },

  async connectWebSerial() {
    if (!navigator.serial) { this.addLog(tr('serial_unsupported'), 'err'); return }
    try {
      const adapter = new WebSerialAdapter()
      await adapter.connect(19200)
      this._serial = adapter
      this.setConnected(true, tr('serial_ok'))
      this.addLog(tr('serial_ok'), 'ok')
    } catch (e) {
      this.addLog(`${tr('conn_fail')}: ${e.message}`, 'err')
    } finally {
      this._updateActionButtons()
    }
  },

  async connectWebUSB() {
    if (!navigator.usb) { this.addLog(tr('usb_unsupported'), 'err'); return }
    const hint = this.$('webusbHint')
    if (hint) hint.classList.add('show')
    try {
      const adapter = new WebUSBAdapter()
      await adapter.connect(19200)
      this._serial = adapter
      const name = adapter.deviceName || (adapter._dev && adapter._dev.productName) || 'USB'
      const chip = adapter.chipName || '?'
      this.setConnected(true, `${name} [${chip}]`)
      this.addLog(`${tr('usb_ok')}: ${name} (${chip})`, 'ok')
    } catch (e) {
      if (hint) hint.classList.remove('show')
      this.addLog(`${tr('conn_fail')}: ${e.message}`, 'err')
    } finally {
      this._updateActionButtons()
    }
  },

  async disconnect() {
    if (this._serial) {
      await this._serial.disconnect().catch(() => {})
      this._serial = null
    }
    this.setConnected(false, tr('disconnected'))
    const hint = this.$('webusbHint')
    if (hint) hint.classList.remove('show')
    this.addLog(tr('disconnected'), 'warn')
    this._updateActionButtons()
  },

  setConnected(connected, text) {
    const dot = this.$('connDot')
    dot.className = 'conn-dot' + (connected ? ' on' : '')
    this.$('connStatus').textContent = text
    this.$('btnDisconnect').disabled = !connected
    this._updateActionButtons()
  },

  // ── Firmware ─────────────────────────────────────────────────────────────
  async loadFirmwareFile(file) {
    if (!file) return

    // Client-side file validation before any processing
    const validationErrors = await FW_VALIDATION.validate(file)
    if (validationErrors.length > 0) {
      this.addLog(tr('fw_rejected') || 'File rejected:', 'err')
      validationErrors.forEach((err) => this.addLog('  • ' + err, 'err'))
      this._fwData = null
      this._fwRaw = null
      this._fwType = null
      this._updateActionButtons()
      return
    }

    this.addLog(`${tr('loading')}: ${file.name} (${file.size} B)`, 'info')
    try {
      const raw = await file.arrayBuffer()
      const proc = await processFirmware(new Uint8Array(raw), (m, t) => this.addLog(m, t))
      const info = getFirmwareInfo(proc.data, proc.raw)
      this._fwData = proc.data
      this._fwRaw = proc.raw
      this._fwType = info.type
      this._renderFwInfo(info, file.name)
      this.addLog(`${tr('fw_detected')}: ${info.type} (${proc.data.length} B)`, info.type !== 'UNKNOWN' ? 'ok' : 'warn')
      this._updateActionButtons()
    } catch (e) {
      this.addLog(`${tr('fw_error')}: ${e.message}`, 'err')
      this._fwData = null
      this._fwRaw = null
      this._fwType = null
      this._updateActionButtons()
    }
  },

  async onFileSelect(e) {
    await this.loadFirmwareFile(e.target.files[0])
  },

  _renderFwInfo(info, filename) {
    const box = this.$('fwInfo')
    const L = window.FLASHER_LABELS || {}
    const row = (k, v, cls = '') => `<div class="fw-k">${k}</div><div class="fw-v ${cls}">${escHtml(String(v))}</div>`
    const typeCls = info.type === 'UNKNOWN' ? 'unk' : 'ok'
    let html = ''
    html += row(L.file || 'File', filename)
    html += row(L.type || 'Type', info.type, typeCls)
    html += row(L.size || 'Size', `${info.size} B (0x${info.size.toString(16).toUpperCase()})`)
    if (info.protocol) html += row(L.protocol || 'Protocol', info.protocol)
    if (info.signature) html += row(L.signature || 'Signature', info.signature)
    if (info.encryption) html += row(L.encryption || 'Encryption', info.encryption)
    if (info.signingOffset) html += row(L.keyoffset || 'Key offset', info.signingOffset)
    if (info.bodySize) html += row(L.bodysize || 'Body size', info.bodySize)
    if (info.sizeSource) html += row(L.sizesource || 'Size source', info.sizeSource)
    box.innerHTML = html
    box.classList.add('filled')
  },

  _updateActionButtons() {
    const sim = this.$('chkSim').checked
    const hasConn = sim || (this._serial && this._serial.connected)
    const hasFw = !!(this._fwData && this._fwType && this._fwType !== 'UNKNOWN')
    const busy = this._flashing || this._dashBusy
    this.$('btnFlash').disabled = !(hasConn && hasFw) || busy
    this.$('btnTest').disabled = !hasConn || busy
    this.$('btnDashWrite').disabled = !(hasConn && this._dashSerialValue()) || busy
  },

  // ── Preferences (per-viewer convenience, storage may be unavailable) ─────
  _loadPrefs() {
    // Setup (guided) mode is the default: it is the safer choice for a first flash.
    this.$('chkSetup').checked = true
    try {
      const raw = localStorage.getItem('flasher_prefs')
      if (!raw) return
      const p = JSON.parse(raw)
      this.$('chkSim').checked = !!p.sim
      this.$('chkDebug').checked = !!p.debug
      this.$('chkSetup').checked = p.setup !== false
    } catch (e) { /* ignore */ }
  },

  _savePrefs() {
    try {
      localStorage.setItem('flasher_prefs', JSON.stringify({
        sim: this.$('chkSim').checked,
        debug: this.$('chkDebug').checked,
        setup: this.$('chkSetup').checked,
      }))
    } catch (e) { /* ignore */ }
  },

  _isSetupMode() { return this.$('chkSetup').checked },

  _updateModePill() {
    const pill = this.$('modePill')
    const setup = this._isSetupMode()
    pill.textContent = setup ? pill.dataset.setup : pill.dataset.start
    pill.classList.toggle('on', setup)
  },

  // ── Dashboard serial (LEQI only) ─────────────────────────────────────────
  _dashBusy: false,

  _dashSerialValue() {
    const full = LeqiSerialNumber.compose(this.$('dashPrefix').value, this.$('dashSerial').value)
    try { return LeqiSerialNumber.validate(full) } catch (e) { return null }
  },

  _updateDashPreview() {
    const full = LeqiSerialNumber.compose(this.$('dashPrefix').value, this.$('dashSerial').value)
    this.$('dashPreview').textContent = full || '-'
    const count = this.$('dashCount')
    count.textContent = `${full.length}/${LEQI_SERIAL_LENGTH}`
    count.classList.toggle('ok', full.length === LEQI_SERIAL_LENGTH)
    this._updateActionButtons()
  },

  async writeDashboardSerial() {
    const full = LeqiSerialNumber.compose(this.$('dashPrefix').value, this.$('dashSerial').value)
    try {
      LeqiSerialNumber.validate(full)
    } catch (e) {
      this.addLog(`${tr('dash_invalid')}: ${e.message}`, 'err')
      return
    }

    const sim = this.$('chkSim').checked
    const debug = this.$('chkDebug').checked
    const hasConn = sim || (this._serial && this._serial.connected)
    if (!hasConn) { this.addLog(tr('need_conn') || 'Please connect via Web Serial or WebUSB first.', 'warn'); return }

    // The dashboard BLE module does not answer the motor controller probe, so
    // setup mode only asks for confirmation here. Start mode writes immediately.
    if (this._isSetupMode() && !sim && !window.confirm(`${tr('dash_confirm')}\n\n${full}`)) {
      this.addLog(tr('setup_cancel'), 'warn')
      return
    }

    this._abortCtrl = { aborted: false }
    this._dashBusy = true
    this._updateActionButtons()
    this.setFlashState('flashing')
    try {
      const serial = sim ? null : this._serial
      const res = await LeqiSerialNumber.write(serial, full, {
        simulation: sim,
        debug,
        onLog: (m, t) => this.addLog(m, t),
        abortSignal: this._abortCtrl,
      })
      if (res.ok) {
        this.addLog(`${sim ? tr('dash_sim_ok') : tr('dash_ok')}: ${res.serial}`, 'ok')
        this.setFlashState('done')
      } else {
        this.addLog(`${tr('dash_fail')} (${res.message})`, 'err')
        this.setFlashState('error')
      }
    } catch (e) {
      this.addLog(`${tr('dash_fail')} (${e.message})`, 'err')
      this.setFlashState('error')
    } finally {
      this._dashBusy = false
      this._updateActionButtons()
    }
  },

  // ── Operations ───────────────────────────────────────────────────────────
  _buildOpts(sim, debug) {
    return {
      simulation: sim,
      debug,
      onLog: (m, t) => this.addLog(m, t),
      onStatus: (s) => this.setStatus(s),
      onProgress: (p) => this.setProgress(p),
      abortSignal: this._abortCtrl,
    }
  },

  async testConnection() {
    const sim = this.$('chkSim').checked
    const hasConn = sim || (this._serial && this._serial.connected)
    if (!hasConn) {
      this.addLog(tr('need_conn') || 'Please connect via Web Serial or WebUSB first.', 'warn')
      return false
    }

    const debug = this.$('chkDebug').checked
    const serial = sim ? new SimulationSerial() : this._serial
    if (sim && !serial.connected) await serial.connect()

    this._abortCtrl = { aborted: false }
    const opts = this._buildOpts(sim, debug)

    try {
      this.setFlashState('flashing')
      this.setStatus('Testing connection…')

      if (this._fwType && this._fwType !== 'UNKNOWN') {
        const type = this._fwType
        const F = type === 'LEQI' ? LeqiFlasher : BrightwayFlasher
        const f = new F(serial, opts)
        const payload = type === 'LEQI' ? (this._fwRaw || this._fwData) : this._fwData
        if (payload) f.loadFirmware(payload)
        await f.testConnection()
        this.addLog(tr('test_pass'), 'ok')
      } else {
        // No firmware loaded: probe hardware (try LEQI first, then Brightway)
        this.addLog('Probing scooter controller…', 'info')
        let detected = false
        let lastErr = null

        // 1. Probe LEQI
        try {
          const fLeqi = new LeqiFlasher(serial, opts)
          await fLeqi.testConnection()
          detected = true
          this.addLog('✓ LEQI protocol detected and connection established!', 'ok')
        } catch (eLeqi) {
          lastErr = eLeqi
          if (serial.clearBuffer) serial.clearBuffer()
        }

        // 2. If not LEQI, probe Brightway
        if (!detected) {
          try {
            const fBw = new BrightwayFlasher(serial, opts)
            await fBw.testConnection()
            detected = true
            this.addLog('✓ Brightway protocol detected and connection established!', 'ok')
          } catch (eBw) {
            lastErr = eBw
            if (serial.clearBuffer) serial.clearBuffer()
          }
        }

        if (!detected) {
          throw new Error(
            lastErr ? lastErr.message : 'No response from controller. Ensure scooter is ON with taillight lit and TX/RX wiring is correct.'
          )
        }
      }

      this.setStatus('Connection test passed')
      this.setFlashState('done')
      return true
    } catch (e) {
      this.addLog(`${tr('test_fail')}: ${e.message}`, 'err')
      this.setStatus('Connection test failed')
      this.setFlashState('error')
      return false
    } finally {
      this._updateActionButtons()
    }
  },

  async startFlash() {
    if (!this._fwData) { this.addLog(tr('need_fw'), 'err'); return }

    // Verify Turnstile before allowing flash to proceed — only if widget is actually active on page
    if (this._turnstileActive) {
      if (!this._turnstileToken) {
        this.addLog(tr('turnstile_fail') || 'Captcha verification required. Please try again.', 'err')
        return
      }
      this.addLog(tr('turnstile_ok') || 'Captcha verified', 'ok')
    }

    const sim = this.$('chkSim').checked
    const debug = this.$('chkDebug').checked

    // Setup mode (guided): probe the controller first, then ask for confirmation.
    // Start mode skips both and flashes right away.
    if (this._isSetupMode()) {
      this.addLog(tr('setup_testing'), 'info')
      if (!(await this.testConnection())) return
      const msg = `${tr('setup_confirm')}\n\n${this._fwType} - ${this._fwData.length} B` +
        `${sim ? '\n' + tr('sim_label') : ''}`
      if (!sim && !window.confirm(msg)) {
        this.addLog(tr('setup_cancel'), 'warn')
        this.setFlashState('idle')
        return
      }
    }

    const serial = sim ? new SimulationSerial() : this._serial
    if (sim) await serial.connect()

    this._abortCtrl = { aborted: false }
    this._flashing = true
    this.$('btnFlash').disabled = true
    this.$('btnAbort').disabled = false

    const opts = this._buildOpts(sim, debug)
    this.setFlashState('flashing')
    this.setStatus(tr('flash_start'))
    this.setProgress(0)

    try {
      if (this._fwType === 'BRIGHTWAY') {
        const f = new BrightwayFlasher(serial, opts)
        f.loadFirmware(this._fwData)
        await f.run()
      } else if (this._fwType === 'LEQI') {
        const f = new LeqiFlasher(serial, opts)
        const payload = this._fwRaw || this._fwData
        f.loadFirmware(payload)
        await f.run()
      } else {
        throw new Error(tr('unknown_type'))
      }
      this.addLog(tr('flash_done'), 'ok')
      this.setFlashState('done')
    } catch (e) {
      this.addLog(`${tr('flash_error')}: ${e.message}`, 'err')
      this.setFlashState('error')
    } finally {
      this._flashing = false
      this.$('btnAbort').disabled = true
      this._updateActionButtons()
    }
  },

  abortFlash() {
    if (this._abortCtrl) this._abortCtrl.aborted = true
    this.addLog(tr('abort'), 'warn')
    this.$('btnAbort').disabled = true
  },

  // ── Log and progress ─────────────────────────────────────────────────────
  addLog(msg, type = '') {
    const el = this._logEl
    const now = new Date()
    const ts = now.toTimeString().substring(0, 8)
    const line = document.createElement('div')
    line.className = 'log-line'
    line.innerHTML = `<span class="log-ts">${ts}</span><span class="log-msg ${type}">${escHtml(msg)}</span>`
    el.appendChild(line)
    el.scrollTop = el.scrollHeight
  },

  exportLog() {
    let text = 'visoire flasher log\nGenerated ' + new Date().toLocaleString() + '\n\n'
    this._logEl.querySelectorAll('.log-line').forEach((l) => {
      const ts = l.querySelector('.log-ts').textContent
      const msg = l.querySelector('.log-msg').textContent
      text += `${ts}  ${msg}\n`
    })
    const blob = new Blob([text], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'flasher.log'
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  },

  setStatus(text) {
    this.$('progressStatus').textContent = text
    if (!this.$('chkDebug').checked) this.addLog(text, 'info')
  },

  setProgress(pct) {
    this.$('progressBar').style.width = pct + '%'
    this.$('progressPct').textContent = pct + '%'
  },

  setFlashState(state) {
    const badge = this.$('flashState')
    const labels = window.FLASHER_STATE || {}
    badge.className = 'state-badge ' + state
    const map = { idle: labels.idle, flashing: labels.flashing, done: labels.done, error: labels.error }
    badge.textContent = map[state] || state
  },
}

function escHtml(s) {
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
}

document.addEventListener('DOMContentLoaded', () => UI.init())
