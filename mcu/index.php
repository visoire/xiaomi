<?php
declare(strict_types=1);
session_start();

/* Language routing. Default is English. */
$langs = ['de', 'en', 'pl'];
$lang  = (isset($_GET['lang']) && in_array($_GET['lang'], $langs, true))
       ? $_GET['lang']
       : 'en';

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$jsonPath = __DIR__ . '/fw/data.json';
$mcuModels = [];

// Map firmware model IDs to their local folder slug and patcher model ID
$slugMap = [
    'dreame.scooter.t2201'  => 'mi4',
    'navee1.scooter.t2209'  => 'mi42',
    'navee1.scooter.t2209g' => 'mi43',
    'navee1.scooter.t2210'  => 'mi4lite',
    'xiaomi.scooter.t2336'  => 'mi4pro2nd',
    'dreame.scooter.p2301'  => 'ultra4',
    'xiaomi.scooter.0005'   => 'mi5',
    'xiaomi.scooter.5pro'   => 'mi5pro',
    'xiaomi.scooter.5max'   => 'mi5max',
    'xiaomi.scooter.elite'  => 'mi5elite',
    'xiaomi.scooter.6'      => 'mi6',
    'xiaomi.scooter.6lite'  => 'mi6lite',
    'xiaomi.scooter.6pro'   => 'mi6pro',
];

if (file_exists($jsonPath)) {
    $jsonData = json_decode(file_get_contents($jsonPath), true);
    // New format: flat array of {model, name, firmware:{mcu_version,mcu_md5,mcu_safe_url,...}, extra:{imageMin,...}}
    if (is_array($jsonData)) {
        foreach ($jsonData as $item) {
            $firmwareId = (string)($item['model'] ?? '');
            if ($firmwareId === '' || !isset($slugMap[$firmwareId])) continue;

            $slug    = $slugMap[$firmwareId];
            $fw      = $item['firmware'] ?? [];
            $extra   = $item['extra']    ?? [];

            // Prefer local image in fw/<slug>/<slug>.png over CDN
            $localImg = 'fw/' . $slug . '/' . $slug . '.png';
            $localImgFile = __DIR__ . '/fw/' . $slug . '/' . $slug . '.png';
            $image = file_exists($localImgFile) ? $localImg : (string)($extra['imageMin'] ?? '');

            // Find the stock firmware filename in the slug directory
            $fwDir    = __DIR__ . '/fw/' . $slug . '/';
            $filename = '';
            if (is_dir($fwDir)) {
                foreach (scandir($fwDir) as $f) {
                    if (str_ends_with($f, '.bin')) { $filename = 'fw/' . $slug . '/' . $f; break; }
                }
            }

            $mcuModels[] = [
                'name'         => $item['name'] ?? $firmwareId,
                'model_id'     => $firmwareId,
                'slug'         => $slug,
                'image'        => $image,
                'mcu_version'  => (string)($fw['mcu_version']  ?? ''),
                'mcu_md5'      => (string)($fw['mcu_md5']      ?? ''),
                'filename'     => $filename,
                'mcu_safe_url' => (string)($fw['mcu_safe_url'] ?? ''),
            ];
        }
    }
}


$tx = [
    'de' => [
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Hardware',
        'nav_steps' => 'Anleitung',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'btn_back' => 'Zurück zur Anleitung',
        'foot_disc' => 'Nur für Bildungszwecke. Modifikationen erlöschen Straßenzulassung und Versicherungsschutz.',
        'credit_scooterteam' => 'Tools und originaler Guide von',
        'credit_bastelpichi' => 'Zusätzliche Ressourcen und Wiki von',
        'mcu_source' => 'Die Firmware-Versionen und Informationen stammen von der offiziellen <a href="https://mi-fw-info.streamlit.app/" target="_blank" rel="noopener" style="color: var(--primary); text-decoration: underline;">mi-fw-info</a> Seite.',
    ],
    'en' => [
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Hardware',
        'nav_steps' => 'Tutorial',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'btn_back' => 'Back to Tutorial',
        'foot_disc' => 'For educational purposes only. Modifications void road approval and insurance.',
        'credit_scooterteam' => 'Tools and original guide by',
        'credit_bastelpichi' => 'Additional resources and wiki by',
        'mcu_source' => 'Firmware versions and details are sourced from the official <a href="https://mi-fw-info.streamlit.app/" target="_blank" rel="noopener" style="color: var(--primary); text-decoration: underline;">mi-fw-info</a> site.',
    ],
    'pl' => [
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Sprzęt',
        'nav_steps' => 'Instrukcja',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Dziennik zmian',
        'btn_back' => 'Powrót do instrukcji',
        'foot_disc' => 'Wyłącznie do celów edukacyjnych. Modyfikacje unieważniają homologację i ubezpieczenie.',
        'credit_scooterteam' => 'Narzędzia i oryginalna instrukcja od',
        'credit_bastelpichi' => 'Dodatkowe zasoby i wiki od',
        'mcu_source' => 'Wersje oprogramowania układowego i szczegóły pochodzą z oficjalnej strony <a href="https://mi-fw-info.streamlit.app/" target="_blank" rel="noopener" style="color: var(--primary); text-decoration: underline;">mi-fw-info</a>.',
    ]
];

$L = static function (string $de, string $en, string $pl) use ($lang): string {
    return $lang === 'de' ? $de : ($lang === 'pl' ? $pl : $en);
};
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#38bdf8">
<title><?= $L('Originale MCU Firmwares', 'Original MCU Firmwares', 'Oryginalne oprogramowanie MCU') ?> - visoire.com</title>
<meta name="description" content="Download original MCU firmware files for Xiaomi e-scooter models. Sourced from mi-fw-info.">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
<link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/lucide@0.479.0/dist/umd/lucide.min.js"></script>
<script>
  (function () {
    var urlParams  = new URLSearchParams(window.location.search);
    var urlLang    = urlParams.get('lang');
    var savedLang  = localStorage.getItem('lang');
    if (urlLang && ['de', 'en', 'pl'].indexOf(urlLang) !== -1) {
      localStorage.setItem('lang', urlLang);
    } else if (savedLang && ['de', 'en', 'pl'].indexOf(savedLang) !== -1) {
      var currentLang = <?= json_encode($lang) ?>;
      if (currentLang !== savedLang) {
        urlParams.set('lang', savedLang);
        window.location.search = urlParams.toString();
      }
    }
  })();
</script>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="hdr" id="hdr">
  <div class="hdr-in">
    <a href="../" class="logo" aria-label="visoire">
      <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>

    <nav class="hdr-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= e($tx[$lang]['nav_wiki']) ?></a>
      <a href="../hardware/?lang=<?= e($lang) ?>"><?= e($tx[$lang]['nav_hw']) ?></a>
      <a href="../#steps"><?= e($tx[$lang]['nav_steps']) ?></a>
      <a href="../patcher/"><?= e($tx[$lang]['nav_patcher']) ?></a>
      <a href="../flasher/"><?= e($tx[$lang]['nav_flasher']) ?></a>
      <a href="../changelog/"><?= e($tx[$lang]['nav_changelog']) ?></a>
    </nav>

    <div class="hdr-right">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>

      <a href="../#steps" class="btn btn-dark btn-buy">
        <?= e($tx[$lang]['btn_back']) ?>
      </a>

      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<!-- Mobile nav -->
<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
  <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= e($tx[$lang]['nav_wiki']) ?></a>
  <a href="../hardware/?lang=<?= e($lang) ?>" class="mob-link"><?= e($tx[$lang]['nav_hw']) ?></a>
  <a href="../#steps" class="mob-link"><?= e($tx[$lang]['nav_steps']) ?></a>
  <a href="../patcher/" class="mob-link"><?= e($tx[$lang]['nav_patcher']) ?></a>
  <a href="../flasher/" class="mob-link"><?= e($tx[$lang]['nav_flasher']) ?></a>
  <a href="../changelog/" class="mob-link"><?= e($tx[$lang]['nav_changelog']) ?></a>
  <div class="mob-bottom">
    <a href="../" class="btn btn-dark" style="width:100%"><?= e($tx[$lang]['btn_back']) ?></a>
    <div class="mob-lang">
      <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
      <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
      <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<main class="wrap" style="margin-top: 40px; margin-bottom: 80px;">
  <div class="mcu-container">
    <div class="mcu-header">
      <h1><?= $L('Originale MCU Firmwares', 'Original MCU Firmwares', 'Oryginalne oprogramowanie MCU') ?></h1>
      <p style="margin-bottom: 20px;"><?= $L('Hier findest du die originalen MCU-Firmware-Dateien für dein Xiaomi Electric Scooter Modell.', 'Here you can find the original MCU firmware files for your Xiaomi Electric Scooter model.', 'Tutaj znajdziesz oryginalne pliki oprogramowania układowego MCU dla swojego modelu Xiaomi Electric Scooter.') ?></p>
      <button class="btn btn-primary" id="openDownloadModalBtn" style="margin: 0 auto; display: inline-flex; justify-content: center; align-items: center; gap: 8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;margin:0;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
        <?= $L('Alle Versionen herunterladen', 'Download all versions', 'Pobierz wszystkie wersje') ?>
      </button>
    </div>

    <div class="mcu-list">
      <?php foreach ($mcuModels as $model): ?>
        <?php 
          $isGen6 = in_array($model['slug'], ['mi6', 'mi6lite', 'mi6pro'], true);
          $hasLocal = ($model['filename'] !== '');
        ?>
        <div class="mcu-card">
          <div class="mcu-img-wrap">
            <img src="<?= htmlspecialchars($model['image']) ?>" alt="<?= htmlspecialchars($model['name']) ?>" loading="lazy" onerror="this.onerror=null;this.style.display='none';this.parentElement.innerHTML='<svg viewBox=\'0 0 48 48\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' style=\'width:48px;height:48px;opacity:0.35;color:var(--text);\'><path d=\'M8 34h6l4-18h12l5 12\'/><circle cx=\'12\' cy=\'37\' r=\'4\'/><circle cx=\'35\' cy=\'37\' r=\'4\'/></svg>'">
          </div>
          <div class="mcu-info">
            <h3 class="mcu-name"><?= htmlspecialchars($model['name']) ?></h3>
            <?php if ($isGen6): ?>
              <span class="mcu-badge" style="background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); font-size: 11px; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                ⚠️ <?= $L('6. Gen (Im Test · M8 wie 5 Elite)', '6th Gen (Testing · M8 same as 5 Elite)', '6. gen (W testach · M8 jak 5 Elite)') ?>
              </span>
            <?php endif; ?>
          </div>
          <div class="mcu-actions" style="display: flex; flex-direction: column; gap: 8px; min-width: 170px;">
            <?php if ($hasLocal): ?>
            <button class="btn btn-primary open-local-modal" 
                    style="width: 100%; min-height: 40px; padding: 0 14px; font-size: 13.5px;"
                    data-name="<?= e($model['name']) ?>"
                    data-model-id="<?= e($model['model_id']) ?>"
                    data-image="<?= e($model['image']) ?>"
                    data-mcu-version="<?= e($model['mcu_version']) ?>"
                    data-mcu-md5="<?= e($model['mcu_md5']) ?>"
                    data-filename="<?= e($model['filename']) ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;margin:0;display:inline-block;vertical-align:middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
              <span style="vertical-align:middle;margin-left: 6px;"><?= $L('Lokal', 'Local', 'Lokalny') ?></span>
            </button>
            <?php else: ?>
            <button class="btn btn-primary" disabled
                    style="width: 100%; min-height: 44px; padding: 0 14px; font-size: 13px; opacity: 0.5; cursor: not-allowed;"
                    title="<?= $L('Kein lokales Backup vorhanden', 'No local backup available', 'Brak lokalnej kopii zapasowej') ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;margin:0;display:inline-block;vertical-align:middle;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              <span style="vertical-align:middle;margin-left: 6px;"><?= $L('Nicht lokal', 'Not bundled', 'Brak lokalnie') ?></span>
            </button>
            <?php endif; ?>
            <button class="btn btn-ghost open-info-modal" 
                    style="width: 100%; min-height: 44px; padding: 0 14px; font-size: 13px;"
                    data-name="<?= e($model['name']) ?>"
                    data-model-id="<?= e($model['model_id']) ?>"
                    data-image="<?= e($model['image']) ?>"
                    data-mcu-version="<?= e($model['mcu_version']) ?>"
                    data-mcu-md5="<?= e($model['mcu_md5']) ?>"
                    data-mcu-safe-url="<?= e($model['mcu_safe_url']) ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;margin:0;display:inline-block;vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              <?= $L('Original Info', 'Original Info', 'Info oryginału') ?>
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="credit-box">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;color:var(--primary);margin:0 auto 10px;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      <span><?= $tx[$lang]['mcu_source'] ?></span>
    </div>
  </div>
</main>

<!-- Footer -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a href="../" class="logo">
          <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
          visoire
        </a>
        <p><?= $L('Firmware-Flashing für Brightway und LEQI Roller. Klar dokumentiert, ohne Umwege.', 'Firmware flashing for Brightway and LEQI scooters. Clearly documented, no detours.', 'Flashowanie firmware do hulajnóg Brightway i LEQI. Jasno opisane, bez zbędnych kroków.') ?></p>
      </div>
      <div class="foot-col">
        <h4><?= $L('Anleitung', 'Guide', 'Instrukcja') ?></h4>
        <ul>
          <li><a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= e($tx[$lang]['nav_wiki']) ?></a></li>
          <li><a href="../hardware/?lang=<?= e($lang) ?>"><?= e($tx[$lang]['nav_hw']) ?></a></li>
          <li><a href="../"><?= e($tx[$lang]['nav_steps']) ?></a></li>
          <li><a href="mailto:visoire@proton.me"><?= $L('Kontakt', 'Contact', 'Kontakt') ?></a></li>
        </ul>
      </div>
      <div class="foot-col">
        <h4><?= $L('Werkzeuge', 'Tools', 'Narzędzia') ?></h4>
        <ul>
          <li><a href="https://bw-patcher.streamlit.app/" target="_blank" rel="noopener">BW Patcher</a></li>
          <li><a href="https://mi-fw-info.streamlit.app/" target="_blank" rel="noopener">mi-fw-info</a></li>
          <li><a href="https://github.com/scooterteam/bw-flasher/releases/" target="_blank" rel="noopener">BW Flasher</a></li>
        </ul>
      </div>
      <div class="foot-col">
        <h4><?= $L('Sprache', 'Language', 'Język') ?></h4>
        <ul>
          <li><a href="?lang=en">English</a></li>
          <li><a href="?lang=de">Deutsch</a></li>
          <li><a href="?lang=pl">Polski</a></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">
      <span class="foot-made"><?= $L('made by', 'made by', 'made by') ?> <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a></span>
      <span class="foot-disc"><?= e($tx[$lang]['foot_disc']) ?></span>
    </div>
  </div>
</footer>

<!-- Info Modal -->
<div class="modal" id="infoModal">
  <div class="modal-box" style="max-width: 520px; border-radius: var(--r-md);">
    <div class="modal-head">
      <h3>
        <span class="yt-dot" style="background: var(--primary);"></span>
        <span id="modalTitle"><?= $L('Originale Firmware Details', 'Original Firmware Details', 'Szczegóły oryginalnego oprogramowania') ?></span>
      </h3>
      <button class="modal-close" id="closeInfoModal" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div style="padding: 24px; display: flex; flex-direction: column; gap: 18px; color: var(--text);">
      <div style="display: flex; gap: 16px; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
        <div class="mcu-img-wrap" style="width: 80px; height: 80px; background: var(--surface); border-radius: var(--r-sm); display: grid; place-items: center; padding: 6px;">
          <img id="modalImage" src="" alt="Scooter Image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 48 48\' fill=\'none\' stroke=\'%23888\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M8 34h6l4-18h12l5 12\'/><circle cx=\'12\' cy=\'37\' r=\'4\'/><circle cx=\'35\' cy=\'37\' r=\'4\'/></svg>'">
        </div>
        <div>
          <h4 id="modalName" style="font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">Xiaomi Scooter</h4>
          <span class="mcu-badge" id="modalModelId" style="font-size: 11px;">model.id</span>
        </div>
      </div>

      <!-- Loading indicator -->
      <div id="infoLoading" style="text-align: center; padding: 20px;">
        <div style="display: inline-block; width: 24px; height: 24px; border: 2px solid var(--border-2); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
        <p style="margin-top: 10px; font-size: 13px; color: var(--muted);"><?= $L('Lade Informationen von GitHub...', 'Fetching info from GitHub...', 'Pobieranie informacji z GitHub...') ?></p>
      </div>
      
      <!-- Live data from GitHub -->
      <div id="infoLiveData" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13.5px;">
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">MCU Version</span>
            <strong id="modalMcuVersion" class="mcu-badge" style="display: inline-block;">-</strong>
          </div>
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">BLE Version</span>
            <strong id="modalBleVersion" class="mcu-badge" style="display: inline-block;">-</strong>
          </div>
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">MD5 (MCU)</span>
            <code id="modalMcuMd5" style="font-family: var(--fm); color: var(--ink); font-size: 12.5px; word-break: break-all; background: var(--surface); padding: 4px 8px; border-radius: var(--r-sm); border: 1px solid var(--border); display: block;">-</code>
          </div>
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">MD5 (BLE)</span>
            <code id="modalBleMd5" style="font-family: var(--fm); color: var(--ink); font-size: 12.5px; word-break: break-all; background: var(--surface); padding: 4px 8px; border-radius: var(--r-sm); border: 1px solid var(--border); display: block;">-</code>
          </div>
        </div>

        <div id="modalChangeLogWrap" style="display: none;">
          <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 6px;">Changelog</span>
          <pre id="modalChangeLog" style="font-family: var(--fb); font-size: 13px; color: var(--text); background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 12px; white-space: pre-wrap; line-height: 1.5; max-height: 120px; overflow-y: auto;">-</pre>
        </div>

        <div id="modalInfoFooter" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
          <a id="modalDownloadBtn" href="" target="_blank" rel="noopener" class="btn btn-primary" style="width: 100%; display: inline-flex; justify-content: center; gap: 8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            <span><?= $L('Vom Xiaomi-CDN herunterladen', 'Download from Xiaomi CDN', 'Pobierz z Xiaomi CDN') ?></span>
          </a>
          <div class="credit-box" style="margin: 0; padding: 10px 14px; font-size: 12px; text-align: left; background: var(--primary-dim); border: 1px solid var(--primary-line);">
            <span style="color: var(--text); line-height: 1.5;">
              <?= $L('Live-Daten von mi-fw-info (GitHub).', 'Live data from mi-fw-info (GitHub).', 'Dane na żywo z mi-fw-info (GitHub).') ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Fallback to cached static data (if GitHub fetch fails) -->
      <div id="infoFallback" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13.5px;">
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;"><?= $L('MCU Version', 'MCU Version', 'Wersja MCU') ?></span>
            <strong id="modalMcuVersionFb" class="mcu-badge" style="display: inline-block;">-</strong>
          </div>
          <div>
            <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">MD5 Hash</span>
            <code id="modalMcuMd5Fb" style="font-family: var(--fm); color: var(--ink); font-size: 12.5px; word-break: break-all; background: var(--surface); padding: 4px 8px; border-radius: var(--r-sm); border: 1px solid var(--border); display: block;">-</code>
          </div>
        </div>
        
        <div style="margin-top: 8px;">
          <a id="modalDownloadBtnFb" href="" target="_blank" rel="noopener" class="btn btn-primary" style="width: 100%; display: inline-flex; justify-content: center; gap: 8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            <span><?= $L('Vom Xiaomi-CDN herunterladen', 'Download from Xiaomi CDN', 'Pobierz z Xiaomi CDN') ?></span>
          </a>
          <div class="credit-box" style="margin: 0; padding: 10px 14px; font-size: 12px; text-align: left; background: var(--amber-bg); border: 1px solid var(--amber-line);">
            <span style="color: var(--text); line-height: 1.5;">
              <?= $L('Zwischengespeicherte Daten aus data.json. Live-Abruf von GitHub fehlgeschlagen.', 'Cached data from data.json. Live fetch from GitHub failed.', 'Dane z pamięci podręcznej z data.json. Pobieranie na żywo z GitHub nie powiodło się.') ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Local Download Modal -->
<div class="modal" id="localModal">
  <div class="modal-box" style="max-width: 520px; border-radius: var(--r-md);">
    <div class="modal-head">
      <h3>
        <span class="yt-dot" style="background: var(--primary);"></span>
        <span id="localModalTitle"><?= $L('Lokales Firmware-Backup', 'Local Firmware Backup', 'Lokalna kopia zapasowa oprogramowania') ?></span>
      </h3>
      <button class="modal-close" id="closeLocalModal" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div style="padding: 24px; display: flex; flex-direction: column; gap: 18px; color: var(--text);">
      <div style="display: flex; gap: 16px; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
        <div class="mcu-img-wrap" style="width: 80px; height: 80px; background: var(--surface); border-radius: var(--r-sm); display: grid; place-items: center; padding: 6px;">
          <img id="localModalImage" src="" alt="Scooter Image" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 48 48\' fill=\'none\' stroke=\'%23888\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M8 34h6l4-18h12l5 12\'/><circle cx=\'12\' cy=\'37\' r=\'4\'/><circle cx=\'35\' cy=\'37\' r=\'4\'/></svg>'">
        </div>
        <div>
          <h4 id="localModalName" style="font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">Xiaomi Scooter</h4>
          <span class="mcu-badge" id="localModalModelId" style="font-size: 11px;">model.id</span>
        </div>
      </div>
      
      <div style="font-size: 13.5px;">
        <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;"><?= $L('MCU Version', 'MCU Version', 'Wersja MCU') ?></span>
        <strong id="localModalMcuVersion" class="mcu-badge" style="display: inline-block;">-</strong>
      </div>
      
      <div style="font-size: 13.5px;">
        <span style="display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; font-family: var(--fm); margin-bottom: 4px;">MD5 Hash</span>
        <code id="localModalMcuMd5" style="font-family: var(--fm); color: var(--ink); font-size: 12.5px; word-break: break-all; background: var(--surface); padding: 4px 8px; border-radius: var(--r-sm); border: 1px solid var(--border); display: block;">-</code>
      </div>
      
      <div class="credit-box" style="margin: 0; padding: 14px; font-size: 13px; text-align: left; background: var(--primary-dim); border: 1px solid var(--primary-line);">
        <p style="color: var(--text); line-height: 1.5;">
          <?= $L(
              'Diese Firmware-Datei wird direkt von unserem Server heruntergeladen. Du kannst sie anschließend im BW Patcher hochladen und anpassen.',
              'This firmware file will be downloaded directly from our server. You can then upload and patch it using the BW Patcher.',
              'Ten plik oprogramowania zostanie pobrany bezpośrednio z naszego serwera. Następnie możesz go przesłać i zmodyfikować za pomocą BW Patcher.'
          ) ?>
        </p>
      </div>
      
      <div style="margin-top: 8px;">
        <a id="localModalDownloadBtn" href="" download class="btn btn-primary" style="width: 100%; display: inline-flex; justify-content: center; gap: 8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
          <span><?= $L('Herunterladen', 'Download', 'Pobierz') ?></span>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Download info modal -->
<div class="modal" id="downloadModal" role="dialog" aria-modal="true" aria-label="<?= $L('Information zur Datei', 'File information', 'Informacje o pliku') ?>">
  <div class="modal-box" style="max-width: 520px; border-radius: var(--r-md);">
    <div class="modal-head">
      <h3>
        <span class="yt-dot" style="background: var(--primary);"></span>
        <?= $L('Information zur Datei', 'File information', 'Informacje o pliku') ?>
      </h3>
      <button class="modal-close" id="closeDownloadModal" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div style="padding: 24px; display: flex; flex-direction: column; gap: 18px; color: var(--text);">
      <div>
        <p style="font-size: 14px; color: var(--text); line-height: 1.6; margin: 0;"><?= $L('Diese Datei enthält alle Firmwares (original und gepatcht) für Xiaomi-Brightway-Leqi Roller, sowie MCUs von mi-fw-info und Patches via bw-patcher.', 'This file contains all firmwares (original and patched) for Xiaomi-Brightway-Leqi scooters, as well as MCUs from mi-fw-info and patches via bw-patcher.', 'Ten plik zawiera wszystkie firmware (oryginalne i po modyfikacjach) dla hulajnóg Xiaomi-Brightway-Leqi, a także MCUs z mi-fw-info i poprawki via bw-patcher.') ?></p>
      </div>
      <div style="background: var(--red-bg); border: 1px solid var(--red-line); border-radius: var(--r-md); padding: 14px;">
        <strong style="color: var(--red); display: block; margin-bottom: 6px; font-family: var(--fm); font-size: 12px; text-transform: uppercase;"><?= $L('Wichtig', 'Important', 'Ważne') ?></strong>
        <p style="font-size: 13px; color: var(--text); line-height: 1.5; margin: 0 0 8px 0;"><?= $L('Die Verteilung von Originalfirmware-Dateien verletzt die geistigen Eigentumsrechte von Brightway und Xiaomi. Nutze daher stets mi-fw-info, um Originalfirmware direkt von den offiziellen Servern herunterzuladen.', 'Distributing original firmware files violates the intellectual property rights of Brightway and Xiaomi. Therefore, always use mi-fw-info to download original firmware directly from official servers.', 'Dystrybucja oryginalnych plików firmware narusza prawa własności intelektualnej Brightway i Xiaomi. Dlatego zawsze używaj mi-fw-info, aby pobierać oryginalny firmware bezpośrednio z oficjalnych serwerów.') ?></p>
        <p style="font-size: 13px; color: var(--text); line-height: 1.5; margin: 0;"><?= $L('Für individuell entwickelte Tools wie BW Flasher und BW Patcher beziehe sie bitte von ihren offiziellen Quellen. Dadurch erhältst du immer die aktuellsten Versionen und behältst ihre volle Multi-Plattform-Funktionalität.', 'For individually developed tools like BW Flasher and BW Patcher, please obtain them from their official sources. This ensures you always have the latest versions and retain their full multi-platform functionality.', 'Dla indywidualnie opracowanych narzędzi, takich jak BW Flasher i BW Patcher, proszę pobierać je z ich oficjalnych źródeł. Dzięki temu będziesz mieć zawsze najnowsze wersje i zachowasz ich pełną wieloplatformową funkcjonalność.') ?></p>
      </div>
      <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
        <a href="../Xiaomi-Brightway-Leqi-FIRMWARES.zip" class="btn btn-primary" download style="flex: 1; justify-content: center; gap: 8px; min-height: 44px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;margin:0;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
          <span><?= $L('Download', 'Download', 'Pobierz') ?></span>
        </a>
        <button class="btn btn-cancel" id="cancelDownloadBtn" style="flex: 1; justify-content: center; min-height: 44px;"><?= $L('Abbrechen', 'Cancel', 'Anuluj') ?></button>
      </div>
    </div>
  </div>
</div>

<script>
  // Mobile navigation logic
  var burger = document.getElementById('burger');
  var mobNav = document.getElementById('mobNav');
  var overlay = document.getElementById('navOverlay');
  if (burger && mobNav && overlay) {
    burger.addEventListener('click', function () {
      burger.classList.toggle('open');
      mobNav.classList.toggle('open');
      overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', function () {
      burger.classList.remove('open');
      mobNav.classList.remove('open');
      overlay.classList.remove('open');
    });
  }

  // Info Modal Logic - Live fetch from GitHub via fetch_info.php
  var infoModal = document.getElementById('infoModal');
  var closeInfoModal = document.getElementById('closeInfoModal');
  var modalName = document.getElementById('modalName');
  var modalModelId = document.getElementById('modalModelId');
  var modalImage = document.getElementById('modalImage');
  var modalMcuVersion = document.getElementById('modalMcuVersion');
  var modalMcuMd5 = document.getElementById('modalMcuMd5');
  var modalDownloadBtn = document.getElementById('modalDownloadBtn');
  var infoLoading = document.getElementById('infoLoading');
  var infoLiveData = document.getElementById('infoLiveData');
  var infoFallback = document.getElementById('infoFallback');
  var modalBleVersion = document.getElementById('modalBleVersion');
  var modalBleMd5 = document.getElementById('modalBleMd5');
  var modalChangeLog = document.getElementById('modalChangeLog');
  var modalChangeLogWrap = document.getElementById('modalChangeLogWrap');

  function resetInfoModal() {
    if (infoLoading) infoLoading.style.display = 'block';
    if (infoLiveData) infoLiveData.style.display = 'none';
    if (infoFallback) infoFallback.style.display = 'none';
  }

  document.querySelectorAll('.open-info-modal').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = btn.getAttribute('data-name');
      var modelId = btn.getAttribute('data-model-id');
      var image = btn.getAttribute('data-image');
      var mcuVersion = btn.getAttribute('data-mcu-version');
      var mcuMd5 = btn.getAttribute('data-mcu-md5');
      var mcuSafeUrl = btn.getAttribute('data-mcu-safe-url');

      // Set basic info immediately
      modalName.textContent = name;
      modalModelId.textContent = modelId;
      modalImage.src = image;

      // Reset to loading state
      resetInfoModal();

      // Show the modal
      infoModal.classList.add('open');

      // Fetch live info from GitHub via our proxy endpoint
      var fetchUrl = 'fetch_info.php?model=' + encodeURIComponent(modelId);

      fetch(fetchUrl)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (!data.ok) {
            throw new Error(data.error || 'Unknown error');
          }

          // Live data available - populate from GitHub
          if (infoLoading) infoLoading.style.display = 'none';
          if (infoLiveData) infoLiveData.style.display = 'block';
          if (infoFallback) infoFallback.style.display = 'none';

          var fw = data.firmware || {};

          // MCU version
          if (modalMcuVersion) {
            modalMcuVersion.textContent = fw.mcu_version || mcuVersion || '-';
          }

          // BLE version
          if (modalBleVersion) {
            modalBleVersion.textContent = fw.version || '-';
          }

          // MCU md5
          if (modalMcuMd5) {
            modalMcuMd5.textContent = fw.mcu_md5 || mcuMd5 || '-';
          }

          // BLE md5
          if (modalBleMd5) {
            modalBleMd5.textContent = fw.md5 || '-';
          }

          // Changelog
          if (modalChangeLog && modalChangeLogWrap) {
            var cl = fw.changeLog || '';
            if (cl.trim() !== '') {
              modalChangeLog.textContent = cl;
              modalChangeLogWrap.style.display = 'block';
            } else {
              modalChangeLogWrap.style.display = 'none';
            }
          }

          // Download button - prefer live mcu_safe_url, fall back to local
          if (modalDownloadBtn) {
            modalDownloadBtn.href = fw.mcu_safe_url || mcuSafeUrl || '#';
          }
        })
        .catch(function(err) {
          // GitHub fetch failed - fall back to cached data from data.json attributes
          console.warn('Live fetch failed, using cached data:', err);

          if (infoLoading) infoLoading.style.display = 'none';
          if (infoLiveData) infoLiveData.style.display = 'none';
          if (infoFallback) infoFallback.style.display = 'block';

          // Use the static attributes from the button
          var fbVersion = document.getElementById('modalMcuVersionFb');
          var fbMd5 = document.getElementById('modalMcuMd5Fb');
          var fbDownload = document.getElementById('modalDownloadBtnFb');

          if (fbVersion) fbVersion.textContent = mcuVersion;
          if (fbMd5) fbMd5.textContent = mcuMd5;
          if (fbDownload) fbDownload.href = mcuSafeUrl || '#';
        });
    });
  });

  if (closeInfoModal && infoModal) {
    closeInfoModal.addEventListener('click', function() {
      infoModal.classList.remove('open');
    });
    
    // Close on overlay click
    infoModal.addEventListener('click', function(e) {
      if (e.target === infoModal) {
        infoModal.classList.remove('open');
      }
    });
  }

  // Local Modal Logic
  var localModal = document.getElementById('localModal');
  var closeLocalModal = document.getElementById('closeLocalModal');
  var localModalName = document.getElementById('localModalName');
  var localModalModelId = document.getElementById('localModalModelId');
  var localModalImage = document.getElementById('localModalImage');
  var localModalMcuVersion = document.getElementById('localModalMcuVersion');
  var localModalMcuMd5 = document.getElementById('localModalMcuMd5');
  var localModalDownloadBtn = document.getElementById('localModalDownloadBtn');

  document.querySelectorAll('.open-local-modal').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = btn.getAttribute('data-name');
      var modelId = btn.getAttribute('data-model-id');
      var image = btn.getAttribute('data-image');
      var mcuVersion = btn.getAttribute('data-mcu-version');
      var mcuMd5 = btn.getAttribute('data-mcu-md5');
      var filename = btn.getAttribute('data-filename');

      localModalName.textContent = name;
      localModalModelId.textContent = modelId;
      localModalImage.src = image;
      localModalMcuVersion.textContent = mcuVersion;
      localModalMcuMd5.textContent = mcuMd5;
      localModalDownloadBtn.href = filename;

      localModal.classList.add('open');
    });
  });

  if (closeLocalModal && localModal) {
    closeLocalModal.addEventListener('click', function() {
      localModal.classList.remove('open');
    });
    
    // Close on overlay click
    localModal.addEventListener('click', function(e) {
      if (e.target === localModal) {
        localModal.classList.remove('open');
      }
    });
  }

  // Download Modal Logic
  var openDownloadModalBtn = document.getElementById('openDownloadModalBtn');
  var downloadModal = document.getElementById('downloadModal');
  var closeDownloadModal = document.getElementById('closeDownloadModal');
  var cancelDownloadBtn = document.getElementById('cancelDownloadBtn');

  if (openDownloadModalBtn && downloadModal) {
    openDownloadModalBtn.addEventListener('click', function() {
      downloadModal.classList.add('open');
      document.body.style.overflow = 'hidden';
      if (closeDownloadModal) closeDownloadModal.focus();
    });
  }

  function closeDownloadModalFn() {
    if (downloadModal) {
      downloadModal.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  if (closeDownloadModal) {
    closeDownloadModal.addEventListener('click', closeDownloadModalFn);
  }
  if (cancelDownloadBtn) {
    cancelDownloadBtn.addEventListener('click', closeDownloadModalFn);
  }
  if (downloadModal) {
    downloadModal.addEventListener('click', function(e) {
      if (e.target === downloadModal) {
        closeDownloadModalFn();
      }
    });
  }

  // Global Escape key listener for all modals on the page
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (infoModal) infoModal.classList.remove('open');
      if (localModal) localModal.classList.remove('open');
      closeDownloadModalFn();
      document.body.style.overflow = '';
    }
  });
</script>
</body>
</html>
