<?php
declare(strict_types=1);

require_once __DIR__ . '/../lang.php';
require_once __DIR__ . '/../_chrome.php';

// Retrieve active language
global $lang;
if (!isset($lang) || !in_array($lang, ['en', 'de', 'pl'], true)) {
    $lang = 'en';
}

$T = [
    'en' => [
        'title' => 'Xiaomi Electric Scooter 4 Lite 2nd Gen Patcher | Jupoma',
        'desc' => 'Region-Changer custom patcher for Xiaomi Electric Scooter 4 Lite 2nd Gen (JUPOMA protocol). Change region to DE (20 km/h) or EU (25 km/h) over Web Serial.',
        'hero_title' => 'Xiaomi Electric Scooter 4 Lite <span class="accent">2nd Gen</span>',
        'hero_sub' => 'Custom Region-Changer tool. Change your scooter region to German (20 km/h) or European (25 km/h) rules over Web Serial.',
        'connect_adapter' => 'Connect USB-to-UART Adapter',
        'select_region' => 'Select Region Patch',
        'apply_patch' => 'Write Patch to Scooter',
        'btn_connect' => 'Connect via Web Serial',
        'btn_connected' => 'Adapter Connected',
        'btn_disconnect' => 'Disconnect',
        'btn_write' => 'Apply Region Patch',
        'card_de_title' => 'Germany Region (DE)',
        'card_de_desc' => 'Configures the speed limit to 20 km/h.',
        'card_eu_title' => 'European Region (EU)',
        'card_eu_desc' => 'Configures the speed limit to 25 km/h.',
        'log_title' => 'Process Log',
        'disclaimer_title' => 'IMPORTANT NOTICE & DISCLAIMER',
        'disclaimer_btn' => 'OK - I understand and accept',
        'disclaimer_wait' => 'Please wait %s seconds...',
        'disclaimer_text' => 'This guide is provided for educational and research purposes only, to help you understand scooter systems, and is not intended for actual modifications. We do not endorse, promote, or encourage altering any scooter\'s firmware or hardware; any changes you make are entirely at your own risk. Be aware that any modification will void your manufacturer\'s warranty. You must comply with all applicable laws and regulations in your area, including vehicle modification rules, speed limits, road safety requirements, and intellectual property laws. Modifying firmware or hardware may introduce serious safety hazards such as compromised brakes, unstable handling, electrical failures, fire risks, or personal injury. The authors make no warranties regarding the accuracy or completeness of this information and assume no liability for any damages, injuries, or losses that may result. By using this guide, you acknowledge and accept all risks, agree to comply with all legal requirements, and take full responsibility for your actions and their consequences. Proceed at your own discretion.',
    ],
    'de' => [
        'title' => 'Xiaomi Electric Scooter 4 Lite 2. Gen Patcher | Jupoma',
        'desc' => 'Regions-Changer Patcher für Xiaomi Electric Scooter 4 Lite 2. Generation (JUPOMA Protokoll). Region auf DE (20 km/h) oder EU (25 km/h) über Web Serial ändern.',
        'hero_title' => 'Xiaomi Electric Scooter 4 Lite <span class="accent">2. Generation</span>',
        'hero_sub' => 'Individuelles Regionsänderungs-Tool. Ändern Sie Ihre Scooter-Region auf deutsche (20 km/h) oder europäische (25 km/h) Regeln über Web Serial.',
        'connect_adapter' => 'USB-zu-UART Adapter verbinden',
        'select_region' => 'Region auswählen',
        'apply_patch' => 'Patch auf Scooter schreiben',
        'btn_connect' => 'Über Web Serial verbinden',
        'btn_connected' => 'Adapter Verbunden',
        'btn_disconnect' => 'Trennen',
        'btn_write' => 'Regions-Patch anwenden',
        'card_de_title' => 'Deutschland Region (DE)',
        'card_de_desc' => 'Konfiguriert das Tempolimit auf 20 km/h.',
        'card_eu_title' => 'Europäische Region (EU)',
        'card_eu_desc' => 'Konfiguriert das Tempolimit auf 25 km/h.',
        'log_title' => 'Prozessprotokoll',
        'disclaimer_title' => 'WICHTIGER HINWEIS & HAFTUNGSAUSSCHLUSS',
        'disclaimer_btn' => 'OK - Ich verstehe und akzeptiere',
        'disclaimer_wait' => 'Bitte warten Sie %s Sekunden...',
        'disclaimer_text' => 'Diese Anleitung dient ausschließlich Bildungs- und Forschungszwecken, um das Verständnis von Scooter-Systemen zu vertiefen, und ist nicht für reale Modifikationen gedacht. Wir befürworten, fördern oder ermutigen in keiner Weise das Verändern der Firmware oder Hardware von Scootern; jegliche Änderungen erfolgen auf Ihr eigenes Risiko. Bitte beachten Sie, dass jegliche Modifikationen die Herstellergarantie erlöschen lassen. Sie müssen alle geltenden Gesetze und Vorschriften in Ihrer Region einhalten, einschließlich der Regeln für Fahrzeugänderungen, Geschwindigkeitsbegrenzungen, Verkehrssicherheitsanforderungen und Gesetze zum geistigen Eigentum. Modifikationen an Firmware oder Hardware können erhebliche Sicherheitsrisiken mit sich bringen, z. B. beeinträchtigte Bremsen, instabiles Fahrverhalten, elektrische Fehler, Brandgefahr oder Personenschäden. Die Autoren übernehmen keine Gewähr für die Richtigkeit oder Vollständigkeit dieser Informationen und haften nicht für Schäden, Verletzungen oder Verluste. Mit der Nutzung dieser Anleitung erkennen Sie alle Risiken an, erklären sich mit allen rechtlichen Bestimmungen einverstanden und tragen die volle Verantwortung für Ihr Handeln. Nutzung erfolgt auf eigene Gefahr.',
    ],
    'pl' => [
        'title' => 'Xiaomi Electric Scooter 4 Lite Gen 2 Patcher | Jupoma',
        'desc' => 'Narzędzie do zmiany regionu Xiaomi Electric Scooter 4 Lite 2. generacji (protokół JUPOMA). Zmień region na PL/DE (20 km/h) lub EU (25 km/h) przez Web Serial.',
        'hero_title' => 'Xiaomi Electric Scooter 4 Lite <span class="accent">2. Generacji</span>',
        'hero_sub' => 'Narzędzie do zmiany regionu. Skonfiguruj region na niemiecki (20 km/h) lub europejski (25 km/h) za pomocą Web Serial.',
        'connect_adapter' => 'Podłącz adapter USB-UART',
        'select_region' => 'Wybierz region',
        'apply_patch' => 'Zapisz patch w hulajnodze',
        'btn_connect' => 'Połącz przez Web Serial',
        'btn_connected' => 'Adapter Połączony',
        'btn_disconnect' => 'Rozłącz',
        'btn_write' => 'Zastosuj zmianę regionu',
        'card_de_title' => 'Region Niemcy (DE)',
        'card_de_desc' => 'Konfiguruje limit prędkości do 20 km/h.',
        'card_eu_title' => 'Region Europa (EU)',
        'card_eu_desc' => 'Konfiguruje limit prędkości do 25 km/h.',
        'log_title' => 'Dziennik procesu',
        'disclaimer_title' => 'WAŻNA INFORMACJA I OSTRZEŻENIE',
        'disclaimer_btn' => 'OK - Rozumiem i akceptuję',
        'disclaimer_wait' => 'Proszę czekać %s sekund...',
        'disclaimer_text' => 'Ten poradnik służy wyłącznie celom edukacyjnym i badawczym, pomagając zrozumieć działanie systemów hulajnóg, i nie jest przeznaczony do wprowadzania rzeczywistych modyfikacji. Nie popieramy, nie promujemy ani nie zachęcamy do modyfikowania oprogramowania układowego ani sprzętu hulajnogi; wszelkie zmiany wprowadzasz wyłącznie na własne ryzyko. Pamiętaj, że każda modyfikacja unieważni gwarancję producenta. Musisz przestrzegać wszystkich obowiązujących przepisów w Twojej okolicy, w tym przepisów dotyczących modyfikacji pojazdów, ograniczeń prędkości, wymogów bezpieczeństwa drogowego i własności intelektualnej. Modyfikowanie oprogramowania lub sprzętu może wiązać się z poważnymi zagrożeniami bezpieczeństwa, takimi jak niesprawne hamulce, niestabilne prowadzenie, awarie elektryczne, pożar lub obrażenia ciała. Autorzy nie gwarantują dokładności ani kompletności tych informacji i nie ponoszą odpowiedzialności za jakiekolwiek szkody, obrażenia lub straty. Korzystając z tego poradnika, potwierdzasz i akceptujesz wszelkie ryzyko oraz bierzesz pełną odpowiedzialność za swoje działania.',
    ],
];

$t = function (string $key) use ($T, $lang) {
    return $T[$lang][$key] ?? $T['en'][$key] ?? $key;
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($t('title')) ?></title>
  <meta name="description" content="<?= htmlspecialchars($t('desc')) ?>">
  <meta name="author" content="visoire & Jupoma">
  <meta name="robots" content="index, follow">

  <link rel="icon" type="image/png" sizes="192x192" href="../../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../../assets/images/xiaomi.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/setup.css?v=<?= time() ?>">
  
  <style>
    /* Custom Jupoma Patcher Styles */
    .jupoma-wrap {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin-top: 18px;
    }
    @media (min-width: 768px) {
      .jupoma-wrap {
        grid-template-columns: 280px 1fr;
      }
    }
    .scooter-showcase {
      background: var(--bg-card);
      border: 1px solid var(--bg-card-border);
      border-radius: var(--radius-lg);
      padding: 24px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
      height: fit-content;
    }
    .scooter-showcase img {
      width: 100%;
      max-width: 200px;
      height: auto;
      object-fit: contain;
      filter: drop-shadow(0 8px 16px rgba(0,0,0,0.4));
      transition: transform var(--transition);
    }
    .scooter-showcase img:hover {
      transform: scale(1.05);
    }
    .scooter-badge {
      background: var(--accent-glow);
      color: var(--accent);
      border: 1px solid var(--accent-dark);
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 999px;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }
    .scooter-spec {
      width: 100%;
      border-top: 1px solid var(--border-light);
      padding-top: 12px;
      font-size: 12.5px;
      color: var(--text-secondary);
      display: flex;
      flex-direction: column;
      gap: 10px;
      text-align: left;
    }
    .scooter-spec div {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .scooter-spec span.lbl {
      color: var(--text-tertiary);
    }
    .scooter-spec span.val {
      font-family: 'JetBrains Mono', monospace;
      font-weight: 600;
      color: var(--text-primary);
      word-break: break-word;
    }

    .patch-step {
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-light);
    }
    .patch-step:last-of-type {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    .patch-step h3 {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 12px;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .patch-step h3 .step-num {
      width: 22px;
      height: 22px;
      border-radius: 6px;
      background: var(--accent-glow);
      color: var(--accent);
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    /* Region selection cards */
    .region-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
    }
    @media (min-width: 480px) {
      .region-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
    .region-card {
      background: var(--bg-secondary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius);
      padding: 20px;
      cursor: pointer;
      text-align: center;
      transition: all var(--transition);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      position: relative;
    }
    .region-card:hover {
      border-color: var(--text-tertiary);
      transform: translateY(-2px);
    }
    .region-card.selected {
      border-color: var(--accent);
      background: var(--accent-glow-lg);
      box-shadow: 0 6px 20px var(--accent-glow);
    }
    .region-card img {
      width: 100px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid rgba(255,255,255,0.08);
      transition: transform var(--transition);
    }
    .region-card:hover img {
      transform: scale(1.04);
    }
    .region-card h4 {
      font-size: 14px;
      font-weight: 700;
      margin: 0;
      color: var(--text-primary);
    }
    .region-card p {
      font-size: 12px;
      color: var(--text-tertiary);
      margin: 0;
      line-height: 1.4;
    }
    .region-tick {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--accent);
      color: #000;
      display: none;
      align-items: center;
      justify-content: center;
    }
    .region-card.selected .region-tick {
      display: inline-flex;
    }
    .region-tick svg {
      width: 10px;
      height: 10px;
      stroke-width: 3;
    }

    /* Connection row */
    .connection-row {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 8px;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
    }
    .status-badge.disconnected {
      background: rgba(255,255,255,0.05);
      color: var(--text-tertiary);
    }
    .status-badge.connected {
      background: var(--accent-glow);
      color: var(--accent);
      box-shadow: 0 0 10px var(--accent-glow);
    }
    .status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
    }

    /* Modal / Disclaimer */
    .modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: rgba(8,8,8,0.92);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .modal-card-custom {
      background: var(--bg-card);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-lg);
      max-width: 500px;
      width: 100%;
      padding: 24px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.6);
      animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalIn {
      from { opacity: 0; transform: translateY(12px) scale(0.97); }
      to { opacity: 1; transform: none; }
    }
    .modal-title {
      color: var(--error);
      font-size: 16px;
      font-weight: 800;
      margin-bottom: 12px;
      text-align: center;
      letter-spacing: 0.5px;
    }
    .modal-body {
      font-size: 12px;
      color: var(--text-secondary);
      line-height: 1.6;
      margin-bottom: 20px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius);
      padding: 12px;
    }
    .modal-footer {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }
    .modal-footer .btn {
      width: 100%;
    }
    .lbl-countdown {
      font-size: 12px;
      color: var(--text-tertiary);
    }
  </style>
</head>
<body>

<header class="header" role="banner">
  <div class="container">
    <a href="../../" class="logo" aria-label="visoire">
      <img src="../../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>

    <nav class="header-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= htmlspecialchars($TOOLS_T[$lang]['nav_wiki'] ?? 'Wiki') ?></a>
      <a href="../../hardware/"><?= htmlspecialchars($TOOLS_T[$lang]['nav_hw'] ?? 'Hardware') ?></a>
      <a href="../../"><?= htmlspecialchars($TOOLS_T[$lang]['nav_tut'] ?? 'Tutorial') ?></a>
      <a href="../../patcher/"><?= htmlspecialchars($TOOLS_T[$lang]['nav_patcher'] ?? 'Patcher') ?></a>
      <a href="../../flasher/"><?= htmlspecialchars($TOOLS_T[$lang]['nav_flasher'] ?? 'Flasher') ?></a>
      <a href="../../changelog/"><?= htmlspecialchars($TOOLS_T[$lang]['nav_changelog'] ?? 'Changelog') ?></a>
    </nav>

    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>
      <a href="../" class="btn btn-dark"><?= htmlspecialchars($TOOLS_T[$lang]['g_back'] ?? 'Back') ?></a>
      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
  <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_wiki'] ?? 'Wiki') ?></a>
  <a href="../../hardware/" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_hw'] ?? 'Hardware') ?></a>
  <a href="../../" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_tut'] ?? 'Tutorial') ?></a>
  <a href="../../patcher/" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_patcher'] ?? 'Patcher') ?></a>
  <a href="../../flasher/" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_flasher'] ?? 'Flasher') ?></a>
  <a href="../../changelog/" class="mob-link"><?= htmlspecialchars($TOOLS_T[$lang]['nav_changelog'] ?? 'Changelog') ?></a>
  <div class="mob-lang">
    <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
    <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
    <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
  </div>
</nav>

<main class="tool-main">
  <div class="container">
    
    <div class="tool-hero">
      <h1><?= $t('hero_title') ?></h1>
      <p><?= htmlspecialchars($t('hero_sub')) ?></p>
    </div>

    <?php render_net_warning(); ?>

    <div class="jupoma-wrap">
      
      <!-- Left: Scooter Showcase -->
      <div class="scooter-showcase">
        <img src="../assets/img/mi4lite2nd.png" alt="Xiaomi Electric Scooter 4 Lite 2nd Gen" loading="lazy">
        
        <div class="scooter-spec">
          <div>
            <span class="lbl">Model</span>
            <span class="val">Xiaomi Electric Scooter 4 Lite (Gen2)</span>
          </div>
          <div>
            <span class="lbl">Manufacturer</span>
            <span class="val">Jupoma</span>
          </div>
          <div>
            <span class="lbl">Patches</span>
            <span class="val">Region-Changer</span>
          </div>
          <div>
            <span class="lbl">Interface</span>
            <span class="val">Web Serial (115200)</span>
          </div>
        </div>
      </div>

      <!-- Right: Patcher interface -->
      <div class="tool-card pad-lg">
        
        <!-- Step 1: Connection -->
        <div class="patch-step">
          <h3><span class="step-num">1</span> <?= htmlspecialchars($t('connect_adapter')) ?></h3>
          <p class="card-hint" style="margin-bottom:12px">Connect the scooter controller dashboard ports (TX, RX, GND) to your USB-to-UART adapter. Turn the scooter on before connecting.</p>
          
          <div class="connection-row">
            <button class="btn btn-primary" id="btnConnect"><?= htmlspecialchars($t('btn_connect')) ?></button>
            <button class="btn btn-ghost hidden" id="btnDisconnect"><?= htmlspecialchars($t('btn_disconnect')) ?></button>
            
            <div class="status-badge disconnected" id="badgeStatus">
              <span class="status-dot"></span>
              <span class="status-text">Disconnected</span>
            </div>
          </div>
        </div>

        <!-- Step 2: Region Selection -->
        <div class="patch-step">
          <h3><span class="step-num">2</span> <?= htmlspecialchars($t('select_region')) ?></h3>
          
          <div class="region-grid">
            <div class="region-card" id="cardDE" onclick="selectRegion('DE')">
              <span class="region-tick">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </span>
              <img src="../assets/img/DE.png" alt="German Flag">
              <h4><?= htmlspecialchars($t('card_de_title')) ?></h4>
              <p><?= htmlspecialchars($t('card_de_desc')) ?></p>
            </div>
            
            <div class="region-card" id="cardEU" onclick="selectRegion('EU')">
              <span class="region-tick">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </span>
              <img src="../assets/img/EU.png" alt="EU Flag">
              <h4><?= htmlspecialchars($t('card_eu_title')) ?></h4>
              <p><?= htmlspecialchars($t('card_eu_desc')) ?></p>
            </div>
          </div>
        </div>

        <!-- Step 3: Write Patch -->
        <div class="patch-step">
          <h3><span class="step-num">3</span> <?= htmlspecialchars($t('apply_patch')) ?></h3>
          <p class="card-hint" style="margin-bottom:12px">Click the button below to flash the selected region payload directly into the scooter controller.</p>
          
          <button class="btn btn-primary" id="btnWrite" disabled style="width:100%;margin-bottom:14px"><?= htmlspecialchars($t('btn_write')) ?></button>
          
          <div>
            <div class="progress-head">
              <span class="p-status" id="progressStatus">Idle</span>
              <span class="p-pct" id="progressPct">0%</span>
            </div>
            <div class="progress-track"><div class="progress-fill" id="progressBar"></div></div>
          </div>
        </div>

        <!-- Terminal Log -->
        <div style="margin-top:24px">
          <h3><?= htmlspecialchars($t('log_title')) ?></h3>
          <div class="log" id="log" aria-live="polite" style="height:160px;margin-top:8px"></div>
        </div>

      </div>

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
      <nav class="footer-links" aria-label="Footer navigation">
        <a href="https://github.com/Jupoma/Xiaomi-4-Lite-Gen2-25km-h-Patcher" target="_blank" rel="noopener" style="color: var(--accent); font-weight: bold;">Jupoma GitHub (Xiaomi 4 Lite Gen2 Patcher)</a>
        <a href="https://github.com/visoire" target="_blank" rel="noopener">GitHub</a>
        <a href="mailto:visoire@proton.me"><?= htmlspecialchars($TOOLS_T[$lang]['contact'] ?? 'Contact') ?></a>
      </nav>
    </div>
  </div>
</footer>

<!-- Countdown Disclaimer Modal -->
<div class="modal-overlay" id="disclaimerModal">
  <div class="modal-card-custom">
    <div class="modal-title"><?= htmlspecialchars($t('disclaimer_title')) ?></div>
    <div class="modal-body"><?= htmlspecialchars($t('disclaimer_text')) ?></div>
    
    <div class="modal-footer">
      <div class="lbl-countdown" id="lblCountdown">Please wait 5 seconds...</div>
      <button class="btn btn-primary" id="btnAccept" disabled onclick="acceptDisclaimer()"><?= htmlspecialchars($t('disclaimer_btn')) ?></button>
    </div>
  </div>
</div>

<script>
  let port = null;
  let writer = null;
  let selectedRegion = null;
  let isConnected = false;

  const payloadEU = new Uint8Array([0x5A, 0x12, 0x97, 0x13, 0x30, 0x35, 0x33, 0x39, 0x33, 0x37, 0x30, 0x30, 0x30, 0x30, 0x30, 0x30, 0x30, 0x4A, 0x55, 0x50, 0x4F, 0x4D, 0x41, 0x24, 0x1A]);
  const payloadDE = new Uint8Array([0x5A, 0x12, 0x97, 0x13, 0x30, 0x35, 0x33, 0x37, 0x37, 0x37, 0x30, 0x30, 0x30, 0x30, 0x30, 0x30, 0x30, 0x4A, 0x55, 0x50, 0x4F, 0x4D, 0x41, 0x6E, 0x96]);

  const elLog = document.getElementById('log');
  const btnConnect = document.getElementById('btnConnect');
  const btnDisconnect = document.getElementById('btnDisconnect');
  const btnWrite = document.getElementById('btnWrite');
  const badgeStatus = document.getElementById('badgeStatus');
  const progressBar = document.getElementById('progressBar');
  const progressPct = document.getElementById('progressPct');
  const progressStatus = document.getElementById('progressStatus');

  function addLog(msg, type = '') {
    const ts = new Date().toTimeString().substring(0, 8);
    const div = document.createElement('div');
    div.className = 'log-line';
    
    const spanTs = document.createElement('span');
    spanTs.className = 'log-ts';
    spanTs.textContent = ts;
    div.appendChild(spanTs);
    
    const spanMsg = document.createElement('span');
    spanMsg.className = `log-msg ${type}`;
    spanMsg.textContent = msg;
    div.appendChild(spanMsg);
    
    elLog.appendChild(div);
    elLog.scrollTop = elLog.scrollHeight;
  }

  // Check Web Serial support
  if (!navigator.serial) {
    addLog('Web Serial API is not supported in this browser. Please use Chrome, Edge or another Chromium-based browser.', 'err');
    btnConnect.disabled = true;
  }

  async function connectSerial() {
    try {
      addLog('Requesting serial port connection...');
      port = await navigator.serial.requestPort();
      
      addLog('Opening serial port at 115200 baud...');
      await port.open({
        baudRate: 115200,
        dataBits: 8,
        stopBits: 1,
        parity: 'none',
        flowControl: 'none'
      });
      
      writer = port.writable.getWriter();
      isConnected = true;
      updateUIState();
      addLog('Connected to serial port successfully.', 'ok');
    } catch (e) {
      addLog(`Connection failed: ${e.message}`, 'err');
      console.error(e);
    }
  }

  async function disconnectSerial() {
    try {
      addLog('Closing connection...');
      if (writer) {
        await writer.releaseLock();
        writer = null;
      }
      if (port) {
        await port.close();
        port = null;
      }
      isConnected = false;
      updateUIState();
      addLog('Connection closed.', 'info');
    } catch (e) {
      addLog(`Error closing connection: ${e.message}`, 'err');
    }
  }

  function updateUIState() {
    if (isConnected) {
      btnConnect.classList.add('hidden');
      btnDisconnect.classList.remove('hidden');
      badgeStatus.className = 'status-badge connected';
      badgeStatus.querySelector('.status-text').textContent = '<?= htmlspecialchars($t('btn_connected')) ?>';
      
      // Enable write button if region is selected
      btnWrite.disabled = !selectedRegion;
    } else {
      btnConnect.classList.remove('hidden');
      btnDisconnect.classList.add('hidden');
      badgeStatus.className = 'status-badge disconnected';
      badgeStatus.querySelector('.status-text').textContent = 'Disconnected';
      btnWrite.disabled = true;
    }
  }

  function selectRegion(region) {
    selectedRegion = region;
    document.getElementById('cardDE').classList.toggle('selected', region === 'DE');
    document.getElementById('cardEU').classList.toggle('selected', region === 'EU');
    
    addLog(`Region selected: ${region}`, 'info');
    
    if (isConnected) {
      btnWrite.disabled = false;
    }
  }

  async function writePatch() {
    if (!isConnected || !writer) {
      addLog('Connection required. Connect your adapter via Web Serial first.', 'err');
      return;
    }
    if (!selectedRegion) {
      addLog('Region required. Select Germany (DE) or European (EU) region first.', 'err');
      return;
    }
    
    btnWrite.disabled = true;
    btnDisconnect.disabled = true;
    progressBar.style.width = '0%';
    progressPct.textContent = '0%';
    progressStatus.textContent = 'Writing...';
    
    try {
      const payload = selectedRegion === 'EU' ? payloadEU : payloadDE;
      addLog(`Preparing to send ${selectedRegion} patch (JUPOMA)...`);
      
      progressBar.style.width = '25%';
      progressPct.textContent = '25%';
      await new Promise(r => setTimeout(r, 200));
      
      const payloadHex = Array.from(payload).map(b => b.toString(16).toUpperCase().padStart(2, '0')).join(' ');
      addLog(`Sending payload: ${payloadHex}`, 'tx');
      
      await writer.write(payload);
      
      progressBar.style.width = '75%';
      progressPct.textContent = '75%';
      await new Promise(r => setTimeout(r, 200));
      
      progressBar.style.width = '100%';
      progressPct.textContent = '100%';
      progressStatus.textContent = 'Done';
      
      addLog('✓ Region patch written successfully!', 'ok');
      addLog('Verification complete. You can restart the scooter now.', 'ok');
    } catch (e) {
      addLog(`Failed to write patch: ${e.message}`, 'err');
      progressStatus.textContent = 'Error';
    } finally {
      btnWrite.disabled = false;
      btnDisconnect.disabled = false;
    }
  }

  btnConnect.addEventListener('click', connectSerial);
  btnDisconnect.addEventListener('click', disconnectSerial);
  btnWrite.addEventListener('click', writePatch);

  // Disclaimer Modal Logic
  let countdownSecs = 5;
  const modal = document.getElementById('disclaimerModal');
  const btnAccept = document.getElementById('btnAccept');
  const lblCountdown = document.getElementById('lblCountdown');

  function updateCountdown() {
    if (countdownSecs > 0) {
      const waitMsg = '<?= htmlspecialchars($t('disclaimer_wait')) ?>';
      lblCountdown.textContent = waitMsg.replace('%s', countdownSecs);
      countdownSecs--;
      setTimeout(updateCountdown, 1000);
    } else {
      lblCountdown.textContent = 'You can now proceed:';
      lblCountdown.style.color = 'var(--accent)';
      btnAccept.disabled = false;
    }
  }

  function acceptDisclaimer() {
    modal.style.display = 'none';
    sessionStorage.setItem('jupoma_disclaimer_accepted', 'true');
    addLog('Disclaimer accepted. Ready to connect.', 'info');
  }

  // Check if already accepted in session
  if (sessionStorage.getItem('jupoma_disclaimer_accepted') === 'true') {
    modal.style.display = 'none';
    addLog('Welcome back! Disclaimer already accepted.', 'info');
  } else {
    updateCountdown();
  }
</script>
<script src="../../assets/js/script.js" defer></script>

</body>
</html>
