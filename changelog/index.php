<?php
declare(strict_types=1);
session_start();

/* Language routing. Default is English. */
$langs = ['de', 'en', 'pl'];
$lang  = (isset($_GET['lang']) && in_array($_GET['lang'], $langs, true))
       ? $_GET['lang']
       : ((isset($_SESSION['lang']) && in_array($_SESSION['lang'], $langs, true))
          ? $_SESSION['lang']
          : ((isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $langs, true))
             ? $_COOKIE['lang']
             : 'en'));

if (isset($_GET['lang']) && in_array($_GET['lang'], $langs, true)) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + 86400 * 365, '/');
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$tx = [
    'de' => [
        'meta_title' => 'Changelog & Release Notes - visoire.com',
        'meta_desc' => 'Aktuelle Änderungen, Versionshinweise und technische Dokumentation für visoire Xiaomi Flasher, Patcher, 6. Generation und Firmware-Tools.',
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Hardware',
        'nav_steps' => 'Anleitung',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'btn_back' => 'Zurück zur Anleitung',
        'badge_latest' => 'Neueste Version',
        'title' => 'Changelog & <span class="accent">Updates</span>',
        'subtitle' => 'Detaillierte Übersicht aller technischen Neuerungen, Engine-Upgrades (bw-flasher 0.7.1, bw-patcher Master), Protokoll-Anpassungen und Support für Xiaomi 6, 6 Lite & 4 Ultra.',
        'foot_disc' => 'Nur für Bildungszwecke. Modifikationen erlöschen Straßenzulassung und Versicherungsschutz.',
        'credit_scooterteam' => 'Tools und originaler Guide von',
        'credit_bastelpichi' => 'Zusätzliche Ressourcen und Wiki von',
    ],
    'en' => [
        'meta_title' => 'Changelog & Release Notes - visoire.com',
        'meta_desc' => 'Recent changes, release notes and technical documentation for visoire Xiaomi Flasher, Patcher, 6th Gen scooters and firmware tools.',
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Hardware',
        'nav_steps' => 'Tutorial',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'btn_back' => 'Back to Tutorial',
        'badge_latest' => 'Latest Release',
        'title' => 'Changelog & <span class="accent">Updates</span>',
        'subtitle' => 'Detailed overview of technical modifications, core engine upgrades (bw-flasher 0.7.1, bw-patcher Master), protocol fixes, and Xiaomi 6, 6 Lite & 4 Ultra support.',
        'foot_disc' => 'For educational purposes only. Modifications void road approval and insurance.',
        'credit_scooterteam' => 'Tools and original guide by',
        'credit_bastelpichi' => 'Additional resources and wiki by',
    ],
    'pl' => [
        'meta_title' => 'Dziennik zmian i informacje o wydaniach - visoire.com',
        'meta_desc' => 'Ostatnie zmiany, informacje techniczne i dokumentacja dla visoire Xiaomi Flasher, Patcher, 6. generacji i narzędzi firmware.',
        'nav_wiki' => 'Wiki',
        'nav_hw' => 'Sprzęt',
        'nav_steps' => 'Instrukcja',
        'nav_patcher' => 'Patcher',
        'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Dziennik zmian',
        'btn_back' => 'Powrót do instrukcji',
        'badge_latest' => 'Najnowsza wersja',
        'title' => 'Dziennik zmian i <span class="accent">aktualizacje</span>',
        'subtitle' => 'Szczegółowy przegląd modyfikacji technicznych, uaktualnień silnika (bw-flasher 0.7.1, bw-patcher Master), protokołów i obsługi Xiaomi 6, 6 Lite oraz 4 Ultra.',
        'foot_disc' => 'Wyłącznie do celów edukacyjnych. Modyfikacje unieważniają homologację i ubezpieczenie.',
        'credit_scooterteam' => 'Narzędzia i oryginalna instrukcja od',
        'credit_bastelpichi' => 'Dodatkowe zasoby i wiki od',
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
<title><?= e($tx[$lang]['meta_title']) ?></title>
<meta name="description" content="<?= e($tx[$lang]['meta_desc']) ?>">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="192x192" href="../assets/img/visoire/logo.png">
<link rel="apple-touch-icon" href="../assets/images/xiaomi.jpg">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<style>
/* Custom Scrollbar */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #0f0f0f; }
::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 999px; }
::-webkit-scrollbar-thumb:hover { background: #38bdf8; }
* { scrollbar-width: thin; scrollbar-color: #2a2a2a #0f0f0f; box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --primary: #38bdf8;
  --primary-hover: #0ea5e9;
  --primary-dim: rgba(56, 189, 248,0.07);
  --primary-line: rgba(56, 189, 248,0.2);
  --bg: #080808;
  --surface: #0f0f0f;
  --surface-2: #141414;
  --surface-3: #1a1a1a;
  --ink: #ffffff;
  --text: #a0a0a0;
  --muted: #666666;
  --faint: #333333;
  --border: #1a1a1a;
  --border-2: #2a2a2a;
  --red: #f87171;
  --red-bg: rgba(248,113,113,0.08);
  --red-line: rgba(248,113,113,0.2);
  --amber: #fbbf24;
  --amber-bg: rgba(251,191,36,0.08);
  --amber-line: rgba(251,191,36,0.25);
  --blue: #60a5fa;
  --blue-bg: rgba(96,165,250,0.08);
  --blue-line: rgba(96,165,250,0.25);
  --r-sm: 10px;
  --r-md: 16px;
  --r-lg: 22px;
  --r-pill: 50px;
  --fh: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --fb: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --fm: 'JetBrains Mono', ui-monospace, monospace;
}

html { scroll-behavior: smooth; }
body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--fb);
  font-size: 16px;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
  overflow-x: hidden;
}

h1, h2, h3, h4 { font-family: var(--fh); color: var(--ink); font-weight: 800; line-height: 1.2; letter-spacing: -0.02em; }
a { color: var(--primary); text-decoration: none; transition: color 0.2s; }
a:hover { color: var(--primary-hover); }

.wrap { width: 100%; max-width: 1080px; margin: 0 auto; padding: 0 20px; }

/* Header & Navbar */
.header,
.hdr {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 13px 0;
  background: rgba(8, 8, 8, 0.8);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid transparent;
  transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
}

.header.scrolled,
.hdr.scrolled {
  background: rgba(8, 8, 8, 0.95);
  border-bottom-color: var(--border);
  box-shadow: 0 6px 22px -16px rgba(0, 0, 0, 0.6);
}

.header .container,
.hdr-in {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 100%;
  max-width: 1140px;
  margin: 0 auto;
  padding: 0 24px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: var(--fh);
  font-weight: 800;
  font-size: 18px;
  letter-spacing: -0.02em;
  color: var(--ink);
  flex-shrink: 0;
  text-decoration: none;
}
.logo:hover {
  color: var(--ink);
}

.header-nav,
.hdr-nav {
  display: none;
  align-items: center;
  gap: 4px;
  margin-left: 8px;
}

.header-nav a,
.hdr-nav a {
  color: var(--text);
  font-weight: 500;
  font-size: 14.5px;
  padding: 8px 12px;
  border-radius: 9px;
  transition: color 0.25s ease, background 0.25s ease;
  text-decoration: none;
}

.header-nav a:hover,
.hdr-nav a:hover,
.header-nav a.active,
.hdr-nav a.active {
  color: var(--primary);
  background: var(--surface-2);
}

.header-cta,
.hdr-right {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}

.lang-sw {
  display: none;
  align-items: center;
  gap: 2px;
  background: var(--surface-2);
  border: 1px solid var(--border-2);
  border-radius: 9999px;
  padding: 3px;
}

.lang-btn {
  font-family: var(--fh);
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
  padding: 6px 11px;
  border-radius: 9999px;
  transition: color 0.2s, background 0.2s;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.lang-btn:hover {
  color: var(--ink);
}

.lang-btn.active {
  color: #000;
  background: var(--primary);
  font-weight: 700;
}

.header-cta .btn,
.hdr-right .btn {
  min-height: 42px;
  padding: 0 16px;
  font-size: 13.5px;
  border-radius: 9999px;
  white-space: nowrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 42px;
  padding: 0 18px;
  border-radius: 9999px;
  font-size: 13.5px;
  font-weight: 600;
  white-space: nowrap;
  transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, color 0.2s ease;
  cursor: pointer;
  text-decoration: none;
}
.btn:active { transform: translateY(0); }
.btn-primary { background: var(--primary); color: #000; box-shadow: 0 6px 18px -8px rgba(56, 189, 248, 0.4); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); }
.btn-dark { background: var(--surface-2); border: 1px solid var(--border-2); color: var(--ink); }
.btn-dark:hover { background: var(--surface-3); border-color: var(--primary); transform: translateY(-2px); }

.burger {
  display: inline-flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 5px;
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border: 1px solid var(--border-2);
  border-radius: 11px;
  background: var(--surface-2);
  cursor: pointer;
  transition: all 0.25s ease;
}

.burger span {
  width: 19px;
  height: 2px;
  background: var(--ink);
  border-radius: 2px;
  transition: transform 0.35s, opacity 0.25s;
}

.burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.burger.open span:nth-child(2) { opacity: 0; }
.burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Mobile Nav */
.nav-overlay {
  position: fixed;
  inset: 0;
  z-index: 998;
  background: rgba(8, 8, 8, 0.6);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}
.nav-overlay.open {
  opacity: 1;
  pointer-events: auto;
}

.mob-nav {
  position: fixed;
  top: 0;
  right: -280px;
  bottom: 0;
  width: 280px;
  z-index: 999;
  background: var(--surface-2);
  border-left: 1px solid var(--border-2);
  padding: 80px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.mob-nav.open {
  right: 0;
}
.mob-link {
  font-size: 16px;
  font-weight: 600;
  color: var(--text);
  padding: 10px 12px;
  border-radius: var(--r-sm);
  transition: all 0.2s;
  text-decoration: none;
}
.mob-link:hover {
  color: var(--ink);
  background: var(--surface-3);
}
.mob-link.active {
  color: var(--primary);
  background: var(--surface-3);
}

.mob-bottom {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.mob-lang {
  display: flex;
  justify-content: center;
  gap: 8px;
  padding-top: 16px;
  border-top: 1px solid var(--border-2);
}
.mob-lang .lang-btn {
  flex: 1;
  text-align: center;
  justify-content: center;
  display: flex;
  align-items: center;
  border: 1px solid var(--border-2);
  background: var(--surface);
  padding: 11px 0;
  border-radius: var(--r-sm);
  color: var(--text);
  font-size: 12px;
  font-weight: 600;
}
.mob-lang .lang-btn.active {
  background: var(--primary);
  border-color: var(--primary);
  color: #000;
  font-weight: 700;
}

@media (min-width: 980px) {
  .header-nav,
  .hdr-nav {
    display: flex;
  }
  .lang-sw {
    display: flex;
  }
  .burger {
    display: none;
  }
}
@media (max-width: 979px) {
  .header-cta .btn,
  .hdr-right .btn {
    display: none;
  }
}

/* Hero */
.hero { padding: 56px 0 36px; text-align: center; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 6px 16px; border-radius: var(--r-pill);
  background: var(--primary-dim); border: 1px solid var(--primary-line);
  color: var(--primary); font-size: 12px; font-weight: 700; font-family: var(--fm);
  letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 20px;
}
.hero h1 { font-size: clamp(34px, 5.5vw, 54px); margin-bottom: 14px; }
.hero h1 .accent { color: var(--primary); }
.hero p { color: var(--text); font-size: 17px; max-width: 720px; margin: 0 auto; }

/* Timeline */
.timeline { display: flex; flex-direction: column; gap: 36px; margin: 30px 0 60px; }

.release-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--r-lg); padding: 36px; position: relative;
  transition: border-color 0.2s, transform 0.2s;
}
.release-card:hover { border-color: var(--border-2); }
.release-card.featured {
  border-color: var(--border-2);
}

.release-header {
  display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between;
  gap: 16px; border-bottom: 1px solid var(--border); padding-bottom: 22px; margin-bottom: 28px;
}
.release-title-group { display: flex; flex-direction: column; gap: 6px; }
.release-title-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.release-version {
  font-family: var(--fm); font-size: 24px; font-weight: 800; color: var(--ink);
  display: inline-flex; align-items: center; gap: 8px;
}

.release-date { font-family: var(--fm); font-size: 13px; color: var(--muted); }

.release-links { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.btn-gh {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--surface-2); border: 1px solid var(--border-2);
  color: var(--ink); padding: 8px 15px; border-radius: var(--r-sm);
  font-size: 13px; font-weight: 600; font-family: var(--fm);
}
.btn-gh:hover { border-color: var(--primary); color: var(--primary); }

/* Feature Blocks inside release */
.change-section { margin-bottom: 30px; }
.change-section:last-child { margin-bottom: 0; }
.change-section h3 {
  font-size: 17px; font-weight: 700; color: var(--ink);
  display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
}
.change-section h3 .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); flex-shrink: 0; }

.change-list { list-style: none; display: flex; flex-direction: column; gap: 11px; }
.change-list li {
  position: relative; padding-left: 24px; font-size: 14.5px; color: var(--text); line-height: 1.6;
}
.change-list li::before {
  content: "›"; position: absolute; left: 6px; top: -1px;
  color: var(--primary); font-size: 18px; font-weight: 800;
}
.change-list li strong { color: var(--ink); }
.change-list li code {
  font-family: var(--fm); font-size: 12.5px; background: var(--surface-2);
  border: 1px solid var(--border); padding: 2px 6px; border-radius: 4px; color: var(--primary);
}

.callout-box {
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: var(--r-md); padding: 18px 20px; margin-top: 14px;
}
.callout-box.highlight {
  background: var(--surface-2); border: 1px solid var(--border); border-left: 3px solid var(--primary);
}
.callout-box.warn {
  background: var(--surface-2); border: 1px solid var(--border); border-left: 3px solid var(--amber);
}
.callout-box.info {
  background: var(--surface-2); border: 1px solid var(--border); border-left: 3px solid var(--blue);
}
.callout-title {
  font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 6px;
  display: flex; align-items: center; gap: 8px;
}
.callout-desc { font-size: 13.5px; color: var(--text); line-height: 1.6; }

/* Grid of mini cards for highlights */
.highlight-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 14px; margin-top: 16px;
}
.highlight-item {
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: var(--r-sm); padding: 16px;
}
.highlight-item h4 { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
.highlight-item p { font-size: 13px; color: var(--text); line-height: 1.55; margin: 0; }

/* Technical specs table */
.spec-table {
  width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 13.5px;
}
.spec-table th, .spec-table td {
  padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border);
}
.spec-table th {
  background: var(--surface-2); color: var(--ink); font-weight: 700; font-family: var(--fm); font-size: 12px; text-transform: uppercase;
}
.spec-table tr:hover td { background: rgba(255,255,255,0.01); }
.spec-table code {
  font-family: var(--fm); font-size: 12px; background: var(--surface-2); padding: 2px 5px; border-radius: 3px; color: var(--primary);
}

/* Footer */
footer {
  border-top: 1px solid var(--border); background: var(--surface); padding: 50px 0 30px;
}
.foot-grid {
  display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px;
}
.foot-brand p { font-size: 14px; color: var(--muted); margin-top: 12px; line-height: 1.6; }
.foot-col h4 { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 16px; }
.foot-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.foot-col ul a { font-size: 13.5px; color: var(--text); }
.foot-col ul a:hover { color: var(--primary); }
.foot-bottom {
  border-top: 1px solid var(--border); padding-top: 24px;
  display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
  font-size: 13px; color: var(--muted);
}

@media (max-width: 860px) {
  .foot-grid { grid-template-columns: 1fr 1fr; }
  .release-card { padding: 24px; }
}
@media (max-width: 560px) {
  .foot-grid { grid-template-columns: 1fr; }
  .hero { padding: 40px 0 24px; }
  .release-header { flex-direction: column; }
}
</style>
</head>
<body>

<!-- Header -->
<header class="header hdr" id="hdr" role="banner">
  <div class="container hdr-in">
    <a href="../?lang=<?= e($lang) ?>" class="logo" aria-label="visoire">
      <img src="../assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>

    <nav class="header-nav hdr-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= e($tx[$lang]['nav_wiki']) ?></a>
      <a href="../hardware/?lang=<?= e($lang) ?>"><?= e($tx[$lang]['nav_hw']) ?></a>
      <a href="/xiaomi/?lang=<?= e($lang) ?>#steps"><?= e($tx[$lang]['nav_steps']) ?></a>
      <a href="/xiaomi/patcher/?lang=<?= e($lang) ?>"><?= e($tx[$lang]['nav_patcher']) ?></a>
      <a href="/xiaomi/flasher/?lang=<?= e($lang) ?>"><?= e($tx[$lang]['nav_flasher']) ?></a>
      <a href="/xiaomi/changelog/?lang=<?= e($lang) ?>" class="active"><?= e($tx[$lang]['nav_changelog']) ?></a>
    </nav>

    <div class="header-cta hdr-right">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>

      <a href="/xiaomi/?lang=<?= e($lang) ?>#steps" class="btn btn-dark"><?= e($tx[$lang]['btn_back']) ?></a>

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
  <a href="/xiaomi/?lang=<?= e($lang) ?>#steps" class="mob-link"><?= e($tx[$lang]['nav_steps']) ?></a>
  <a href="/xiaomi/patcher/?lang=<?= e($lang) ?>" class="mob-link"><?= e($tx[$lang]['nav_patcher']) ?></a>
  <a href="/xiaomi/flasher/?lang=<?= e($lang) ?>" class="mob-link"><?= e($tx[$lang]['nav_flasher']) ?></a>
  <a href="/xiaomi/changelog/?lang=<?= e($lang) ?>" class="mob-link active"><?= e($tx[$lang]['nav_changelog']) ?></a>
  <div class="mob-bottom">
    <a href="/xiaomi/?lang=<?= e($lang) ?>#steps" class="btn btn-dark" style="width:100%"><?= e($tx[$lang]['btn_back']) ?></a>
    <div class="mob-lang">
      <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
      <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
      <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
    </div>
  </div>
</nav>

<!-- Main content -->
<main class="wrap">

  <section class="hero">
    <div class="eyebrow">
      <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span>
      <?= e($tx[$lang]['badge_latest']) ?>: bw-flasher 0.7.1 - <?= $L('Plaintext LEQI & Stabilitäts-Upgrade', 'Plaintext LEQI & Stability Upgrade', 'Obsługa Plaintext LEQI i uaktualnienie stabilności') ?>
    </div>
    <h1><?= $tx[$lang]['title'] ?></h1>
    <p><?= e($tx[$lang]['subtitle']) ?></p>
    <div style="margin-top: 22px; display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
      <a href="https://github.com/visoire" target="_blank" rel="noopener" class="btn-gh" style="background: rgba(56, 189, 248, 0.08); border-color: rgba(56, 189, 248, 0.28); color: var(--primary); padding: 9px 18px; font-size: 13.5px;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
        GitHub @visoire ↗
      </a>
    </div>
  </section>

  <div class="timeline">

    <!-- RELEASE 2026-10-02: bw-patcher 1.1.6, NGFW, dashboard serial, setup mode -->
    <article class="release-card featured">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version"><?= $L('bw-patcher 1.1.6, NGFW-Patcher, Dashboard-Seriennummer & Setup-Modus', 'bw-patcher 1.1.6, NGFW Patcher, Dashboard Serial & Setup Mode', 'bw-patcher 1.1.6, NGFW Patcher, numer seryjny wy&#347;wietlacza i tryb Setup') ?></span>
          </div>
          <span class="release-date">visoire · 2026-10-02</span>
        </div>
        <div class="release-links">
          <a href="/xiaomi/ngfw/" class="btn-gh"><?= $L('NGFW &#246;ffnen', 'Open NGFW', 'Otw&#243;rz NGFW') ?></a>
          <a href="/xiaomi/flasher/" class="btn-gh"><?= $L('Flasher &#246;ffnen', 'Open Flasher', 'Otw&#243;rz flasher') ?></a>
        </div>
      </header>
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('&Auml;nderungen', 'Changes', 'Zmiany') ?></h3>
        <ul class="change-list">
          <li><strong>bw-patcher 1.1.6:</strong> <?= $L('4 Ultra patcht jetzt auch den Strom (nicht mehr experimentell), Mi 6 / 6 Lite mit verbessertem Motor-Start-Speed.', '4 Ultra now also patches the current limits (no longer experimental); Mi 6 / 6 Lite get improved motor start speed patches.', '4 Ultra patchuje teraz tak&#380;e pr&#261;d (nie jest ju&#380; eksperymentalny); Mi 6 / 6 Lite maj&#261; ulepszony patch prędko&#347;ci startu silnika.') ?></li>
          <li><strong>bw-flasher 0.7.1:</strong> <?= $L('Bereits aktuell, Paket unver&auml;ndert &uuml;bernommen und gepr&uuml;ft.', 'Already current; package verified unchanged.', 'Ju&#380; aktualny; pakiet zweryfikowany bez zmian.') ?></li>
          <li><strong><?= $L('Setup-/Start-Modus', 'Setup / Start mode', 'Tryb Setup / Start') ?>:</strong> <?= $L('Neuer Schalter im Flasher: Setup testet zuerst die Verbindung und fragt nach Best&auml;tigung, Start flasht sofort.', 'New switch in the flasher: Setup probes the connection first and asks for confirmation, Start flashes immediately.', 'Nowy prze&#322;&#261;cznik we flasherze: Setup najpierw testuje po&#322;&#261;czenie i prosi o potwierdzenie, Start wgrywa od razu.') ?></li>
          <li><strong><?= $L('Dashboard-Seriennummer (LEQI)', 'Dashboard serial (LEQI)', 'Numer seryjny wy&#347;wietlacza (LEQI)') ?>:</strong> <?= $L('Pr&auml;fix + Seriennummer (19 Zeichen), Befehl 0x97, mit Debug und Simulation.', 'Prefix + serial (19 characters), command 0x97, with debug and simulation support.', 'Prefiks + numer (19 znak&#243;w), polecenie 0x97, z debugiem i symulacj&#261;.') ?></li>
          <li><strong>NGFW:</strong> <?= $L('Neue Seite mit NGFW-Patcher (Xiaomi M365-Familie, Ninebot, 4 Pro Italien), Debug, Simulation, Setup-Modus und ScooterFlasher-Befehl inkl. Seriennummer.', 'New page with the NGFW patcher (Xiaomi M365 family, Ninebot, 4 Pro Italian), debug, simulation, setup mode and a ScooterFlasher command builder including the serial.', 'Nowa strona z patcherem NGFW (rodzina Xiaomi M365, Ninebot, 4 Pro Italian), debug, symulacja, tryb Setup i generator polecenia ScooterFlasher z numerem seryjnym.') ?></li>
          <li><strong><?= $L('CRC-Fehler', 'CRC failures', 'B&#322;&#281;dy CRC') ?>:</strong> <?= $L('Rohe LEQI-Bodies (Mi 6 / 6 Lite) werden nicht mehr f&auml;lschlich als Voll-Image geschnitten; Brightway sendet bei NAK den Block erneut statt abzubrechen.', 'Raw LEQI bodies (Mi 6 / 6 Lite) are no longer mis-cut as full images; Brightway resends a chunk on NAK instead of aborting.', 'Surowe body LEQI (Mi 6 / 6 Lite) nie s&#261; ju&#380; b&#322;&#281;dnie ci&#281;te jako pe&#322;ny obraz; Brightway ponawia blok po NAK zamiast przerywa&#263;.') ?></li>
          <li><strong>Error 43:</strong> <?= $L('Neue Modelle Xiaomi Scooter 42 und 43 mit eigener Original-Firmware (navee1.scooter.t2209 / t2209g).', 'New models Xiaomi Scooter 42 and 43 with their own stock firmware (navee1.scooter.t2209 / t2209g).', 'Nowe modele Xiaomi Scooter 42 i 43 z w&#322;asnym firmware (navee1.scooter.t2209 / t2209g).') ?></li>
        </ul>
      </div>
    </article>

    <!-- RELEASE BW-FLASHER 0.7.1 -->
    <article class="release-card">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version"><?= $L('bw-flasher 0.7.1 - Plaintext LEQI & Stabilitäts-Upgrade', 'bw-flasher 0.7.1 - Plaintext LEQI & Stability Upgrade', 'bw-flasher 0.7.1 - Obsługa Plaintext LEQI i uaktualnienie stabilności') ?></span>
          </div>
          <span class="release-date">ScooterTeam & visoire Port · 2026-09-20</span>
        </div>

        <div class="release-links">
          <a href="https://github.com/scooterteam/bw-flasher/releases/tag/v0.7.1" target="_blank" rel="noopener" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            bw-flasher v0.7.1
          </a>
          <a href="/xiaomi/flasher/" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <?= $L('Flasher öffnen', 'Open Flasher', 'Otwórz flasher') ?>
          </a>
        </div>
      </header>

      <!-- Section 1: Plaintext LEQI & Header Tag Validation -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('1. Plaintext LEQI-Unterstützung & EU1 Header-Erkennung', '1. Plaintext LEQI Support & EU1 Header Tag Validation', '1. Obsługa Plaintext LEQI i weryfikacja nagłówka EU1') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Version <strong>0.7.1</strong> erweitert die LEQI-Protokoll-Engine für neuere Scooter-Generationen (wie Xiaomi 5 Plus), deren MCU-Images nicht mehr XOR-0xAA-verschlüsselt sind und keine Legacy-CRC am Dateiende aufweisen. Zudem liest die Firmware-Erkennung nun direkt den <code>EU1\\0</code>-Tag und das 16-Bit Größenfeld aus dem Image-Header bei Offset <code>0x0A</code>.',
            'Version <strong>0.7.1</strong> extends the LEQI flasher engine to support newer scooter generations (such as Xiaomi 5 Plus) featuring plaintext MCU binaries without XOR-0xAA encryption or legacy trailing CRC checks. Firmware detection now also verifies the authoritative <code>EU1\\0</code> header tag and 16-bit body size directly from offset <code>0x0A</code>.',
            'Wersja <strong>0.7.1</strong> rozszerza silnik LEQI o obsługę nowszych modeli (np. Xiaomi 5 Plus) z nieszyfrowanym obrazem MCU (plaintext), pomijając wymóg sumy kontrolnej CRC XOR. Wykrywanie firmware sprawdza teraz bezpośrednio nagłówek <code>EU1\\0</code> i pole rozmiaru od pozycji <code>0x0A</code>.'
          ) ?>
        </p>

        <ul class="change-list">
          <li>
            <strong><?= $L('is_plaintext_leqi_body() & embedded_crc_ok()', 'is_plaintext_leqi_body() & embedded_crc_ok()', 'is_plaintext_leqi_body() & embedded_crc_ok()') ?>:</strong>
            <?= $L('Erkennt Plaintext-Images anhand der 0xAA-Dichte im Probenbereich 0x80–0x400. Bei Plaintext-Dateien wird die strikte CRC-Validierung sicher übersprungen, sodass 5 Plus-Firmware problemlos geflasht werden kann.', 'Detects plaintext binaries via 0xAA sample density between 0x80 and 0x400. Bypasses strict legacy CRC checks on plaintext binaries while maintaining strict validation for encrypted Elite-style firmware.', 'Wykrywa nieszyfrowany firmware na podstawie gęstości bajtów 0xAA w oknie 0x80–0x400 i bezpiecznie pomija walidację CRC dla modeli 5 Plus, zachowując ją dla starszych modeli.') ?>
          </li>
          <li>
            <strong><?= $L('EU1-Header-Validierung', 'EU1 Header Validation', 'Weryfikacja nagłówka EU1') ?>:</strong>
            <?= $L('Prüft den <code>EU1\\0</code>-Tag bei Offset <code>0x0A</code> und die Little-Endian-Länge bei <code>0x0E</code> für schnellere und zuverlässigere Typ-Identifikation.', 'Checks the <code>EU1\\0</code> tag at offset <code>0x0A</code> and little-endian body length at <code>0x0E</code> for faster, bulletproof type identification.', 'Sprawdza sygnaturę <code>EU1\\0</code> na pozycji <code>0x0A</code> i długość little-endian na <code>0x0E</code> dla szybkiej i pewnej identyfikacji typu.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 2: Flasher Connection Test & WebSerial Fixes -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('2. Flasher Verbindungstest ohne Firmware & WebSerial Fixes', '2. Flasher Standalone Connection Probe & WebSerial Fixes', '2. Test połączenia bez firmware i poprawki WebSerial') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Der Button „Verbindung testen“ im Web-Flasher kann nun sofort nach dem Anschließen des USB/FTDI-Adapters geklickt werden – noch bevor eine Firmware geladen wurde. Das System sendet einen Sondierungstest an den Controller (LEQI DeviceInfo & Brightway UID/get_ver) und zeigt sofort Modell, Firmware-Version und Region an.',
            'The "Test connection" button in the Web Flasher can now be pressed immediately after connecting the serial adapter in Step 1, without requiring a firmware file to be selected first. It probes the controller (LEQI DeviceInfo and Brightway UID/get_ver) and reports detected protocol, version, and region.',
            'Przycisk „Testuj połączenie” w Web Flasherze jest teraz aktywny zaraz po podłączeniu adaptera szeregowego (krok 1), bez konieczności uprzedniego wyboru pliku firmware. Bada kontroler komendami LEQI DeviceInfo oraz Brightway get_ver i wyświetla wykryty protokół, wersję oraz region.'
          ) ?>
        </p>

        <ul class="change-list">
          <li>
            <strong><?= $L('WebSerial Reader Lock Release', 'WebSerial Reader Lock Release', 'Zwalnianie blokady czytnika WebSerial') ?>:</strong>
            <?= $L('Löst die interne WebSerial-Stream-Sperre beim Trennen sauber auf, um Blockaden bei schnellem Neuverbinden zu verhindern.', 'Explicitly releases the stream reader lock before closing ports in WebSerialAdapter, preventing port lockup on reconnect.', 'Jawnie zwalnia blokadę czytnika przed zamknięciem portu, zapobiegając blokowaniu portu COM przy ponownym połączeniu.') ?>
          </li>
          <li>
            <strong><?= $L('Controller-Autoprobe', 'Controller Auto-Probe', 'Automatyczne badanie kontrolera') ?>:</strong>
            <?= $L('Diagnostiziert den Controller ohne Flash-Risiko und ermittelt automatisch, ob ein LEQI- oder Brightway-Board angeschlossen ist.', 'Safely inspects the connected controller with zero flash risk, reporting whether an LEQI or Brightway board is present.', 'Bezpiecznie sonduje podłączony kontroler bez ryzyka nadpisania pamięci, sprawdzając obecność płyty LEQI lub Brightway.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 3: Patcher & Setup Backend Stability -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('3. Patcher & Setup Backend-Stabilität auf Windows & Linux', '3. Patcher & Setup Backend Stability on Windows & Linux', '3. Stabilność backendu Patcher & Setup na Windows i Linux') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Beseitigung von Windows-spezifischen Pfad- und Shell-Problemen beim Patchen und Bereitstellen von Firmware. Python-Befehle werden nun direkt über <code>proc_open</code> mit Argument-Arrays ausgeführt, stderr-Fehlerausgaben werden detailliert erfasst und temporäre Verzeichnisse prüfen die Schreibrechte mit Fallback auf lokale Ordner.',
            'Resolved Windows-specific shell quoting, rate-limiting, and path handling issues in firmware patching and setup. Python commands are now passed as structured arrays to <code>proc_open</code> without shell escaping bugs, actual stderr diagnostics are preserved, and temp directories fall back locally if system permissions are restricted.',
            'Wyeliminowano problemy z cudzysłowami w powłoce Windows, limitami zapytań i uprawnieniami do folderów tymczasowych. Polecenia Pythona są przekazywane do <code>proc_open</code> jako tablica argumentów, błędy stderr są precyzyjnie raportowane, a katalogi temp mają lokalny fallback.'
          ) ?>
        </p>

        <ul class="change-list">
          <li>
            <strong><?= $L('Python-Auto-Discovery', 'Python Auto-Discovery', 'Automatyczne wykrywanie Pythona') ?>:</strong>
            <?= $L('Sucht dynamisch nach Python 3 in PATH, Windows AppData, Program Files und venvs.', 'Dynamically detects Python 3 binaries across system PATH, Windows AppData, Program Files, and virtual environments.', 'Dynamicznie wykrywa Pythona 3 w PATH, katalogach AppData użytkownika, Program Files i wirtualnych środowiskach.') ?>
          </li>
          <li>
            <strong><?= $L('Rate-Limiting Dev-Exemption', 'Rate-Limiting Dev Exemption', 'Wyłączenie limitu zapytań dla localhost') ?>:</strong>
            <?= $L('Lokale Anfragen (127.0.0.1, ::1) sind von der IP-Drosselung ausgenommen, wodurch Fehlermeldungen bei schnellen Patch-Iterationen entfallen.', 'Local development requests are exempt from the rate limiter, preventing 429 throttling errors during testing and rapid patch adjustments.', 'Lokalne zapytania deweloperskie są wyłączone z limitów IP, co eliminuje błędy 429 przy częstych zmianach patchy.') ?>
          </li>
        </ul>
      </div>
    </article>

    <!-- RELEASE BW-FLASHER 0.7.0 -->
    <article class="release-card">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version"><?= $L('bw-flasher 0.7.0 - Refactored Core Engine & UART-Verbesserungen', 'bw-flasher 0.7.0 - Refactored Core Engine & UART Improvements', 'bw-flasher 0.7.0 - Przebudowany silnik i usprawnienia UART') ?></span>
          </div>
          <span class="release-date">ScooterTeam & visoire Port · 2026-09-12</span>
        </div>

        <div class="release-links">
          <a href="https://github.com/scooterteam/bw-flasher/releases/tag/v0.7.0" target="_blank" rel="noopener" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            bw-flasher v0.7.0
          </a>
          <a href="/xiaomi/flasher/" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <?= $L('Flasher öffnen', 'Open Flasher', 'Otwórz flasher') ?>
          </a>
        </div>
      </header>

      <!-- Section 1: Modular Architecture -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('1. Modulare Architektur & Trennung der Protokoll-Klassen', '1. Modular Architecture & Protocol Class Separation', '1. Modularna architektura i separacja klas protokołów') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'In Version <strong>0.7.0</strong> wurde die monolithische Flasher-Datei <code>flash_uart.py</code> vollständig aufgeteilt. Die Brightway-DFU-Logik befindet sich nun in <code>brightway_flasher.py</code>, das LEQI-Protokoll in <code>leqi_flasher.py</code> und die gemeinsame Basis in <code>base_flasher.py</code>. <code>flash_uart.py</code> bleibt als Rückwärtskompatibilitäts-Shim erhalten.',
            'Version <strong>0.7.0</strong> fully splits the monolithic <code>flash_uart.py</code> into dedicated modules: <code>brightway_flasher.py</code> handles Brightway DFU, <code>leqi_flasher.py</code> handles the LEQI binary protocol, and <code>base_flasher.py</code> provides the shared ABC. <code>flash_uart.py</code> is retained as a backward-compatibility shim.',
            'Wersja <strong>0.7.0</strong> dzieli monolityczny plik <code>flash_uart.py</code> na dedykowane moduły: <code>brightway_flasher.py</code> (DFU Brightway), <code>leqi_flasher.py</code> (protokół binarny LEQI) oraz <code>base_flasher.py</code> (wspólna klasa ABC). <code>flash_uart.py</code> zachowany jako shim zgodności.'
          ) ?>
        </p>

        <ul class="change-list">
          <li>
            <strong><?= $L('BrightwayFlasher – detect_firmware_type()', 'BrightwayFlasher - detect_firmware_type()', 'BrightwayFlasher - detect_firmware_type()') ?>:</strong>
            <?= $L('Neue statische Methode prüft die <code>DEPRD5C\0</code>-Signatur bei Offset <code>0x800</code> und das <code>637C</code>-Muster für eine sichere Firmware-Erkennung.', 'New static method checks for the <code>DEPRD5C\0</code> signature at offset <code>0x800</code> and the <code>637C</code> pattern, enabling reliable auto-detection of Brightway binaries.', 'Nowa statyczna metoda sprawdza sygnaturę <code>DEPRD5C\0</code> na pozycji <code>0x800</code> i wzorzec <code>637C</code> do niezawodnego rozpoznawania firmware Brightway.') ?>
          </li>
          <li>
            <strong><?= $L('base_flasher – Fabrik-Funktionen', 'base_flasher - Factory Functions', 'base_flasher - Funkcje fabryczne') ?>:</strong>
            <?= $L('Neue Hilfsfunktionen <code>detect_firmware_type()</code>, <code>detect_firmware_file()</code>, <code>create_flasher_for_firmware()</code> und <code>get_firmware_info()</code> ermöglichen eine automatische Flasher-Auswahl ohne manuelle Typ-Angabe.', 'New helpers <code>detect_firmware_type()</code>, <code>detect_firmware_file()</code>, <code>create_flasher_for_firmware()</code> and <code>get_firmware_info()</code> in <code>base_flasher.py</code> enable automatic flasher selection by firmware type.', 'Nowe helpery <code>detect_firmware_type()</code>, <code>detect_firmware_file()</code>, <code>create_flasher_for_firmware()</code> i <code>get_firmware_info()</code> w <code>base_flasher.py</code> umożliwiają automatyczny wybór flashera na podstawie typu firmware.') ?>
          </li>
          <li>
            <strong><?= $L('FirmwareType.NINEBOT', 'FirmwareType.NINEBOT', 'FirmwareType.NINEBOT') ?>:</strong>
            <?= $L('Neuer Enum-Wert als Platzhalter für zukünftige Ninebot-Unterstützung hinzugefügt.', 'New enum value added as a placeholder for future Ninebot firmware support.', 'Nowa wartość enum jako zaślepka dla przyszłej obsługi Ninebot.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 2: Serial Number Module -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('2. serial_number.py – Eigenständiges LEQI Seriennummer-Modul', '2. serial_number.py - Standalone LEQI Serial Number Module', '2. serial_number.py - Samodzielny moduł numeru seryjnego LEQI') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Der gesamte Seriennummer-Code wurde aus dem alten Set-Serial-Skript extrahiert und in das neue Modul <code>bwflasher/serial_number.py</code> überführt. Das CLI-Skript <code>scripts/set_serialnumber.py</code> ist jetzt ein schlanker Wrapper.',
            'All serial number logic has been extracted from the old standalone script into the new <code>bwflasher/serial_number.py</code> module. The CLI script <code>scripts/set_serialnumber.py</code> is now a thin wrapper around this module.',
            'Cała logika numeru seryjnego została przeniesiona do modułu <code>bwflasher/serial_number.py</code>. Skrypt CLI <code>scripts/set_serialnumber.py</code> jest teraz cienką nakładką.'
          ) ?>
        </p>

        <ul class="change-list">
          <li>
            <strong><?= $L('validate_serial_number()', 'validate_serial_number()', 'validate_serial_number()') ?>:</strong>
            <?= $L('Erzwingt genau 19 ASCII-Zeichen. Frühere Versionen erlaubten variable Längen.', 'Enforces exactly 19 ASCII characters. Previous versions accepted variable lengths.', 'Wymaga dokładnie 19 znaków ASCII. Wcześniejsze wersje akceptowały zmienną długość.') ?>
          </li>
          <li>
            <strong><?= $L('parse_response() & read_response()', 'parse_response() & read_response()', 'parse_response() & read_response()') ?>:</strong>
            <?= $L('Strukturierte Antwort-Validierung mit CRC-Prüfung und typsicherer Rückgabe <code>(bool, str|None, str)</code>.', 'Structured response parsing with CRC verification and typed return <code>(bool, str|None, str)</code>.', 'Strukturowana weryfikacja odpowiedzi z kontrolą CRC i typowanym zwrotem <code>(bool, str|None, str)</code>.') ?>
          </li>
          <li>
            <strong><?= $L('--simulation Flag', '--simulation flag', 'Flaga --simulation') ?>:</strong>
            <?= $L('<code>send_serial_command(simulation=True)</code> simuliert die vollständige TX/RX-Sequenz ohne seriellen Port.', '<code>send_serial_command(simulation=True)</code> simulates the full TX/RX round-trip without opening a serial port.', '<code>send_serial_command(simulation=True)</code> symuluje pełną sekwencję TX/RX bez otwierania portu szeregowego.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 3: LEQI Flasher Improvements -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('3. LeqiFlasher – UART-Timing & Fehlerbehandlung', '3. LeqiFlasher - UART Timing & Error Handling', '3. LeqiFlasher - Timing UART i obsługa błędów') ?></h3>
        <ul class="change-list">
          <li>
            <strong><?= $L('End-Command Retry-Logik', 'End-Command Retry Logic', 'Logika ponowień dla polecenia końcowego') ?>:</strong>
            <?= $L('<code>_send_end_command()</code> versucht den End-Befehl (<code>0x05</code>) nun bis zu 10 Mal mit 60 ms Verzögerung. Behebt sporadische Timeouts bei langsamen Controllern.', '<code>_send_end_command()</code> now retries the end packet (<code>0x05</code>) up to 10 times with 60 ms spacing. Fixes intermittent timeouts on slow controllers.', '<code>_send_end_command()</code> powtarza polecenie końca (<code>0x05</code>) do 10 razy co 60 ms. Naprawia sporadyczne timeouty na wolnych kontrolerach.') ?>
          </li>
          <li>
            <strong><?= $L('Detaillierte Chunk-Fehlerstatistiken', 'Detailed Chunk Error Counts', 'Szczegółowe statystyki błędów chunków') ?>:</strong>
            <?= $L('Beim Senden von Firmware-Daten werden fehlgeschlagene Chunks (<code>failed_chunks</code>) gezählt. Am Ende wird ein Gesamtfehler ausgelöst, wenn mindestens ein Chunk ungültig war.', 'During firmware data transfer, failed chunks are tallied in <code>failed_chunks</code> and a summary error is raised if any chunk had an invalid/missing response.', 'Podczas transferu danych firmware zliczane są nieudane chunki. Jeśli choć jeden chunk był nieprawidłowy, generowany jest błąd zbiorczy.') ?>
          </li>
          <li>
            <strong><?= $L('test_connection() – DeviceInfo (simulation=False)', 'test_connection() - Real DeviceInfo Response Parsing', 'test_connection() - Parsowanie DeviceInfo w trybie rzeczywistym') ?>:</strong>
            <?= $L('Echte Verbindungstests geben jetzt Controller-Firmware-Version und Region aus der DeviceInfo-Antwort aus (identisch mit dem Simulationspfad).', 'Real device connection tests now extract and log the controller firmware version and region from the DeviceInfo reply (parity with the simulation path).', 'Testy połączenia z rzeczywistym urządzeniem teraz odczytują i logują wersję firmware kontrolera oraz region z odpowiedzi DeviceInfo.') ?>
          </li>
        </ul>
      </div>
    </article>

    <!-- RELEASE BW-PATCHER MASTER UPDATE -->
    <article class="release-card">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version"><?= $L('bw-patcher Master: Mi 6, Mi 6 Lite & 4 Ultra Rework', 'bw-patcher Master: Mi 6, Mi 6 Lite & 4 Ultra Rework', 'bw-patcher Master: Mi 6, Mi 6 Lite i przebudowa 4 Ultra') ?></span>
          </div>
          <span class="release-date">ScooterTeam & visoire Port · 2026-09-03</span>
        </div>

        <div class="release-links">
          <a href="https://github.com/scooterteam/bw-patcher" target="_blank" rel="noopener" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            bw-patcher
          </a>
          <a href="/xiaomi/patcher/" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <?= $L('Patcher öffnen', 'Open Patcher', 'Otwórz patcher') ?>
          </a>
          <a href="/xiaomi/setup/" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <?= $L('Setup-Assistent', 'Setup Wizard', 'Kreator konfiguracji') ?>
          </a>
        </div>
      </header>

      <!-- Section 1: Mi 6 and Mi 6 Lite Support -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('1. Experimenteller Support für Xiaomi 6 & 6 Lite (bw-patcher)', '1. Experimental Support for Xiaomi 6 & 6 Lite (bw-patcher)', '1. Eksperymentalna obsługa dla Xiaomi 6 i 6 Lite (bw-patcher)') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Im <strong>bw-patcher</strong> wurde experimenteller Support für den <strong>Xiaomi Scooter 6</strong> und <strong>Xiaomi Scooter 6 Lite</strong> hinzugefügt. Das Flash- und Patch-Verfahren entspricht 1:1 dem des 5 Elite (LEQI N32 Controller via M8 Julet-Stecker).',
            'Experimental support for the <strong>Xiaomi Scooter 6</strong> and <strong>Xiaomi Scooter 6 Lite</strong> has been added to <strong>bw-patcher</strong>. The patching and flashing procedure is 1:1 identical to the 5 Elite (LEQI N32 controller via M8 Julet connector).',
            'W <strong>bw-patcherze</strong> dodano eksperymentalną obsługę dla hulajnóg <strong>Xiaomi Scooter 6</strong> oraz <strong>Xiaomi Scooter 6 Lite</strong>. Procedura patchowania i wgrywania jest w 100% zgodna z modelem 5 Elite (sterownik LEQI N32 przez złącze M8 Julet).'
          ) ?>
        </p>

        <div class="callout-box">
          <div class="callout-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $L('Offizieller Release-Hinweis:', 'Official Release Note:', 'Oficjalny komunikat:') ?>
          </div>
          <p class="callout-desc" style="font-family: var(--fm); font-size: 13px; color: var(--ink);">
            "Experimental support for mi 6 and mi 6 lite has been added to bw patcher. Same procedure as 5 Elite. Testers welcome!"
          </p>
        </div>

        <ul class="change-list" style="margin-top: 14px;">
          <li>
            <strong><?= $L('Neue Module & LeqiSpeedPatcher Basis', 'New Modules & LeqiSpeedPatcher Base', 'Nowe moduły i baza LeqiSpeedPatcher') ?>:</strong> 
            <?= $L('<code>bwpatcher/modules/mi6.py</code>, <code>mi6lite.py</code> und die gemeinsame Basis <code>leqi_speed.py</code> für Assembler-Injektion und Umgehung regionaler Begrenzungen.', '<code>bwpatcher/modules/mi6.py</code>, <code>mi6lite.py</code> and shared base <code>leqi_speed.py</code> for assembly injection and regional limit bypass.', 'Dodano <code>bwpatcher/modules/mi6.py</code>, <code>mi6lite.py</code> i bazę <code>leqi_speed.py</code> do wstrzykiwania kodu asemblera i ominięcia blokady regionalnej.') ?>
          </li>
          <li>
            <strong><?= $L('Unterstützte Patches', 'Supported Patches', 'Obsługiwane patche') ?>:</strong> 
            <?= $L('Sport-Modus (SLS), Drive-Modus (SLD) und Fußgänger-Modus (SLP) Geschwindigkeitsgrenzen sowie automatischer Bypass der regionalen Drosselung.', 'Sport mode (SLS), Drive mode (SLD), and Pedestrian mode (SLP) speed limits, plus automated regional speed limit bypass.', 'Limity prędkości dla trybów Sport (SLS), Drive (SLD) i Pieszego (SLP) wraz z automatycznym ominięciem ograniczeń regionalnych.') ?>
          </li>
          <li>
            <strong><?= $L('Dynamische EU1-Header-Größe', 'Dynamic EU1 Header Sizing', 'Dynamiczny rozmiar nagłówka EU1') ?>:</strong> 
            <?= $L('Unterstützt variable Firmware-Größen (z. B. 0xA800 für Mi 6, 0xB000 für Mi 6 Lite) mit automatischer Verschlüsselung (XOR 0xAA) und CRC-16.', 'Handles arbitrary LEQI firmware sizes (e.g., 0xA800 for Mi 6, 0xB000 for Mi 6 Lite) with automated XOR 0xAA encryption and CRC-16.', 'Obsługuje zmienne rozmiary firmware LEQI (np. 0xA800 dla Mi 6, 0xB000 dla Mi 6 Lite) z szyfrowaniem XOR 0xAA i sumą CRC-16.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 2: 4 Ultra Patch Rework -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('2. 4 Ultra Patch überarbeitet & als experimentell hinzugefügt', '2. 4 Ultra Patch Reworked & Added as Experimental', '2. Przebudowany patch dla 4 Ultra i status eksperymentalny') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Der Patch für den <strong>Xiaomi Scooter 4 Ultra</strong> (LKS32 Controller) wurde von Grund auf überarbeitet. Frühere Signature-Matching-Fehler beim Patchen über die CLI wurden behoben.',
            'The patch for the <strong>Xiaomi Scooter 4 Ultra</strong> (LKS32 controller) has been completely reworked, resolving previous signature matching errors during CLI execution.',
            'Patch dla hulajnogi <strong>Xiaomi Scooter 4 Ultra</strong> (kontroler LKS32) został gruntownie przebudowany, eliminując wcześniejsze błędy dopasowywania sygnatur w CLI.'
          ) ?>
        </p>

        <div class="callout-box">
          <div class="callout-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $L('Offizieller Release-Hinweis:', 'Official Release Note:', 'Oficjalny komunikat:') ?>
          </div>
          <p class="callout-desc" style="font-family: var(--fm); font-size: 13px; color: var(--ink);">
            "4 ultra patch has also been reworked and added as experimental."
          </p>
        </div>

        <ul class="change-list" style="margin-top: 14px;">
          <li>
            <strong><?= $L('Unabhängige Pool-Slots', 'Independent Literal Pool Slots', 'Niezależne sloty pamięci dosłownej') ?>:</strong> 
            <?= $L('Unabhängige Literal-Pool-Offsets für Drive (<code>sld</code>) und Sport (<code>sls</code>), wodurch Reihenfolge-Abhängigkeiten eliminiert wurden.', 'Independent literal pool slots for Drive (<code>sld</code>) and Sport (<code>sls</code>), eliminating branch site ordering issues.', 'Niezależne sloty w puli dla trybów Drive (<code>sld</code>) i Sport (<code>sls</code>) eliminujące zależność od kolejności wywołań.') ?>
          </li>
          <li>
            <strong><?= $L('Umfassende Patch-Auswahl', 'Comprehensive Patch Selection', 'Pełny wybór patchy') ?>:</strong> 
            <?= $L('Unterstützt SLS, SLD, DMS, MSS, CCE (Tempomat), RFM (Region Free) und FDV (Fake Firmware Version).', 'Fully supports SLS, SLD, DMS, MSS, CCE (cruise control), RFM (region free), and FDV (fake version).', 'Pełna obsługa SLS, SLD, DMS, MSS, CCE (tempomat), RFM (brak regionu) i FDV (fałszywa wersja).') ?>
          </li>
        </ul>
      </div>

      <!-- Section 3: Guided Setup & UI Badges -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('3. Setup-Assistent Fix & Experimentelle Kennzeichnung', '3. Guided Setup Fix & Experimental Badges', '3. Poprawka w kreatorze i oznaczenia eksperymentalne') ?></h3>
        <ul class="change-list">
          <li>
            <strong><?= $L('Behebung der Größenbegrenzung im Setup-Flasher', 'Setup Wizard Flasher Size Fix', 'Poprawka limitu rozmiaru w kreatorze') ?>:</strong> 
            <?= $L('Die veraltete statische Begrenzung (<code>0x9880</code>) in <code>setup/assets/js/leqi.js</code> wurde durch die dynamische EU1-Header-Erkennung aus dem Flasher ersetzt. Verhindert Beschädigung/Abschneiden von Mi 6 und Mi 6 Lite Firmwares.', 'Replaced the legacy hardcoded size limit (<code>0x9880</code>) in <code>setup/assets/js/leqi.js</code> with dynamic EU1 header parsing and CRC validation, preventing firmware truncation on Mi 6 and Mi 6 Lite.', 'Zastąpiono sztywny limit (<code>0x9880</code>) dynamicznym odczytem nagłówka EU1 i walidacją CRC w kreatorze konfiguracji.') ?>
          </li>
          <li>
            <strong><?= $L('Token-Reihenfolge korrigiert', 'Token Ordering Corrected', 'Poprawiona kolejność tokenów') ?>:</strong> 
            <?= $L('In <code>setup/prepare.php</code> wird die Prüfsumme (<code>chk</code>) nun vor dem Reassemblieren des verschlüsselten Images (<code>img</code>) berechnet.', 'In <code>setup/prepare.php</code>, the checksum token (<code>chk</code>) is now computed prior to full image reassembly (<code>img</code>).', 'W <code>setup/prepare.php</code> suma kontrolna (<code>chk</code>) jest teraz obliczana przed złożeniem zaszyfrowanego obrazu (<code>img</code>).') ?>
          </li>
          <li>
            <strong><?= $L('EXP-Badges & Hinweise', 'EXP Badges & Tester Notices', 'Odznaki EXP i komunikaty dla testerów') ?>:</strong> 
            <?= $L('Mi 6, Mi 6 Lite und 4 Ultra verfügen nun über gut sichtbare <code>EXP</code>-Badges und kontextuelle Hinweisfenster im Patcher, Flasher und Setup-Assistenten.', 'Mi 6, Mi 6 Lite, and 4 Ultra now feature prominent <code>EXP</code> badges and contextual tester notices in Patcher, Flasher, and Guided Setup.', 'Modele Mi 6, Mi 6 Lite oraz 4 Ultra otrzymały czytelne oznaczenia <code>EXP</code> oraz ostrzeżenia w patcherze, flasherze i kreatorze.') ?>
          </li>
        </ul>
      </div>
    </article>

    <!-- RELEASE 0.6.2 -->
    <article class="release-card">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version">bw-flasher 0.6.2 & 6th Gen Update</span>
          </div>
          <span class="release-date">ScooterTeam & visoire Port · 2026-08-29</span>
        </div>

        <div class="release-links">
          <a href="https://github.com/scooterteam/bw-flasher/releases/tag/v0.6.2" target="_blank" rel="noopener" class="btn-gh">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            bw-flasher v0.6.2
          </a>
          <a href="https://github.com/scooterteam/bw-flasher/releases" target="_blank" rel="noopener" class="btn-gh">
            All Releases ↗
          </a>
        </div>
      </header>

      <!-- Section 1: Flasher Core Engine -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('1. bw-flasher 0.6.2 Core Engine Upgrade & LEQI EU1 Parsing', '1. bw-flasher 0.6.2 Core Engine Upgrade & LEQI EU1 Parsing', '1. Aktualizacja silnika bw-flasher 0.6.2 i obsługa LEQI EU1') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Die neueste Version von <strong>bw-flasher 0.6.2</strong> wurde vollständig in den Web-Flasher portiert. Dieses Update führt das dynamische Auslesen der LEQI-Firmwaregröße über den EU1-Header ein und garantiert durch Prüfsummenvalidierung höchste Zuverlässigkeit.',
            'The latest version of <strong>bw-flasher 0.6.2</strong> has been fully ported into the web flasher. This update introduces dynamic extraction of LEQI firmware body sizes from the EU1 header and ensures robust checksum validation before flashing.',
            'Najnowsza wersja <strong>bw-flasher 0.6.2</strong> została w pełni przeniesiona do web-flashera. Wprowadza dynamiczne odczytywanie rozmiaru firmware LEQI z nagłówka EU1 oraz weryfikację sumy kontrolnej przed rozpoczęciem zapisu.'
          ) ?>
        </p>

        <div class="callout-box">
          <div class="callout-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $L('Offizieller Release-Changelog (v0.6.2):', 'Official Release Changelog (v0.6.2):', 'Oficjalny opis wydania (v0.6.2):') ?>
          </div>
          <p class="callout-desc" style="font-family: var(--fm); font-size: 13px; color: var(--ink);">
            "Read LEQI firmware body size from the EU1 full-image header instead of the legacy fixed extraction window, and validate embedded CRC on load."
          </p>
        </div>

        <ul class="change-list" style="margin-top: 14px;">
          <li>
            <strong><?= $L('EU1 Header Parsing', 'EU1 Header Parsing', 'Przetwarzanie nagłówka EU1') ?>:</strong> 
            <?= $L('LEQI-Firmware-Body-Größe wird direkt aus dem Image-Header (uint16 LE bei Offset <code>0x0E</code> bei vorhandenem <code>EU1\0</code>-Tag bei <code>0x0A</code>) ausgelesen. Automatischer Fallback auf AA-Padding-Erkennung für Legacy-Dateien.', 'LEQI firmware body size is now read directly from the image header (uint16 LE at offset <code>0x0E</code> when <code>EU1\0</code> tag is at <code>0x0A</code>). Falls back to legacy AA-padding detection for files without the tag.', 'Rozmiar ciała oprogramowania LEQI jest teraz odczytywany z nagłówka (uint16 LE na pozycji <code>0x0E</code> przy tagu <code>EU1\0</code> na <code>0x0A</code>).') ?>
          </li>
          <li>
            <strong><?= $L('Eingebettete CRC-16 Validierung', 'Embedded CRC-16 Validation', 'Walidacja sumy kontrolnej CRC-16') ?>:</strong> 
            <?= $L('Der Firmware-Body trägt eine bit-reversierte CRC-16 (Polynom <code>0x8005</code>) in den letzten zwei Bytes. Dateien mit fehlerhafter Prüfsumme werden vor dem seriellen Senden abgewiesen.', 'Firmware body carries a bit-reversed CRC-16 (poly <code>0x8005</code>) in the last two bytes. Corrupted files are safely rejected before sending serial traffic.', 'Ciało firmware zawiera sumę CRC-16 (wielomian <code>0x8005</code>) w ostatnich 2 bajtach. Pliki z niezgodną sumą są natychmiast odrzucane.') ?>
          </li>
          <li>
            <strong><?= $L('DeviceInfo Verbindungstest (Read-Only)', 'DeviceInfo Read-Only Connection Test', 'Test połączenia DeviceInfo (tylko do odczytu)') ?>:</strong> 
            <?= $L('<code>testConnection</code> sendet nun Befehl <code>0x02</code> (DeviceInfo) statt <code>0x03</code> (Flash-Start). Liest Version und Region des Controllers sicher aus, ohne Schreiboperationen auszulösen.', '<code>testConnection</code> now sends command <code>0x02</code> (DeviceInfo) instead of <code>0x03</code> (firmware update start), safely reporting controller version and region without altering state.', '<code>testConnection</code> wysyła polecenie <code>0x02</code> (DeviceInfo) zamiast <code>0x03</code>, bezpiecznie odczytując wersję i region kontrolera.') ?>
          </li>
          <li>
            <strong><?= $L('Variabler Serial Reader & uint16 Guard', 'Variable-length Serial Reader & uint16 Guard', 'Czytnik o zmiennej długości i ochrona uint16') ?>:</strong> 
            <?= $L('<code>_readLeqiResponse</code> unterstützt 17-Byte DeviceInfo-Antworten und bricht nach 20 ms Leitungsruhe ab. Firmware-Größen über <code>0xFFFF</code> werden sicher abgefangen.', '<code>_readLeqiResponse</code> supports 17-byte DeviceInfo replies with 20ms silence early-break. Prevents body sizes exceeding <code>0xFFFF</code>.', 'Obsługa 17-bajtowych odpowiedzi DeviceInfo z wczesnym zakończeniem przy braku transmisji.') ?>
          </li>
          <li>
            <strong><?= $L('Raw Buffer Erhaltung', 'Raw Buffer Preservation', 'Zachowanie bufora surowego') ?>:</strong> 
            <?= $L('<code>processFirmware</code> liefert <code>{data, raw}</code>. LEQI-Flashs nutzen den unbeschnittenen Puffer, sodass Header und CRC vollständig erhalten bleiben.', '<code>processFirmware</code> returns <code>{data, raw}</code>. LEQI flashing preserves the original raw buffer keeping header and CRC intact.', 'Flashowanie LEQI zachowuje oryginalny nagłówek i sumę CRC w nienaruszonym stanie.') ?>
          </li>
          <li>
            <strong><?= $L('Standalone BLE Serial Number Tool', 'Standalone BLE Serial Number Tool', 'Niezależne narzędzie numeru seryjnego BLE') ?>:</strong> 
            <?= $L('Neues Python-CLI-Skript <code>flasher/scripts/set_serialnumber.py</code> (Befehl <code>0x97</code>) zum Schreiben der 19-stelligen Seriennummer in den BLE-NVM (RTL8762C).', 'Added standalone Python CLI script <code>flasher/scripts/set_serialnumber.py</code> (command <code>0x97</code>) to write a 19-char serial into BLE NVM (RTL8762C).', 'Dodano skrypt <code>flasher/scripts/set_serialnumber.py</code> (polecenie <code>0x97</code>) do zapisu numeru seryjnego w pamięci modułu BLE.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 2: 6th Gen Scooters -->
      <div class="change-section">
        <h3><span class="dot" style="background: var(--amber);"></span><?= $L('2. Xiaomi 6. Generation (6, 6 Lite, 6 Pro, 6 Max, 6 Ultra)', '2. Xiaomi 6th Generation (6, 6 Lite, 6 Pro, 6 Max, 6 Ultra)', '2. Xiaomi 6. generacja (6, 6 Lite, 6 Pro, 6 Max, 6 Ultra)') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Vollständige Integration der neuen 6. Generation über alle Web-Komponenten hinweg. Kabeltyp, Teststatus und Firmware-Details sind exakt erfasst.',
            'Comprehensive integration of the new 6th Generation scooters across all web components, mapping cable types, testing status, and firmware details.',
            'Pełna integracja nowej 6. generacji we wszystkich komponentach strony, z precyzyjnym mapowaniem kabli, stanu testów i danych firmware.'
          ) ?>
        </p>

        <div class="highlight-grid">
          <div class="highlight-item">
            <h4><?= $L('🔌 Julet 5-Pin M8 MALE (1:1 wie 5 Elite)', '🔌 Julet 5-Pin M8 MALE (1:1 like 5 Elite)', '🔌 Julet 5-Pin M8 MALE (1:1 jak 5 Elite)') ?></h4>
            <p><?= $L('Alle Modelle der 6. Generation nutzen exakt denselben 5-Pin Stecker (M8 MALE, LEQI-Protokoll) wie der Xiaomi Scooter 5 Elite.', 'All 6th gen models share the identical 5-pin M8 MALE connector (LEQI protocol) as the Xiaomi Scooter 5 Elite.', 'Wszystkie modele 6. generacji korzystają z identycznego złącza 5-pin M8 MALE co Xiaomi Scooter 5 Elite.') ?></p>
          </div>
          <div class="highlight-item">
            <h4><?= $L('⚠️ Teststatus: Scooter 6 & 6 Lite', '⚠️ Testing State: Scooter 6 & 6 Lite', '⚠️ Stan testowy: Scooter 6 & 6 Lite') ?></h4>
            <p><?= $L('Flashen über UART funktioniert bereits einwandfrei. Der Speed-Unlock befindet sich zurzeit in der Verifizierung durch Tester.', 'Flashing via M8 UART adapter is verified and working. Speed unlock tuning is currently under real-world testing.', 'Flashowanie przez adapter UART M8 działa stabilnie. Odblokowanie prędkości jest w trakcie weryfikacji przez testerów.') ?></p>
          </div>
          <div class="highlight-item">
            <h4><?= $L('❌ 6 Pro / 6 Max / 6 Ultra: Nicht unterstützt', '❌ 6 Pro / 6 Max / 6 Ultra: Unsupported', '❌ 6 Pro / 6 Max / 6 Ultra: Nieobsługiwane') ?></h4>
            <p><?= $L('Für Pro, Max und Ultra ist kein Tuning-Support und kein Flashing verfügbar.', 'No firmware speed unlock or flashing support is planned for Pro, Max, and Ultra variants.', 'Brak planowanego wsparcia dla odblokowania prędkości lub zapisu w modelach Pro, Max i Ultra.') ?></p>
          </div>
        </div>

        <table class="spec-table">
          <thead>
            <tr>
              <th><?= $L('Modell', 'Model', 'Model') ?></th>
              <th>Slug / ID</th>
              <th><?= $L('Body-Größe', 'Body Size', 'Rozmiar ciała') ?></th>
              <th>CRC-16</th>
              <th>MCU / BLE</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Xiaomi Scooter 6</strong></td>
              <td><code>mi6</code> · <code>xiaomi.scooter.6</code></td>
              <td><code>0xA800</code> (43,008 B)</td>
              <td><code>0x5DF3</code></td>
              <td>v0115 / 2.7.0_0027</td>
              <td><span style="color: var(--amber); font-weight: 600;">⚠️ <?= $L('Im Test', 'Testing', 'W testach') ?></span></td>
            </tr>
            <tr>
              <td><strong>Xiaomi Scooter 6 Lite</strong></td>
              <td><code>mi6lite</code> · <code>xiaomi.scooter.6lite</code></td>
              <td><code>0xB000</code> (45,056 B)</td>
              <td><code>0x0936</code></td>
              <td>v0128 / 2.7.0_0026</td>
              <td><span style="color: var(--amber); font-weight: 600;">⚠️ <?= $L('Im Test', 'Testing', 'W testach') ?></span></td>
            </tr>
            <tr>
              <td><strong>Xiaomi Scooter 6 Pro</strong></td>
              <td><code>mi6pro</code> · <code>xiaomi.scooter.6pro</code></td>
              <td>-</td>
              <td>-</td>
              <td>v0051 / 2.7.0_0050</td>
              <td><span style="color: var(--red); font-weight: 600;">❌ <?= $L('Nicht unterstützt', 'Unsupported', 'Brak wsparcia') ?></span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Section 3: Patcher Filters -->
      <div class="change-section">
        <h3><span class="dot" style="background: var(--blue);"></span><?= $L('3. Kabel- & Protokoll-Filtersystem im Patcher', '3. Cable & Protocol Filter System in Patcher', '3. System filtrów złączy i protokołów w patcherze') ?></h3>
        <p style="font-size: 14.5px; color: var(--text); margin-bottom: 12px;">
          <?= $L(
            'Oberhalb des Modell-Rasters im Firmware-Patcher wurde ein Schnellfilter integriert, um gezielt nach Steckertyp oder Übertragungsprotokoll zu filtern.',
            'An interactive filter pill bar has been introduced above the model grid in the Firmware Patcher to quickly filter by connector type or protocol.',
            'W patcherze firmware wprowadzono interaktywny pasek filtrów nad listą modeli do szybkiego wyszukiwania po typie złącza lub protokole.'
          ) ?>
        </p>

        <ul class="change-list">
          <li><strong>Stecker-Filter:</strong> <code>M7 MALE</code> (3 Lite, 4, 4 Lite, 4 Ultra, 5 Max, 5 Pro), <code>M7 FEMALE</code> (4 Pro 2nd Gen, 5), <code>M8 MALE</code> (4 Lite 2nd Gen, 5 Elite, 6, 6 Lite, 6 Pro).</li>
          <li><strong>Protokoll-Filter:</strong> <code>Brightway</code> und <code>LEQI</code>.</li>
          <li><strong>Kombinierbar:</strong> Filter können beliebig kombiniert und durch erneutes Klicken zurückgesetzt werden.</li>
        </ul>
      </div>

      <!-- Section 4: Mobile-First UX & Clarifications -->
      <div class="change-section">
        <h3><span class="dot"></span><?= $L('4. Mobile-First UX & Modell-Klarstellungen', '4. Mobile-First UX & Model Clarifications', '4. Interfejs mobilny i wyjaśnienia modeli') ?></h3>
        <ul class="change-list">
          <li>
            <strong><?= $L('Mobile-First Redesign', 'Mobile-First Redesign', 'Mobilny interfejs użytkownika') ?>:</strong> 
            <?= $L('48px Touch-Ziele, Bottom-Sheet Disclaimer, Notch/Safe-Area-Optimierung und farbcodierte Terminal-Logs (Blau/Lila/Gedimmt).', '48px minimum touch targets, mobile bottom-sheet disclaimer, notch/safe-area insets, and color-coded serial log output.', 'Minimalny obszar dotyku 48px, mobilne okna dialogowe, obsługa wcięć ekranu oraz kolorowe logi transmisji.') ?>
          </li>
          <li>
            <strong><?= $L('Xiaomi 4 Ultra vs Ultra 2 Hinweis', 'Xiaomi 4 Ultra vs Ultra 2 Clarification', 'Wyjaśnienie Xiaomi 4 Ultra a Ultra 2') ?>:</strong> 
            <?= $L('Der <strong>Xiaomi 4 Ultra</strong> (EU) und der <strong>Xiaomi 4 Ultra 2</strong> (DE) teilen sich dieselbe Hardware und Modell-ID (<code>dreame.scooter.p2301</code>). Beide werden im Patcher identisch über <strong>Scooter 4 Ultra</strong> gepatcht.', 'Both <strong>Xiaomi 4 Ultra</strong> (EU) and <strong>Xiaomi 4 Ultra 2</strong> (DE) share the same hardware and model ID (<code>dreame.scooter.p2301</code>). Patching and flashing via <strong>Scooter 4 Ultra</strong> works identically for both.', 'Modele <strong>Xiaomi 4 Ultra</strong> (EU) oraz <strong>Xiaomi 4 Ultra 2</strong> (DE) mają identyczny identyfikator sprzętowy (<code>dreame.scooter.p2301</code>). Wybór <strong>Scooter 4 Ultra</strong> działa identycznie dla obu wersji.') ?>
          </li>
          <li>
            <strong><?= $L('Mehrsprachige Benutzeroberfläche', 'Trilingual Interface', 'Trójjęzyczny interfejs') ?>:</strong> 
            <?= $L('Vollständige Übersetzungen und Fehlermeldungen auf Deutsch, Englisch und Polnisch.', 'Comprehensive translations and error handling in German, English, and Polish.', 'Pełne tłumaczenia i obsługa komunikatów w języku niemieckim, angielskim i polskim.') ?>
          </li>
        </ul>
      </div>

      <!-- Section 5: Engineering & AI Disclosure -->
      <div class="change-section">
        <h3><span class="dot" style="background: var(--blue);"></span><?= $L('5. Entwicklung, Qualitätssicherung & KI-Transparenz', '5. Engineering, Quality Assurance & AI Disclosure', '5. Inżynieria, kontrola jakości i informacja o AI') ?></h3>
        <div class="callout-box">
          <div class="callout-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <?= $L('Technische Umsetzung & Dokumentation', 'Engineering & Documentation Standards', 'Standardy inżynieryjne i dokumentacja') ?>
          </div>
          <p class="callout-desc">
            <?= $L(
              'Die Web-Architektur, Tool-Portierungen und die technische Dokumentation dieser Plattform werden mit Unterstützung fortschrittlicher KI-Modelle (unter anderem Anthropic Claude Opus 5 und Google Gemini 3.7 Pro) entwickelt und gepflegt. Jeder Code-Baustein, jedes Protokollpaket und jede Firmware-Struktur wird durch sorgfältige manuelle Audits, Quellcode-Analysen und Hardware-Tests an echten E-Scootern verifiziert. Dies sichert höchste Zuverlässigkeit, schnelle Updates bei neuen Hersteller-Firmwares und eine klare, verständliche Dokumentation.',
              'The web architecture, browser tool ports, and technical documentation on this platform are developed and maintained with the assistance of advanced AI engineering models (including Anthropic Claude Opus 5 and Google Gemini 3.7 Pro). Every code component, protocol packet structure, and firmware routine undergoes rigorous manual verification, byte-level audits, and physical validation on real scooter hardware. This ensures high reliability, rapid updates for new firmware revisions, and crystal-clear documentation.',
              'Architektura serwisu, porty narzędzi w przeglądarce oraz dokumentacja techniczna są rozwijane przy wsparciu zaawansowanych modeli AI (w tym Anthropic Claude Opus 5 oraz Google Gemini 3.7 Pro). Każdy fragment kodu, pakiet protokołu i struktura oprogramowania przechodzi dokładne ręczne audyty, analizę kodu źródłowego oraz testy sprzętowe na rzeczywistych hulajnogach, co gwarantuje najwyższy poziom bezpieczeństwa i stabilności.'
            ) ?>
          </p>
        </div>
      </div>
    </article>

    <!-- RELEASE 0.6.0 -->
    <article class="release-card">
      <header class="release-header">
        <div class="release-title-group">
          <div class="release-title-row">
            <span class="release-version">bw-flasher 0.6.0</span>
          </div>
          <span class="release-date">ScooterTeam & visoire Port · 2026-08-28</span>
        </div>

        <div class="release-links">
          <a href="https://github.com/scooterteam/bw-flasher/releases/tag/v0.6.0" target="_blank" rel="noopener" class="btn-gh">
            bw-flasher v0.6.0 ↗
          </a>
        </div>
      </header>

      <div class="change-section">
        <h3><span class="dot"></span><?= $L('Erster nativer Web-Flasher Port', 'First Native Web-Flasher Port', 'Pierwszy natywny port Web-Flasher') ?></h3>
        <ul class="change-list">
          <li><?= $L('Vollständiges Flashen im Browser über Web Serial & WebUSB (Chrome, Edge, Opera auf Desktop & Android).', 'Full browser-based firmware flashing over Web Serial & WebUSB (Chrome, Edge, Opera on desktop & Android).', 'Pełne flashowanie w przeglądarce za pomocą Web Serial i WebUSB.') ?></li>
          <li><?= $L('Unterstützung für Brightway- und LEQI-Controller (N32 / GD32 / HC32).', 'Support for Brightway and LEQI controllers (N32 / GD32 / HC32).', 'Obsługa kontrolerów Brightway i LEQI (N32 / GD32 / HC32).') ?></li>
          <li><?= $L('Integrierter Setup-Wizard, Patcher mit automatischer Prüfsummenkorrektur und lokaler MCU-Spiegel.', 'Integrated setup wizard, firmware patcher with CRC auto-fix, and local MCU binary repository.', 'Zintegrowany kreator konfiguracji, patcher z automatyczną sumą kontrolną i lokalna baza MCU.') ?></li>
        </ul>
      </div>
    </article>

  </div>

</main>

<!-- Footer -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a href="../?lang=<?= e($lang) ?>" class="logo">
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
          <li><a href="/xiaomi/?lang=<?= e($lang) ?>#steps"><?= e($tx[$lang]['nav_steps']) ?></a></li>
          <li><a href="mailto:visoire@proton.me"><?= $L('Kontakt', 'Contact', 'Kontakt') ?></a></li>
        </ul>
      </div>
      <div class="foot-col">
        <h4><?= $L('Werkzeuge', 'Tools', 'Narzędzia') ?></h4>
        <ul>
          <li><a href="/xiaomi/patcher/?lang=<?= e($lang) ?>">BW Patcher</a></li>
          <li><a href="/xiaomi/flasher/?lang=<?= e($lang) ?>">BW Flasher</a></li>
          <li><a href="/xiaomi/mcu/?lang=<?= e($lang) ?>">MCU Firmwares</a></li>
          <li><a href="/xiaomi/changelog/?lang=<?= e($lang) ?>">Changelog</a></li>
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
      <span class="foot-made"><?= $L('made by', 'made by', 'made by') ?> <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a> · <?= e($tx[$lang]['credit_scooterteam']) ?> <a href="https://github.com/scooterteam" target="_blank" rel="noopener">scooterteam</a> · <?= e($tx[$lang]['credit_bastelpichi']) ?> <a href="https://wiki.bastelpichi.de/" target="_blank" rel="noopener">Bastelpichi</a></span>
      <span><?= e($tx[$lang]['foot_disc']) ?></span>
    </div>
  </div>
</footer>

<script>
  // Mobile navigation + Header scroll behavior
  (function () {
    var burger = document.getElementById('burger');
    var mobNav = document.getElementById('mobNav');
    var overlay = document.getElementById('navOverlay');
    var hdr = document.getElementById('hdr');

    function closeMob() {
      if (mobNav) mobNav.classList.remove('open');
      if (overlay) overlay.classList.remove('open');
      if (burger) {
        burger.classList.remove('open');
        burger.setAttribute('aria-expanded', 'false');
      }
    }

    if (burger && mobNav && overlay) {
      burger.addEventListener('click', function () {
        var open = mobNav.classList.toggle('open');
        overlay.classList.toggle('open', open);
        burger.classList.toggle('open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      overlay.addEventListener('click', closeMob);
    }

    if (hdr) {
      window.addEventListener('scroll', function () {
        hdr.classList.toggle('scrolled', window.scrollY > 10);
      });
      hdr.classList.toggle('scrolled', window.scrollY > 10);
    }
  })();
</script>
</body>
</html>
