// patch-ui.js
// Shared patch selection widget used by the patcher and the setup wizard.
// Renders one row per patch available for a model, with iOS style toggles,
// speed sliders and the 4 digit firmware version field. Reports selections in
// the exact shape patch.php expects: [{ id, value? }].
// Built by visoire, open source.

'use strict'

const PatchUI = {
  _defs: {},
  _labels: {},
  _unit: 'km/h',
  _rows: [],
  _onChange: null,

  configure({ defs, labels, unit, onChange }) {
    this._defs = defs || {}
    this._labels = labels || {}
    this._unit = unit || 'km/h'
    this._onChange = onChange || (() => {})
  },

  // Build the rows for the given model object (needs .patches array).
  render(container, model) {
    container.innerHTML = ''
    this._rows = []
    const ids = (model && model.patches) || []
    if (ids.length === 0) {
      const isDe = localStorage.getItem('lang') === 'de' || (new URLSearchParams(window.location.search)).get('lang') === 'de'
      const isPl = localStorage.getItem('lang') === 'pl' || (new URLSearchParams(window.location.search)).get('lang') === 'pl'
      container.innerHTML = `<div class="patch-empty" style="padding: 24px; text-align: center; color: #fbbf24; font-size: 13.5px; line-height: 1.6;">
        ⚠️ ${isDe
          ? 'Custom-Patches für dieses Modell befinden sich noch in der Entwicklung. Die originale Firmware steht im MCU-Bereich zum Download bereit.'
          : (isPl
            ? 'Niestandardowe łatki dla tego modelu są w trakcie opracowywania. Oryginalne oprogramowanie układowe można pobrać w sekcji MCU.'
            : 'Custom patches for this model are currently under development. Stock firmware download is available in the MCU section.')}
      </div>`
      return
    }
    ids.forEach((id) => {
      const def = this._defs[id]
      if (!def) return
      const L = this._labels[id] || { name: id, help: '' }

      const row = document.createElement('div')
      row.className = 'patch-row'

      const top = document.createElement('div')
      top.className = 'patch-top'

      const sw = document.createElement('label')
      sw.className = 'switch'
      const cb = document.createElement('input')
      cb.type = 'checkbox'
      const track = document.createElement('span')
      track.className = 'track'
      sw.appendChild(cb)
      sw.appendChild(track)

      const meta = document.createElement('div')
      meta.className = 'patch-meta'
      meta.innerHTML = `<div class="p-name"></div><div class="p-help"></div>`
      meta.querySelector('.p-name').textContent = L.name
      meta.querySelector('.p-help').textContent = L.help

      top.appendChild(sw)
      top.appendChild(meta)
      row.appendChild(top)

      const ctrl = document.createElement('div')
      ctrl.className = 'patch-ctrl'
      let getValue = () => undefined

      if (def.kind === 'speed') {
        ctrl.classList.add('has-value')
        // .slider-head: large green value display + range limits (matches patcher.js)
        const head = document.createElement('div')
        head.className = 'slider-head'
        const valWrap = document.createElement('span')
        valWrap.className = 'val'
        const numSpan = document.createElement('span')
        numSpan.className = 'num'
        numSpan.textContent = Number(def.def).toFixed(1)
        const unitSmall = document.createElement('small')
        unitSmall.textContent = this._unit
        valWrap.appendChild(numSpan)
        valWrap.appendChild(unitSmall)
        const limSpan = document.createElement('span')
        limSpan.className = 'lim'
        limSpan.textContent = `${def.min.toFixed(1)} – ${def.max.toFixed(1)}`
        head.appendChild(valWrap)
        head.appendChild(limSpan)
        ctrl.appendChild(head)
        const range = document.createElement('input')
        range.type = 'range'
        range.min = def.min; range.max = def.max; range.step = def.step
        range.value = def.def
        range.addEventListener('input', () => {
          numSpan.textContent = Number(range.value).toFixed(1)
          this._fire()
        })
        ctrl.appendChild(range)
        getValue = () => Number(range.value)
      } else if (def.kind === 'fdv') {
        ctrl.classList.add('has-value', 'fdv-ctrl')
        const txt = document.createElement('input')
        txt.type = 'text'
        txt.className = 'fdv-input'
        txt.placeholder = '0000'
        txt.maxLength = def.len || 4
        txt.value = ''
        txt.inputMode = 'numeric'
        txt.addEventListener('input', () => {
          txt.value = txt.value.replace(/\D/g, '').slice(0, 4)
          const ok = /^\d{4}$/.test(txt.value) || txt.value === ''
          txt.classList.toggle('err', !ok)
          this._fire()
        })
        ctrl.appendChild(txt)
        getValue = () => txt.value
      }

      row.appendChild(ctrl)

      cb.addEventListener('change', () => {
        row.classList.toggle('on', cb.checked)
        if (cb.checked && def.kind === 'fdv') {
          txt.focus()
          txt.select()
        }
        this._fire()
      })

      this._rows.push({ id, def, cb, getValue, row })
      container.appendChild(row)
    })
    this._fire()
  },

  _fire() { this._onChange(this.selections(), this.tags(), this.isValid()) },

  // Selections in patch.php shape.
  selections() {
    const out = []
    this._rows.forEach((r) => {
      if (!r.cb.checked) return
      if (r.def.kind === 'toggle') out.push({ id: r.id })
      else out.push({ id: r.id, value: r.getValue() })
    })
    return out
  },

  // Human readable tags for the summary box.
  tags() {
    const out = []
    this._rows.forEach((r) => {
      if (!r.cb.checked) return
      const L = this._labels[r.id] || { name: r.id }
      if (r.def.kind === 'speed') out.push(`${L.name}: ${Number(r.getValue()).toFixed(1)} ${this._unit}`)
      else if (r.def.kind === 'fdv') out.push(`${L.name}: ${r.getValue()}`)
      else out.push(L.name)
    })
    return out
  },

  // All enabled fdv fields must hold exactly four digits when filled.
  isValid() {
    return this._rows.every((r) => {
      if (!r.cb.checked || r.def.kind !== 'fdv') return true
      const v = r.getValue()
      return v === '' || /^\d{4}$/.test(v)
    })
  },

  hasSelection() { return this.selections().length > 0 },
}

window.PatchUI = PatchUI
