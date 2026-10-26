<?php
// inc/lang.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'de', 'pl'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + (86400 * 365), "/");
}

$lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';

$t = [
    'en' => [
        // Navigation
        'nav_hw' => 'Hardware Guide',
        'nav_steps' => 'Tutorial',
        'nav_help' => 'Help',
        'nav_wiki' => 'Wiki',
        'nav_changelog' => 'Changelog',
        'btn_tutorial' => 'Tutorial',
        'contact' => 'Contact',
        'close' => 'Close ✕',

        // Meta & Hero
        'title' => 'Hardware Guide: What to Look for When Buying a Flashing Cable',
        'desc' => 'Technical specifications, pinout requirements, and chipset details for flashing Xiaomi, Brightway, and LEQI scooter controllers independently.',
        'guide_badge' => 'Educational Technical Reference',
        'non_commercial_badge' => 'Non-Commercial / Open Guide',
        'product_title' => 'Hardware Guide: What to Look for When Buying a Flashing Cable',
        'guide_lead' => 'Firmware flashing and diagnostic communication with scooter motor controllers require specific serial interface hardware. This guide explains chipsets, voltage logic levels, connector pinouts, and common pitfalls when sourcing your own cable.',

        // Interactive Model Inspector
        'inspector_title' => 'Scooter Connector Inspector',
        'choose_model' => 'Select Scooter Model to Check Requirements',
        'select_default' => '- Select Model -',
        'conn_label' => 'Required Connector',
        'compat_flashable' => 'Firmware Flashable',
        'flashable_yes' => 'Supported',
        'flashable_no' => 'Not Flashable',
        'flashable_testing' => 'In Testing',
        'model_5_note' => '2025 models may use M6',
        'model_6_testing_note' => '6th Gen: In testing - flashing works, speed unlock unverified',
        'model_6_unsupported_note' => 'No flashing support currently available',

        // Tabs
        'tab_overview' => 'Overview',
        'tab_specs' => 'Key Specifications',
        'tab_mistakes' => 'Common Mistakes',
        'tab_compat' => 'Compatibility List',
        'tab_faq' => 'FAQ',
        'tab_hints' => 'Important Legal Notices',

        // Section: Overview
        'overview_title' => 'How Flashing Hardware Works',
        'overview_p1' => 'Scooter electronic control units (ECUs) communicate over standard <strong>asynchronous serial UART</strong> protocols. Because modern computers do not have physical RS-232/UART serial ports, a <strong>USB-to-UART bridge adapter</strong> is necessary to translate USB packets into serial logic levels.',
        'overview_li1' => '<strong>Serial Bridge:</strong> Converts USB data to TTL/CMOS serial pulses (TX/RX) understood by the microcontroller (STM32, LKS32, ES32, N32).',
        'overview_li2' => '<strong>Physical Interconnect:</strong> Uses a waterproof circular Julet connector matching the scooter wiring harness between the handlebar and the base.',
        'overview_li3' => '<strong>Direct Flashing:</strong> Allows unbricking, diagnostics, and loading custom or stock firmware directly to the controller.',
        'overview_note' => '<strong>Non-Commercial Notice:</strong> This documentation is strictly educational. We do not manufacture, sell, endorse, or earn affiliate revenue from any specific hardware vendor or store.',

        // Section: Key Specifications
        'specs_title' => 'Key Specifications to Check When Sourcing Hardware',
        'spec_chipset_title' => '1. Chipset Selection (FT232RL vs. Alternatives)',
        'spec_chipset_desc' => 'The USB-to-UART IC handles serial timing, packet buffering, and baud rate generation. The <strong>FTDI FT232RL</strong> is the industry benchmark for stability and driver support across Windows, macOS, and Linux. Other workable alternatives include Silicon Labs <strong>CP2102</strong> and WCH <strong>CH340G/C</strong>. Note that low-cost counterfeit FTDI clones may malfunction or produce driver errors on modern operating systems.',
        
        'spec_voltage_title' => '2. Logic Voltage Level (3.3V vs. 5V)',
        'spec_voltage_desc' => 'Most modern scooter microcontrollers operate on <strong>3.3V logic levels</strong>. Many UART adapters feature a physical jumper or switch allowing selection between 3.3V and 5V. Ensure your adapter logic is configured to <strong>3.3V</strong> (or 5V-tolerant if specified by your board schematics) to prevent permanent over-voltage damage to the MCU RX pin.',

        'spec_pinout_title' => '3. TX / RX Crossover Pinout Definition',
        'spec_pinout_desc' => 'Serial data requires crossover wiring: the computer\'s Transmit line (TX) connects to the scooter\'s Receive line (RX), and the computer\'s Receive line (RX) connects to the scooter\'s Transmit line (TX). A shared <strong>Ground (GND)</strong> line is mandatory for electrical signal reference. The power line (VCC) is typically disconnected because the scooter powers its own logic when switched on.',

        'spec_connector_title' => '4. Connector Gender & Sizing (M7 vs. M8 Julet)',
        'spec_connector_desc' => 'Scooters utilize waterproof circular Julet connectors with keyed notches. Matching the exact gender (MALE pins vs. FEMALE sockets) and thread/outer diameter (M7 for Brightway vs. M8 for LEQI/6th Gen) is critical. Forcing mismatched connectors will bend pins or ruin the seal.',

        // Section: Common Mistakes
        'mistakes_title' => 'Common Mistakes to Avoid',
        'mistake_1_title' => 'Mistake 1: Using Power-Only USB Cables',
        'mistake_1_desc' => 'Many cheap USB charging cables only have VBUS and GND power wires, omitting the D+ and D- data differential pairs. A power-only cable will power the adapter LED, but the operating system will not detect any COM/tty port. Always verify your USB cable transmits data.',

        'mistake_2_title' => 'Mistake 2: Missing or Faulty Drivers',
        'mistake_2_desc' => 'On Windows, UART adapters require matching virtual COM port (VCP) drivers. FTDI chips require the official FTDI CDM driver, CP2102 requires Silicon Labs VCP drivers, and CH340 requires CH341SER. In Windows Device Manager, verify that the device shows up under "Ports (COM & LPT)" without a yellow exclamation mark.',

        'mistake_3_title' => 'Mistake 3: Reversed TX/RX or Inadvertent VCC Short',
        'mistake_3_desc' => 'Connecting TX to TX will cause communication timeouts without damage. However, connecting 5V VCC to a serial data line or shorting VCC to GND can permanently destroy the scooter microcontroller or the USB port. Always check pinouts with a continuity multimeter before powering on.',

        'mistake_4_title' => 'Mistake 4: Connector Misalignment & Forcing Pins',
        'mistake_4_desc' => 'Julet plugs have an internal alignment groove. Forcing connectors without aligning the arrow markings will shear the internal guide or push the thin contact pins inside the housing.',

        // Compatibility & Comparison
        'compat_title' => 'Connector Compatibility Matrix',
        'compat_desc' => 'Newer Xiaomi scooter models are no longer manufactured by Ninebot and therefore cannot be unlocked with Scooterhacking Utility. Instead, the Brightway and LEQI produced models listed below can be unlocked via a cable connection to a PC.<br><br><strong>Currently supported & working cable unlock:</strong><br>Xiaomi Scooter 4 · 4 Lite · 4 Pro (2nd Gen) · 4 Ultra · 5 · 5 Pro · 5 Max · Elite<br><br><strong>Not supported via this method:</strong><br>Xiaomi Scooter 3 Lite · 4 Pro · 4 Pro Max · 4 Pro Plus · 4 Lite (2nd Gen) [Jupoma method only] · 5 Plus · 6 / 6 Lite / 6 Pro / 6 Max / 6 Ultra (all 6th Gen variants currently in testing)',
        'compat_model' => 'Model',
        'compat_type' => 'Connector Standard',
        'compat_os' => 'Compatible with Windows, Linux, and macOS Web Serial / WebUSB environments. Reference: <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" style="color: var(--accent);">wiki.bastelpichi.de/brightway</a>',
        'compat_difference_title' => 'Pin Comparison: M7 Male vs. M7 Female',
        'compat_difference_subtitle' => 'Examine the plug at your handlebar disconnect point to determine gender:',
        'compat_difference_male' => 'M7 MALE (Plug with Pins)',
        'compat_difference_male_badge' => '5 Pins (Male)',
        'compat_male_pin1' => '5 visible gold-plated pins protruding from plug face',
        'compat_male_pin2' => 'Mating keyway notch for twist-proof mechanical insertion',
        'compat_male_pin3' => 'Models: Xiaomi Scooter 4, 4 Ultra, 4 Lite, 5 Max, 5 Pro',
        'compat_difference_female' => 'M7 FEMALE (Socket with Receptacles)',
        'compat_difference_female_badge' => '5 Holes (Female)',
        'compat_female_pin1' => '5 recessed contact receptacles inside insulated collar',
        'compat_female_pin2' => 'Outer guide ridge matching the Male keyway notch',
        'compat_female_pin3' => 'Models: Xiaomi Scooter 4 Pro (2nd Gen), Xiaomi Scooter 5',
        'compat_gen6_title' => 'Xiaomi Elite & 6th Generation / LEQI Hardware Note (M8)',
        'compat_gen6_text' => 'Xiaomi Scooter Elite, 6, and 6 Lite utilize the LEQI architecture with an <strong>M8 Julet connector</strong> (larger diameter than M7). Do not attempt to plug an M7 adapter into an M8 socket. Xiaomi Scooter 4 Lite (2nd Gen) also uses M8 Julet but requires the separate Jupoma unlock method instead of this cable procedure. 6th Generation variants are currently in testing and are not supported for cable flashing at this time.',
        'compat_check_tip_title' => 'Quick Visual Verification Tip',
        'compat_check_tip_desc' => 'Unscrew the 4 handlebar screws, gently lift the handlebar, and unplug the circular connector. Look directly into the plug face coming from the scooter body to count pins and check gender.',

        // FAQ
        'faq_title' => 'Frequently Asked Questions (FAQ)',
        'faq_q1' => 'How do I know which connector type my scooter uses?',
        'faq_a1' => 'Refer to the Compatibility Matrix above or inspect the physical connector by loosening the handlebar head screws. Compare the connector size (M7 = ~7mm, M8 = ~8mm) and whether it has protruding pins (MALE) or receptacle holes (FEMALE).',
        'faq_q2' => 'Do I need advanced soldering skills to flash my scooter?',
        'faq_a2' => 'No soldering to the scooter is necessary if you use a cable with the correct pre-crimped Julet connector. If assembling a cable from individual parts, soldering or secure DuPont pin crimping to the UART board is required.',
        'faq_q3' => 'Can I legally ride a modified scooter on public roads in the EU / Germany?',
        'faq_a3' => 'No. Under EU standards and German regulations (eKFV / StVO), modifying speed limits or engine parameters permanently voids the vehicle operating permit (ABE). Modified scooters may only be operated on private property.',
        'faq_q4' => 'Is this flashing cable a hardware tuning chip?',
        'faq_a4' => 'No. It is simply a passive USB-to-serial communication cable that allows a computer to exchange data with the scooter microcontroller.',
        'faq_q5' => 'Will flashing custom firmware void my manufacturer warranty?',
        'faq_a5' => 'Yes. Any modification of the original firmware voids the manufacturer warranty and service agreements.',
        'faq_q6' => 'Can changes made via serial flashing be reversed?',
        'faq_a6' => 'Yes, provided you flash the authentic stock firmware binary for your exact model back to the microcontroller.',
        'faq_q7' => 'Is there a secret brake lever combination to unlock speed?',
        'faq_a7' => 'No. Brightway and LEQI controllers utilize secure firmware parameters that cannot be bypassed with brake lever combinations; serial firmware reprogramming is necessary.',
        'faq_q8' => 'What is the difference between the Xiaomi 4 Ultra and the 4 Ultra 2?',
        'faq_a8' => 'The <strong>Xiaomi Electric Scooter 4 Ultra</strong> is the EU/international version, while the <strong>Xiaomi Electric Scooter 4 Ultra 2</strong> is tuned for German market limits. Both share the exact same hardware controller (model ID <code>dreame.scooter.p2301</code>) and pinout.',

        // Legal Notices
        'hints_title' => 'Important Legal Notices (EU & Germany)',
        'legal_details' => '<div class="legal-content"><p style="margin: 0 0 16px; font-weight: 700; color: var(--accent); font-size: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">Important Legal Notices & Compliance (EU / Germany)</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">1. Purpose of Documentation</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">This website and hardware guide are provided exclusively for technical education, hardware diagnostics, system analysis, and research purposes. We do not offer or promote commercial modification services or hardware sales.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">2. Regulatory Compliance in Germany (eKFV / StVO / StVZO)</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Under German law (Elektrokleinstfahrzeuge-Verordnung – eKFV, StVO, StVZO), electric scooters permitted on public roads must possess a valid General Operating License (Allgemeine Betriebserlaubnis – ABE) with a design-governed maximum speed of 20 km/h. Any alteration of vehicle parameters, motor power, or maximum speed immediately voids the operating permit (Erlöschen der Betriebserlaubnis gemäß § 19 Abs. 2 StVZO). Operating a modified vehicle on public streets, cycle paths, or public squares is a regulatory offense or criminal act, and results in the complete loss of mandatory third-party liability insurance coverage (Pflichtversicherungsgesetz).</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">3. European Union Machinery & Road Safety Directives</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Across the European Union, national vehicle regulations apply in conjunction with EU Machinery Directive standards (2006/42/EC) and EN 17128. Bypassing manufacturer security restrictions or speed caps is illegal on public roads throughout EU member states.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">4. Disclaimer of Liability</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">All information is provided "as is" without warranty of any kind. The user assumes full individual responsibility for any implementation, testing, and use of hardware or software. Any liability for vehicle damage, bricked microcontrollers, electrical component failure, personal injury, or property damage resulting from following technical specifications is expressly disclaimed.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">5. Non-Commercial Status</p><p style="margin: 0; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">This project operates independently and is neither affiliated with nor endorsed by Xiaomi Corporation, Brightway, or LEQI. No commercial sales, affiliate commissions, or sponsored hardware offerings take place on this platform.</p></div>',

        // Footer
        'footer_impressum' => 'Impressum',
        'footer_privacy' => 'Privacy Policy',
        'footer_agb' => 'Terms of Service',
    ],

    'de' => [
        // Navigation
        'nav_hw' => 'Hardware-Guide',
        'nav_steps' => 'Anleitung',
        'nav_help' => 'Hilfe',
        'nav_wiki' => 'Wiki',
        'nav_changelog' => 'Changelog',
        'btn_tutorial' => 'Anleitung',
        'contact' => 'Kontakt',
        'close' => 'Schließen ✕',

        // Meta & Hero
        'title' => 'Hardware-Guide: Worauf beim Kauf eines Flash-Kabels zu achten ist',
        'desc' => 'Technische Spezifikationen, Pinbelegung, Spannungspegel und Chipsatz-Anforderungen für das Flashen von Xiaomi-, Brightway- und LEQI-Scootern.',
        'guide_badge' => 'Technischer Leitfaden',
        'non_commercial_badge' => 'Nicht-kommerziell / Open Guide',
        'product_title' => 'Hardware-Guide: Worauf beim Kauf eines Flash-Kabels zu achten ist',
        'guide_lead' => 'Firmware-Updates, Diagnosen und Controller-Kommunikation bei Xiaomi E-Scootern erfordern spezifische serielle Schnittstellen-Hardware. Dieser Leitfaden erklärt Chipsätze, Logikpegel, Stecker-Pinouts und typische Stolperfallen beim Zusammenstellen eigener Hardware.',

        // Interactive Model Inspector
        'inspector_title' => 'Scooter-Stecker-Prüfer',
        'choose_model' => 'Wähle dein Scooter-Modell zur Prüfung der Hardware',
        'select_default' => '- Modell auswählen -',
        'conn_label' => 'Erforderlicher Stecker',
        'compat_flashable' => 'Firmware flashbar',
        'flashable_yes' => 'Unterstützt',
        'flashable_no' => 'Nicht flashbar',
        'flashable_testing' => 'Im Test',
        'model_5_note' => '2025er Modelle nutzen evtl. M6',
        'model_6_testing_note' => '6. Generation: Im Test - Flashen möglich, Entsperrung unbestätigt',
        'model_6_unsupported_note' => 'Derzeit keine Flash-Unterstützung',

        // Tabs
        'tab_overview' => 'Übersicht',
        'tab_specs' => 'Wichtige Spezifikationen',
        'tab_mistakes' => 'Häufige Fehler',
        'tab_compat' => 'Kompatibilität',
        'tab_faq' => 'FAQ',
        'tab_hints' => 'Wichtige rechtliche Hinweise',

        // Section: Overview
        'overview_title' => 'Wie die Flash-Hardware funktioniert',
        'overview_p1' => 'Die Motorsteuergeräte (Controller/ECUs) von E-Scootern kommunizieren intern über ein <strong>asynchrones serielles UART-Protokoll</strong>. Da moderne Computer keine RS-232- oder TTL-Schnittstellen mehr besitzen, wird ein <strong>USB-zu-UART-Bridge-Adapter</strong> benötigt, um USB-Befehle in serielle Logiksignale zu übersetzen.',
        'overview_li1' => '<strong>Serielle Bridge:</strong> Wandelt USB-Daten in TTL-Logikimpulse (TX/RX) um, die vom Mikrocontroller (STM32, LKS32, ES32, N32) verarbeitet werden.',
        'overview_li2' => '<strong>Passende Steckverbindung:</strong> Nutzt einen wasserdichten Rundstecker (Julet 5-Pin), der zur Trennstelle am Lenkerholm passt.',
        'overview_li3' => '<strong>Direkte Diagnose:</strong> Ermöglicht Reparaturen (Unbricking), Systemauslesung und das Aufspielen von Original- oder Custom-Firmware.',
        'overview_note' => '<strong>Nicht-kommerzieller Hinweis:</strong> Diese Dokumentation dient rein Bildungs- und Analysezwecken. Wir vertreiben keine Hardware, betreiben keinen Shop und nutzen keine Affiliate-Links.',

        // Section: Key Specifications
        'specs_title' => 'Wichtige Spezifikationen beim Hardware-Kauf',
        'spec_chipset_title' => '1. Chipsatz-Auswahl (FT232RL vs. Alternativen)',
        'spec_chipset_desc' => 'Der USB-UART-Chip steuert Timing, Pufferung und Baudraten-Präzision. Der <strong>FTDI FT232RL</strong> gilt als stabilster Industriestandard mit hervorragender Treiberunterstützung unter Windows, macOS und Linux. Funktionierende Alternativen sind der Silicon Labs <strong>CP2102</strong> sowie der WCH <strong>CH340G/C</strong>. Achtung: Billige FTDI-Fälschungen können Treiberprobleme verursachen (z. B. Fehlercode 10).',

        'spec_voltage_title' => '2. Logik-Spannungspegel (3,3V vs. 5V)',
        'spec_voltage_desc' => 'Moderne Scooter-Mikrocontroller arbeiten auf <strong>3,3V-Logikpegeln</strong>. Viele UART-Adapter haben einen Jumper oder Schalter zur Wahl zwischen 3,3V und 5V. Achte unbedingt darauf, den Adapter auf <strong>3,3V</strong> einzustellen. Ein dauerhafter 5V-Logikpegel an einem 3,3V-RX-Pin kann das Steuergerät irreparabel beschädigen.',

        'spec_pinout_title' => '3. TX / RX Crossover-Pinbelegung',
        'spec_pinout_desc' => 'Die serielle Datenübertragung erfordert eine Kreuzung der Signale: Transmit (TX) des Adapters muss an Receive (RX) des Scooters, und Receive (RX) des Adapters an Transmit (TX) des Scooters. Eine gemeinsame <strong>Masse (GND)</strong> ist für das Bezugspotenzial zwingend erforderlich. Die Stromversorgungsleitung (VCC) bleibt in der Regel unbelegt, da der Scooter beim Einschalten seine eigene Logik speist.',

        'spec_connector_title' => '4. Stecker-Geschlecht & Größe (M7 vs. M8 Julet)',
        'spec_connector_desc' => 'Xiaomi-Scooter verwenden wasserdichte Julet-5-Pin-Steckverbinder mit Führungsnut. Das passende Geschlecht (MALE-Stifte vs. FEMALE-Buchsen) sowie der Durchmesser (M7 bei Brightway vs. M8 bei LEQI / 6. Generation) müssen exakt übereinstimmen.',

        // Section: Common Mistakes
        'mistakes_title' => 'Häufige Fehler & wie man sie vermeidet',
        'mistake_1_title' => 'Fehler 1: Reine Ladekabel ohne Datenleitungen',
        'mistake_1_desc' => 'Viele billige USB-Kabel besitzen nur Adern für VBUS und GND, aber keine Datenleitungen (D+/D-). Die LED des Adapters leuchtet zwar, aber im Betriebssystem wird kein COM-Port erkannt. Verwende immer ein vollwertiges Datenkabel.',

        'mistake_2_title' => 'Fehler 2: Falsche oder fehlende Treiberinstallation',
        'mistake_2_desc' => 'Unter Windows benötigt der UART-Adapter den passenden Virtual-COM-Port-Treiber (z. B. FTDI CDM oder CP210x VCP). Prüfe im Windows-Geräte-Manager unter „Anschlüsse (COM & LPT)“, ob der Adapter fehlerfrei ohne gelbes Ausrufezeichen gelistet ist.',

        'mistake_3_title' => 'Fehler 3: Vertauschte Signal- oder Stromleitungen',
        'mistake_3_desc' => 'Ein Vertauschen von TX und RX führt lediglich zu Kommunikations-Timeouts. Ein versehentliches Anlegen von 5V VCC an eine Datenleitung oder ein Kurzschluss zwischen VCC und GND kann jedoch den Controller oder den USB-Port zerstören. Vor dem Einschalten stets mit dem Multimeter auf Durchgang prüfen.',

        'mistake_4_title' => 'Fehler 4: Falsches Zusammenstecken ohne Ausrichtung',
        'mistake_4_desc' => 'Julet-Stecker besitzen eine feine Führungsnut. Wer den Stecker gewaltsam verdreht hineindrückt, verbiegt die dünnen Kontaktstifte oder zerstört die Dichtung.',

        // Compatibility & Comparison
        'compat_title' => 'Kompatibilitätstabelle der Modelle',
        'compat_desc' => 'Da die neueren Xiaomi-Modelle nicht mehr von Ninebot produziert werden, können sie nicht mit Scooterhacking Utility funktionieren. Stattdessen können diese Modelle (von den Produzenten Brightway und LeQi) mit einer Kabelverbindung am PC entsperrt werden.<br><br><strong>Aktuell klappt das mit diesen Modellen:</strong><br>Xiaomi Scooter 4 · 4 Lite · 4 Pro (2nd Gen) · 4 Ultra · 5 · 5 Pro · 5 Max · Elite<br><br><strong>Dies funktioniert NICHT mit:</strong><br>Xiaomi Scooter 3 Lite · 4 Pro · 4 Pro Max · 4 Pro Plus · 4 Lite (2nd Gen) [nur Jupoma-Methode] · 5 Plus · 6 / 6 Lite / 6 Pro / 6 Max / 6 Ultra (alle 6. Gen. aktuell im Test)',
        'compat_model' => 'Modell',
        'compat_type' => 'Steckertyp',
        'compat_os' => 'Kompatibel mit Windows, Linux und macOS über Web Serial / WebUSB. Referenz: <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" style="color: var(--accent);">wiki.bastelpichi.de/brightway</a>',
        'compat_difference_title' => 'Pin-Vergleich: M7 Male vs. M7 Female',
        'compat_difference_subtitle' => 'Untersuche den Stecker an deinem Lenker, um den richtigen Steckertyp zu bestimmen:',
        'compat_difference_male' => 'M7 MALE (Stecker mit Stiften)',
        'compat_difference_male_badge' => '5 Stifte (Male)',
        'compat_male_pin1' => '5 sichtbare, vergoldete Kontaktstifte ragen aus dem Gehäuse',
        'compat_male_pin2' => 'Führungsnut für verdrehsicheres Einstecken',
        'compat_male_pin3' => 'Modelle: Xiaomi Scooter 4, 4 Ultra, 4 Lite, 5 Max, 5 Pro',
        'compat_difference_female' => 'M7 FEMALE (Buchse mit Öffnungen)',
        'compat_difference_female_badge' => '5 Buchsen (Female)',
        'compat_female_pin1' => '5 vertiefte Kontakthülsen im isolierten Kragen',
        'compat_female_pin2' => 'Äußerer Führungssteg passend zur Nut des Gegenstücks',
        'compat_female_pin3' => 'Modelle: Xiaomi Scooter 4 Pro (2nd Gen), Xiaomi Scooter 5',
        'compat_gen6_title' => 'Hinweis: Elite & 6. Generation / LEQI (M8)',
        'compat_gen6_text' => 'Xiaomi Scooter Elite, 6 und 6 Lite nutzen LEQI-Controller mit einem <strong>M8-Julet-Stecker</strong> (größerer Durchmesser als M7). Ein M7-Kabel passt mechanisch nicht in einen M8-Anschluss. Xiaomi Scooter 4 Lite (2nd Gen) nutzt ebenfalls M8 Julet, erfordert aber die separate Jupoma-Entsperrmethode statt dieses Kabel-Verfahrens. Die 6. Generation befindet sich aktuell im Test und wird derzeit nicht unterstützt.',
        'compat_check_tip_title' => 'Tipp zur Sichtprüfung',
        'compat_check_tip_desc' => 'Löse die 4 Schrauben am Lenkerkopf, hebe den Lenker vorsichtig an und trenne die Rundsteckverbindung. Schau direkt in den Stecker des aus der Lenkstange kommenden Kabels.',

        // FAQ
        'faq_title' => 'Häufig gestellte Fragen (FAQ)',
        'faq_q1' => 'Welchen Stecker benötigt mein Scooter?',
        'faq_a1' => 'Prüfe die obige Tabelle oder löse die 4 Schrauben am Lenkerkopf für eine kurze Sichtprüfung. Vergleiche Durchmesser (M7 ca. 7mm, M8 ca. 8mm) und Geschlecht (Stifte = MALE, Löcher = FEMALE).',
        'faq_q2' => 'Brauche ich fortgeschrittene Elektronik-Kenntnisse?',
        'faq_a2' => 'Nein. Mit einem fertig konfektionierten Kabel mit Julet-Stecker ist kein Löten am Fahrzeug nötig. Bei Eigenbau müssen grundlegende Kenntnisse zu seriellen Schnittstellen (TX/RX/GND) und Treibern vorhanden sein.',
        'faq_q3' => 'Darf ich einen modifizierten Scooter im Straßenverkehr fahren?',
        'faq_a3' => 'Nein. Jede Veränderung von Leistung oder Höchstgeschwindigkeit führt in Deutschland (gemäß eKFV / StVZO) zum sofortigen Erlöschen der Betriebserlaubnis (ABE). Die Nutzung ist nur auf Privatgelände zulässig.',
        'faq_q4' => 'Ist das Flash-Kabel ein Tuning-Chip?',
        'faq_a4' => 'Nein. Es handelt sich um ein reines Diagnose- und Datenübertragungskabel zwischen Computer und Controller.',
        'faq_q5' => 'Erlischt durch das Flashen die Herstellergarantie?',
        'faq_a5' => 'Ja. Veränderungen an der Steuergerätesoftware führen zum Verlust von Garantie- und Gewährleistungsansprüchen gegenüber dem Hersteller.',
        'faq_q6' => 'Kann eine Software-Änderung rückgängig gemacht werden?',
        'faq_a6' => 'Ja, indem die originale Hersteller-Firmware für das jeweilige Modell wieder auf den Controller aufgespielt wird.',
        'faq_q7' => 'Gibt es einen Bremshebel-Code zum Entsperren?',
        'faq_a7' => 'Nein. Brightway- und LEQI-Controller können nicht über Tastenkombinationen freigeschaltet werden. Es ist ein direktes Flashen via UART erforderlich.',
        'faq_q8' => 'Was ist der Unterschied zwischen dem Xiaomi 4 Ultra und dem 4 Ultra 2?',
        'faq_a8' => 'Der <strong>Xiaomi Electric Scooter 4 Ultra</strong> ist die EU-Version, der <strong>4 Ultra 2</strong> die deutsche Version mit StVO-konformer Beschleunigung. Beide besitzen dieselbe Hardware (Modell-ID <code>dreame.scooter.p2301</code>) und dieselbe Steckerbelegung.',

        // Legal Notices
        'hints_title' => 'Wichtige rechtliche Hinweise (EU & Deutschland)',
        'legal_details' => '<div class="legal-content"><p style="margin: 0 0 16px; font-weight: 700; color: var(--accent); font-size: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">Wichtige rechtliche Hinweise (Deutschland & EU)</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">1. Bildungs- und Forschungszweck</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Diese Dokumentation dient ausschließlich Bildungs-, Diagnose- und Forschungszwecken sowie dem Verständnis von Embedded-Hardware. Es werden keine kommerziellen Dienstleistungen oder Produkte angeboten.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">2. Gesetzliche Bestimmungen in Deutschland (eKFV, StVZO, StVO)</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">In Deutschland unterliegen Elektro-Tretroller der <strong>Elektrokleinstfahrzeuge-Verordnung (eKFV)</strong>. Für die Teilnahme am öffentlichen Straßenverkehr ist eine gültige Allgemeine Betriebserlaubnis (ABE) zwingend erforderlich, welche eine bauartbedingte Höchstgeschwindigkeit von maximal 20 km/h vorschreibt. Jegliche Manipulation an der Motorsteuerung oder Anhebung der Höchstgeschwindigkeit führt zum <strong>unmittelbaren Erlöschen der Betriebserlaubnis gemäß § 19 Abs. 2 StVZO</strong>. Das Führen eines Fahrzeugs ohne Betriebserlaubnis im öffentlichen Verkehrsraum stellt eine Ordnungswidrigkeit bzw. Straftat dar und führt zum vollständigen Verlust des Haftpflichtversicherungsschutzes (§ 6 PflVG).</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">3. Vorgaben in der Europäischen Union</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">In den Mitgliedstaaten der EU gelten harmonisierte Sicherheitsnormen (u. a. Maschinenrichtlinie 2006/42/EG sowie EN 17128) in Kombination mit jeweiligen nationalen Straßenverkehrsgesetzen. Die Aufhebung herstellerseitiger Geschwindigkeitsbegrenzungen ist auf öffentlichen Verkehrsflächen in allen EU-Ländern unzulässig.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">4. Vollständiger Haftungsausschluss</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Jegliche Nachbauten, Tests und Eingriffe in Steuergeräte erfolgen auf eigene Verantwortung und Gefahr. Eine Haftung für direkte oder indirekte Schäden an Fahrzeugen, Elektronik, Personen oder Sachwerten ist ausnahmslos ausgeschlossen.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">5. Unabhängigkeit & Nicht-Kommerzialität</p><p style="margin: 0; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Dieses Projekt ist unabhängig und steht in keiner geschäftlichen Beziehung zur Xiaomi Corporation, Brightway oder LEQI. Es werden keinerlei kommerzielle Verkäufe oder Provisionslinks bereitgestellt.</p></div>',

        // Footer
        'footer_impressum' => 'Impressum',
        'footer_privacy' => 'Datenschutzerklärung',
        'footer_agb' => 'AGB',
    ],

    'pl' => [
        // Navigation
        'nav_hw' => 'Przewodnik sprzętowy',
        'nav_steps' => 'Instrukcja',
        'nav_help' => 'Pomoc',
        'nav_wiki' => 'Wiki',
        'nav_changelog' => 'Changelog',
        'btn_tutorial' => 'Instrukcja',
        'contact' => 'Kontakt',
        'close' => 'Zamknij ✕',

        // Meta & Hero
        'title' => 'Przewodnik sprzętowy: Na co zwrócić uwagę przy zakupie kabla do flashowania',
        'desc' => 'Specyfikacje techniczne, wyprowadzenia pinów, poziomy logiczne i wymagania dotyczące chipsetów dla hulajnóg Xiaomi, Brightway i LEQI.',
        'guide_badge' => 'Przewodnik techniczny',
        'non_commercial_badge' => 'Projekt niekomercyjny / Otwarte źródło',
        'product_title' => 'Przewodnik sprzętowy: Na co zwrócić uwagę przy zakupie kabla do flashowania',
        'guide_lead' => 'Flashowanie oprogramowania układowego i diagnostyka sterowników hulajnóg Xiaomi wymagają odpowiedniego adaptera szeregowego. Ten przewodnik wyjaśnia wymagania dotyczące chipsetów, poziomów napięć i złączy.',

        // Interactive Model Inspector
        'inspector_title' => 'Sprawdź wymagane złącze dla swojego modelu',
        'choose_model' => 'Wybierz model hulajnogi',
        'select_default' => '- Wybierz model -',
        'conn_label' => 'Wymagane złącze',
        'compat_flashable' => 'Możliwość flashowania',
        'flashable_yes' => 'Obsługiwany',
        'flashable_no' => 'Brak możliwości',
        'flashable_testing' => 'W fazie testów',
        'model_5_note' => 'Modele 2025 mogą używać złącza M6',
        'model_6_testing_note' => '6. generacja: W fazie testów',
        'model_6_unsupported_note' => 'Obecnie brak wsparcia dla flashowania',

        // Tabs
        'tab_overview' => 'Przegląd',
        'tab_specs' => 'Kluczowe specyfikacje',
        'tab_mistakes' => 'Częste błędy',
        'tab_compat' => 'Kompatybilność',
        'tab_faq' => 'FAQ',
        'tab_hints' => 'Ważne informacje prawne',

        // Section: Overview
        'overview_title' => 'Jak działa sprzęt do programowania',
        'overview_p1' => 'Sterowniki silnika (ESC/ECU) w hulajnogach komunikują się za pośrednictwem asynchronicznego <strong>interfejsu szeregowego UART</strong>. Aby połączyć je z komputerem, wymagany jest <strong>adapter USB-UART</strong> konwertujący pakiety USB na poziomy logiczne TTL.',
        'overview_li1' => '<strong>Konwerter szeregowy:</strong> Przekształca sygnały USB na poziomy logiczne TX/RX procesora (STM32, LKS32, ES32, N32).',
        'overview_li2' => '<strong>Złącze wodoszczelne:</strong> Dopasowane okrągłe złącze Julet 5-pin łączące kierownicę ze sztycą.',
        'overview_li3' => '<strong>Bezpośrednia komunikacja:</strong> Umożliwia naprawę (unbricking), odczyt parametrów oraz wgrywanie oprogramowania.',
        'overview_note' => '<strong>Informacja o charakterze niekomercyjnym:</strong> Dokumentacja ma charakter wyłącznie edukacyjny. Nie sprzedajemy urządzeń ani nie pobieramy prowizji partnerskich.',

        // Section: Key Specifications
        'specs_title' => 'Na co zwrócić uwagę wybierając sprzęt',
        'spec_chipset_title' => '1. Chipset (FT232RL a inne opcje)',
        'spec_chipset_desc' => 'Zalecanym standardem jest <strong>FTDI FT232RL</strong> z uwagi na wysoką stabilność i precyzję taktowania. Dopuszczalne są również <strong>CP2102</strong> i <strong>CH340G/C</strong>. Należy unikać podrabianych układów FTDI powodujących błędy sterowników w systemie Windows.',

        'spec_voltage_title' => '2. Poziomy napięcia logicznego (3.3V a 5V)',
        'spec_voltage_desc' => 'Mikrokontrolery hulajnóg pracują przy logice <strong>3.3V</strong>. Upewnij się, że zworka adaptera ustawiona jest na 3.3V, aby zapobiec uszkodzeniu pinu RX procesora zbyt wysokim napięciem 5V.',

        'spec_pinout_title' => '3. Połączenie krosowane TX / RX',
        'spec_pinout_desc' => 'Linia TX konwertera łączy się z RX hulajnogi, a linia RX konwertera z TX hulajnogi. Wspólna masa (GND) jest bezwzględnie wymagana. Linia VCC zazwyczaj nie jest podłączana.',

        'spec_connector_title' => '4. Typ i rozmiar złącza (Julet M7 a M8)',
        'spec_connector_desc' => 'Hulajnogi Brightway wykorzystują złącza Julet M7 (męskie lub żeńskie), natomiast modele LEQI (np. seria 6 oraz 5 Elite) złącza M8. Złącza te nie są ze sobą kompatybilne.',

        // Section: Common Mistakes
        'mistakes_title' => 'Typowe błędy i sposoby ich unikania',
        'mistake_1_title' => 'Błąd 1: Przewody USB wyłącznie do ładowania',
        'mistake_1_desc' => 'Kable bez linii przesyłu danych D+/D- uniemożliwiają wykrycie adaptera w systemie, mimo że dioda zasilania świeci.',

        'mistake_2_title' => 'Błąd 2: Brak poprawnych sterowników',
        'mistake_2_desc' => 'W Menedżerze urządzeń Windows sprawdź, czy adapter figuruje w sekcji Porty (COM i LPT) bez ostrzeżeń.',

        'mistake_3_title' => 'Błąd 3: Pomylenie pinów lub zwarcie VCC',
        'mistake_3_desc' => 'Zwarcie zasilania do linii danych może trwale uszkodzić mikrokontroler. Zawsze sprawdzaj połączenia multimetrem przed włączeniem zasilania.',

        'mistake_4_title' => 'Błąd 4: Wciskanie złącza na siłę bez dopasowania rowka',
        'mistake_4_desc' => 'Okrągłe złącza Julet mają rowek pozycjonujący. Wciskanie ich pod złym kątem powoduje wygięcie pinów.',

        // Compatibility & Comparison
        'compat_title' => 'Tabela kompatybilności złączy',
        'compat_desc' => 'Nowsze modele hulajnóg Xiaomi nie są już produkowane przez Ninebot i nie można ich odblokować za pomocą Scooterhacking Utility. Zamiast tego modele wyprodukowane przez Brightway i LEQI można odblokować za pomocą połączenia kablowego z komputerem.<br><br><strong>Obecnie obsługiwane i działające odblokowanie kablem:</strong><br>Xiaomi Scooter 4 · 4 Lite · 4 Pro (2nd Gen) · 4 Ultra · 5 · 5 Pro · 5 Max · Elite<br><br><strong>Nieobsługiwane tą metodą:</strong><br>Xiaomi Scooter 3 Lite · 4 Pro · 4 Pro Max · 4 Pro Plus · 4 Lite (2nd Gen) [tylko metoda Jupoma] · 5 Plus · 6 / 6 Lite / 6 Pro / 6 Max / 6 Ultra (wszystkie warianty 6. generacji obecnie w fazie testów)',
        'compat_model' => 'Model',
        'compat_type' => 'Typ złącza',
        'compat_os' => 'Kompatybilność z Windows, Linux i macOS przez Web Serial / WebUSB. Referencja: <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" style="color: var(--accent);">wiki.bastelpichi.de/brightway</a>',
        'compat_difference_title' => 'Porównanie pinów: M7 Male a M7 Female',
        'compat_difference_subtitle' => 'Sprawdź wtyczkę na kierownicy, aby wybrać odpowiednie złącze:',
        'compat_difference_male' => 'M7 MALE (Wtyczka z pinami)',
        'compat_difference_male_badge' => '5 pinów (Męskie)',
        'compat_male_pin1' => '5 widocznych pozłacanych pinów wystających ze złącza',
        'compat_male_pin2' => 'Wewnętrzny rowek pozycjonujący',
        'compat_male_pin3' => 'Modele: Xiaomi 4, 4 Ultra, 4 Lite, 5 Max, 5 Pro',
        'compat_difference_female' => 'M7 FEMALE (Gniazdo z otworami)',
        'compat_difference_female_badge' => '5 otworów (Żeńskie)',
        'compat_female_pin1' => '5 zagłębionych otworów stykowych w kołnierzu',
        'compat_female_pin2' => 'Wypustka pasująca do rowka wtyczki',
        'compat_female_pin3' => 'Modele: Xiaomi 4 Pro (2nd Gen), Xiaomi 5',
        'compat_gen6_title' => 'Uwaga: Elite & generacja 6. / LEQI (M8)',
        'compat_gen6_text' => 'Xiaomi Scooter Elite, 6 i 6 Lite korzystają ze sterowników LEQI ze złączem <strong>M8 Julet</strong> (większa średnica niż M7). Nie podłączaj adaptera M7 do gniazda M8. Xiaomi Scooter 4 Lite (2nd Gen) również wykorzystuje M8 Julet, ale wymaga oddzielnej metody odblokowania Jupoma zamiast tej procedury kablowej. Warianty 6. generacji są obecnie w fazie testów i nie są jeszcze obsługiwane.',
        'compat_check_tip_title' => 'Wskazówka weryfikacji wizualnej',
        'compat_check_tip_desc' => 'Odkręć 4 śruby mostka kierownicy, unieś kierownicę i rozłącz okrągłe złącze, aby sprawdzić piny.',

        // FAQ
        'faq_title' => 'Często zadawane pytania (FAQ)',
        'faq_q1' => 'Jakiego kabla potrzebuje moja hulajnoga?',
        'faq_a1' => 'Sprawdź tabelę powyżej lub rozłącz złącze kierownicy, aby dokonać weryfikacji wizualnej (średnica M7 lub M8 oraz piny męskie/żeńskie).',
        'faq_q2' => 'Czy potrzebuję zaawansowanej wiedzy elektronicznej?',
        'faq_a2' => 'Nie. Stosując gotowy przewód z wtykiem Julet nie trzeba lutować elementów hulajnogi. Wystarczy podłączyć adapter USB i postępować zgodnie z instrukcją.',
        'faq_q3' => 'Czy po modyfikacji można legalnie poruszać się po drogach publicznych?',
        'faq_a3' => 'Nie. Zmiana parametrów prędkości lub mocy powoduje utratę homologacji drogowej. Jazda dozwolona jest wyłącznie na terenie prywatnym.',
        'faq_q4' => 'Czy kabel do flashowania to chip tuningowy?',
        'faq_a4' => 'Nie. Jest to wyłącznie kabel komunikacyjny USB-UART łączący komputer z mikrokontrolerem.',
        'faq_q5' => 'Czy flashowanie unieważnia gwarancję producenta?',
        'faq_a5' => 'Tak. Wszelkie modyfikacje oprogramowania sterownika skutkują utratą gwarancji producenta.',
        'faq_q6' => 'Czy można przywrócić oryginalne oprogramowanie?',
        'faq_a6' => 'Tak, wgrywając z powrotem oryginalny plik firmware (stock) przeznaczony dla danego modelu.',
        'faq_q7' => 'Czy istnieje kombinacja manetek odblokowująca prędkość?',
        'faq_a7' => 'Nie. Sterowniki Brightway i LEQI nie posiadają ukrytych kombinacji klawiszy; wymagają bezpośredniego wgrania oprogramowania przez UART.',
        'faq_q8' => 'Jaka jest różnica między Xiaomi 4 Ultra a 4 Ultra 2?',
        'faq_a8' => 'Xiaomi Scooter 4 Ultra to wersja międzynarodowa, a Ultra 2 to wersja dostosowana do rynku niemieckiego. Oba modele posiadają ten sam sterownik (<code>dreame.scooter.p2301</code>).',

        // Legal Notices
        'hints_title' => 'Ważne informacje prawne (UE i Niemcy)',
        'legal_details' => '<div class="legal-content"><p style="margin: 0 0 16px; font-weight: 700; color: var(--accent); font-size: 16px; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">Ważne informacje prawne (Niemcy i UE)</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">1. Cel edukacyjny i diagnostyczny</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Materiały zawarte na tej stronie mają charakter wyłącznie edukacyjny i służą celom diagnostycznym oraz analizie systemów wbudowanych.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">2. Przepisy w Niemczech i UE</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Wszelkie modyfikacje parametrów pojazdu skutkują natychmiastową utratą dopuszczenia do ruchu (ABE) i ubezpieczenia w Niemczech (eKFV) oraz naruszają przepisy o ruchu drogowym w krajach UE.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">3. Wyłączenie odpowiedzialności</p><p style="margin: 0 0 16px; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Użytkownik podejmuje wszelkie działania na własną odpowiedzialność. Wyklucza się odpowiedzialność za uszkodzenia sprzętu lub inne szkody.</p><p style="margin: 0 0 6px; font-weight: 700; color: var(--text-primary); font-size: 14px;">4. Charakter niekomercyjny</p><p style="margin: 0; color: var(--text-secondary); font-size: 13.5px; line-height: 1.7;">Projekt jest niezależny i nie jest powiązany z Xiaomi Corporation, Brightway ani LEQI. Na stronie nie są prowadzone żadne działania komercyjne.</p></div>',

        // Footer
        'footer_impressum' => 'Impressum',
        'footer_privacy' => 'Polityka prywatności',
        'footer_agb' => 'Regulamin',
    ],
];

function __(string $key): string
{
    global $t, $lang;
    return $t[$lang][$key] ?? $t['en'][$key] ?? $key;
}
