<?php
declare(strict_types=1);
require_once __DIR__ . '/lang.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(ft('page_title')) ?></title>
  <meta name="description" content="<?= htmlspecialchars(ft('page_desc')) ?>">
  <meta name="author" content="visoire">
  <meta name="robots" content="index, follow">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars(ft('hero_title')) ?> | visoire">
  <meta property="og:description" content="<?= htmlspecialchars(ft('page_desc')) ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/flasher.css?v=<?= time() ?>">
</head>
<body>

<!-- HEADER -->
<header class="header" role="banner">
  <div class="container">
    <a href="../" class="logo" aria-label="visoire">
      <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>
    <nav class="header-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= htmlspecialchars(ft('nav_wiki')) ?></a>
      <a href="../hardware/?lang=<?= htmlspecialchars($lang) ?>"><?= htmlspecialchars(ft('nav_hw')) ?></a>
      <a href="../"><?= htmlspecialchars(ft('nav_tut')) ?></a>
      <a href="../patcher/"><?= htmlspecialchars(ft('nav_patcher')) ?></a>
      <a href="../flasher/" class="active"><?= htmlspecialchars(ft('nav_flasher')) ?></a>
      <a href="../changelog/"><?= htmlspecialchars(ft('nav_changelog')) ?></a>
    </nav>
    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>
      <a href="../#steps" class="btn btn-dark"><?= htmlspecialchars(ft('btn_back')) ?></a>
      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= htmlspecialchars(ft('nav_wiki')) ?></a>
      <a href="../hardware/?lang=<?= htmlspecialchars($lang) ?>" class="mob-link"><?= htmlspecialchars(ft('nav_hw')) ?></a>
      <a href="../" class="mob-link"><?= htmlspecialchars(ft('nav_tut')) ?></a>
      <a href="../patcher/" class="mob-link"><?= htmlspecialchars(ft('nav_patcher')) ?></a>
      <a href="../flasher/" class="mob-link"><?= htmlspecialchars(ft('nav_flasher')) ?></a>
      <a href="../changelog/" class="mob-link"><?= htmlspecialchars(ft('nav_changelog')) ?></a>
      <div class="mob-bottom">
        <a href="../" class="btn btn-dark" style="width:100%"><?= htmlspecialchars(ft('btn_back')) ?></a>
        <div class="mob-lang">
          <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
          <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
          <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
        </div>
      </div>
    </nav>

<!-- DISCLAIMER -->
<div class="disc-overlay" id="discOverlay">
  <div class="disc-box">
    <h2><?= htmlspecialchars(ft('disc_title')) ?></h2>
    <p><?= htmlspecialchars(ft('disc_body')) ?></p>
    <div style="display:flex;flex-direction:column;gap:12px;align-items:center;width:100%">
      <div id="lblCountdown" style="font-size:13px;color:var(--text-tertiary)">Please wait 5 seconds...</div>
      <div class="disc-actions" style="width:100%">
        <button class="btn btn-secondary" onclick="window.location.href = (window.location.pathname.startsWith('/xiaomi') ? '/xiaomi/' : '/');"><?= htmlspecialchars(ft('disc_decline')) ?></button>
        <button id="btnAccept" class="btn btn-primary" disabled onclick="flasherAcceptDisclaimer()"><?= htmlspecialchars(ft('disc_accept')) ?></button>
      </div>
    </div>
  </div>
</div>

<!-- MAIN -->
<main class="tool-main">
  <div class="container">

    <div class="tool-hero">
      <h1><?= htmlspecialchars(ft('hero_title')) ?></h1>
      <p><?= htmlspecialchars(ft('hero_sub')) ?></p>
    </div>

    <!-- Stable connection warning -->
    <div class="net-warn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
      <div>
        <h3><?= htmlspecialchars(ft('net_title')) ?></h3>
        <p><?= htmlspecialchars(ft('net_body')) ?></p>
      </div>
    </div>

    <!-- Browser unsupported -->
    <div class="browser-warn" id="browserWarn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
      <span><?= htmlspecialchars(ft('browser_warn')) ?></span>
    </div>

    <div class="flash-grid">
      <div class="flash-col-main">

        <!-- Step 1: Connect -->
        <section class="tool-card">
          <div class="tool-step-head">
            <span class="num">1</span><h2><?= htmlspecialchars(ft('step1_title')) ?></h2>
            <p><?= htmlspecialchars(ft('step1_sub')) ?></p>
          </div>
          <div class="conn-status">
            <span class="conn-dot" id="connDot"></span>
            <span id="connStatus"><?= htmlspecialchars(ft('conn_none')) ?></span>
          </div>
          <div class="conn-btns">
            <button class="conn-btn" id="btnWebSerial">
              <span class="cb-title"><?= htmlspecialchars(ft('btn_webserial')) ?></span>
              <span class="cb-hint"><?= htmlspecialchars(ft('btn_webserial_hint')) ?></span>
            </button>
            <button class="conn-btn" id="btnWebUSB">
              <span class="cb-title"><?= htmlspecialchars(ft('btn_webusb')) ?></span>
              <span class="cb-hint"><?= htmlspecialchars(ft('btn_webusb_hint')) ?></span>
            </button>
          </div>
          <div class="conn-actions">
            <button class="btn btn-secondary" id="btnDisconnect" disabled><?= htmlspecialchars(ft('btn_disconnect')) ?></button>
            <button class="btn btn-secondary" id="btnTest" disabled><?= htmlspecialchars(ft('btn_test')) ?></button>
          </div>
          <div class="webusb-hint" id="webusbHint"><?= htmlspecialchars(ft('webusb_hint')) ?></div>
        </section>

        <!-- Step 2: Firmware -->
        <section class="tool-card">
          <div class="tool-step-head">
            <span class="num">2</span><h2><?= htmlspecialchars(ft('step2_title')) ?></h2>
            <p><?= htmlspecialchars(ft('step2_sub')) ?></p>
          </div>
          <div class="drop-zone" id="dropZone" tabindex="0" role="button" aria-label="<?= htmlspecialchars(ft('drop_label')) ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="dz-label"><?= htmlspecialchars(ft('drop_label')) ?></span>
            <span class="dz-hint"><?= htmlspecialchars(ft('drop_hint')) ?></span>
          </div>
          <input type="file" id="fwFile" accept=".bin,.zip,.enc" hidden>
          <div class="fw-info" id="fwInfo"></div>
        </section>

        <!-- Step 3: Options -->
        <section class="tool-card">
          <div class="tool-step-head">
            <span class="num">3</span><h2><?= htmlspecialchars(ft('step3_title')) ?></h2>
          </div>
          <div class="toggle-row">
            <div class="tr-text">
              <h4><?= htmlspecialchars(ft('sim_label')) ?></h4>
              <p><?= htmlspecialchars(ft('sim_sub')) ?></p>
            </div>
            <label class="switch">
              <input type="checkbox" id="chkSim">
              <span class="track"></span>
            </label>
          </div>
          <div class="toggle-row">
            <div class="tr-text">
              <h4><?= htmlspecialchars(ft('debug_label')) ?></h4>
              <p><?= htmlspecialchars(ft('debug_sub')) ?></p>
            </div>
            <label class="switch">
              <input type="checkbox" id="chkDebug">
              <span class="track"></span>
            </label>
          </div>
          <div class="toggle-row">
            <div class="tr-text">
              <h4><?= htmlspecialchars(ft('mode_label')) ?> <span class="mode-pill" id="modePill" data-setup="<?= htmlspecialchars(ft('mode_setup')) ?>" data-start="<?= htmlspecialchars(ft('mode_start')) ?>"><?= htmlspecialchars(ft('mode_start')) ?></span></h4>
              <p><?= htmlspecialchars(ft('mode_sub')) ?></p>
            </div>
            <label class="switch">
              <input type="checkbox" id="chkSetup">
              <span class="track"></span>
            </label>
          </div>
        </section>

        <!-- Optional: dashboard serial (LEQI only) -->
        <section class="tool-card" id="dashCard">
          <div class="tool-step-head">
            <span class="num num-opt">+</span><h2><?= htmlspecialchars(ft('dash_title')) ?></h2>
            <p><?= htmlspecialchars(ft('dash_sub')) ?></p>
          </div>
          <div class="dash-fields">
            <label class="dash-field dash-prefix">
              <span><?= htmlspecialchars(ft('dash_prefix')) ?></span>
              <input type="text" id="dashPrefix" maxlength="5" placeholder="05393" autocomplete="off" spellcheck="false" inputmode="text" aria-describedby="dashHint">
            </label>
            <label class="dash-field dash-serial">
              <span><?= htmlspecialchars(ft('dash_serial')) ?></span>
              <input type="text" id="dashSerial" maxlength="14" placeholder="00000ABCDEF000" autocomplete="off" spellcheck="false" inputmode="text" aria-describedby="dashHint">
            </label>
          </div>
          <p class="dash-hint" id="dashHint"><?= htmlspecialchars(ft('dash_hint')) ?></p>
          <div class="dash-preview"><span><?= htmlspecialchars(ft('dash_preview')) ?></span><code id="dashPreview">-</code><span class="dash-count" id="dashCount">0/19</span></div>
          <div class="flash-actions">
            <button class="btn btn-secondary" id="btnDashWrite" disabled><?= htmlspecialchars(ft('btn_dash_write')) ?></button>
          </div>
          <p class="baud-info"><?= htmlspecialchars(ft('dash_note')) ?></p>
        </section>

        <!-- Step 4: Flash -->
        <section class="tool-card">
          <div class="tool-step-head">
            <span class="num">4</span><h2><?= htmlspecialchars(ft('step4_title')) ?></h2>
          </div>
          <div class="flash-actions">
            <button class="btn btn-flash" id="btnFlash" disabled><?= htmlspecialchars(ft('btn_flash')) ?></button>
            <button class="btn btn-secondary" id="btnAbort" disabled><?= htmlspecialchars(ft('btn_abort')) ?></button>
          </div>
          <p class="baud-info"><?= htmlspecialchars(ft('baud_info')) ?></p>
          <div class="progress-head">
            <span class="progress-status" id="progressStatus"><?= htmlspecialchars(ft('progress_ready')) ?></span>
            <span class="progress-pct" id="progressPct">0%</span>
          </div>
          <div class="progress-track"><div class="progress-bar" id="progressBar"></div></div>
        </section>

      </div>

      <div class="flash-col-log">
        <section class="tool-card">
          <div class="log-head">
            <h2><?= htmlspecialchars(ft('log_title')) ?></h2>
            <span class="state-badge idle" id="flashState"><?= htmlspecialchars(ft('state_idle')) ?></span>
          </div>
          <div class="log-head" style="margin-bottom:12px">
            <div></div>
            <div class="log-btns">
              <button class="btn btn-secondary" id="btnClear"><?= htmlspecialchars(ft('btn_clear')) ?></button>
              <button class="btn btn-secondary" id="btnExport"><?= htmlspecialchars(ft('btn_export')) ?></button>
            </div>
          </div>
          <div class="log-term" id="log" role="log" aria-live="polite"></div>
        </section>
      </div>
    </div>

    <p class="tool-foot-note"><?= htmlspecialchars(ft('foot_made')) ?></p>

  </div>
</main>

<!-- Global drop overlay -->
<div class="global-drop" id="globalDrop">
  <div class="gd-box">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    <h3><?= htmlspecialchars(ft('drop_label')) ?></h3>
    <p><?= htmlspecialchars(ft('drop_hint')) ?></p>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-inner">
      <p class="footer-copy">
        &copy; <?= date('Y') ?> made by <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a><br>
        <span style="font-size: 11px; opacity: 0.7;">Open source, CC BY-NC-SA 4.0</span>
      </p>
      <nav class="footer-links" aria-label="Footer navigation">
        <a href="https://github.com/visoire" target="_blank" rel="noopener">GitHub</a>
        <a href="mailto:visoire@proton.me"><?= htmlspecialchars(ft('foot_contact')) ?></a>
      </nav>
    </div>
  </div>
</footer>

<!-- i18n payload for the controller -->
<script>
  window.FLASHER_I18N = <?= flasher_js_i18n() ?>;
  window.FLASHER_LABELS = {
    file: <?= json_encode(ft('fw_file'), JSON_UNESCAPED_UNICODE) ?>,
    type: <?= json_encode(ft('fw_type'), JSON_UNESCAPED_UNICODE) ?>,
    size: <?= json_encode(ft('fw_size'), JSON_UNESCAPED_UNICODE) ?>,
    protocol: <?= json_encode(ft('fw_protocol'), JSON_UNESCAPED_UNICODE) ?>,
    signature: <?= json_encode(ft('fw_signature'), JSON_UNESCAPED_UNICODE) ?>,
    encryption: <?= json_encode(ft('fw_encryption'), JSON_UNESCAPED_UNICODE) ?>,
    keyoffset: <?= json_encode(ft('fw_keyoffset'), JSON_UNESCAPED_UNICODE) ?>,
    bodysize: <?= json_encode(ft('fw_bodysize'), JSON_UNESCAPED_UNICODE) ?>,
    sizesource: <?= json_encode(ft('fw_sizesource'), JSON_UNESCAPED_UNICODE) ?>
  };
  window.FLASHER_STATE = {
    idle: <?= json_encode(ft('state_idle'), JSON_UNESCAPED_UNICODE) ?>,
    flashing: <?= json_encode(ft('state_flashing'), JSON_UNESCAPED_UNICODE) ?>,
    done: <?= json_encode(ft('state_done'), JSON_UNESCAPED_UNICODE) ?>,
    error: <?= json_encode(ft('state_error'), JSON_UNESCAPED_UNICODE) ?>
  };
</script>

<!-- JSZip for zipped firmware extraction -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Protocol core, unchanged from the verified bw-flasher-web build -->
<script src="assets/js/helpers.js"></script>
<script src="assets/js/crc.js"></script>
<script src="assets/js/keygen.js"></script>
<script src="assets/js/firmware.js"></script>
<script src="assets/js/serial.js"></script>
<script src="assets/js/brightway.js"></script>
<script src="assets/js/leqi.js"></script>
<script src="assets/js/serialnumber.js"></script>

<!-- visoire UI controller -->
<script src="assets/js/flasher-ui.js"></script>

<!-- Shared chrome behaviour -->
<script>
  (function () {
    var burger = document.getElementById('burger'), mob = document.getElementById('mobNav'), ov = document.getElementById('navOverlay');
    function close() { mob.classList.remove('open'); ov.classList.remove('open'); burger.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }
    if (burger) burger.addEventListener('click', function () {
      var open = mob.classList.toggle('open'); ov.classList.toggle('open', open); burger.classList.toggle('open', open); burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    if (ov) ov.addEventListener('click', close);
    var hdr = document.querySelector('.header');
    window.addEventListener('scroll', function () { if (hdr) hdr.classList.toggle('scrolled', window.scrollY > 10); });
  })();
  (function () {
    function isAccepted() {
      var cookies = document.cookie.split(';');
      for (var i = 0; i < cookies.length; i++) {
        var c = cookies[i].trim();
        if (c.indexOf('flasher_accept=1') === 0 || c.indexOf('setup_accept=1') === 0 || c.indexOf('disclaimer_accept=1') === 0 || c.indexOf('localstorage_accepted=1') === 0) {
          return true;
        }
      }
      try {
        if (localStorage.getItem('flasher_accept') === '1' || localStorage.getItem('setup_accept') === '1' || localStorage.getItem('disclaimer_accept') === '1' || localStorage.getItem('localstorage_accepted') === '1') {
          return true;
        }
      } catch (e) {}
      return false;
    }

    var o = document.getElementById('discOverlay');
    if (isAccepted()) {
      if (o) o.style.display = 'none';
      return;
    }

    window.flasherAcceptDisclaimer = function () {
      if (o) o.style.display = 'none';
      var expires = new Date(Date.now() + 365 * 864e5).toUTCString();
      var cOpts = '; path=/; expires=' + expires + '; SameSite=Lax';
      document.cookie = 'flasher_accept=1' + cOpts;
      document.cookie = 'disclaimer_accept=1' + cOpts;
      document.cookie = 'localstorage_accepted=1' + cOpts;
      try {
        localStorage.setItem('flasher_accept', '1');
        localStorage.setItem('disclaimer_accept', '1');
        localStorage.setItem('localstorage_accepted', '1');
      } catch (e) {}
    };

    var countdownSecs = 5;
    var btnAccept = document.getElementById('btnAccept');
    var lblCountdown = document.getElementById('lblCountdown');

    function updateCountdown() {
      if (countdownSecs > 0) {
        var waitMsg = '<?= htmlspecialchars(ft('disc_wait')) ?>';
        if (lblCountdown) lblCountdown.textContent = waitMsg.replace('%s', countdownSecs);
        countdownSecs--;
        setTimeout(updateCountdown, 1000);
      } else {
        if (lblCountdown) {
          lblCountdown.textContent = 'You can now proceed:';
          lblCountdown.style.color = 'var(--accent)';
        }
        if (btnAccept) btnAccept.disabled = false;
      }
    }
    updateCountdown();
  })();
</script>
</body>
</html>
