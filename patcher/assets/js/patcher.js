/**
 * patcher.js - visoire Firmware Patcher (frontend)
 * Drives source mode, model selection, patch configuration and the call to
 * patch.php. Patcher core by ScooterTeam (CC BY-NC-SA 4.0), web version by visoire.
 */
'use strict'

;(function () {
  var DATA = window.PATCHER_DATA || { patchDefs: {}, models: [] }
  var I = window.PATCHER_I18N || {}

  var state = { mode: 'local', model: null, file: null, values: {}, enabled: {}, turnstileToken: null, fwIndex: null, dirty: false, filterConnector: null, filterProtocol: null }

  // ── beforeunload guard: warn when patches are configured or a flash is in progress ──
  window.addEventListener('beforeunload', function (e) {
    if (state.dirty) {
      e.preventDefault()
      e.returnValue = I.leaveWarn || 'You have unsaved patch changes. Leave anyway?'
    }
  })

  // Clean, consistent scooter illustration used in every model card.
  function scooterSvg() {
    return (
      '<svg viewBox="0 0 120 70" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
      '<g stroke="var(--accent)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none">' +
      '<path d="M16 50 H40 L58 16 H74"/>' +
      '<path d="M74 16 L92 50"/>' +
      '<path d="M58 16 v-6 h12"/>' +
      '<circle cx="16" cy="54" r="9"/>' +
      '<circle cx="96" cy="54" r="9"/>' +
      '<path d="M40 50 H92" opacity="0.5"/>' +
      '</g>' +
      '<circle cx="16" cy="54" r="3" fill="var(--accent)"/>' +
      '<circle cx="96" cy="54" r="3" fill="var(--accent)"/>' +
      '</svg>'
    )
  }

  function el(id) { return document.getElementById(id) }
  function modelById(id) { for (var i = 0; i < DATA.models.length; i++) if (DATA.models[i].id === id) return DATA.models[i]; return null }

  // ── Mode toggle ─────────────────────────────────────────────────────────
  function setMode(mode) {
    state.mode = mode
    state.model = null
    el('modeLocal').classList.toggle('active', mode === 'local')
    el('modeUpload').classList.toggle('active', mode === 'upload')
    el('panelLocal').classList.toggle('hidden', mode !== 'local')
    el('panelUpload').classList.toggle('hidden', mode !== 'upload')
    document.querySelectorAll('#scooterGrid .model-card').forEach(function (c) { c.classList.remove('selected') })
    if (el('uploadModel')) el('uploadModel').value = ''
    var infoBox = el('modelInfoBox')
    if (infoBox) infoBox.classList.add('hidden')
    var verEl = el('verCheck')
    if (verEl) verEl.classList.add('hidden')
    renderPatches()
    updateButton()
  }

  // ── Local model grid ────────────────────────────────────────────────────
  function buildGrid() {
    var grid = el('scooterGrid')
    grid.innerHTML = ''
    DATA.models.forEach(function (m) {
      var card = document.createElement('div')
      card.className = 'model-card' + (m.localAvailable ? '' : ' upload-only') + (m.experimental ? ' experimental' : '')
      card.tabIndex = 0
      card.setAttribute('data-id', m.id)
      
      var glyph = '<svg class="m-glyph" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 34h6l4-18h12l5 12"/><circle cx="12" cy="37" r="4"/><circle cx="35" cy="37" r="4"/></svg>'
      var glyphEscaped = glyph.replace(/"/g, "'").replace(/'/g, "\\'")
      var img = m.image
        ? '<img class="m-img" src="' + esc(m.image) + '" alt="" loading="lazy" onerror="this.outerHTML=\'' + glyphEscaped + '\'">'
        : glyph
      
      var expBadge = m.experimental ? '<span class="m-exp-badge">EXP</span>' : ''

      card.innerHTML =
        expBadge +
        '<span class="m-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>' +
        img + '<div class="m-name"></div><div class="m-proto"></div>'
      
      card.querySelector('.m-name').textContent = m.name
      card.querySelector('.m-proto').textContent = m.protocol
      
      card.setAttribute('data-connector', (m.connector || '').replace(/\s+/g, '_').toUpperCase())
      card.setAttribute('data-protocol', (m.protocol || '').toUpperCase())

      if (m.localAvailable) {
        var choose = function () { selectLocal(m.id, card) }
        card.addEventListener('click', choose)
        card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose() } })
      }
      grid.appendChild(card)
    })
  }

  function selectLocal(id, card) {
    state.model = id
    document.querySelectorAll('#scooterGrid .model-card').forEach(function (c) { c.classList.remove('selected') })
    card.classList.add('selected')
    
    var m = modelById(id)
    if (m) {
      var isDe = localStorage.getItem('lang') === 'de' || (new URLSearchParams(window.location.search)).get('lang') === 'de';
      var isPl = localStorage.getItem('lang') === 'pl' || (new URLSearchParams(window.location.search)).get('lang') === 'pl';
      var tModelId = isDe ? 'Modell-ID' : (isPl ? 'ID modelu' : 'Model ID');
      var tVersion = isDe ? 'Werks-Version' : (isPl ? 'Wersja fabryczna' : 'Factory Version');
      var tProtocol = isDe ? 'Protokoll' : (isPl ? 'Protokół' : 'Protocol');
      var tConnector = isDe ? 'Anschluss' : (isPl ? 'Złącze' : 'Connector');
      var tLocalFile = isDe ? 'Lokale Datei' : (isPl ? 'Lokalny plik' : 'Local File');
      // m.filename is now 'fw/<slug>/<md5_filename>.bin', relative to mcu/
      var localUrl = '../mcu/' + m.filename.split('/').map(encodeURIComponent).join('/');

      var infoBox = el('modelInfoBox')
      if (infoBox) {
        var noticeHtml = '';
        if (id === 'mi6pro') {
          noticeHtml =
            '<div class="model-info-item" style="grid-column: span 2; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
              '<span style="color: #fbbf24; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
                '<span style="font-size: 14px;">⚠️</span> ' + (isDe ? 'Xiaomi 6 Pro: Originale Firmware im MCU-Bereich verfügbar. Custom-Patch-Unterstützung befindet sich im Test (M8-Stecker).' : (isPl ? 'Xiaomi 6 Pro: Oryginalny firmware dostępny w sekcji MCU. Modyfikacje w trakcie testów (złącze M8).' : 'Xiaomi 6 Pro: Stock firmware available in MCU section. Custom patch support in testing (M8 connector).')) +
              '</span>' +
            '</div>';
        } else if (id === 'mi6' || id === 'mi6lite') {
          noticeHtml =
            '<div class="model-info-item" style="grid-column: span 2; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
              '<span style="color: #fbbf24; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
                '<span style="font-size: 14px;">⚠️</span> ' + esc(I.gen6TestingWarn || 'Experimental support added to bw patcher. Same procedure as 5 Elite (M8 connector). Testers welcome!') +
              '</span>' +
            '</div>';
        } else if (id === 'mi4' || id === 'mi42' || id === 'mi43') {
          noticeHtml =
            '<div class="model-info-item" style="grid-column: span 2; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); border-radius: 6px; padding: 10px 12px; margin-top: 4px;">' +
              '<span style="color: #38bdf8; font-weight: 600; font-size: 12.5px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">' +
                '<span style="font-size: 14px;">ℹ️</span> ' + esc(I.mi4Err43Hint || 'Display shows Error 43 after flashing? Your scooter is a Scooter 42 / 43 variant (DE version or BMS 0111). Select "Xiaomi Electric Scooter 42" or "43" instead of "Scooter 4".') +
              '</span>' +
            '</div>';
        }

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
          noticeHtml;
        infoBox.classList.remove('hidden')
      }
      
      // Show version check
      var verEl = el('verCheck')
      if (verEl) {
        verEl.classList.remove('hidden')
        if (modelIsOutdated(m)) {
          verEl.className = 'ver-check outdated'
          verEl.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
            '<span>Newer MCU v' + esc(VERSION_INDEX[m.firmwareId]) + ' available</span>'
        } else {
          verEl.className = 'ver-check current'
          verEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' +
            '<span>MCU v' + esc(m.bundledVersion) + ' (latest)</span>'
        }
      }
    }
    
    renderPatches()
    updateButton()
  }

  // ── Upload dropdown ───────────────────────────────────────────────────────
  function buildDropdown() {
    var sel = el('uploadModel')
    DATA.models.forEach(function (m) {
      var o = document.createElement('option')
      o.value = m.id
      o.textContent = m.name + '  (' + m.connector + ')' + (m.experimental ? ' [' + (I.expBadge || 'Experimental') + ']' : '')
      sel.appendChild(o)
    })
    sel.addEventListener('change', function () {
      state.model = sel.value || null
      renderPatches()
      updateButton()
    })
  }

  // ── Drop zone ─────────────────────────────────────────────────────────────
  function setupDrop() {
    var zone = el('dropZone'), input = el('fwFile')
    function pick(file) {
      if (!file) return
      state.file = file
      el('dropFileName').textContent = file.name + ' (' + file.size + ' B)'
      zone.classList.add('has-file')
      updateButton()
    }
    zone.addEventListener('click', function () { input.click() })
    zone.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click() } })
    input.addEventListener('change', function (e) { pick(e.target.files[0]) })
    ;['dragenter', 'dragover'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.add('drag') })
    })
    ;['dragleave', 'drop'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.remove('drag') })
    })
    zone.addEventListener('drop', function (e) { if (e.dataTransfer.files[0]) pick(e.dataTransfer.files[0]) })
  }

  // ── Patch rows ──────────────────────────────────────────────────────────
  function renderPatches() {
    var list = el('patchList'), empty = el('patchEmpty')
    state.values = {}
    state.enabled = {}
    var m = state.model ? modelById(state.model) : null
    if (!m) {
      list.classList.add('hidden')
      empty.classList.remove('hidden')
      empty.textContent = I.noModel || 'Select a model or upload firmware'
      list.innerHTML = ''
      return
    }
    if (!m.patches || m.patches.length === 0) {
      list.classList.add('hidden')
      empty.classList.remove('hidden')
      var isDe = localStorage.getItem('lang') === 'de' || (new URLSearchParams(window.location.search)).get('lang') === 'de';
      var isPl = localStorage.getItem('lang') === 'pl' || (new URLSearchParams(window.location.search)).get('lang') === 'pl';
      empty.innerHTML = '<div style="padding: 8px 0; color: #fbbf24; font-weight: 500; font-size: 13px;">' +
        '⚠️ ' + (isDe
          ? 'Custom-Patches für dieses Modell befinden sich noch in der Entwicklung. Die originale Firmware steht im MCU-Bereich zum Download bereit.'
          : (isPl
            ? 'Niestandardowe łatki dla tego modelu są w trakcie opracowywania. Oryginalne oprogramowanie układowe można pobrać w sekcji MCU.'
            : 'Custom patches for this model are currently under development. Stock firmware download is available in the MCU section.')) +
        '</div>'
      list.innerHTML = ''
      return
    }
    empty.classList.add('hidden')
    list.classList.remove('hidden')
    list.innerHTML = ''

    m.patches.forEach(function (pid) {
      var def = DATA.patchDefs[pid]
      if (!def) return
      var meta = (I.p && I.p[pid]) || [pid, '']
      var row = document.createElement('div')
      row.className = 'patch-row'
      row.setAttribute('data-pid', pid)

      var control = ''
      if (def.kind === 'speed') {
        control =
          '<div class="patch-control">' +
          '<div class="slider-head">' +
          '<span class="val"><span class="num">' + def.def.toFixed(1) + '</span><small>' + esc(I.kmh) + '</small></span>' +
          '<span class="lim">' + def.min.toFixed(1) + ' – ' + def.max.toFixed(1) + '</span>' +
          '</div>' +
          '<input type="range" min="' + def.min + '" max="' + def.max + '" step="' + def.step + '" value="' + def.def + '">' +
          '</div>'
      } else if (def.kind === 'fdv') {
        control =
          '<div class="patch-control">' +
          '<input type="text" class="fdv-input" maxlength="4" inputmode="numeric" pattern="\\d{4}" placeholder="0000">' +
          '</div>'
      }

      row.innerHTML =
        '<div class="patch-top">' +
        '<div class="patch-info"><h4>' + esc(meta[0]) + '</h4><p>' + esc(meta[1]) + '</p></div>' +
        '<label class="switch"><input type="checkbox"><span class="track"></span></label>' +
        '</div>' + control
      list.appendChild(row)

      var toggle = row.querySelector('.switch input')
      toggle.addEventListener('change', function () {
        state.enabled[pid] = toggle.checked
        row.classList.toggle('on', toggle.checked)
        state.dirty = true
        updateButton()
      })

      if (def.kind === 'speed') {
        var range = row.querySelector('input[type=range]')
        var numEl = row.querySelector('.val .num')
        state.values[pid] = def.def
        range.addEventListener('input', function () {
          var v = parseFloat(range.value)
          state.values[pid] = v
          numEl.textContent = v.toFixed(1)
          state.dirty = true
        })
      } else if (def.kind === 'fdv') {
        var inp = row.querySelector('.fdv-input')
        state.values[pid] = ''
        inp.addEventListener('input', function () {
          inp.value = inp.value.replace(/\D/g, '').slice(0, 4)
          state.values[pid] = inp.value
          state.dirty = true
          updateButton()
        })
        // Focus + select when toggled on
        toggle.addEventListener('change', function () {
          if (toggle.checked) { inp.focus(); inp.select() }
        })
      }
    })
  }

  // ── Validation + submit ───────────────────────────────────────────────────
  function selectedPatches() {
    var out = []
    Object.keys(state.enabled).forEach(function (pid) {
      if (!state.enabled[pid]) return
      var def = DATA.patchDefs[pid]
      if (def.kind === 'toggle') out.push({ id: pid })
      else if (def.kind === 'speed') out.push({ id: pid, value: state.values[pid] })
      else if (def.kind === 'fdv') out.push({ id: pid, value: state.values[pid] })
    })
    return out
  }

  function updateButton() {
    var patches = selectedPatches()
    var curModel = state.model ? modelById(state.model) : null
    var hasModel = !!state.model
    var hasSource = state.mode === 'local' ? (hasModel && curModel && curModel.localAvailable) : (!!state.file && hasModel)
    var fdvOk = patches.every(function (p) { return p.id !== 'fdv' || p.value === '' || /^\d{4}$/.test(String(p.value)) })
    el('btnPatch').disabled = !(hasModel && hasSource && patches.length > 0 && fdvOk)
  }

  function showResult(kind, text, blobUrl) {
    var r = el('result')
    r.className = 'result show ' + kind
    el('resultText').textContent = text
    el('resultIco').innerHTML = kind === 'ok'
      ? '<path d="M20 6L9 17l-5-5"/>'
      : '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>'
    var re = el('reDownload')
    if (blobUrl) { re.classList.remove('hidden'); re.href = blobUrl; re.download = 'patched_' + state.model + '_firmware.bin' }
    else re.classList.add('hidden')
  }

  function setLoading(on) {
    var btn = el('btnPatch'), label = el('btnPatchLabel')
    btn.classList.toggle('loading', on)
    if (on) { label.textContent = I.patching; btn.querySelector('svg').style.display = 'none'; if (!btn.querySelector('.spinner')) { var s = document.createElement('span'); s.className = 'spinner'; btn.insertBefore(s, label) } }
    else { label.textContent = I.patch; btn.querySelector('svg').style.display = ''; var sp = btn.querySelector('.spinner'); if (sp) sp.remove() }
    btn.disabled = on
  }

  // Turnstile callbacks
  window.onTurnstileSuccess = function (token) {
    state.turnstileToken = token
    updateButton()
  }
  window.onTurnstileError = function () {
    state.turnstileToken = null
    updateButton()
  }
  window.onTurnstileExpired = function () {
    state.turnstileToken = null
    updateButton()
  }

  function submit() {
    var fd = new FormData()
    fd.append('model', state.model)
    fd.append('mode', state.mode)
    fd.append('advanced', el('advMode').checked ? '1' : '0')
    fd.append('patches', JSON.stringify(selectedPatches()))
    if (state.mode === 'upload' && state.file) fd.append('firmware', state.file, state.file.name)
    if (state.turnstileToken) fd.append('cf-turnstile-response', state.turnstileToken)

    setLoading(true)
    el('result').className = 'result'

    fetch('patch.php', { method: 'POST', body: fd })
      .then(function (resp) {
        var ct = resp.headers.get('Content-Type') || ''
        if (resp.ok && ct.indexOf('application/octet-stream') !== -1) {
          var changed = resp.headers.get('X-Patch-Changed-Bytes')
          return resp.blob().then(function (blob) {
            var url = URL.createObjectURL(blob)
            var a = document.createElement('a')
            a.href = url
            a.download = 'patched_' + state.model + '_firmware.bin'
            document.body.appendChild(a); a.click(); document.body.removeChild(a)
            var msg = I.resultOk
            showResult('ok', msg, url)
            state.dirty = false
          })
        }
        return resp.json().then(function (j) { throw new Error((j && j.error) || I.errGeneric) })
          .catch(function (e) { throw (e instanceof Error ? e : new Error(I.errGeneric)) })
      })
      .catch(function (err) { showResult('err', err.message || I.errGeneric) })
      .then(function () { setLoading(false); updateButton() })
  }

  function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') }
  function sfmt(s) { var a = Array.prototype.slice.call(arguments, 1); var i = 0; return String(s).replace(/%s/g, function () { return i < a.length ? a[i++] : '%s' }) }

  // ── Version index: fetched once at startup, cached in sessionStorage ────
  var VERSION_INDEX = null

  function loadVersionIndex() {
    try {
      var cached = sessionStorage.getItem('patcher_fw_index')
      if (cached) {
        var parsed = JSON.parse(cached)
        if (parsed && parsed.ts && (Date.now() - parsed.ts < 300000)) {
          VERSION_INDEX = parsed.data || {}
          return
        }
      }
    } catch (e) {}
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
        VERSION_INDEX = idx
        try { sessionStorage.setItem('patcher_fw_index', JSON.stringify({ ts: Date.now(), data: idx })) } catch (e) {}
        applyOutdatedFlags()
      })
      .catch(function () { VERSION_INDEX = {} })
  }

  function modelIsOutdated(m) {
    if (!VERSION_INDEX || !m) return false
    var latest = VERSION_INDEX[m.firmwareId]
    if (!latest) return false
    // Extract MCU number from version strings like "0017" or "2.5.0_0017"
    function mcuNum(v) {
      if (!v) return NaN
      var s = String(v)
      var underscore = s.indexOf('_')
      if (underscore !== -1) s = s.slice(underscore + 1)
      return parseInt(s, 10)
    }
    var localInt  = mcuNum(m.bundledVersion)
    var latestInt = mcuNum(latest)
    if (isNaN(localInt) || isNaN(latestInt)) return false
    return latestInt > localInt
  }

  function applyOutdatedFlags() {
    if (!VERSION_INDEX) return
    DATA.models.forEach(function (m) {
      if (modelIsOutdated(m)) {
        var cards = document.querySelectorAll('#scooterGrid .model-card[data-id="' + escAttr(m.id) + '"]')
        cards.forEach(function (c) { c.classList.add('outdated') })
      }
    })
  }

  function escAttr(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;') }

  // ── Init ──────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    buildGrid()
    buildFilterBar()
    buildDropdown()
    setupDrop()
    loadVersionIndex()
    el('modeLocal').addEventListener('click', function () { setMode('local') })
    el('modeUpload').addEventListener('click', function () { setMode('upload') })
    el('advMode').addEventListener('change', updateButton)
    el('btnPatch').addEventListener('click', submit)
    renderPatches()
    updateButton()
  })

  // ── Filter bar ──────────────────────────────────────────────────────────
  function buildFilterBar() {
    var bar = el('filterBar')
    if (!bar) return
    bar.innerHTML = ''

    // Collect unique connectors and protocols from models
    var connectors = [], protocols = []
    DATA.models.forEach(function (m) {
      var c = (m.connector || '').toUpperCase()
      var p = (m.protocol || '').toUpperCase()
      if (c && connectors.indexOf(c) === -1) connectors.push(c)
      if (p && protocols.indexOf(p) === -1) protocols.push(p)
    })

    // "All" pill
    var allPill = document.createElement('button')
    allPill.type = 'button'
    allPill.className = 'filter-pill active'
    allPill.textContent = I.filterAll || 'All'
    allPill.addEventListener('click', function () {
      state.filterConnector = null
      state.filterProtocol = null
      refreshFilterPills()
      applyFilters()
    })
    bar.appendChild(allPill)

    // Separator
    if (connectors.length > 0) {
      var sep1 = document.createElement('span')
      sep1.className = 'filter-sep'
      bar.appendChild(sep1)

      // Connector group
      var cGroup = document.createElement('span')
      cGroup.className = 'filter-group'
      var cLabel = document.createElement('span')
      cLabel.className = 'filter-group-label'
      cLabel.textContent = I.filterConnector || 'Connector'
      cGroup.appendChild(cLabel)
      connectors.forEach(function (c) {
        var pill = document.createElement('button')
        pill.type = 'button'
        pill.className = 'filter-pill'
        pill.setAttribute('data-filter-connector', c.replace(/\s+/g, '_'))
        pill.textContent = c.replace(/_/g, ' ')
        pill.addEventListener('click', function () {
          state.filterConnector = state.filterConnector === c.replace(/\s+/g, '_') ? null : c.replace(/\s+/g, '_')
          refreshFilterPills()
          applyFilters()
        })
        cGroup.appendChild(pill)
      })
      bar.appendChild(cGroup)
    }

    // Protocol group
    if (protocols.length > 1) {
      var sep2 = document.createElement('span')
      sep2.className = 'filter-sep'
      bar.appendChild(sep2)

      var pGroup = document.createElement('span')
      pGroup.className = 'filter-group'
      var pLabel = document.createElement('span')
      pLabel.className = 'filter-group-label'
      pLabel.textContent = I.filterProtocol || 'Protocol'
      pGroup.appendChild(pLabel)
      protocols.forEach(function (p) {
        var pill = document.createElement('button')
        pill.type = 'button'
        pill.className = 'filter-pill'
        pill.setAttribute('data-filter-protocol', p)
        pill.textContent = p
        pill.addEventListener('click', function () {
          state.filterProtocol = state.filterProtocol === p ? null : p
          refreshFilterPills()
          applyFilters()
        })
        pGroup.appendChild(pill)
      })
      bar.appendChild(pGroup)
    }
  }

  function refreshFilterPills() {
    var bar = el('filterBar')
    if (!bar) return
    var noFilter = !state.filterConnector && !state.filterProtocol
    // All pill
    var pills = bar.querySelectorAll('.filter-pill')
    pills.forEach(function (p) {
      var fc = p.getAttribute('data-filter-connector')
      var fp = p.getAttribute('data-filter-protocol')
      if (!fc && !fp) {
        // This is the "All" pill
        p.classList.toggle('active', noFilter)
      } else if (fc) {
        p.classList.toggle('active', state.filterConnector === fc)
      } else if (fp) {
        p.classList.toggle('active', state.filterProtocol === fp)
      }
    })
  }

  function applyFilters() {
    var cards = document.querySelectorAll('#scooterGrid .model-card')
    cards.forEach(function (card) {
      var cMatch = !state.filterConnector || card.getAttribute('data-connector') === state.filterConnector
      var pMatch = !state.filterProtocol || card.getAttribute('data-protocol') === state.filterProtocol
      card.classList.toggle('filter-hidden', !(cMatch && pMatch))
    })
  }
})()
