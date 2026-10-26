<?php
declare(strict_types=1);
require_once __DIR__ . '/lang.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(ng('page_title')) ?></title>
  <meta name="description" content="<?= htmlspecialchars(ng('page_desc')) ?>">
  <meta name="author" content="visoire">
  <link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../flasher/assets/css/flasher.css?v=<?= time() ?>">
  <link rel="stylesheet" href="assets/ngfw.css?v=<?= time() ?>">
</head>
<body>

<header class="header" role="banner">
  <div class="container">
    <a href="../" class="logo" aria-label="visoire">
      <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>
    <nav class="header-nav" aria-label="Main navigation">
      <a href="../patcher/">Patcher</a>
      <a href="../flasher/">Flasher</a>
      <a href="../ngfw/" class="active">NGFW</a>
      <a href="../changelog/">Changelog</a>
    </nav>
    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl">PL</a>
      </div>
      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
  <a href="../patcher/" class="mob-link">Patcher</a>
  <a href="../flasher/" class="mob-link">Flasher</a>
  <a href="../ngfw/" class="mob-link">NGFW</a>
  <a href="../changelog/" class="mob-link">Changelog</a>
</nav>

<main class="tool-main">
  <div class="container">
    <div class="tool-hero">
      <h1><?= htmlspecialchars(ng('hero_title')) ?></h1>
      <p><?= htmlspecialchars(ng('hero_sub')) ?></p>
    </div>

    <div class="net-warn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
      <div><h3><?= htmlspecialchars(ng('warn_title')) ?></h3><p><?= htmlspecialchars(ng('warn_body')) ?></p></div>
    </div>

    <div class="flash-grid">
      <div class="flash-col-main">

        <section class="tool-card">
          <div class="tool-step-head"><span class="num">1</span><h2><?= htmlspecialchars(ng('step1')) ?></h2></div>
          <div class="ng-row">
            <label class="dash-field"><span><?= htmlspecialchars(ng('family')) ?></span>
              <select id="ngFamily">
                <option value="mi"><?= htmlspecialchars(ng('fam_mi')) ?></option>
                <option value="nb"><?= htmlspecialchars(ng('fam_nb')) ?></option>
              </select></label>
            <label class="dash-field"><span><?= htmlspecialchars(ng('model')) ?></span>
              <select id="ngModel"></select></label>
          </div>
        </section>

        <section class="tool-card">
          <div class="tool-step-head"><span class="num">2</span><h2><?= htmlspecialchars(ng('step2')) ?></h2></div>
          <div class="drop-zone" id="ngDrop" tabindex="0" role="button" aria-label="<?= htmlspecialchars(ng('drop')) ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="dz-label"><?= htmlspecialchars(ng('drop')) ?></span>
            <span class="dz-hint"><?= htmlspecialchars(ng('drop_hint')) ?></span>
          </div>
          <input type="file" id="ngFile" accept=".bin" hidden>
          <div class="fw-info" id="ngFwInfo"></div>
        </section>

        <section class="tool-card">
          <div class="tool-step-head"><span class="num">3</span><h2><?= htmlspecialchars(ng('step3')) ?></h2></div>
          <div class="ng-patches" id="ngPatches"></div>
        </section>

        <section class="tool-card">
          <div class="tool-step-head"><span class="num">4</span><h2><?= htmlspecialchars(ng('step4')) ?></h2></div>
          <div class="toggle-row">
            <div class="tr-text"><h4><?= htmlspecialchars(ng('sim_label')) ?></h4><p><?= htmlspecialchars(ng('sim_sub')) ?></p></div>
            <label class="switch"><input type="checkbox" id="ngSim"><span class="track"></span></label>
          </div>
          <div class="toggle-row">
            <div class="tr-text"><h4><?= htmlspecialchars(ng('debug_label')) ?></h4><p><?= htmlspecialchars(ng('debug_sub')) ?></p></div>
            <label class="switch"><input type="checkbox" id="ngDebug"><span class="track"></span></label>
          </div>
          <div class="toggle-row">
            <div class="tr-text"><h4><?= htmlspecialchars(ng('mode_label')) ?> <span class="mode-pill" id="ngModePill" data-setup="<?= htmlspecialchars(ng('mode_setup')) ?>" data-start="<?= htmlspecialchars(ng('mode_start')) ?>"><?= htmlspecialchars(ng('mode_start')) ?></span></h4><p><?= htmlspecialchars(ng('mode_sub')) ?></p></div>
            <label class="switch"><input type="checkbox" id="ngSetup"><span class="track"></span></label>
          </div>
        </section>

        <section class="tool-card">
          <div class="tool-step-head"><span class="num num-opt">+</span><h2><?= htmlspecialchars(ng('step5')) ?></h2><p><?= htmlspecialchars(ng('serial_sub')) ?></p></div>
          <div class="ng-row">
            <label class="dash-field"><span><?= htmlspecialchars(ng('target')) ?></span>
              <select id="ngTarget">
                <option value="ESC"><?= htmlspecialchars(ng('tg_esc')) ?></option>
                <option value="BLE"><?= htmlspecialchars(ng('tg_ble')) ?></option>
              </select></label>
            <label class="dash-field"><span><?= htmlspecialchars(ng('chip')) ?></span>
              <select id="ngChip"><option value="">auto</option><option value="stm32">stm32</option><option value="gd32">gd32</option><option value="at32">at32</option></select></label>
          </div>
          <div class="dash-fields" id="ngEscFields">
            <label class="dash-field dash-prefix"><span><?= htmlspecialchars(ng('prefix')) ?></span><input type="text" id="ngPrefix" maxlength="5" placeholder="32124" autocomplete="off" spellcheck="false"></label>
            <label class="dash-field dash-serial"><span><?= htmlspecialchars(ng('serial')) ?></span><input type="text" id="ngSerial" maxlength="8" placeholder="00000000" autocomplete="off" spellcheck="false"></label>
          </div>
          <div class="dash-fields" id="ngBleFields" hidden>
            <label class="dash-field dash-serial"><span><?= htmlspecialchars(ng('ble_name')) ?></span><input type="text" id="ngBle" maxlength="16" placeholder="NBScooter0000" autocomplete="off" spellcheck="false"></label>
          </div>
          <p class="dash-hint" id="ngSerialHint"></p>
          <div class="dash-preview"><span><?= htmlspecialchars(ng('cmd_label')) ?></span><code id="ngCmd">-</code><button class="btn btn-secondary ng-copy" id="ngCopy" type="button"><?= htmlspecialchars(ng('btn_copy')) ?></button></div>
        </section>

        <section class="tool-card">
          <div class="flash-actions">
            <button class="btn btn-flash" id="ngRun" disabled><?= htmlspecialchars(ng('btn_patch')) ?></button>
            <a class="btn btn-secondary ng-dl" id="ngDownload" hidden><?= htmlspecialchars(ng('btn_dl')) ?></a>
          </div>
          <div class="progress-head"><span class="progress-status" id="ngStatus">-</span></div>
        </section>
      </div>

      <div class="flash-col-log">
        <section class="tool-card">
          <div class="log-head"><h2><?= htmlspecialchars(ng('report')) ?></h2><span class="state-badge idle" id="ngState">Idle</span></div>
          <div class="log-term" id="ngLog" role="log" aria-live="polite"></div>
        </section>
      </div>
    </div>

    <p class="tool-foot-note"><?= htmlspecialchars(ng('license')) ?></p>
  </div>
</main>

<footer class="footer" role="contentinfo">
  <div class="container"><div class="footer-inner">
    <p class="footer-copy">&copy; <?= date('Y') ?> made by <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a></p>
  </div></div>
</footer>

<script>window.NGFW_I18N = <?= ng_js_i18n() ?>;</script>
<script src="assets/ngfw.js"></script>
<script>
  (function () {
    var burger = document.getElementById('burger'), mob = document.getElementById('mobNav'), ov = document.getElementById('navOverlay');
    function toggle(open) { mob.classList.toggle('open', open); ov.classList.toggle('open', open); burger.classList.toggle('open', open); burger.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    if (burger) burger.addEventListener('click', function () { toggle(!mob.classList.contains('open')); });
    if (ov) ov.addEventListener('click', function () { toggle(false); });
  })();
</script>
</body>
</html>
