// ngfw.js - controller for the NGFW patcher page.
// Options match the rest of the site: simulation, debug, setup/start mode and a
// serial block (builds the ScooterFlasher command). Patcher core: NGFW (AGPL-3.0).

'use strict'

const NG_T = window.NGFW_I18N || {}
const ngt = (k) => NG_T[k] || k

const NG_MODELS = {
  mi: [
    ['1s', '1S', '1s'], ['pro2', 'Pro 2', 'pro2'], ['lite', 'Lite', 'lite'], ['mi3', 'Mi 3', 'mi3'],
    ['4pro', 'Xiaomi 4 Pro (scooter.v8, F1)', '4pro'], ['4proita', 'Xiaomi 4 Pro Italian (ninebot.scooter.15, STM32F4)', '4proita'],
  ],
  nb: [
    ['4plus', 'Xiaomi 4 Pro Plus', '4proplus'], ['4max', 'Xiaomi 4 Pro Max', '4promax'],
    ['f2pro', 'F2 Pro', 'f2pro'], ['f2plus', 'F2 Plus', 'f2plus'], ['f2', 'F2', 'f2'], ['g2', 'G2', 'g2'],
  ],
}

// [code, label, number placeholder or null]
const NG_PATCHES = {
  mi: [
    ['dpc', 'DPC (register)'], ['rcm', 'No charging fix'], ['rks', 'Remove KERS'], ['kml', 'Custom KERS'],
    ['rab', 'Remove autobrake'], ['rml', 'Remove model lock'], ['rfm', 'Region free'],
    ['sld', 'Speed limit drive (km/h)', 22], ['sls', 'Speed limit sport (km/h)', 27], ['slp', 'Speed limit pedestrian (km/h)', 9],
    ['mss', 'Motor start speed (km/h)', 3], ['ccd', 'Cruise control delay (s)', 2], ['sdt', 'Shutdown time (s)', 1],
    ['amp', 'Phase current pedestrian (mA)', 10000], ['amd', 'Phase current drive (mA)', 20000], ['ams', 'Phase current sport (mA)', 30000],
    ['alm', 'Max phase currents'], ['abr', 'Brake currents'], ['crc', 'Current raising coefficient', 1000],
    ['wsc', 'Wheel size constant', null], ['vlt', 'Volt limit (V)', null], ['bud', 'BMS baudrate', 76800],
    ['blm', 'Brakelight + auto-light'], ['llm', 'Lower light'], ['pnb', 'Pedestrian no-blink'], ['bts', 'Button swap'],
    ['amm', 'Current meter'], ['lrb', 'Lever resolution'],
  ],
  nb: [
    ['dpc', 'DPC (register)'], ['rcm', 'No charging fix'], ['rks', 'Remove KERS'], ['kml', 'Custom KERS'],
    ['rab', 'Remove autobrake'], ['rfm', 'Region free'], ['dmn', 'Disable motor NTC'], ['asc', 'Allow serial change'],
    ['skc', 'Skip key check'], ['slp', 'Speed params (23/22/21/20)'], ['ccd', 'Cruise control delay (s)', 3],
    ['amp', 'Phase current pedestrian (mA)', 5000], ['ame', 'Phase current eco (mA)', 10000], ['amd', 'Phase current drive (mA)', 20000],
    ['ams', 'Phase current sport (mA)', 30000], ['mme', 'Max current eco (mA)', 20000], ['mmd', 'Max current drive (mA)', 40000],
    ['mms', 'Max current sport (mA)', 60000], ['bud', 'BMS baudrate', 76800],
  ],
}

const NG = {
  $: (id) => document.getElementById(id),
  _file: null,
  _download: null,
  _busy: false,

  init() {
    this.$('ngFamily').addEventListener('change', () => { this._renderModels(); this._renderPatches(); this._cmd() })
    this.$('ngModel').addEventListener('change', () => { this._cmd(); this._serialUi() })
    this.$('ngTarget').addEventListener('change', () => { this._serialUi(); this._cmd() })
    ;['ngPrefix', 'ngSerial', 'ngBle', 'ngChip'].forEach((id) => this.$(id).addEventListener('input', () => this._cmd()))
    this.$('ngChip').addEventListener('change', () => this._cmd())
    this.$('ngCopy').addEventListener('click', () => this._copy())
    this.$('ngRun').addEventListener('click', () => this.run())
    this.$('ngSim').addEventListener('change', () => this._save())
    this.$('ngDebug').addEventListener('change', () => this._save())
    this.$('ngSetup').addEventListener('change', () => { this._save(); this._pill() })

    const drop = this.$('ngDrop')
    drop.addEventListener('click', () => this.$('ngFile').click())
    drop.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.$('ngFile').click() } })
    ;['dragenter', 'dragover'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('drag') }))
    ;['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.remove('drag') }))
    drop.addEventListener('drop', (e) => this._setFile(e.dataTransfer.files[0]))
    this.$('ngFile').addEventListener('change', (e) => this._setFile(e.target.files[0]))

    this._renderModels()
    this._renderPatches()
    this._load()
    this._pill()
    this._serialUi()
    this._cmd()
    this._state('idle', 'Idle')
  },

  _load() {
    this.$('ngSetup').checked = true // guided is the safer default
    try {
      const raw = localStorage.getItem('ngfw_prefs')
      if (!raw) return
      const p = JSON.parse(raw)
      this.$('ngSim').checked = !!p.sim
      this.$('ngDebug').checked = !!p.debug
      this.$('ngSetup').checked = p.setup !== false
    } catch (e) { /* storage unavailable */ }
  },
  _save() {
    try {
      localStorage.setItem('ngfw_prefs', JSON.stringify({ sim: this.$('ngSim').checked, debug: this.$('ngDebug').checked, setup: this.$('ngSetup').checked }))
    } catch (e) { /* ignore */ }
  },
  _pill() {
    const p = this.$('ngModePill'), on = this.$('ngSetup').checked
    p.textContent = on ? p.dataset.setup : p.dataset.start
    p.classList.toggle('on', on)
  },

  _family() { return this.$('ngFamily').value },
  _model() { return this.$('ngModel').value },
  _scooterDevice() {
    const m = NG_MODELS[this._family()].find((x) => x[0] === this._model())
    return m ? m[2] : this._model()
  },

  _renderModels() {
    const sel = this.$('ngModel')
    sel.innerHTML = ''
    NG_MODELS[this._family()].forEach(([id, name]) => {
      const o = document.createElement('option')
      o.value = id; o.textContent = name
      sel.appendChild(o)
    })
    this._serialUi()
  },

  _renderPatches() {
    const box = this.$('ngPatches')
    box.innerHTML = ''
    NG_PATCHES[this._family()].forEach(([code, label, def]) => {
      const row = document.createElement('label')
      row.className = 'ng-patch'
      const cb = document.createElement('input')
      cb.type = 'checkbox'; cb.dataset.code = code
      const name = document.createElement('span')
      name.className = 'ng-name'; name.textContent = label
      const c = document.createElement('span')
      c.className = 'ng-code'; c.textContent = code
      row.append(cb, name, c)
      if (def !== undefined) {
        const num = document.createElement('input')
        num.type = 'number'; num.step = 'any'; num.dataset.val = code
        num.placeholder = def === null ? 'auto' : String(def)
        num.setAttribute('aria-label', `${label} ${ngt('val')}`)
        num.addEventListener('click', (e) => e.stopPropagation())
        row.appendChild(num)
      }
      cb.addEventListener('change', () => { row.classList.toggle('sel', cb.checked); this._ready() })
      box.appendChild(row)
    })
    this._ready()
  },

  _selected() {
    const out = []
    this.$('ngPatches').querySelectorAll('input[type=checkbox]:checked').forEach((cb) => {
      const item = { id: cb.dataset.code }
      const num = this.$('ngPatches').querySelector(`input[data-val="${cb.dataset.code}"]`)
      if (num && num.value !== '') item.value = num.value
      out.push(item)
    })
    return out
  },

  _ready() { this.$('ngRun').disabled = this._busy || !this._file || this._selected().length === 0 },

  _setFile(f) {
    if (!f) return
    if (!/\.bin$/i.test(f.name)) { this.log('Please choose a .bin file.', 'err'); return }
    if (f.size > 4 * 1024 * 1024) { this.log('The file exceeds the 4MB limit.', 'err'); return }
    this._file = f
    const box = this.$('ngFwInfo')
    box.innerHTML = `<div class="fw-k">File</div><div class="fw-v ok">${esc(f.name)}</div><div class="fw-k">Size</div><div class="fw-v">${f.size} B (0x${f.size.toString(16).toUpperCase()})</div>`
    box.classList.add('filled')
    this._ready()
  },

  // ── Serial + ScooterFlasher command ──────────────────────────────────────
  _serialUi() {
    const ble = this.$('ngTarget').value === 'BLE'
    this.$('ngEscFields').hidden = ble
    this.$('ngBleFields').hidden = !ble
    const ita = this._model() === '4proita'
    this.$('ngSerialHint').textContent = ita && !ble ? ngt('serial_ita') : (ble ? ngt('serial_hint_ble') : ngt('serial_hint_esc'))
  },

  _cmd() {
    const ble = this.$('ngTarget').value === 'BLE'
    const parts = ['python -m scooterflasher', `-d ${this._scooterDevice()}`, `--target ${ble ? 'BLE' : 'ESC'}`]
    const chip = this.$('ngChip').value
    if (chip) parts.push(`--chip ${chip}`)
    let sn = ''
    if (ble) sn = this.$('ngBle').value.trim()
    else if (this._model() !== '4proita') {
      const pre = this.$('ngPrefix').value.trim(), ser = this.$('ngSerial').value.trim()
      if (pre || ser) sn = `${pre}/${ser}`
    }
    if (sn) parts.push(`--sn ${sn}`)
    if (this.$('ngSim').checked) parts.push('--dry-run')
    parts.push('--cfw patched.bin')
    this.$('ngCmd').textContent = parts.join(' ')
  },

  async _copy() {
    try { await navigator.clipboard.writeText(this.$('ngCmd').textContent) } catch (e) { /* ignore */ }
  },

  // ── Run ──────────────────────────────────────────────────────────────────
  async _call(simulation) {
    const fd = new FormData()
    fd.append('family', this._family())
    fd.append('model', this._model())
    fd.append('patches', JSON.stringify(this._selected()))
    fd.append('debug', this.$('ngDebug').checked ? '1' : '0')
    fd.append('simulation', simulation ? '1' : '0')
    fd.append('firmware', this._file)
    const res = await fetch('patch.php', { method: 'POST', body: fd })
    let data = null
    try { data = await res.json() } catch (e) { /* not json */ }
    if (!data || !data.ok) throw new Error((data && data.error) || `HTTP ${res.status}`)
    return data
  },

  async run() {
    if (!this._file) { this.log(ngt('need_fw'), 'warn'); return }
    if (!this._selected().length) { this.log(ngt('need_patch'), 'warn'); return }
    const sim = this.$('ngSim').checked
    this._busy = true
    this._ready()
    this.$('ngDownload').hidden = true
    this._state('flashing', 'Working')
    try {
      if (this.$('ngSetup').checked && !sim) {
        this.log(ngt('setup_sim'), 'info')
        const probe = await this._call(true)
        this._report(probe)
        if (!window.confirm(`${ngt('setup_confirm')}\n\n${this._file.name}`)) {
          this.log(ngt('setup_cancel'), 'warn')
          this._state('idle', 'Idle')
          return
        }
      }
      const data = await this._call(sim)
      this._report(data)
      if (sim) {
        this.log(ngt('sim_done'), 'ok')
        this.$('ngStatus').textContent = ngt('sim_done')
      } else {
        this._offer(data)
        this.log(ngt('patch_done'), 'ok')
        this.$('ngStatus').textContent = ngt('patch_done')
      }
      this._state('done', 'Done')
    } catch (e) {
      this.log(e.message, 'err')
      this.$('ngStatus').textContent = e.message
      this._state('error', 'Error')
    } finally {
      this._busy = false
      this._ready()
    }
  },

  _offer(data) {
    if (this._download) URL.revokeObjectURL(this._download)
    const bin = Uint8Array.from(atob(data.file_b64), (c) => c.charCodeAt(0))
    this._download = URL.createObjectURL(new Blob([bin], { type: 'application/octet-stream' }))
    const a = this.$('ngDownload')
    a.href = this._download
    a.download = data.filename || 'ngfw_patched.bin'
    a.hidden = false
  },

  _report(d) {
    this.log(`${d.simulation ? '[SIM] ' : ''}${d.type}/${d.model}: ${d.changed_bytes} ${ngt('changed')}`, 'info')
    d.report.forEach((r) => {
      if (r.status === 'ok') {
        this.log(`✓ ${r.patch}: ${r.items.length} change(s)`, 'ok')
        r.items.forEach((it) => {
          this.log(`   ${it.desc} @ ${it.offset}`, 'dbg')
          if (it.pre !== undefined) {
            this.log(`   << ${it.pre}`, 'rx')
            this.log(`   >> ${it.post}`, 'tx')
            ;(it.pre_dis || []).forEach((x) => this.log(`   < ${x}`, 'dbg'))
            ;(it.post_dis || []).forEach((x) => this.log(`   > ${x}`, 'dbg'))
          }
        })
      } else {
        this.log(`✗ ${r.patch}: ${r.status === 'sigerr' ? 'signature not found' : r.error}`, 'err')
      }
    })
    if (d.failed && d.failed.length) this.log(`${ngt('failed')}: ${d.failed.join(', ')}`, 'warn')
  },

  log(msg, type = '') {
    const el = this.$('ngLog')
    const line = document.createElement('div')
    line.className = 'log-line'
    line.innerHTML = `<span class="log-ts">${new Date().toTimeString().substring(0, 8)}</span><span class="log-msg ${type}">${esc(msg)}</span>`
    el.appendChild(line)
    el.scrollTop = el.scrollHeight
  },

  _state(cls, text) {
    const b = this.$('ngState')
    b.className = 'state-badge ' + cls
    b.textContent = text
  },
}

function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') }

document.addEventListener('DOMContentLoaded', () => NG.init())
