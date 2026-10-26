<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inc/functions.php';

$scooterOptions = getScooterOptions();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(__('title')) ?></title>
  <meta name="description" content="<?= htmlspecialchars(__('desc')) ?>">
  <meta name="keywords" content="Hardware Guide, Xiaomi Scooter Flashing Cable, USB-C UART, FTDI FT232RL, 3.3V Logic Level, M7 MALE FEMALE, M8 Julet, Scooter 4, Scooter 5, Scooter 6">
  <meta name="robots" content="index, follow">
  <meta name="author" content="visoire">
  <link rel="canonical" href="https://visoire.com/xiaomi/hardware/">

  <!-- Open Graph -->
  <meta property="og:type"        content="article">
  <meta property="og:title"       content="<?= htmlspecialchars(__('title')) ?>">
  <meta property="og:description" content="<?= htmlspecialchars(__('desc')) ?>">
  <meta property="og:image"       content="../assets/img/image_1.webp">
  <meta property="og:url"         content="https://visoire.com/xiaomi/hardware/">

  <link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
  <link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars(__('title')) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars(__('desc')) ?>">
  <meta name="twitter:image"       content="../assets/img/image_1.webp">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time() ?>">

  <!-- Technical Article Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "TechArticle",
    "headline": "Hardware Guide: What to Look for When Buying a Flashing Cable",
    "description": "Technical reference detailing FTDI USB-UART serial adapters, 3.3V vs 5V logic levels, TX/RX crossover pinouts, and Julet connector standards for Xiaomi scooter flashing.",
    "author": {
      "@type": "Organization",
      "name": "visoire"
    },
    "image": "../assets/img/image_1.webp",
    "url": "https://visoire.com/xiaomi/hardware/"
  }
  </script>

  <script>
    window.i18n = {
      conn_label:              <?= json_encode(__('conn_label')) ?>,
      compat_flashable:        <?= json_encode(__('compat_flashable')) ?>,
      flashable_yes:           <?= json_encode(__('flashable_yes')) ?>,
      flashable_no:            <?= json_encode(__('flashable_no')) ?>,
      flashable_testing:       <?= json_encode(__('flashable_testing')) ?>,
      modal_label_pins_male:   <?= json_encode(__('compat_difference_male_badge')) ?>,
      modal_label_pins_female: <?= json_encode(__('compat_difference_female_badge')) ?>
    };

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
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= __('nav_wiki') ?></a>
      <a href="#guide" class="active"><?= __('nav_hw') ?></a>
      <a href="../"><?= __('nav_steps') ?></a>
      <a href="../patcher/"><?= $lang === 'de' ? 'Patcher' : ($lang === 'pl' ? 'Patcher' : 'Patcher') ?></a>
      <a href="../flasher/"><?= $lang === 'de' ? 'Flasher' : ($lang === 'pl' ? 'Flasher' : 'Flasher') ?></a>
      <a href="../changelog/"><?= __('nav_changelog') ?></a>
    </nav>

    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>

      <a href="../" class="btn btn-dark">
        <?= __('btn_tutorial') ?>
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
  <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= __('nav_wiki') ?></a>
  <a href="#guide" class="mob-link active"><?= __('nav_hw') ?></a>
  <a href="../" class="mob-link"><?= __('nav_steps') ?></a>
  <a href="../patcher/" class="mob-link">Patcher</a>
  <a href="../flasher/" class="mob-link">Flasher</a>
  <a href="../changelog/" class="mob-link"><?= __('nav_changelog') ?></a>
  <div class="mob-bottom">
    <a href="../" class="btn btn-dark" style="width:100%"><?= __('btn_tutorial') ?></a>
    <div class="mob-lang">
      <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
      <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
      <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
    </div>
  </div>
</nav>

<!-- MAIN -->
<main>

  <!-- HERO / GUIDE HEADER SECTION -->
  <section class="product-section glow-bg" id="guide" aria-labelledby="guide-title">
    <div class="container">
      <div class="product-grid">

        <!-- LEFT COLUMN: HARDWARE SHOWCASE GALLERY -->
        <div class="gallery fade-in" aria-label="Hardware Overview">
          <div class="gallery-stack">
            <div class="gallery-stack-item lightbox-trigger" role="button" tabindex="0" aria-label="Hardware Cable Overview">
              <img
                id="productMainImg"
                src="../assets/img/main.jpg"
                alt="Flashing Cable and Connector Architecture"
                loading="eager"
              >
            </div>
          </div>

          <!-- Quick Hardware Reference Cards -->
          <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; background: var(--bg-card); border: 1px solid var(--border-light); padding: 12px 14px; border-radius: var(--radius-sm); font-size: 13px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: var(--accent); flex-shrink: 0;"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
              <span><strong>Interface:</strong> USB to TTL/UART (3.3V Logic)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; background: var(--bg-card); border: 1px solid var(--border-light); padding: 12px 14px; border-radius: var(--radius-sm); font-size: 13px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: #10b981; flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
              <span><strong>Pinout:</strong> TX &rarr; RX | RX &rarr; TX | GND &rarr; GND</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; background: var(--bg-card); border: 1px solid var(--border-light); padding: 12px 14px; border-radius: var(--radius-sm); font-size: 13px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: #60a5fa; flex-shrink: 0;"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
              <span><strong>Standard:</strong> Julet 5-Pin Waterproof Connector (M7 / M8)</span>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: GUIDE TITLE & INTERACTIVE CONNECTOR INSPECTOR -->
        <div class="product-info fade-in fade-in-delay-1">

          <h1 class="product-title" id="guide-title">
            <?= __('product_title') ?>
          </h1>

          <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 24px;">
            <?= __('guide_lead') ?>
          </p>

          <div class="divider"></div>

          <!-- Interactive Model Inspector -->
          <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: var(--accent);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <h2 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0;"><?= __('inspector_title') ?></h2>
            </div>
            <p style="font-size: 13.5px; color: var(--text-secondary); margin: 0 0 16px; line-height: 1.6;"><?= __('choose_model') ?>:</p>

            <div class="custom-select-wrap">
              <select id="scooterSelect" class="custom-select" aria-label="Select Scooter Model">
                <option value=""><?= __('select_default') ?></option>

                <optgroup label="M7 MALE (Julet 5-Pin Male)">
                  <?php foreach ($scooterOptions['M7_MALE'] as $model): ?>
                  <option value="<?= sanitize($model['name']) ?>" data-connector="M7 MALE" data-flashable="<?= $model['flashable'] ? '1' : '0' ?>">
                    <?= sanitize($model['name']) ?><?= !$model['flashable'] ? ' (' . __('flashable_no') . ')' : '' ?>
                  </option>
                  <?php endforeach; ?>
                </optgroup>

                <optgroup label="M7 FEMALE (Julet 5-Pin Female)">
                  <?php foreach ($scooterOptions['M7_FEMALE'] as $model): ?>
                  <option value="<?= sanitize($model['name']) ?>" data-connector="M7 FEMALE" data-flashable="<?= $model['flashable'] ? '1' : '0' ?>">
                    <?= sanitize($model['name']) ?><?= $model['name'] === 'Xiaomi Scooter 5' ? ' (' . __('model_5_note') . ')' : '' ?>
                  </option>
                  <?php endforeach; ?>
                </optgroup>

                <optgroup label="M8 MALE (Julet 5-Pin Male, LEQI)">
                  <?php foreach ($scooterOptions['M8_MALE'] as $model): ?>
                  <option value="<?= sanitize($model['name']) ?>" data-connector="M8 MALE" data-flashable="<?= $model['flashable'] ? '1' : '0' ?>" data-testing="<?= !empty($model['testing']) ? '1' : '0' ?>">
                    <?= sanitize($model['name']) ?><?= !empty($model['testing']) ? ' (' . __('flashable_testing') . ')' : (!$model['flashable'] ? ' (' . __('flashable_no') . ')' : '') ?>
                  </option>
                  <?php endforeach; ?>
                </optgroup>
              </select>

              <svg class="select-arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="4 6 8 10 12 6"/>
              </svg>
            </div>

            <div id="connectorBadge" class="connector-badge" aria-live="polite" style="display: none; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 16px;">
              <div style="display: flex; align-items: center; gap: 6px; margin: 0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 14px; height: 14px; flex-shrink: 0;"><path d="M18 6L6 18M6 6l12 12"/><rect x="2" y="2" width="8" height="8" rx="2"/><rect x="14" y="14" width="8" height="8" rx="2"/></svg>
                <span id="connectorText" style="font-weight: 700;"></span>
              </div>
              <div id="flashableBadge" style="display: flex; align-items: center; gap: 6px; margin: 0;">
                <svg id="flashableIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 14px; height: 14px; flex-shrink: 0;"></svg>
                <span id="flashableText" style="font-weight: 600;"></span>
              </div>
            </div>

            <!-- Dynamic Technical Specification Output -->
            <div id="inspectorDetails" style="display: none; margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border); font-size: 13px;">
              <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: rgba(255,255,255,0.02); border-radius: 6px;">
                  <span style="color: var(--text-secondary);"><?= __('compat_type') ?>:</span>
                  <span id="detailsConn" style="color: var(--text-primary); font-weight: 700;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: rgba(255,255,255,0.02); border-radius: 6px;">
                  <span style="color: var(--text-secondary);">Voltage Logic:</span>
                  <span id="detailsVolt" style="color: #10b981; font-weight: 600;"></span>
                </div>
                <div style="padding: 10px 12px; background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.15); border-radius: 6px; color: var(--accent); font-family: monospace; font-size: 12px;">
                  <span id="detailsCrossover"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Navigation Link to Technical Tabs -->
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="#tab-specs" class="btn btn-secondary" onclick="document.querySelector('[data-tab=\'specs\']').click();">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; margin-right: 6px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              <?= __('tab_specs') ?>
            </a>
            <a href="#tab-mistakes" class="btn btn-secondary" onclick="document.querySelector('[data-tab=\'mistakes\']').click();">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; margin-right: 6px; color: #fbbf24;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <?= __('tab_mistakes') ?>
            </a>
            <a href="#tab-compat" class="btn btn-secondary" onclick="document.querySelector('[data-tab=\'kompatibilitaet\']').click();">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; margin-right: 6px;"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
              <?= __('tab_compat') ?>
            </a>
          </div>

        </div>
        <!-- /product-info -->

      </div>
    </div>
  </section>

  <!-- EDUCATIONAL DETAILS SECTION (TABS) -->
  <section class="description-section" aria-label="Technical Guide Sections">
    <div class="container">

      <div class="description-inner">

        <!-- Sticky Navigation Sidebar -->
        <nav class="description-nav" aria-label="Technical Guide Navigation">
          <button class="description-nav-item active" data-tab="overview" type="button"><?= __('tab_overview') ?></button>
          <button class="description-nav-item" data-tab="specs" type="button"><?= __('tab_specs') ?></button>
          <button class="description-nav-item" data-tab="mistakes" type="button"><?= __('tab_mistakes') ?></button>
          <button class="description-nav-item" data-tab="kompatibilitaet" type="button"><?= __('tab_compat') ?></button>
          <button class="description-nav-item" data-tab="faq" type="button"><?= __('tab_faq') ?></button>
          <button class="description-nav-item" data-tab="hinweise" type="button"><?= __('tab_hints') ?></button>
        </nav>

        <!-- Tab Contents -->
        <div class="description-content">

          <!-- TAB 1: OVERVIEW -->
          <div id="tab-overview" class="desc-tab active fade-in">
            <h3><?= __('overview_title') ?></h3>
            <p><?= __('overview_p1') ?></p>
            <ul class="include-list" style="margin: 20px 0;">
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?= __('overview_li1') ?></li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?= __('overview_li2') ?></li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?= __('overview_li3') ?></li>
            </ul>

            <!-- Serial Communication Diagram Box -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 24px; margin: 24px 0;">
              <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: var(--accent);"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                Physical Communication Chain
              </h4>
              <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; text-align: center; font-size: 12px;">
                <div style="flex: 1; min-width: 120px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid var(--border);">
                  <strong style="color: var(--text-primary); display: block; margin-bottom: 4px;">Computer</strong>
                  <span style="color: var(--text-secondary);">Web Serial API / Browser</span>
                </div>
                <span style="color: var(--accent); font-weight: 700;">&rarr; USB &rarr;</span>
                <div style="flex: 1; min-width: 120px; padding: 12px; background: rgba(56, 189, 248, 0.06); border-radius: 8px; border: 1px solid rgba(56, 189, 248, 0.2);">
                  <strong style="color: var(--accent); display: block; margin-bottom: 4px;">FT232RL UART</strong>
                  <span style="color: var(--text-secondary);">3.3V Logic Conversion</span>
                </div>
                <span style="color: var(--accent); font-weight: 700;">&rarr; Julet &rarr;</span>
                <div style="flex: 1; min-width: 120px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid var(--border);">
                  <strong style="color: var(--text-primary); display: block; margin-bottom: 4px;">Scooter MCU</strong>
                  <span style="color: var(--text-secondary);">Brightway / LEQI Controller</span>
                </div>
              </div>
            </div>

            <div class="disclaimer-box" role="note" style="margin-top: 20px;">
              <?= __('overview_note') ?>
            </div>
          </div>

          <!-- TAB 2: KEY SPECIFICATIONS -->
          <div id="tab-specs" class="desc-tab">
            <h3><?= __('specs_title') ?></h3>

            <div style="display: flex; flex-direction: column; gap: 20px; margin-top: 20px;">
              <!-- Spec 1: Chipset -->
              <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px;">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--accent); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
                  <?= __('spec_chipset_title') ?>
                </h4>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin: 0;"><?= __('spec_chipset_desc') ?></p>
              </div>

              <!-- Spec 2: Voltage Logic -->
              <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px;">
                <h4 style="font-size: 16px; font-weight: 700; color: #10b981; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                  <?= __('spec_voltage_title') ?>
                </h4>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin: 0;"><?= __('spec_voltage_desc') ?></p>
              </div>

              <!-- Spec 3: Pinout & Crossover -->
              <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px;">
                <h4 style="font-size: 16px; font-weight: 700; color: #60a5fa; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                  <?= __('spec_pinout_title') ?>
                </h4>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin: 0;"><?= __('spec_pinout_desc') ?></p>
              </div>

              <!-- Spec 4: Connector Sizing & Gender -->
              <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px;">
                <h4 style="font-size: 16px; font-weight: 700; color: #fb923c; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  <?= __('spec_connector_title') ?>
                </h4>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin: 0;"><?= __('spec_connector_desc') ?></p>
              </div>
            </div>
          </div>

          <!-- TAB 3: COMMON MISTAKES -->
          <div id="tab-mistakes" class="desc-tab">
            <h3><?= __('mistakes_title') ?></h3>

            <div style="display: flex; flex-direction: column; gap: 18px; margin-top: 20px;">
              <!-- Mistake 1 -->
              <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius); padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 700; color: #ef4444; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  <?= __('mistake_1_title') ?>
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.65; margin: 0;"><?= __('mistake_1_desc') ?></p>
              </div>

              <!-- Mistake 2 -->
              <div style="background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: var(--radius); padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 700; color: #fbbf24; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  <?= __('mistake_2_title') ?>
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.65; margin: 0;"><?= __('mistake_2_desc') ?></p>
              </div>

              <!-- Mistake 3 -->
              <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius); padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 700; color: #ef4444; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  <?= __('mistake_3_title') ?>
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.65; margin: 0;"><?= __('mistake_3_desc') ?></p>
              </div>

              <!-- Mistake 4 -->
              <div style="background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: var(--radius); padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 700; color: #fbbf24; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  <?= __('mistake_4_title') ?>
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.65; margin: 0;"><?= __('mistake_4_desc') ?></p>
              </div>
            </div>
          </div>

          <!-- TAB 4: COMPATIBILITY LIST & PIN COMPARISON -->
          <div id="tab-kompatibilitaet" class="desc-tab">
            <h3><?= __('compat_title') ?></h3>
            <p><?= __('compat_desc') ?></p>

            <table class="compat-table" aria-label="Compatibility Table">
              <thead>
                <tr>
                  <th scope="col"><?= __('compat_model') ?></th>
                  <th scope="col"><?= __('compat_type') ?></th>
                  <th scope="col"><?= __('compat_flashable') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($scooterOptions['M7_MALE'] as $m): ?>
                <tr>
                  <td class="compat-model"><?= sanitize($m['name']) ?></td>
                  <td class="compat-type-male">M7 MALE</td>
                  <td class="compat-type-<?= $m['flashable'] ? 'yes' : 'no' ?>">
                    <?php if ($m['flashable']): ?>
                      &#x2705; <?= __('flashable_yes') ?>
                    <?php else: ?>
                      &#x274C; <?= __('flashable_no') ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php foreach ($scooterOptions['M7_FEMALE'] as $m): ?>
                <tr>
                  <td class="compat-model">
                    <?= sanitize($m['name']) ?><?php if ($m['name'] === 'Xiaomi Scooter 5'): ?><small style="color: var(--text-muted); display: block;">(<?= __('model_5_note') ?>)</small><?php endif; ?>
                  </td>
                  <td class="compat-type-female">M7 FEMALE</td>
                  <td class="compat-type-<?= $m['flashable'] ? 'yes' : 'no' ?>">
                    <?php if ($m['flashable']): ?>
                      &#x2705; <?= __('flashable_yes') ?>
                    <?php else: ?>
                      &#x274C; <?= __('flashable_no') ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>

                <?php foreach ($scooterOptions['M8_MALE'] as $m): ?>
                <tr>
                  <td class="compat-model">
                    <?= sanitize($m['name']) ?>
                    <?php if (!empty($m['testing'])): ?>
                      <small style="color: #fbbf24; display: block; font-weight: 500;">(<?= __('model_6_testing_note') ?>)</small>
                    <?php elseif (in_array($m['name'], ['Xiaomi Scooter 6 Pro', 'Xiaomi Scooter 6 Max', 'Xiaomi Scooter 6 Ultra'], true)): ?>
                      <small style="color: var(--text-muted); display: block;">(<?= __('model_6_unsupported_note') ?>)</small>
                    <?php endif; ?>
                  </td>
                  <td class="compat-type-m8">M8 MALE</td>
                  <td class="compat-type-<?= !empty($m['testing']) ? 'warning' : ($m['flashable'] ? 'yes' : 'no') ?>">
                    <?php if (!empty($m['testing'])): ?>
                      &#x26A0;&#xFE0F; <?= __('flashable_testing') ?>
                    <?php elseif ($m['flashable']): ?>
                      &#x2705; <?= __('flashable_yes') ?>
                    <?php else: ?>
                      &#x274C; <?= __('flashable_no') ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="disclaimer-box" style="margin-top: 20px; border-color: rgba(251,191,36,0.3); background: rgba(251,191,36,0.06);" role="note">
              <p style="margin: 0 0 8px; font-weight: 700; font-size: 13.5px; color: #fbbf24; display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 16px; height: 16px; color: #fbbf24; flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <?= __('compat_gen6_title') ?>
              </p>
              <p style="margin: 0; font-size: 13px; color: var(--text-secondary); line-height: 1.65;"><?= __('compat_gen6_text') ?></p>
            </div>

            <!-- Pin Difference Visual Comparison Cards -->
            <div class="pin-diff-container" style="margin-top: 28px;">
              <div class="pin-diff-header">
                <h4>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 18px; height: 18px; color: var(--accent);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                  <?= __('compat_difference_title') ?>
                </h4>
                <p><?= __('compat_difference_subtitle') ?></p>
              </div>

              <div class="pin-diff-grid">
                <!-- M7 MALE Card -->
                <div class="pin-diff-card male">
                  <div class="pin-diff-img-wrap lightbox-trigger" data-full="../assets/img/malem7.webp" role="button" tabindex="0" aria-label="M7 Male Pins">
                    <img src="../assets/img/malem7.webp" alt="M7 Male Julet Connector Pins" loading="lazy">
                    <span class="pin-diff-zoom-hint">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                      Zoom
                    </span>
                  </div>
                  <div class="pin-diff-body">
                    <div class="pin-diff-title-row">
                      <h5 class="pin-diff-title"><?= __('compat_difference_male') ?></h5>
                      <span class="pin-badge male"><?= __('compat_difference_male_badge') ?></span>
                    </div>
                    <ul class="pin-diff-list">
                      <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color: var(--accent);"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= __('compat_male_pin1') ?></span>
                      </li>
                      <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color: var(--accent);"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= __('compat_male_pin2') ?></span>
                      </li>
                      <li class="models">
                        <?= __('compat_male_pin3') ?>
                      </li>
                    </ul>
                  </div>
                </div>

                <!-- M7 FEMALE Card -->
                <div class="pin-diff-card female">
                  <div class="pin-diff-img-wrap lightbox-trigger" data-full="../assets/img/femalem7.webp" role="button" tabindex="0" aria-label="M7 Female Socket">
                    <img src="../assets/img/femalem7.webp" alt="M7 Female Julet Connector Socket" loading="lazy">
                    <span class="pin-diff-zoom-hint">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                      Zoom
                    </span>
                  </div>
                  <div class="pin-diff-body">
                    <div class="pin-diff-title-row">
                      <h5 class="pin-diff-title"><?= __('compat_difference_female') ?></h5>
                      <span class="pin-badge female"><?= __('compat_difference_female_badge') ?></span>
                    </div>
                    <ul class="pin-diff-list">
                      <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color: #60a5fa;"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= __('compat_female_pin1') ?></span>
                      </li>
                      <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color: #60a5fa;"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= __('compat_female_pin2') ?></span>
                      </li>
                      <li class="models">
                        <?= __('compat_female_pin3') ?>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Quick Check Tip -->
              <div class="pin-check-tip">
                <div class="pin-check-tip-title">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 16px; height: 16px;"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  <?= __('compat_check_tip_title') ?>
                </div>
                <div><?= __('compat_check_tip_desc') ?></div>
              </div>
            </div>

            <p style="margin-top: 16px; font-size: 13px; color: var(--text-muted);"><?= __('compat_os') ?></p>
          </div>

          <!-- TAB 5: FAQ -->
          <div id="tab-faq" class="desc-tab">
            <h3><?= __('faq_title') ?></h3>
            <div class="faq-list" style="display: flex; flex-direction: column; gap: 16px; margin-top: 20px;">

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q1') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a1') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q2') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a2') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q3') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a3') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q4') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a4') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q5') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a5') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q6') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a6') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q7') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a7') ?>
                </div>
              </details>

              <details style="background: var(--bg-card); padding: 16px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-light); cursor: pointer;">
                <summary style="font-weight: 600; font-size: 16px; color: var(--text-primary); outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                  <?= __('faq_q8') ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><polyline points="6 9 12 15 18 9"/></svg>
                </summary>
                <div style="margin-top: 12px; font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                  <?= __('faq_a8') ?>
                </div>
              </details>

            </div>
          </div>

          <!-- TAB 6: IMPORTANT LEGAL NOTICES (EU & GERMANY) -->
          <div id="tab-hinweise" class="desc-tab">
            <h3><?= __('hints_title') ?></h3>
            <div class="disclaimer-box" role="note" style="border-color: var(--border-light); background: var(--bg-card); padding: 24px; border-radius: var(--radius);">
              <?= __('legal_details') ?>
            </div>
          </div>

        </div>
        <!-- /description-content -->

      </div>
    </div>
  </section>

</main>

<!-- FOOTER -->
<?php
$langPrefix = '';
if ($lang === 'de') {
    $langPrefix = '/de';
} elseif ($lang === 'pl') {
    $langPrefix = '/pl';
}
?>
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-inner">
      <p class="footer-copy">
        &copy; <?= date('Y') ?> <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a><br>
        <span style="font-size: 11px; opacity: 0.7;">Open Source Educational Guide &bull; Non-Commercial</span>
      </p>
      <nav class="footer-links" aria-label="Footer-Navigation">
        <a href="https://github.com/visoire" target="_blank" rel="noopener">GitHub</a>
        <a href="mailto:visoire@proton.me"><?= __('contact') ?></a>
        <a href="<?= $langPrefix ?>/impressum/"><?= __('footer_impressum') ?></a>
        <a href="<?= $langPrefix ?>/privacy-policy/"><?= __('footer_privacy') ?></a>
        <a href="<?= $langPrefix ?>/terms-of-service/"><?= __('footer_agb') ?></a>
      </nav>
    </div>
  </div>
</footer>

<!-- LIGHTBOX MODAL FOR PIN & HARDWARE VISUAL INSPECTION -->
<div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Connector Inspection Zoom">
  <img id="lightboxImg" class="lightbox-img" src="" alt="Connector Zoom">
  <button id="lightboxClose" class="lightbox-close" type="button" aria-label="<?= __('close') ?>"><?= __('close') ?></button>
</div>

<script src="../assets/js/script.js?v=<?= time() ?>" defer></script>
</body>
</html>
