<?php
declare(strict_types=1);

require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/_chrome.php';
require_once __DIR__ . '/config.php';

$models = setup_models();
$models['models'][] = [
    'id'             => 'mi4lite2nd',
    'firmwareId'     => 'xiaomi.scooter.lq4lp',
    'name'           => 'Xiaomi Electric Scooter 4 Lite 2nd Gen',
    'image'          => 'assets/img/mi4lite2nd.png',
    'bundledVersion' => '',
    'bundledMd5'     => '',
    'filename'       => '',
    'mcu_safe_url'   => '',
    'protocol'       => 'JUPOMA',
    'connector'      => 'M8 MALE',
    'localAvailable' => true,
    'patches'        => [],
    'customRedirect' => 'mi4lite2nd/',
];

$patchIds = ['sls', 'sld', 'slp', 'dms', 'mss', 'cce', 'rfm', 'fdv'];
$patchLabels = [];
foreach ($patchIds as $pid) {
    $patchLabels[$pid] = ['name' => t('patch_' . $pid), 'help' => t('patch_' . $pid . '_h')];
}

// i18n exported to the wizard controller (controller name => lang key).
$map = [
    'g_idle' => 'g_idle', 'g_busy' => 'g_busy', 'g_done' => 'g_done',
    'g_error' => 'g_error', 'g_next' => 'g_next', 'g_back' => 'g_back',
    'step1' => 's_step1', 'step2' => 's_step2', 'step3' => 's_step3',
    's1_open_flasher' => 's_s1_open_flasher',
    's2_none' => 's_s2_none', 's2_will' => 's_s2_will',
    's2_building' => 's_s2_building', 's2_fail' => 's_s2_fail',
    's3_need_conn' => 's_s3_need_conn', 's3_test_ok' => 's_s3_test_ok',
    's3_test_fail' => 's_s3_test_fail', 's3_connected' => 's_s3_connected',
    's3_flashing' => 's_s3_flashing', 's3_done' => 's_s3_done',
    's3_failed' => 's_s3_failed', 's3_abort' => 's_s3_abort',
    's_model' => 's_model', 's_protocol' => 's_protocol',
    's_version' => 's_version', 's_custom' => 's_custom', 'p_changed' => 'p_changed',
    'p_selected' => 'p_selected',
    'leave_warn' => 's_leave_warn', 'gh_outdated' => 'gh_outdated',
    'gh_link' => 'gh_link', 'f_serial_unsupported' => 'f_serial_unsupported',
];
$sjs = [];
foreach ($map as $jsName => $langKey) {
    $sjs[$jsName] = t($langKey);
}
?>
<!DOCTYPE html>
<html lang="<?= te($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= te(t('s_title')) ?></title>
  <meta name="description" content="<?= te(t('s_desc')) ?>">
  <meta name="author" content="visoire">
  <meta name="robots" content="index, follow">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= te(t('s_hero_title_a') . t('s_hero_title_b')) ?> | visoire">
  <meta property="og:description" content="<?= te(t('s_desc')) ?>">

  <link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/setup.css?v=<?= time() ?>">
  <!-- Cloudflare Turnstile -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
</head>
<body>

<?php render_header('setup'); ?>

<!-- DISCLAIMER -->
<div class="disc-overlay" id="discOverlay">
  <div class="disc-box">
    <h2><?= htmlspecialchars(t('disc_title')) ?></h2>
    <p><?= htmlspecialchars(t('disc_body')) ?></p>
    <div style="display:flex;flex-direction:column;gap:12px;align-items:center;width:100%">
      <div id="lblCountdown" style="font-size:13px;color:var(--text-tertiary)">Please wait 5 seconds...</div>
      <div class="disc-actions" style="width:100%">
        <button class="btn btn-secondary" onclick="window.location.href = (window.location.pathname.startsWith('/xiaomi') ? '/xiaomi/' : '/');"><?= htmlspecialchars(t('disc_decline')) ?></button>
        <button id="btnAccept" class="btn btn-primary" disabled onclick="setupAcceptDisclaimer()"><?= htmlspecialchars(t('disc_accept')) ?></button>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
      var c = cookies[i].trim();
      if (c.indexOf('setup_accept=1') === 0 || c.indexOf('flasher_accept=1') === 0 || c.indexOf('disclaimer_accept=1') === 0 || c.indexOf('localstorage_accepted=1') === 0) {
        var o = document.getElementById('discOverlay'); if (o) o.style.display = 'none';
        return;
      }
    }
    try {
      if (localStorage.getItem('setup_accept') === '1' || localStorage.getItem('flasher_accept') === '1' || localStorage.getItem('disclaimer_accept') === '1' || localStorage.getItem('localstorage_accepted') === '1') {
        var o = document.getElementById('discOverlay'); if (o) o.style.display = 'none';
        return;
      }
    } catch (e) {}
  })();
</script>

<main class="tool-main">
  <div class="container">

    <div class="tool-hero">
      <h1><?= te(t('s_hero_title_a')) ?><span class="accent"><?= te(t('s_hero_title_b')) ?></span></h1>
      <p><?= te(t('s_hero_sub')) ?></p>
    </div>

    <div id="ghBanner" class="gh-banner" role="note"></div>

    <div class="browser-warn" id="browserWarn" role="alert">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      <p><?= te(t('f_browser_warn')) ?></p>
    </div>

    <?php render_net_warning(); ?>

    <div class="tool-wrap">

      <div class="steps" id="stepDots"></div>
      <div class="setup-err-banner hidden" id="setupErrorBanner" role="alert"></div>

      <!-- STEP 1: select scooter -->
      <section class="wiz-page active" id="page0">
        <div class="tool-card">
          <div class="card-head"><span class="step-num">1</span><h2><?= te(t('s_step1')) ?></h2></div>
          <p class="card-hint"><?= te(t('s_s1_h')) ?></p>
          <div class="model-grid" id="modelGrid"></div>
          <div class="ver-check hidden" id="verCheck"></div>
          <div class="model-info-box hidden" id="modelInfoBox"></div>

          <div class="custom-row" id="customRow" style="margin-top:18px">
            <button class="btn btn-ghost" id="customToggle" type="button">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <?= te(t('s_custom')) ?>
            </button>
            <span class="muted" style="font-size:12.5px;margin-left:8px"><?= te(t('s_custom_h')) ?></span>
            <input type="file" id="customFile" accept=".bin,application/octet-stream" hidden>
          </div>
        </div>
      </section>

      <!-- STEP 2: select patches -->
      <section class="wiz-page" id="page1">
        <div class="tool-card">
          <div class="card-head"><span class="step-num">2</span><h2><?= te(t('s_step2')) ?></h2></div>
          <p class="card-hint"><?= te(t('s_s2_h')) ?></p>
          <div class="patch-list" id="patchList"></div>
          <div class="summary-card">
            <div class="sc-head">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
              <span><?= te(t('p_summary')) ?></span>
              <span class="sc-count" id="s2Count">0 <?= te(t('p_selected')) ?></span>
            </div>
            <div class="sc-body" id="s2Summary"><span class="muted"><?= te(t('s_s2_none')) ?></span></div>
          </div>
        </div>
      </section>

      <!-- STEP 3: connect and flash -->
      <section class="wiz-page" id="page2">
        <div class="tool-card">
          <div class="card-head"><span class="step-num">3</span><h2><?= te(t('s_step3')) ?></h2><span style="flex:1"></span><span class="state-badge idle" id="flashState"><?= te(t('g_idle')) ?></span></div>
          <p class="card-hint"><?= te(t('s_s3_h')) ?></p>

          <div class="fw-info" id="fwSummary"></div>

          <div class="conn-row" style="margin-top:18px">
            <span class="conn-dot" id="connDot"></span>
            <span class="conn-status" id="connStatus"><?= te(t('f_disconnected')) ?></span>
          </div>
          <div class="conn-buttons">
            <button class="btn btn-primary" id="btnWebSerial"><?= te(t('s_s3_btn_serial')) ?></button>
            <button class="btn btn-secondary" id="btnWebUSB"><?= te(t('s_s3_btn_usb')) ?></button>
          </div>
          <div class="usb-hint" id="usbHint" style="margin-top:12px">
            <div class="drop-hint" style="margin:0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span><?= te(t('f_usb_hint')) ?></span></div>
          </div>

          <div style="margin-top:18px">
            <div class="progress-head">
              <span class="p-status" id="progressStatus"><?= te(t('g_idle')) ?></span>
              <span class="p-pct" id="progressPct">0%</span>
            </div>
            <div class="progress-track"><div class="progress-fill" id="progressBar"></div></div>
          </div>

          <div class="conn-buttons" style="margin-top:16px">
            <button class="btn btn-secondary" id="btnTest" disabled><?= te(t('s_s3_test')) ?></button>
            <button class="btn btn-primary" id="btnFlash" disabled><?= te(t('s_s3_flash')) ?></button>
            <button class="btn btn-ghost" id="btnAbort" disabled><?= te(t('s_s3_abort')) ?></button>
          </div>

          <div class="log" id="log" aria-live="polite" style="margin-top:18px;height:200px"></div>
        </div>
      </section>

      <!-- Wizard nav -->
      <div class="wiz-nav">
        <div class="cf-turnstile" id="cfTurnstile" data-sitekey="0x4AAAAAABu4yov2SkEUYDfh" data-callback="onTurnstileSuccess" data-error-callback="onTurnstileError" data-expired-callback="onTurnstileExpired"></div>
        <button class="btn btn-ghost" id="btnCancel"><?= te(t('g_cancel')) ?></button>
        <span class="spacer"></span>
        <button class="btn btn-secondary hidden" id="btnBack"><?= te(t('g_back')) ?></button>
        <button class="btn btn-primary" id="btnNext" disabled><?= te(t('g_next')) ?></button>
      </div>

    </div>
  </div>
</main>

<?php render_footer(); ?>

<script>
  window.SETUP_DATA = <?= json_encode(['models' => $models['models'], 'patchDefs' => $models['patchDefs']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.SETUP_PATCH_LABELS = <?= json_encode($patchLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.SETUP_I18N = <?= json_encode($sjs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  window.SETUP_GH = { current: '0.7.1', repo: 'scooterteam/bw-flasher' };
</script>
<script src="../assets/js/script.js" defer></script>
<script src="assets/js/helpers.js" defer></script>
<script src="assets/js/crc.js" defer></script>
<script src="assets/js/keygen.js" defer></script>
<script src="assets/js/firmware.js" defer></script>
<script src="assets/js/serial.js" defer></script>
<script src="assets/js/brightway.js" defer></script>
<script src="assets/js/leqi.js" defer></script>
<script src="assets/js/patch-ui.js" defer></script>
<script src="assets/js/setup.js?v=<?= time() ?>" defer></script>
<script>
  (function () {
    var o = document.getElementById('discOverlay');
    if (!o || o.style.display === 'none') {
      return;
    }

    window.setupAcceptDisclaimer = function () {
      if (o) o.style.display = 'none';
      var expires = new Date(Date.now() + 365 * 864e5).toUTCString();
      var cOpts = '; path=/; expires=' + expires + '; SameSite=Lax';
      document.cookie = 'setup_accept=1' + cOpts;
      document.cookie = 'disclaimer_accept=1' + cOpts;
      document.cookie = 'localstorage_accepted=1' + cOpts;
      try {
        localStorage.setItem('setup_accept', '1');
        localStorage.setItem('disclaimer_accept', '1');
        localStorage.setItem('localstorage_accepted', '1');
      } catch (e) {}
    };

    var countdownSecs = 5;
    var btnAccept = document.getElementById('btnAccept');
    var lblCountdown = document.getElementById('lblCountdown');

    function updateCountdown() {
      if (countdownSecs > 0) {
        var waitMsg = '<?= htmlspecialchars(t('disc_wait')) ?>';
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
