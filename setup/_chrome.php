<?php
declare(strict_types=1);

/**
 * _chrome.php
 * Shared header, mobile navigation and footer for the visoire web tools.
 * Matches the main site markup (.header, .burger, .mob-nav, .footer) so the
 * existing assets/css/styles.css and assets/js/script.js style and drive it.
 *
 * Requires lang.php to be loaded first (provides t(), te(), $lang).
 * An identical copy lives in each tool folder so tools stay drop in.
 */

/**
 * Render the fixed header plus the mobile navigation overlay.
 *
 * @param string $active One of: wiki, hw, tut, patcher, flasher, setup.
 */
function render_header(string $active = ''): void
{
    global $lang;
    $a = static fn(string $k): string => $active === $k ? ' class="active"' : '';
    $am = static fn(string $k): string => $active === $k ? ' active' : '';
    ?>
<header class="header" role="banner">
  <div class="container">
    <a href="../" class="logo" aria-label="visoire">
      <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>

    <nav class="header-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= te(t('nav_wiki')) ?></a>
      <a href="../hardware/"><?= te(t('nav_hw')) ?></a>
      <a href="../"><?= te(t('nav_tut')) ?></a>
      <a href="../patcher/"<?= $a('patcher') ?>><?= te(t('nav_patcher')) ?></a>
      <a href="../flasher/"<?= $a('flasher') ?>><?= te(t('nav_flasher')) ?></a>
      <a href="../changelog/"<?= $a('changelog') ?>><?= te(t('nav_changelog')) ?></a>
    </nav>

    <div class="header-cta">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>
      <a href="../#steps" class="btn btn-dark"><?= te(t('btn_back')) ?></a>
      <button class="burger" id="burger" aria-label="Menu" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
<nav class="mob-nav" id="mobNav" aria-label="Mobile navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= te(t('nav_wiki')) ?></a>
      <a href="../hardware/" class="mob-link"><?= te(t('nav_hw')) ?></a>
      <a href="../" class="mob-link"><?= te(t('nav_tut')) ?></a>
      <a href="../patcher/" class="mob-link<?= $am('patcher') ?>"><?= te(t('nav_patcher')) ?></a>
      <a href="../flasher/" class="mob-link<?= $am('flasher') ?>"><?= te(t('nav_flasher')) ?></a>
      <a href="../changelog/" class="mob-link<?= $am('changelog') ?>"><?= te(t('nav_changelog')) ?></a>
      <div class="mob-bottom">
        <a href="../" class="btn btn-dark" style="width:100%"><?= te(t('btn_back')) ?></a>
        <div class="mob-lang">
          <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
          <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
          <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
        </div>
      </div>
    </nav>
<?php
}

/**
 * Render the shared tool footer with attribution and licence.
 */
function render_footer(): void
{
    ?>
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-inner">
      <p class="footer-copy">
        &copy; <?= date('Y') ?> made by <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a><br>
        <span style="font-size: 11px; opacity: 0.7;">Open source, CC BY-NC-SA 4.0</span>
      </p>
      <nav class="footer-links" aria-label="Footer navigation">
        <a href="https://github.com/visoire" target="_blank" rel="noopener">GitHub</a>
        <a href="mailto:visoire@proton.me"><?= te(t('contact')) ?></a>
      </nav>
    </div>
  </div>
</footer>
<?php
}

/**
 * Render the persistent network stability warning banner.
 */
function render_net_warning(): void
{
    ?>
<div class="net-warn" role="note">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
  <div>
    <strong><?= te(t('net_warn_title')) ?></strong>
    <p><?= te(t('net_warn_body')) ?></p>
  </div>
</div>
<?php
}
