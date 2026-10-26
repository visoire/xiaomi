// setup.js
// Guided three step wizard: select scooter, select patches, connect and flash.
// Step 1 shows all 8 scooters from the /mcu folder, checks freshness via
// data.json (mcu index) and warns if the bundled version is outdated.
// Step 2 prepares the firmware through prepare.php.
// Step 3 connects over Web Serial or WebUSB, tests and flashes.
// Built by visoire. Core protocol and patch logic by ScooterTeam, CC BY-NC-SA 4.0.

'use strict'

const ST = (window.SETUP_I18N || {})
const str = (k) => ST[k] || k
const SDATA = window.SETUP_DATA || { models: [], patchDefs: {} }

function sfmt(s, ...a) { let i = 0; return String(s).replace(/%s/g, () => (i < a.length ? a[i++] : '%s')) }
function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') }
function escA(s) { return esc(s).replace(/"/g, '&quot;') }

// Turnstile callbacks
window.onTurnstileSuccess = function (token) {
  W.turnstileToken = token
}
window.onTurnstileError = function () {
  W.turnstileToken = null
}
window.onTurnstileExpired = function () {
  W.turnstileToken = null
}

const W = {
  $: (id) => document.getElementById(id),
  step: 0,
  model: null,
  mode: 'local',
  customFile: null,
  fwBytes: null,
  fwType: null,
  fwChanged: 0,
  versionOk: false,
  serial: null,
  abortCtrl: null,
  flashing: false,
  engaged: false,
  turnstileToken: null,

  init() {
    PatchUI.configure({
      defs: SDATA.patchDefs,
      labels: window.SETUP_PATCH_LABELS || {},
      unit: 'km/h',
      onChange: (sel, tags) => this._onPatchChange(sel, tags),
    })

    this._buildModelGrid()
    this._setupCustom()
    this._wireNav()
    this._wireFlash()
    this._guardUnload()
    this.loadVersionIndex()
    this._checkUpdate()

    if (!navigator.serial && !navigator.usb) {
      const warn = this.$('browserWarn')
      if (warn) warn.classList.add('show')
    }

    this._go(0)
  },

  // ── Step navigation ──────────────────────────────────────────────────────
  _wireNav() {
    this.$('btnNext').addEventListener('click', () => this._next())
    this.$('btnBack').addEventListener('click', () => this._back())
    this.$('btnCancel').addEventListener('click', () => this._cancel())
  },

  _showError(msg) {
    const el = this.$('setupErrorBanner')
    if (!el) return
    el.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
      '<div class="err-msg">' + esc(msg) + '</div>' +
      '<button type="button" class="err-close" aria-label="Close" onclick="document.getElementById(\'setupErrorBanner\').classList.add(\'hidden\')">' +
        '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
      '</button>'
    el.classList.remove('hidden')
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })
  },

  _hideError() {
    const el = this.$('setupErrorBanner')
    if (el) el.classList.add('hidden')
  },

  _go(step) {
    this._hideError()
    this.step = step
    for (let i = 0; i < 3; i++) {
      this.$('page' + i).classList.toggle('active', i === step)
    }
    this._renderDots()
    this.$('btnBack').classList.toggle('hidden', step === 0)
    this.$('btnNext').classList.toggle('hidden', step === 2)
    if (step === 0) this.$('btnNext').disabled = !(this.model || (this.mode === 'custom' && this.customFile))
    if (step === 1) this.$('btnNext').disabled = false
    window.scrollTo({ top: 0, behavior: 'smooth' })
  },

  _renderDots() {
    const labels = [str('step1'), str('step2'), str('step3')]
    const host = this.$('stepDots')
    host.innerHTML = ''
    for (let i = 0; i < 3; i++) {
      const dot = document.createElement('div')
      dot.className = 'step-dot' + (i === this.step ? ' active' : i < this.step ? ' done' : '')
      dot.innerHTML = `<div class="sd-circle">${i < this.step ? '&#10003;' : (i + 1)}</div><div class="sd-label">${esc(labels[i])}</div>`
      host.appendChild(dot)
      if (i < 2) {
        const bar = document.createElement('div')
        bar.className = 'step-bar' + (i < this.step ? ' done' : '')
        host.appendChild(bar)
      }
    }
  },

  async _next() {
    this._hideError()
    if (this.step === 0) {
      if (!this.model && !(this.mode === 'custom' && this.customFile)) return
      this.engaged = true
      if (this.mode === 'custom') {
        const ok = await this._prepareCustomRaw()
        if (ok) this._go(2)
      } else {
        PatchUI.render(this.$('patchList'), this.model)
        this._go(1)
      }
    } else if (this.step === 1) {
      if (!PatchUI.isValid()) return
      const ok = await this._prepare()
      if (ok) this._go(2)
    }
  },

  _back() {
    if (this.flashing) return
    if (this.step === 2 && this.mode === 'custom') { this._go(0); return }
    if (this.step > 0) this._go(this.step - 1)
  },

  _cancel() {
    if (this.flashing) return
    this._hideError()
    this.model = null; this.mode = 'local'; this.customFile = null
    this.fwBytes = null; this.fwType = null; this.engaged = false
    document.querySelectorAll('.model-card.selected').forEach((c) => c.classList.remove('selected'))
    this.$('verCheck').classList.add('hidden')
    const infoBox = this.$('modelInfoBox')
    if (infoBox) infoBox.classList.add('hidden')
    this.$('customRow').classList.remove('hidden')
    this._setProgress(0); this._setStatus(str('g_idle'))
    this._setState('idle')
    this._go(0)
  },

  // ── Step 1: model grid + version check ─────────────────────────────────────
  _buildModelGrid() {
    const grid = this.$('modelGrid')
    if (!grid) return
    grid.innerHTML = ''

    SDATA.models.forEach((m) => {
      const card = document.createElement('div')
      card.className = 'model-card' + (m.localAvailable ? '' : ' upload-only') + (m.experimental ? ' experimental' : '')
      card.tabIndex = 0
      card.dataset.id = m.id

      const glyph = '<svg class="m-glyph" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 34h6l4-18h12l5 12"/><circle cx="12" cy="37" r="4"/><circle cx="35" cy="37" r="4"/></svg>'
      const glyphEscaped = glyph.replace(/"/g, "'").replace(/'/g, "\\'")

      const img = m.image
        ? '<img class="m-img" src="' + escA(m.image) + '" alt="" loading="lazy" onerror="this.outerHTML=\'' + glyphEscaped + '\'">'
        : glyph

      const expBadge = m.experimental ? '<span class="m-exp-badge">EXP</span>' : ''

      card.innerHTML =
        expBadge +
        '<span class="m-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>' +
        img + '<div class="m-name"></div><div class="m-proto"></div>'

      card.querySelector('.m-name').textContent = m.name
      card.querySelector('.m-proto').textContent = m.protocol

      const choose = () => this._selectModel(m)
      card.addEventListener('click', choose)
      card.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose() } })

      grid.appendChild(card)
    })
  },

  async _selectModel(m) {
    if (m.customRedirect) {
      window.location.href = m.customRedirect
      return
    }
    this.mode = 'local'
    this.customFile = null
    this.model = m
    document.querySelectorAll('#modelGrid .model-card').forEach((c) => c.classList.toggle('selected', c.dataset.id === m.id))

    const isDe = localStorage.getItem('lang') === 'de' || (new URLSearchParams(window.location.search)).get('lang') === 'de'
    const isPl = localStorage.getItem('lang') === 'pl' || (new URLSearchParams(window.location.search)).get('lang') === 'pl'
    const tModelId = isDe ? 'Modell-ID' : (isPl ? 'ID modelu' : 'Model ID')
    const tVersion = isDe ? 'Werks-Version' : (isPl ? 'Wersja fabryczna' : 'Factory Version')
    const tProtocol = isDe ? 'Protokoll' : (isPl ? 'Protokół' : 'Protocol')
    const tConnector = isDe ? 'Anschluss' : (isPl ? 'Złącze' : 'Connector')
    const tLocalFile = isDe ? 'Lokale Datei' : (isPl ? 'Lokalny plik' : 'Local File')
    const localUrl = '../mcu/' + encodeURIComponent(m.filename)

    let noticeHtml = ''
    if (m.id === 'mi6pro') {
      noticeHtml =
        '<div class="model-info-item" style="grid-column: span 2; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
          '<span style="color: #fbbf24; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
            '<span style="font-size: 14px;">⚠️</span> ' + (isDe ? 'Xiaomi 6 Pro: Originale Firmware im MCU-Bereich verfügbar. Custom-Patch-Unterstützung befindet sich im Test (M8-Stecker).' : (isPl ? 'Xiaomi 6 Pro: Oryginalny firmware dostępny w sekcji MCU. Modyfikacje w trakcie testów (złącze M8).' : 'Xiaomi 6 Pro: Stock firmware available in MCU section. Custom patch support in testing (M8 connector).')) +
          '</span>' +
        '</div>'
    } else if (m.id === 'mi6' || m.id === 'mi6lite') {
      noticeHtml =
        '<div class="model-info-item" style="grid-column: span 2; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
          '<span style="color: #fbbf24; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
            '<span style="font-size: 14px;">⚠️</span> ' + (isDe ? 'Experimenteller Support im bw patcher hinzugefügt! Gleiches Verfahren wie 5 Elite (M8-Stecker). Tester willkommen!' : (isPl ? 'Dodano eksperymentalną obsługę w bw patcherze! Taka sama procedura jak w 5 Elite (złącze M8). Testerzy mile widziani!' : 'Experimental support added to bw patcher! Same procedure as 5 Elite (M8 connector). Testers welcome!')) +
          '</span>' +
        '</div>'
    } else if (m.id === 'mi4' || m.id === 'mi42' || m.id === 'mi43') {
      noticeHtml =
        '<div class="model-info-item" style="grid-column: span 2; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
          '<span style="color: #38bdf8; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
            '<span style="font-size: 14px;">ℹ️</span> ' + (isDe ? 'Fehler 43 nach dem Flashen? Dein Roller ist eine Scooter-42/43-Variante (DE-Version oder BMS 0111). Wähle "Xiaomi Electric Scooter 42" oder "43" statt "Scooter 4".' : (isPl ? 'Błąd 43 po flashowaniu? Twoja hulajnoga to wariant Scooter 42 / 43 (wersja DE lub BMS 0111). Wybierz "Xiaomi Electric Scooter 42" lub "43" zamiast "Scooter 4".' : 'Error 43 after flashing? Your scooter is a Scooter 42 / 43 variant (DE version or BMS 0111). Select "Xiaomi Electric Scooter 42" or "43" instead of "Scooter 4".')) +
          '</span>' +
        '</div>'
    }

    const infoBox = this.$('modelInfoBox')
    if (infoBox) {
      infoBox.innerHTML =
        '<div class="model-info-item">' +
          '<span class="model-info-label">' + esc(tModelId) + '</span>' +
          '<span class="model-info-value"><code>' + esc(m.firmwareId) + '</code></span>' +
        '</div>' +
        '<div class="model-info-item">' +
          '<span class="model-info-label">' + esc(tVersion) + '</span>' +
          '<span class="model-info-value"><code>v' + esc(m.bundledVersion) + '</code></span>' +
        '</div>' +
        '<div class="model-info-item">' +
          '<span class="model-info-label">' + esc(tProtocol) + '</span>' +
          '<span class="model-info-value">' + esc(m.protocol) + '</span>' +
        '</div>' +
        '<div class="model-info-item">' +
          '<span class="model-info-label">' + esc(tConnector) + '</span>' +
          '<span class="model-info-value"><code>' + esc(m.connector) + '</code></span>' +
        '</div>' +
        (m.localAvailable ? 
        '<div class="model-info-item" style="grid-column: span 2;">' +
          '<span class="model-info-label">' + esc(tLocalFile) + '</span>' +
          '<span class="model-info-value">' +
            '<a href="' + esc(localUrl) + '" target="_blank" rel="noopener" style="text-decoration:none;color:var(--accent)"><code>' + esc(m.filename) + '</code></a>' +
          '</span>' +
        '</div>' : '') +
        noticeHtml
      infoBox.classList.remove('hidden')
    }

    if (!m.localAvailable) {
      this.$('btnNext').disabled = true
      this._showVerWithCheck(m)
      return
    }
    this.$('btnNext').disabled = false
    this._showVerWithCheck(m)
  },

  _showVerWithCheck(m) {
    const bundledVer = m.bundledVersion || '?'
    const el = this.$('verCheck')
    el.classList.remove('hidden')

    if (this._modelIsOutdated(m)) {
      el.className = 'ver-check outdated'
      el.innerHTML =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
        '<span>Newer MCU v' + esc(this.VERSION_INDEX[m.firmwareId]) + ' available</span>'
    } else {
      el.className = 'ver-check current'
      el.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
        '<span>MCU v' + esc(bundledVer) + ' (latest)</span>'
    }
  },

  // ── Version index: fetched once at startup, cached in sessionStorage ────
  VERSION_INDEX: null,

  loadVersionIndex() {
    const self = this
    // Try cache first
    try {
      const cached = sessionStorage.getItem('setup_fw_index')
      if (cached) {
        const parsed = JSON.parse(cached)
        if (parsed && parsed.ts && (Date.now() - parsed.ts < 300000)) { // 5 min TTL
          this.VERSION_INDEX = parsed.data || {}
          this._applyOutdatedFlags()
          return
        }
      }
    } catch (e) {}
    // Fetch
    fetch('https://raw.githubusercontent.com/argusblack/mi-fw-info/main/data.json', { signal: AbortSignal.timeout(10000) })
      .then(function (r) { if (!r.ok) throw new Error(); return r.json() })
      .then(function (data) {
        var idx = {}
        if (Array.isArray(data)) {
          for (var i = 0; i < data.length; i++) {
            if (data[i].model && data[i].firmware && data[i].firmware.mcu_version) {
              idx[data[i].model] = data[i].firmware.mcu_version
            }
          }
        }
        self.VERSION_INDEX = idx
        try { sessionStorage.setItem('setup_fw_index', JSON.stringify({ ts: Date.now(), data: idx })) } catch (e) {}
        self._applyOutdatedFlags()
      })
      .catch(function () {
        // Silent fail
        self.VERSION_INDEX = {}
      })
  },

  _modelIsOutdated(m) {
    if (!this.VERSION_INDEX || !m) return false
    const latest = this.VERSION_INDEX[m.firmwareId]
    if (!latest) return false
    const localInt = parseInt(m.bundledVersion, 10)
    const latestInt = parseInt(latest, 10)
    if (isNaN(localInt) || isNaN(latestInt)) return false
    return latestInt > localInt
  },

  _applyOutdatedFlags() {
    if (!this.VERSION_INDEX) return
    const self = this
    SDATA.models.forEach(function (m) {
      if (self._modelIsOutdated(m)) {
        document.querySelectorAll('#modelGrid .model-card[data-id="' + escA(m.id) + '"]').forEach(function (c) {
          c.classList.add('outdated')
        })
      }
    })
  },

  _flasherLink() {
    return '<a href="../flasher/">' + esc(str('s1_open_flasher')) + '</a>'
  },

  // Custom firmware upload on step 1.
  _setupCustom() {
    const btn = this.$('customToggle')
    const input = this.$('customFile')
    if (!btn || !input) return
    btn.addEventListener('click', () => input.click())
    input.addEventListener('change', (e) => {
      const f = e.target.files[0]
      if (!f) return
      this._hideError()
      if (!f.name.toLowerCase().endsWith('.bin')) {
        this._showError(str('p_err') + ': Please select a .bin firmware file.')
        input.value = ''
        return
      }
      if (f.size > 4 * 1024 * 1024) {
        this._showError(str('p_err') + ': File exceeds 4MB limit.')
        input.value = ''
        return
      }
      this.mode = 'custom'
      this.customFile = f
      this.model = null
      document.querySelectorAll('#modelGrid .model-card').forEach((c) => c.classList.remove('selected'))
      const verEl = this.$('verCheck')
      verEl.classList.remove('hidden')
      verEl.className = 'ver-check current'
      verEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
        '<span>' + esc(str('s_custom')) + ': ' + esc(f.name) + ' (' + f.size + ' B)</span>'
      const infoBox = this.$('modelInfoBox')
      if (infoBox) infoBox.classList.add('hidden')
      this.$('btnNext').disabled = false
    })
  },

  // ── Step 2: patch selection + firmware prepare ──────────────────────────────
  _onPatchChange(sel, tags) {
    const summary = this.$('s2Summary')
    const count = this.$('s2Count')
    if (!tags.length) {
      summary.innerHTML = '<span class="muted">' + esc(str('s2_none')) + '</span>'
      if (count) count.textContent = '0 ' + esc(str('p_selected'))
    } else {
      const patchIco = '<svg class="sc-tag-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>'
      summary.innerHTML = tags.map((t) => '<span class="sc-tag">' + patchIco + esc(t) + '</span>').join('')
      if (count) count.textContent = tags.length + ' ' + esc(str('p_selected'))
    }
    this.$('btnNext').disabled = !PatchUI.isValid()
  },

  async _prepare() {
    const sel = PatchUI.selections()
    const fd = new FormData()
    fd.append('model', this.model.id)
    fd.append('mode', 'local')
    fd.append('advanced', '0')
    fd.append('patches', JSON.stringify(sel))
    if (this.turnstileToken) fd.append('cf-turnstile-response', this.turnstileToken)
    return this._postPrepare(fd)
  },

  async _prepareCustomRaw() {
    this._hideError()
    try {
      const buf = await this.customFile.arrayBuffer()
      this.fwBytes = new Uint8Array(buf)
      this.fwType = detectFirmwareType(this.fwBytes)
      if (!this.fwType || this.fwType.type === 'unknown') {
        this._showError(str('f_unknown_type') + ' Please upload a valid scooter MCU firmware .bin file.')
        return false
      }
      this.fwChanged = 0
      this._renderFwSummary({ custom: true })
      return true
    } catch (e) {
      this._showError(str('f_fw_error') + ': ' + (e.message || 'File could not be read.'))
      return false
    }
  },

  async _postPrepare(fd) {
    this._hideError()
    this._lockNav(true, str('s2_building'))
    try {
      const res = await fetch('prepare.php', { method: 'POST', body: fd })
      const ctype = res.headers.get('Content-Type') || ''
      if (!res.ok || ctype.includes('application/json')) {
        let msg = str('s2_fail')
        try { const j = await res.json(); if (j && j.error) msg = j.error } catch (_) {}
        this._showError(str('s2_fail') + ': ' + msg)
        return false
      }
      this.fwChanged = parseInt(res.headers.get('X-Changed-Bytes') || '0', 10)
      const buf = await res.arrayBuffer()
      this.fwBytes = new Uint8Array(buf)
      this.fwType = detectFirmwareType(this.fwBytes)
      this._renderFwSummary({})
      return true
    } catch (e) {
      this._showError(str('s2_fail') + ': ' + e.message)
      return false
    } finally {
      this._lockNav(false)
    }
  },

  _lockNav(on, text) {
    this.$('btnNext').disabled = on
    this.$('btnBack').disabled = on
    if (on) { this.$('btnNext').textContent = text } else { this.$('btnNext').textContent = str('g_next') }
  },

  // ── Step 3: connect, test, flash ────────────────────────────────────────────
  _renderFwSummary(opt) {
    const box = this.$('fwSummary')
    if (!box) return
    let rows = []
    if (opt.custom) {
      rows.push([str('s_model'), str('s_custom')])
    } else {
      rows.push([str('s_model'), this.model.name])
      rows.push([str('s_version'), this.model.bundledVersion || '?'])
    }
    rows.push([str('s_protocol'), this.fwType || '?'])
    box.innerHTML = rows.map(([k, v]) => '<div class="fw-k">' + esc(k) + '</div><div class="fw-v">' + esc(String(v)) + '</div>').join('')
    box.classList.add('filled')
  },

  _wireFlash() {
    this.$('btnWebSerial').addEventListener('click', () => this._connectSerial())
    this.$('btnWebUSB').addEventListener('click', () => this._connectUSB())
    this.$('btnTest').addEventListener('click', () => this._test())
    this.$('btnFlash').addEventListener('click', () => this._flash())
    this.$('btnAbort').addEventListener('click', () => this._abort())
    if (!navigator.usb) this.$('btnWebUSB').disabled = true
    if (!navigator.serial) this.$('btnWebSerial').disabled = true
  },

  async _connectSerial() {
    if (!navigator.serial) { this._log(str('f_serial_unsupported') || 'no serial', 'err'); return }
    try {
      const a = new WebSerialAdapter()
      await a.connect(19200)
      this.serial = a
      this._setConn(true, str('s3_connected'))
      this._log(str('s3_connected'), 'ok')
    } catch (e) {
      this.serial = null
      this._setConn(false, str('s3_not_connected') || 'not connected')
      this._log('Connection failed: ' + e.message, 'err')
    }
  },

  async _connectUSB() {
    if (!navigator.usb) { this._log('no usb', 'err'); return }
    this.$('usbHint').classList.add('show')
    try {
      const a = new WebUSBAdapter()
      await a.connect(19200)
      this.serial = a
      this._setConn(true, str('s3_connected'))
      this._log(str('s3_connected'), 'ok')
    } catch (e) {
      this.serial = null
      this._setConn(false, str('s3_not_connected') || 'not connected')
      this.$('usbHint').classList.remove('show')
      this._log('Connection failed: ' + e.message, 'err')
    }
  },

  _setConn(on, text) {
    this.$('connDot').className = 'conn-dot' + (on ? ' on' : '')
    this.$('connStatus').textContent = text
    this.$('btnTest').disabled = !on
    if (!on) {
      // Disable btnFlash on disconnect to prevent stale state from a prior successful test
      if (this.$('btnFlash')) this.$('btnFlash').disabled = true
      const hint = this.$('usbHint')
      if (hint) hint.classList.remove('show')
    }
  },

  _opts() {
    return {
      simulation: false, debug: false,
      onLog: (m, t) => this._log(m, t),
      onStatus: (s) => this._setStatus(s),
      onProgress: (p) => this._setProgress(p),
      abortSignal: this.abortCtrl,
    }
  },

  async _test() {
    if (!this.serial || !this.serial.connected) { this._log(str('s3_need_conn'), 'warn'); return }
    this.abortCtrl = { aborted: false }
    this._setState('flashing')
    try {
      const F = this.fwType === 'LEQI' ? LeqiFlasher : BrightwayFlasher
      const f = new F(this.serial, this._opts())
      if (this.fwBytes) f.loadFirmware(this.fwBytes)
      await f.testConnection()
      this._log(str('s3_test_ok'), 'ok')
      this._setState('done')
      this.$('btnFlash').disabled = false
    } catch (e) {
      this._log(str('s3_test_fail') + ': ' + e.message, 'err')
      this._setState('error')
      this.$('btnFlash').disabled = true
    }
  },

  async _flash() {
    if (!this.serial || !this.serial.connected) { this._log(str('s3_need_conn'), 'err'); return }
    if (!this.fwBytes) { this._log('no firmware', 'err'); return }
    this.abortCtrl = { aborted: false }
    this.flashing = true
    this.$('btnFlash').disabled = true
    this.$('btnAbort').disabled = false
    this.$('btnBack').disabled = true
    this.$('btnCancel').disabled = true
    this._setState('flashing')
    this._setStatus(str('s3_flashing'))
    this._setProgress(0)
    try {
      if (this.fwType === 'BRIGHTWAY') {
        const f = new BrightwayFlasher(this.serial, this._opts()); f.loadFirmware(this.fwBytes); await f.run()
      } else if (this.fwType === 'LEQI') {
        const f = new LeqiFlasher(this.serial, this._opts()); f.loadFirmware(this.fwBytes); await f.run()
      } else { throw new Error('unknown type') }
      this._log(str('s3_done'), 'ok')
      this._setState('done')
      this._setStatus(str('s3_done'))
    } catch (e) {
      this._log(str('s3_failed') + ': ' + e.message, 'err')
      this._setState('error')
    } finally {
      this.flashing = false
      this.$('btnAbort').disabled = true
      this.$('btnBack').disabled = false
      this.$('btnCancel').disabled = false
    }
  },

  _abort() {
    if (this.abortCtrl) this.abortCtrl.aborted = true
    this._log(str('s3_abort'), 'warn')
    this.$('btnAbort').disabled = true
  },

  // ── shared helpers ──────────────────────────────────────────────────────────
  _log(msg, type) {
    type = type || ''
    const el = this.$('log')
    const ts = new Date().toTimeString().substring(0, 8)
    const line = document.createElement('div')
    line.className = 'log-line'
    line.innerHTML = '<span class="log-ts">' + ts + '</span><span class="log-msg ' + type + '">' + esc(msg) + '</span>'
    el.appendChild(line); el.scrollTop = el.scrollHeight
  },
  _setStatus(t) { this.$('progressStatus').textContent = t },
  _setProgress(p) { this.$('progressBar').style.width = p + '%'; this.$('progressPct').textContent = p + '%' },
  _setState(s) {
    const badge = this.$('flashState')
    badge.className = 'state-badge ' + s
    const map = { idle: str('g_idle'), flashing: str('g_busy'), done: str('g_done'), error: str('g_error') }
    badge.textContent = map[s] || s
  },

  _guardUnload() {
    window.addEventListener('beforeunload', (e) => {
      if (this.flashing || this.engaged || (this.step >= 1 && this.fwBytes)) {
        e.preventDefault(); e.returnValue = str('leave_warn')
      }
    })
  },

  async _checkUpdate() {
    const cfg = window.SETUP_GH || { current: '0.7.1', repo: 'scooterteam/bw-flasher' }
    try {
      const r = await fetch('https://api.github.com/repos/' + cfg.repo + '/releases', {
        headers: { Accept: 'application/vnd.github.v3+json' }, signal: AbortSignal.timeout(5000),
      })
      if (!r.ok) return
      const rel = await r.json()
      if (!Array.isArray(rel) || !rel[0]) return
      const latest = String(rel[0].tag_name || '').replace(/^v/, '')
      const newer = latest.split('.').map(Number), cur = cfg.current.split('.').map(Number)
      let isNewer = false
      for (let i = 0; i < 3; i++) { if ((newer[i] || 0) > (cur[i] || 0)) { isNewer = true; break } if ((newer[i] || 0) < (cur[i] || 0)) break }
      const b = this.$('ghBanner')
      if (isNewer && b) {
        const url = rel[0].html_url || 'https://github.com/' + cfg.repo + '/releases'
        b.className = 'gh-banner warn show'
        b.innerHTML = esc(sfmt(str('gh_outdated'), latest, cfg.current)) +
          ' <a href="' + escA(url) + '" target="_blank" rel="noopener">' + esc(str('gh_link')) + '</a>' +
          '<button class="gh-x" onclick="this.parentElement.classList.remove(\'show\')">&times;</button>'
      }
    } catch (e) { /* silent */ }
  },
}

document.addEventListener('DOMContentLoaded', () => {
  W.init()
})
