<?php
declare(strict_types=1);
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/config.php';

$models = patcher_models();

$logoFilter = 'filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%);';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(pt('page_title')) ?></title>
  <meta name="description" content="<?= htmlspecialchars(pt('page_desc')) ?>">
  <meta name="author" content="visoire">
  <meta name="robots" content="index, follow">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars(pt('hero_title')) ?> | visoire">
  <meta property="og:description" content="<?= htmlspecialchars(pt('page_desc')) ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/patcher.css?v=<?= time() ?>">
  <!-- Cloudflare Turnstile -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
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
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= htmlspecialchars(pt('nav_wiki')) ?></a>
      <a href="../hardware/?lang=<?= htmlspecialchars($lang) ?>"><?= htmlspecialchars(pt('nav_hw')) ?></a>
      <a href="../"><?= htmlspecialchars(pt('nav_tut')) ?></a>
      <a href="../patcher/" class="active"><?= htmlspecialchars(pt('nav_patcher')) ?></a>
      <a href="../flasher/"><?= htmlspecialchars(pt('nav_flasher')) ?></a>
      <a href="../changelog/"><?= htmlspecialchars(pt('nav_changelog')) ?></a>
    </nav>
    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>
      <a href="../#steps" class="btn btn-dark"><?= htmlspecialchars(pt('btn_back')) ?></a>
      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= htmlspecialchars(pt('nav_wiki')) ?></a>
      <a href="../hardware/?lang=<?= htmlspecialchars($lang) ?>" class="mob-link"><?= htmlspecialchars(pt('nav_hw')) ?></a>
      <a href="../" class="mob-link"><?= htmlspecialchars(pt('nav_tut')) ?></a>
      <a href="../patcher/" class="mob-link"><?= htmlspecialchars(pt('nav_patcher')) ?></a>
      <a href="../flasher/" class="mob-link"><?= htmlspecialchars(pt('nav_flasher')) ?></a>
      <a href="../changelog/" class="mob-link"><?= htmlspecialchars(pt('nav_changelog')) ?></a>
      <div class="mob-bottom">
        <a href="../" class="btn btn-dark" style="width:100%"><?= htmlspecialchars(pt('btn_back')) ?></a>
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
    <h2><?= htmlspecialchars(pt('disc_title')) ?></h2>
    <p><?= htmlspecialchars(pt('disc_body')) ?></p>
    <div style="display:flex;flex-direction:column;gap:12px;align-items:center;width:100%">
      <div id="lblCountdown" style="font-size:13px;color:var(--text-tertiary)">Please wait 5 seconds...</div>
      <div class="disc-actions" style="width:100%">
        <button class="btn btn-secondary" onclick="window.location.href = (window.location.pathname.startsWith('/xiaomi') ? '/xiaomi/' : '/');"><?= htmlspecialchars(pt('disc_decline')) ?></button>
        <button id="btnAccept" class="btn btn-primary" disabled onclick="patcherAcceptDisclaimer()"><?= htmlspecialchars(pt('disc_accept')) ?></button>
      </div>
    </div>
  </div>
</div>

<main class="tool-main">
  <div class="container">
    <div class="tool-wrap">

      <div class="tool-hero">
        <h1><?= htmlspecialchars(pt('hero_title')) ?></h1>
        <p><?= htmlspecialchars(pt('hero_sub')) ?></p>
      </div>

    <!-- Step 1: source -->
    <section class="tool-card">
      <div class="tool-step-head">
        <span class="num">1</span><h2><?= htmlspecialchars(pt('mode_title')) ?></h2>
      </div>
      <div class="mode-grid">
        <button type="button" class="mode-opt active" data-mode="local" id="modeLocal">
          <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
          <span><h3><?= htmlspecialchars(pt('mode_local')) ?></h3><p><?= htmlspecialchars(pt('mode_local_sub')) ?></p></span>
        </button>
        <button type="button" class="mode-opt" data-mode="upload" id="modeUpload">
          <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0L8 8m4-4l4 4"/><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg></span>
          <span><h3><?= htmlspecialchars(pt('mode_upload')) ?></h3><p><?= htmlspecialchars(pt('mode_upload_sub')) ?></p></span>
        </button>
      </div>
    </section>

    <!-- Step 2a: local model grid -->
    <section class="tool-card" id="panelLocal">
      <div class="tool-step-head">
        <span class="num">2</span><h2><?= htmlspecialchars(pt('step_model')) ?></h2>
        <p><?= htmlspecialchars(pt('step_model_sub')) ?></p>
      </div>
      <div class="filter-bar" id="filterBar"></div>
      <div class="model-grid" id="scooterGrid"></div>
      <div class="ver-check hidden" id="verCheck"></div>
      <div class="model-info-box hidden" id="modelInfoBox"></div>
    </section>

    <!-- Step 2b: upload -->
    <section class="tool-card hidden" id="panelUpload">
      <div class="tool-step-head">
        <span class="num">2</span><h2><?= htmlspecialchars(pt('step_upload')) ?></h2>
        <p><?= htmlspecialchars(pt('step_upload_sub')) ?></p>
      </div>
      <div class="drop-zone" id="dropZone" role="button" tabindex="0" aria-label="<?= htmlspecialchars(pt('drop_label')) ?>">
        <div class="drop-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0L8 8m4-4l4 4"/><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg></div>
        <div class="drop-label"><?= htmlspecialchars(pt('drop_label')) ?></div>
        <div class="drop-hint"><?= htmlspecialchars(pt('drop_hint')) ?></div>
        <div class="drop-file"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><span id="dropFileName"></span></div>
        <input type="file" id="fwFile" accept=".bin,.BIN" hidden>
      </div>
      <div class="field-label"><?= htmlspecialchars(pt('upload_model')) ?></div>
      <div class="select-wrap">
        <select id="uploadModel">
          <option value=""><?= htmlspecialchars(pt('select_model_ph')) ?></option>
        </select>
      </div>
    </section>

    <!-- Step 3: patches -->
    <section class="tool-card">
      <div class="tool-step-head">
        <span class="num">3</span><h2><?= htmlspecialchars(pt('step_patches')) ?></h2>
        <p><?= htmlspecialchars(pt('step_patches_sub')) ?></p>
      </div>
      <div id="patchArea">
        <div class="patch-empty" id="patchEmpty"><?= htmlspecialchars(pt('no_model')) ?></div>
        <div class="patch-list hidden" id="patchList"></div>
      </div>

      <label class="custom-checkbox-container" style="margin-top: 18px;">
        <input type="checkbox" id="advMode" class="custom-checkbox-input">
        <span class="custom-checkbox-box"></span>
        <span class="custom-checkbox-label"><?= htmlspecialchars(pt('advanced')) ?></span>
      </label>

      <div class="action-bar">
        <div class="cf-turnstile" id="cfTurnstile" data-sitekey="0x4AAAAAABu4yov2SkEUYDfh" data-callback="onTurnstileSuccess" data-error-callback="onTurnstileError" data-expired-callback="onTurnstileExpired"></div>
        <button class="btn-patch" id="btnPatch" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0L8 8m4-4l4 4" transform="rotate(180 12 12)"/><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
          <span id="btnPatchLabel"><?= htmlspecialchars(pt('btn_patch')) ?></span>
        </button>
        <div class="net-warn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5a7 7 0 0114 0"/><path d="M8.5 16a3.5 3.5 0 017 0"/><circle cx="12" cy="19" r="0.6" fill="currentColor"/></svg>
          <?= htmlspecialchars(pt('net_warn')) ?>
        </div>
        <div class="result" id="result">
          <svg id="resultIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          <span id="resultText"></span>
          <a href="#" class="re hidden" id="reDownload"><?= htmlspecialchars(pt('redownload')) ?></a>
        </div>
      </div>
    </section>

    </div>
  </div>
</main>

<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-inner">
      <p class="footer-copy">
        &copy; <?= date('Y') ?> made by <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a><br>
        <span style="font-size: 11px; opacity: 0.7;">Open source, CC BY-NC-SA 4.0</span>
      </p>
      <nav class="footer-links" aria-label="Footer">
        <a href="https://github.com/visoire" target="_blank" rel="noopener">GitHub</a>
        <a href="mailto:visoire@proton.me"><?= htmlspecialchars(pt('contact')) ?></a>
      </nav>
    </div>
  </div>
</footer>

<script>
  window.PATCHER_DATA = <?= json_encode($models, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  window.PATCHER_I18N = <?= json_encode([
      'p' => [
          'sls' => [pt('p_sls'), pt('p_sls_d')],
          'sld' => [pt('p_sld'), pt('p_sld_d')],
          'slp' => [pt('p_slp'), pt('p_slp_d')],
          'dms' => [pt('p_dms'), pt('p_dms_d')],
          'mss' => [pt('p_mss'), pt('p_mss_d')],
          'cce' => [pt('p_cce'), pt('p_cce_d')],
          'rfm' => [pt('p_rfm'), pt('p_rfm_d')],
          'fdv' => [pt('p_fdv'), pt('p_fdv_d')],
      ],
      'kmh' => pt('unit_kmh'),
      'connector' => pt('connector'),
      'version' => pt('version'),
      'localOnly' => pt('local_only'),
      'patching' => pt('btn_patching'),
      'patch' => pt('btn_patch'),
      'resultOk' => pt('result_ok'),
      'changed' => pt('result_changed'),
      'errGeneric' => pt('err_generic'),
      'leaveWarn' => pt('leave_warn'),
      'filterAll' => pt('filter_all'),
      'filterConnector' => pt('filter_connector'),
      'filterProtocol' => pt('filter_protocol'),
      'gen6TestingWarn' => pt('gen6_testing_warn'),
      'mi4Err43Hint' => pt('mi4_err43_hint'),
      'gen6UnsupportedWarn' => pt('gen6_unsupported_warn'),
      'expBadge' => pt('experimental_badge'),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="assets/js/patcher.js?v=<?= time() ?>" defer></script>
<script>
  /* Burger + disclaimer (shared chrome behaviour) */
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
        if (c.indexOf('patcher_accept=1') === 0 || c.indexOf('flasher_accept=1') === 0 || c.indexOf('setup_accept=1') === 0 || c.indexOf('disclaimer_accept=1') === 0 || c.indexOf('localstorage_accepted=1') === 0) {
          return true;
        }
      }
      try {
        if (localStorage.getItem('patcher_accept') === '1' || localStorage.getItem('flasher_accept') === '1' || localStorage.getItem('setup_accept') === '1' || localStorage.getItem('disclaimer_accept') === '1' || localStorage.getItem('localstorage_accepted') === '1') {
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

    window.patcherAcceptDisclaimer = function () {
      if (o) o.style.display = 'none';
      var expires = new Date(Date.now() + 365 * 864e5).toUTCString();
      var cOpts = '; path=/; expires=' + expires + '; SameSite=Lax';
      document.cookie = 'patcher_accept=1' + cOpts;
      document.cookie = 'disclaimer_accept=1' + cOpts;
      document.cookie = 'localstorage_accepted=1' + cOpts;
      try {
        localStorage.setItem('patcher_accept', '1');
        localStorage.setItem('disclaimer_accept', '1');
        localStorage.setItem('localstorage_accepted', '1');
      } catch (e) {}
    };

    var countdownSecs = 5;
    var btnAccept = document.getElementById('btnAccept');
    var lblCountdown = document.getElementById('lblCountdown');

    function updateCountdown() {
      if (countdownSecs > 0) {
        var waitMsg = '<?= htmlspecialchars(pt('disc_wait')) ?>';
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
