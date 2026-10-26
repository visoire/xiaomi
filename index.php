<?php
declare(strict_types=1);

/* Language routing. Default is English. */
$langs = ['de', 'en', 'pl'];
$lang  = (isset($_GET['lang']) && in_array($_GET['lang'], $langs, true))
       ? $_GET['lang']
       : 'en';

if (!function_exists('e')) {
    function e(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/* Static data */
$videos = [
    ['id' => 'YJl8iXx8V6k'],
    ['id' => 'vq3OMxMlir0'],
    ['id' => '9ldzxTGJYt0'],
    ['id' => 'MEVXANRJ1IM'],
    ['id' => 'ABy94w3M44Q'],
    ['id' => 'LUPODSxsKwI'],
];

$models = [
    ['n' => '4',              'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '4 Lite',         'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '4 Ultra',        'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '4 Pro 2nd Gen',  'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '5',              'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '5 Max',          'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '5 Pro',          'mfr' => 'Brightway', 'ok' => true,  'con' => 'M7'],
    ['n' => '5 Elite',        'mfr' => 'LEQI',      'ok' => true,  'con' => 'M8'],
    ['n' => '6',              'mfr' => 'LEQI',      'ok' => true,  'con' => 'M8'],
    ['n' => '6 Lite',         'mfr' => 'LEQI',      'ok' => true,  'con' => 'M8'],
    ['n' => '6 Pro',          'mfr' => 'LEQI',      'ok' => false, 'con' => 'M8'],
    ['n' => '6 Max',          'mfr' => 'LEQI',      'ok' => false, 'con' => 'M8'],
    ['n' => '6 Ultra',        'mfr' => 'LEQI',      'ok' => false, 'con' => 'M8'],
    ['n' => '3 Lite',         'mfr' => 'Brightway', 'ok' => false, 'con' => 'M7'],
    ['n' => '4 Lite 2nd Gen', 'mfr' => 'LEQI',      'ok' => false, 'con' => 'M8'],
];

/* Translations */
$t = [

/* DEUTSCH */
'de' => [
    'meta_title'  => 'Xiaomi Scooter Tuning Anleitung',
    'meta_desc'   => 'MCU-Firmware auf Xiaomi E-Scootern patchen und flashen. Brightway, LEQI. UART-Verkabelung, Patcher, Flasher und Lösungen für häufige Fehler.',
    'meta_locale' => 'de_DE',
    'nav_hw'      => 'Hardware-Guide',
    'nav_steps'   => 'Anleitung',
    'nav_help'    => 'Hilfe',
    'nav_wiki'    => 'Wiki',
    'nav_patcher' => 'Patcher',
    'nav_flasher' => 'Flasher',
    'nav_setup'   => 'Setup',
    'nav_changelog' => 'Changelog',
    'btn_yt'      => 'Videos',
    'btn_buy'     => 'Hardware-Guide',
    'eyebrow'     => 'Brightway & LEQI',
    'h1'          => 'Tuning Guide',
    'sub'         => 'Du willst deinen Roller schneller machen. Hier steht wie das geht, ohne Umwege.',
    'warn_title' => 'Hinweis',
        'warn_text' => 'Für Bildungszwecke bestimmt. Jede Veränderung von Fahrzeugparametern führt zum Erlöschen der Betriebserlaubnis. Die Nutzung im öffentlichen Straßenverkehr nach Eingriffen in das Steuergerät ist verboten. Die Nutzung von Tuning-Komponenten im öffentlichen Straßenverkehr kann eingeschränkt oder nicht erlaubt sein. Die Verantwortung für den Einbau und die Nutzung liegt ausschließlich beim Besitzer.',
    'badge_ok'    => 'Getestet',
    'badge_tba'   => 'Ausstehend',
    'sec_hw'      => 'Was du brauchst',
    'hw' => [
        ['icon' => 'zap', 'title' => 'UART-Adapter',       'desc' => 'CH340, FT232RL oder CP2102. Auf 5V einstellen.'],
        ['icon' => 'plug', 'title' => 'Dashboard-Kabel',    'desc' => 'Julet 5-Pin. M7 für Brightway, M8 für LEQI & 6. Gen (1:1 wie 5 Elite).'],
        ['icon' => 'wrench', 'title' => 'Inbusschlüssel 3mm', 'desc' => 'Für die vier Schrauben am Lenkerkopf.'],
        ['icon' => 'laptop', 'title' => 'Computer/Laptop',           'desc' => 'Kompatibel mit Windows, macOS und Linux.'],
    ],
    'sec_steps'   => 'Anleitung',
    'steps' => [
        [
            'title' => 'Firmware herunterladen',
            'text'  => 'Öffne mi-fw-info.streamlit.app und such nach deinem Rollermodell. Lad die MCU-Firmware auf deinen Computer. Nur das MCU-Paket, nicht das BLE-Paket.',
            'note'  => null,
            'links' => [['label' => 'mi-fw-info.streamlit.app', 'url' => 'https://mi-fw-info.streamlit.app/']],
        ],
        [
            'title' => 'Firmware patchen',
            'text'  => 'Öffne BW Patcher im Browser. Lad deine MCU-Datei hoch und wähl dein Modell. Aktiviere die Geschwindigkeitsanpassung, dann lad die gepatchte Datei runter.',
            'note'  => 'Begrenze auf 29 km/h im Patcher, wenn du kein akustisches Warnsignal bei hoher Geschwindigkeit willst.',
            'links' => [['label' => 'bw-patcher.streamlit.app', 'url' => 'https://bw-patcher.streamlit.app/']],
        ],
        [
            'title' => 'Roller öffnen',
            'text'  => 'Lös die vier Schrauben am Lenkerkopf mit dem 3-mm-Inbusschlüssel. Heb den Lenker ab. Schalte den Roller ein und aktiviere das Rücklicht dauerhaft (Xiaomi-Home-App oder Taste). Trenne dann den 5-poligen Stecker zwischen Lenker und Basis.',
            'note'  => 'Das Rücklicht muss leuchten, bevor du den Stecker trennst.',
            'links' => [],
        ],
        [
            'title' => 'Adapter anschließen',
            'text'  => 'Verbinde den Julet-5-Pin-Stecker mit dem UART-Adapter. Steck den Adapter in einen USB-Port. Schau im Gerätemanager nach dem COM-Port. Falls kein Port erscheint, installiere den FTDI-Treiber manuell. Verbinde dann den Julet-Stecker mit dem Kabel aus dem Holm. Pfeilmarkierungen beachten.',
            'note'  => 'Wenn der Stecker nicht passt, die untere Hälfte des Gehäuses aufschneiden. Nicht die obere.',
            'links' => [['label' => 'FTDI Treiber', 'url' => 'https://ftdichip.com/wp-content/uploads/2025/03/CDM2123620_Setup.zip']],
        ],
        [
            'title' => 'Firmware flashen',
            'text'  => 'Starte BW Flasher, wähl den COM-Port und lad die gepatchte Datei. Debug-Modus aktivieren. Start drücken. Kabel und Roller während der Übertragung nicht bewegen. Auf die Erfolgsmeldung in der Ausgabe warten.',
            'note'  => null,
            'links' => [['label' => 'BW Flasher Download', 'url' => 'https://github.com/scooterteam/bw-flasher/releases/']],
        ],
        [
            'title' => 'Zusammenbauen',
            'text'  => 'Adapter trennen. Originalstecker wieder verbinden. Lenker einsetzen und Schrauben festziehen. Bremsen und alle Funktionen testen, bevor du fährst.',
            'note'  => null,
            'links' => [],
        ],
    ],
    'sec_help'    => 'Häufige Probleme',
    'faq' => [
        ['q' => 'Fortschritt bleibt bei 0%',            'a' => 'BW Flasher neu starten. Anderen USB-Port und USB-Kabel (z. B. USB-A-zu-USB-C oder USB-C-zu-USB-C) versuchen. Sicherstellen, dass der Roller an ist. FTDI-Treiber neu installieren.'],
        ['q' => 'Ungültige Firmware-Datei',             'a' => 'Nur die MCU-Firmware von mi-fw-info verwenden. Das vollständige Paket oder die BLE-Datei funktioniert nicht.'],
        ['q' => 'Fortschritt stockt bei 5% oder 45%',   'a' => 'Anderen USB-Port, USB-Kabel (z. B. USB-A-zu-USB-C oder USB-C-zu-USB-C) oder einen USB-Hub dazwischenschalten.'],
        ['q' => 'COM-Port wird nicht erkannt',          'a' => 'FTDI-Treiber installieren oder aktualisieren. Anderen USB-Anschluss und USB-Kabel (z. B. USB-A-zu-USB-C oder USB-C-zu-USB-C) versuchen.'],
        ['q' => 'Wie kann ich zur ursprünglichen Firmware zurückkehren?', 'a' => 'Entweder über die offizielle App aktualisieren (das löscht die gepatchte Firmware und installiert die offizielle), oder direkt die ursprüngliche MCU-Firmware von mi-fw-info flashen.'],
        ['q' => 'Nach dem Flashen geht der Roller nur 20 km/h statt 35 km/h', 'a' => 'Versuche den Sportmodus, den Fahrmodus oder beide freizuschalten.'],
        ['q' => 'Max retries reached. Check your connection.', 'a' => 'Bitte führen Sie folgende Schritte noch einmal genau aus: Verkabelung prüfen: RX mit RX, TX mit TX, GND mit GND verbinden, das ist entscheidend. Die Verbindung soll nicht ins Lenkrad Kopf gehen sondern darunter (nicht am Lenker einstecken).'],
        ['q' => 'Brightway Fehlercodes', 'a' => "Brightway Fehlercodes:\n\nError 10 - Kommunikationsfehler des Dashboards.\nError 11 - Controller-Überlastung.\nError 12 - Controller-Fehler.\nError 14 - Gashebel-Kabelfehler.\nError 15 - Bremshebel-Kabelfehler.\nError 18 - Motorfehler.\nError 21 - Batterie-Kommunikationsfehler.\nError 24 - Batterie-Überspannung.\nError 28 - Controller-Fehler.\nError 29 - Controller-Fehler.\nError 39 - Batteriefehler.\nError 40 - Controller-Fehler.\nError 45 - Controller überhitzt.\nError 50 - Batterie-Temperaturfehler.\nError 52 - Batteriefehler.\nError 53 - Batteriefehler.\nError 54 - Hohe Batterietemperatur.\nError 55 - Batterietemperatur ist zu niedrig."],
        ['q' => 'Xiaomi Scooter 4 zeigt Fehler 43 nach dem Flashen (Tuning oder Original-Firmware)', 'a' => "Wenn du einen Xiaomi Electric Scooter 4 (besonders die deutsche DE-Version oder Modelle mit BMS 0111) besitzt und nach dem Flashen der Fehler 43 auf dem Display erscheint - selbst wenn der Flashvorgang bei 100% erfolgreich war (egal ob bei getunter oder originaler Firmware) - benötigt dein Roller eine andere Firmware-Basis.\n\nWähle auf mi-fw-info.streamlit.app das Modell \"Xiaomi scooter 42\" anstelle von \"Xiaomi scooter 4\" aus. Lade die MCU-Firmware für Xiaomi scooter 42 herunter (oder patche diese) und flashe sie auf den Roller. Das Aufspielen der Firmware für Xiaomi scooter 42 behebt den Fehler 43 sofort.\n\nIm Web-Patcher, in der geführten Einrichtung und auf der MCU-Seite gibt es jetzt Xiaomi Electric Scooter 42 und 43 (navee1.scooter.t2209 / t2209g) als eigene Modelle mit passender Original-Firmware. Wähle 42 (bzw. 43) statt Scooter 4."],
    ],
    'modal_title' => 'Video-Tutorials',
    'vid_titles'  => ['Tutorial Teil 1', 'Tutorial Teil 2', 'Tutorial Teil 3', 'Stecker-Guide', 'Tutorial Teil 4', 'Screen Trick (MPH statt KMH)'],
    'foot_made'   => 'made by visoire',
    'foot_disc'   => 'Nur für Bildungszwecke. Modifikationen erlöschen Straßenzulassung und Versicherungsschutz.',
    'credit_scooterteam' => 'Tools and original guide by',
    'credit_bastelpichi' => 'Additional resources and wiki by',
    'download_expl' => 'Die Nutzung dieser Download-Datei ist **vollständig optional**. Du kannst *alle Schritte auch manuell durchführen*, ohne diese Datei zu nutzen. Wichtig: Die Verteilung von Originalfirmware-Dateien verletzt geistiges Eigentum von Brightway und Xiaomi. Nutze daher stets mi-fw-info.streamlit.app für Originalfirmware direkt von den offiziellen Servern. Individuell entwickelte Tools wie BW Flasher und BW Patcher sollten separat von ihren offiziellen Quellen bezogen werden, um stets aktuell zu bleiben und ihre Multi-Plattform-Funktionalität zu nutzen.',
],

/* ENGLISH */
'en' => [
    'meta_title'  => 'Xiaomi Scooter Tuning Guide',
    'meta_desc'   => 'Flash and patch MCU firmware on Xiaomi e-scooters. Brightway, LEQI and 6th gen. UART wiring, patcher, flasher and fixes for the common errors.',
    'meta_locale' => 'en_US',
    'nav_hw'      => 'Hardware Guide',
    'nav_steps'   => 'Tutorial',
    'nav_help'    => 'Help',
    'nav_wiki'    => 'Wiki',
    'nav_patcher' => 'Patcher',
    'nav_flasher' => 'Flasher',
    'nav_setup'   => 'Setup',
    'nav_changelog' => 'Changelog',
    'btn_yt'      => 'Videos',
    'btn_buy'     => 'Hardware Guide',
    'eyebrow'     => 'Brightway & LEQI',
    'h1'          => 'Tuning Guide',
    'sub'         => 'Want to push past the speed limit? Here is how to flash your scooter. No fluff.',
    'warn_title' => 'Important Notice',
        'warn_text' => 'For educational purposes only. Any modification to vehicle parameters will void your operating license immediately. Use on public roads after making changes to the control unit is prohibited. Use of tuning components on public roads may be restricted or not allowed. Responsibility for installation and use lies solely with the owner.',
    'badge_ok'    => 'Tested',
    'badge_tba'   => 'Pending',
    'sec_hw'      => 'What You Need',
    'hw' => [
        ['icon' => 'zap', 'title' => 'UART Adapter',    'desc' => 'CH340, FT232RL or CP2102. Must be set to 5V.'],
        ['icon' => 'plug', 'title' => 'Dashboard Cable', 'desc' => 'Julet 5-pin. M7 for Brightway, M8 for LEQI & 6th Gen (1:1 like 5 Elite).'],
        ['icon' => 'wrench', 'title' => '3mm Allen Key',   'desc' => 'For the four bolts on the handlebar head.'],
        ['icon' => 'laptop', 'title' => 'Computer/Laptop',      'desc' => 'Compatible with Windows, macOS, and Linux.'],
    ],
    'sec_steps'   => 'Tutorial',
    'steps' => [
        [
            'title' => 'Get the Firmware',
            'text'  => 'Open mi-fw-info.streamlit.app and find your scooter model. Download the MCU firmware to your computer. The MCU package only, not the BLE one.',
            'note'  => null,
            'links' => [['label' => 'mi-fw-info.streamlit.app', 'url' => 'https://mi-fw-info.streamlit.app/']],
        ],
        [
            'title' => 'Patch the Firmware',
            'text'  => 'Open BW Patcher in your browser. Upload your MCU file and select your model. Enable the speed limit removal, then download the patched file.',
            'note'  => 'Set the limit to 29 km/h to avoid the acoustic warning at high speeds.',
            'links' => [['label' => 'bw-patcher.streamlit.app', 'url' => 'https://bw-patcher.streamlit.app/']],
        ],
        [
            'title' => 'Open the Scooter',
            'text'  => 'Unscrew the four bolts on the handlebar head with a 3mm Allen key. Lift off the handlebar. Turn on the scooter and enable the rear light permanently (Xiaomi Home app or button). Then disconnect the 5-pin connector between the handlebar and the base.',
            'note'  => 'The rear light must be on before you disconnect the cable.',
            'links' => [],
        ],
        [
            'title' => 'Connect the Adapter',
            'text'  => 'Connect the Julet 5-pin connector to the UART adapter. Plug the adapter into a USB port. Check Device Manager for the COM port. If nothing shows up, install the FTDI driver manually. Then connect the Julet connector to the cable in the stem, following the arrow markings.',
            'note'  => 'If the connector does not fit, cut open the bottom half of the housing. Not the top.',
            'links' => [['label' => 'FTDI Driver', 'url' => 'https://ftdichip.com/wp-content/uploads/2025/03/CDM2123620_Setup.zip']],
        ],
        [
            'title' => 'Flash the Firmware',
            'text'  => 'Open BW Flasher, select your COM port and load the patched file. Enable debug mode. Hit Start. Do not move the cables or scooter during the transfer. Wait for the success message in the output.',
            'note'  => null,
            'links' => [['label' => 'BW Flasher Download', 'url' => 'https://github.com/scooterteam/bw-flasher/releases/']],
        ],
        [
            'title' => 'Reassemble',
            'text'  => 'Disconnect the adapter. Reconnect the original cable. Reinsert the handlebar and tighten the screws. Test brakes and all electronics before riding.',
            'note'  => null,
            'links' => [],
        ],
    ],
    'sec_help'    => 'Common Issues',
    'faq' => [
        ['q' => 'Progress stuck at 0%',        'a' => 'Restart BW Flasher. Try a different USB port and USB cable (e.g., USB-A to USB-C or USB-C to USB-C). Make sure the scooter is on. Reinstall the FTDI driver.'],
        ['q' => 'Invalid Firmware File',        'a' => 'Only use the MCU firmware from mi-fw-info. The full package or BLE file will not work.'],
        ['q' => 'Progress stuck at 5% or 45%', 'a' => 'Try a different USB port, USB cable (e.g., USB-A to USB-C or USB-C to USB-C), or add a USB hub between the adapter and PC.'],
        ['q' => 'COM port not recognized',      'a' => 'Install or update the FTDI driver. Try a different USB port and USB cable (e.g., USB-A to USB-C or USB-C to USB-C).'],
        ['q' => 'How do I revert back to the original firmware?', 'a' => 'Either update via the official app (this will erase the patched firmware and install the official one), or flash the original MCU firmware directly from mi-fw-info.'],
        ['q' => 'After flashing, the scooter only goes 20 km/h instead of 35 km/h', 'a' => 'Try sport mode, drive mode, or unlock both if that doesn\'t work.'],
        ['q' => 'Max retries reached. Check your connection.', 'a' => 'Please perform the following steps exactly again: Check wiring: Connect RX to RX, TX to TX, GND to GND, this is crucial. The connection should not go into the handlebar head but underneath (do not plug into the handlebar).'],
        ['q' => 'Brightway error codes', 'a' => "Brightway error codes:\n\nError 10 - Dashboard's communication error.\nError 11 - Controller overload.\nError 12 - Controller error.\nError 14 - Accelerator cable error.\nError 15 - Brake lever cable error.\nError 18 - Motor error.\nError 21 - Battery's communication error.\nError 24 - Battery overvoltage.\nError 28 - Controller error.\nError 29 - Controller error.\nError 39 - Battery error.\nError 40 - Controller error.\nError 45 - Controller overheated.\nError 50 - Battery temperature error.\nError 52 - Battery error.\nError 53 - Battery error.\nError 54 - High battery temperature.\nError 55 - The battery temperature is too low."],
        ['q' => 'Xiaomi Scooter 4 shows Error 43 after flashing (tuning or original firmware)', 'a' => "If you have a Xiaomi Electric Scooter 4 (especially the German DE version or models with BMS 0111) and the display shows Error 43 after flashing - even though the flash reached 100% success with tuned or original firmware - your scooter needs a different firmware base.\n\nSolution: On mi-fw-info.streamlit.app, you must select \"Xiaomi scooter 42\" instead of \"Xiaomi scooter 4\". Download the MCU firmware for Xiaomi scooter 42 (or patch that file) and flash it to the scooter. Flashing the Xiaomi scooter 42 firmware resolves Error 43 immediately.\n\nThe web Patcher, Guided Setup and MCU page now offer Xiaomi Electric Scooter 42 and 43 (navee1.scooter.t2209 / t2209g) as their own models with the matching stock firmware bundled, so you no longer have to guess. Pick 42 (or 43 for the 43 variant) instead of Scooter 4. If Error 43 persists: check that the firmware was not flashed with the wrong 4-series image, re-flash the correct stock image once to restore the controller, then apply your patches."],
    ],
    'modal_title' => 'Video Tutorials',
    'vid_titles'  => ['Tutorial Part 1', 'Tutorial Part 2', 'Tutorial Part 3', 'Connector Guide', 'Tutorial Part 4', 'Screen Trick (MPH instead of KMH)'],
    'foot_made'   => 'made by visoire',
    'foot_disc'   => 'For educational purposes only. Modifications void road approval and insurance.',
    'credit_scooterteam' => 'Tools and original guide by',
    'credit_bastelpichi' => 'Additional resources and wiki by',
    'download_expl' => 'Using this download file is **completely optional**. You can *do all steps manually* without this file. Important: Distributing original firmware files violates the intellectual property of Brightway and Xiaomi. Therefore, always use mi-fw-info.streamlit.app to download original firmware directly from the official servers. Individually developed tools like BW Flasher and BW Patcher should be obtained separately from their official sources to stay up-to-date and retain their multi-platform functionality.',
],

/* POLSKI */
'pl' => [
    'meta_title'  => 'Xiaomi Scooter, poradnik tuningu',
    'meta_desc'   => 'Patchowanie i flashowanie firmware MCU w hulajnogach Xiaomi. Brightway, LEQI. Podłączenie UART, patcher, flasher i typowe błędy.',
    'meta_locale' => 'pl_PL',
    'nav_hw'      => 'Przewodnik sprzętowy',
    'nav_steps'   => 'Instrukcja',
    'nav_help'    => 'Pomoc',
    'nav_wiki'    => 'Wiki',
    'nav_patcher' => 'Patcher',
    'nav_flasher' => 'Flasher',
    'nav_setup'   => 'Kreator',
    'nav_changelog' => 'Dziennik zmian',
    'btn_yt'      => 'Filmy',
    'btn_buy'     => 'Przewodnik sprzętowy',
    'eyebrow'     => 'Brightway & LEQI',
    'h1'          => 'Tuning Guide',
    'sub'         => 'Chcesz pojechać szybciej? Tu masz instrukcję. Bez owijania w bawełnę.',
    'warn_title' => 'Ważna informacja',
        'warn_text' => 'Tylko do celów edukacyjnych. Każda zmiana parametrów pojazdu prowadzi do natychmiastowego utracenia homologacji. Użycie na drogach publicznych po ingerencji w sterownik jest zabronione. Użycie komponentów tuningu na drogach publicznych może być ograniczone lub niedozwolone. Odpowiedzialność za montaż i użycie leży wyłącznie u właściciela.',
    'badge_ok'    => 'Przetestowany',
    'badge_tba'   => 'Oczekujący',
    'sec_hw'      => 'Czego potrzebujesz',
    'hw' => [
        ['icon' => 'zap', 'title' => 'Adapter UART',        'desc' => 'CH340, FT232RL lub CP2102. Ustawić na 5V.'],
        ['icon' => 'plug', 'title' => 'Kabel',       'desc' => 'Julet 5-pin. M7 dla Brightway, M8 dla LEQI i 6. gen (1:1 jak 5 Elite).'],
        ['icon' => 'wrench', 'title' => 'Klucz imbusowy 3mm', 'desc' => 'Do czterech śrub na główce kierownicy.'],
        ['icon' => 'laptop', 'title' => 'Komputer/Laptop',   'desc' => 'Kompatybilny z systemami Windows, macOS i Linux.'],
    ],
    'sec_steps'   => 'Instrukcja',
    'steps' => [
        [
            'title' => 'Pobierz firmware',
            'text'  => 'Wejdź na mi-fw-info.streamlit.app i znajdź swój model. Pobierz firmware MCU na komputer. Tylko paczka MCU, nie BLE.',
            'note'  => null,
            'links' => [['label' => 'mi-fw-info.streamlit.app', 'url' => 'https://mi-fw-info.streamlit.app/']],
        ],
        [
            'title' => 'Patchuj firmware',
            'text'  => 'Otwórz BW Patcher w przeglądarce. Wgraj plik MCU i wybierz model. Aktywuj usunięcie ograniczenia prędkości i pobierz zmodyfikowany plik.',
            'note'  => 'Ustaw limit na 29 km/h, żeby uniknąć sygnału dźwiękowego przy wysokich prędkościach.',
            'links' => [['label' => 'bw-patcher.streamlit.app', 'url' => 'https://bw-patcher.streamlit.app/']],
        ],
        [
            'title' => 'Otwórz hulajnogę',
            'text'  => 'Odkręć cztery śruby na główce kierownicy kluczem imbusowym 3mm. Zdejmij kierownicę. Włącz hulajnogę i aktywuj tylne światło na stałe (aplikacja Xiaomi Home lub przycisk). Odłącz 5-pinową wtyczkę między kierownicą a podstawą.',
            'note'  => 'Tylne światło musi być włączone przed odłączeniem kabla.',
            'links' => [],
        ],
        [
            'title' => 'Podłącz adapter',
            'text'  => 'Podłącz wtyczkę Julet 5-pin do adaptera UART. Wsuń adapter do portu USB. W Menedżerze urządzeń sprawdź port COM. Jeśli nic nie pojawia się, zainstaluj sterownik FTDI ręcznie. Podłącz wtyczkę Julet do kabla w rurze, zgodnie ze strzałkami.',
            'note'  => 'Jeśli wtyczka Julet nie pasuje, odetnij dolną połowę obudowy. Nie górną.',
            'links' => [['label' => 'Sterownik FTDI', 'url' => 'https://ftdichip.com/wp-content/uploads/2025/03/CDM2123620_Setup.zip']],
        ],
        [
            'title' => 'Wgraj firmware',
            'text'  => 'Otwórz BW Flasher, wybierz port COM i załaduj zmodyfikowany plik. Włącz tryb debugowania. Naciśnij Start. Nie ruszaj kabli ani hulajnogi podczas transferu. Czekaj na komunikat o powodzeniu.',
            'note'  => null,
            'links' => [['label' => 'BW Flasher Download', 'url' => 'https://github.com/scooterteam/bw-flasher/releases/']],
        ],
        [
            'title' => 'Złóż z powrotem',
            'text'  => 'Odłącz adapter. Podłącz z powrotem oryginalny kabel. Włóż kierownicę i dokręć śruby. Sprawdź hamulce i elektronikę przed jazdą.',
            'note'  => null,
            'links' => [],
        ],
    ],
    'sec_help'    => 'Częste problemy',
    'faq' => [
        ['q' => 'Postęp utknął na 0%',           'a' => 'Uruchom ponownie BW Flasher. Spróbuj innego portu USB i kabla USB (np. USB-A na USB-C lub USB-C na USB-C). Upewnij się, że hulajnoga jest włączona. Przeinstaluj sterownik FTDI.'],
        ['q' => 'Nieprawidłowy plik firmware',    'a' => 'Używaj tylko firmware MCU z mi-fw-info. Pełna paczka ani plik BLE nie zadziałają.'],
        ['q' => 'Postęp utknął na 5% lub 45%',   'a' => 'Spróbuj innego portu USB, kabla USB (np. USB-A na USB-C lub USB-C na USB-C) lub dodaj hub USB między adapterem a komputerem.'],
        ['q' => 'Port COM nie jest rozpoznawany', 'a' => 'Zainstaluj lub zaktualizuj sterownik FTDI. Spróbuj innego portu USB i kabla USB (np. USB-A na USB-C lub USB-C na USB-C).'],
        ['q' => 'Jak wrócić do oryginalnego firmware?', 'a' => 'Albo zaktualizuj przez oficjalną aplikację (to usunie zmodyfikowany firmware i zainstaluje oficjalny), albo wgraj bezpośrednio oryginalny firmware MCU z mi-fw-info.'],
        ['q' => 'Po wgraniu hulajnoga jedzie tylko 20 km/h zamiast 35 km/h', 'a' => 'Spróbuj trybu sportowego, trybu jazdy lub odblokuj oba, jeśli to nie pomoże.'],
        ['q' => 'Max retries reached. Check your connection.', 'a' => 'Proszę wykonać następujące kroki ponownie dokładnie: Sprawdź okablowanie: Połącz RX z RX, TX z TX, GND z GND, to ma kluczowe znaczenie. Połączenie nie powinno iść do głowy kierownicy, ale pod nią (nie wkładaj do kierownicy).'],
        ['q' => 'Kody błędów Brightway', 'a' => "Kody błędów Brightway:\n\nError 10 - Błąd komunikacji wyświetlacza (Dashboard).\nError 11 - Przeciążenie kontrolera.\nError 12 - Błąd kontrolera.\nError 14 - Błąd manetki gazu / kabla przyspieszenia.\nError 15 - Błąd klamki hamulca / kabla hamulca.\nError 18 - Błąd silnika.\nError 21 - Błąd komunikacji baterii.\nError 24 - Przepięcie / za wysokie napięcie baterii.\nError 28 - Błąd kontrolera.\nError 29 - Błąd kontrolera.\nError 39 - Błąd baterii.\nError 40 - Błąd kontrolera.\nError 45 - Przegrzanie kontrolera.\nError 50 - Błąd temperatury baterii.\nError 52 - Błąd baterii.\nError 53 - Błąd baterii.\nError 54 - Wysoka temperatura baterii.\nError 55 - Zbyt niska temperatura baterii."],
        ['q' => 'Xiaomi Scooter 4 wyświetla błąd 43 po flashowaniu (tuning lub oryginalny firmware)', 'a' => "Jeśli masz Xiaomi Electric Scooter 4 (szczególnie wersję niemiecką DE lub egzemplarze z BMS 0111) i po wgraniu oprogramowania na wyświetlaczu pojawia się błąd 43 - nawet jeśli flashowanie zakończyło się w 100% sukcesem (zarówno przy tuningu, jak i powrocie do oryginalnego oprogramowania) - Twoja hulajnoga wymaga innej bazy firmware.\n\nRozwiązanie: Na stronie mi-fw-info.streamlit.app musisz wybrać model \"Xiaomi scooter 42\" zamiast \"Xiaomi scooter 4\". Pobierz firmware MCU dla Xiaomi scooter 42 (lub spatchuj ten plik) i wgraj do hulajnogi. Wgranie oprogramowania dla Xiaomi scooter 42 natychmiast usuwa błąd 43.\n\nW Patcherze, kreatorze i na stronie MCU są teraz Xiaomi Electric Scooter 42 i 43 (navee1.scooter.t2209 / t2209g) jako osobne modele z właściwym firmware. Wybierz 42 (lub 43) zamiast Scooter 4."],
    ],
    'modal_title' => 'Filmy instruktażowe',
    'vid_titles'  => ['Tutorial cz. 1', 'Tutorial cz. 2', 'Tutorial cz. 3', 'Poradnik wtyczek', 'Tutorial cz. 4', 'Screen Trick (MPH zamiast KMH)'],
    'foot_made'   => 'made by visoire',
    'foot_disc'   => 'Wyłącznie do celów edukacyjnych. Modyfikacje unieważniają homologację i ubezpieczenie.',
    'credit_scooterteam' => 'Narzędzia i oryginalna instrukcja od',
    'credit_bastelpichi' => 'Dodatkowe zasoby i wiki od',
    'download_expl' => 'Użycie tej pliku do pobrania jest **całkowicie opcjonalne**. Możesz *wykonać wszystkie kroki ręcznie*, bez tego pliku. Ważne: Dystrybucja oryginalnych plików firmware narusza prawa własności intelektualnej Brightway i Xiaomi. Dlatego zawsze używaj mi-fw-info.streamlit.app, aby pobierać oryginalny firmware bezpośrednio z oficjalnych serwerów. Indywidualnie opracowane narzędzia, takie jak BW Flasher i BW Patcher, należy pobierać oddzielnie z ich oficjalnych źródeł, aby pozostać aktualnym i zachować ich wieloplatformową funkcjonalność.',
],

]; // end $t

$tx = $t[$lang];

/* Derived stats, computed from real data */
$nTested = count(array_filter($models, static fn($m) => $m['ok'] === true));
$nSteps  = count($tx['steps']);
$nTools  = count($tx['hw']);

/* Inline microcopy that does not warrant a full translation array */
$L = static function (string $de, string $en, string $pl) use ($lang): string {
    return $lang === 'de' ? $de : ($lang === 'pl' ? $pl : $en);
};
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php
$baseUrl = 'https://vivalet.xyz/xiaomi';
$canonicalUrl = $baseUrl . '/?lang=' . $lang;
$ogImage    = $baseUrl . '/assets/images/xiaomi.jpg';
?>
<meta name="theme-color" content="#38bdf8">
<title><?= e($tx['meta_title']) ?></title>
<meta name="description" content="<?= e($tx['meta_desc']) ?>">
<link rel="canonical" href="<?= e($canonicalUrl) ?>">
<link rel="alternate" hreflang="en" href="<?= e($baseUrl . '/?lang=en') ?>">
<link rel="alternate" hreflang="de" href="<?= e($baseUrl . '/?lang=de') ?>">
<link rel="alternate" hreflang="pl" href="<?= e($baseUrl . '/?lang=pl') ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($baseUrl . '/') ?>">
<meta property="og:type" content="article">
<meta property="og:site_name" content="visoire">
<meta property="og:locale" content="<?= e($tx['meta_locale']) ?>">
<meta property="og:title" content="<?= e($tx['meta_title']) ?>">
<meta property="og:description" content="<?= e($tx['meta_desc']) ?>">
<meta property="og:url" content="<?= e($canonicalUrl) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="192x192" href="assets/img/visoire/logo.png">
<link rel="apple-touch-icon" href="assets/images/xiaomi.jpg">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/lucide@0.479.0/dist/umd/lucide.min.js"></script>
<style>
/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: #0f0f0f;
}
::-webkit-scrollbar-thumb {
  background: #2a2a2a;
  border-radius: 999px;
  transition: background 0.2s;
}
::-webkit-scrollbar-thumb:hover {
  background: #38bdf8;
}
* {
  scrollbar-width: thin;
  scrollbar-color: #2a2a2a #0f0f0f;
}

/* ============================================================
   Design tokens, light visual language
   ============================================================ */
:root{
  --primary:#38bdf8;
  --primary-hover:#0ea5e9;
  --primary-dim:rgba(56, 189, 248,.07);
  --primary-line:rgba(56, 189, 248,.16);
  --bg:#080808;
  --surface:#0f0f0f;
  --surface-2:#141414;
  --ink:#ffffff;
  --text:#a0a0a0;
  --muted:#666666;
  --faint:#333333;
  --border:#1a1a1a;
  --border-2:#2a2a2a;
  --red:#f87171;
  --red-bg:rgba(248,113,113,.08);
  --red-line:rgba(248,113,113,.2);
  --amber:#fbbf24;
  --amber-bg:rgba(251,191,36,.08);
  --amber-line:rgba(251,191,36,.2);
  --green:#10b981;
  --green-bg:rgba(16,185,129,.12);
  --r-sm:10px;
  --r-md:16px;
  --r-lg:22px;
  --r-xl:28px;
  --r-pill:50px;
  --sh-sm:0 1px 2px rgba(0,0,0,.5);
  --sh-md:0 8px 24px -10px rgba(0,0,0,.6),0 2px 6px -2px rgba(0,0,0,.3);
  --sh-lg:0 30px 60px -22px rgba(0,0,0,.8);
  --fh:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,sans-serif;
  --fb:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,sans-serif;
  --fm:'JetBrains Mono',ui-monospace,monospace;
}

/* ============================================================
   Reset and base
   ============================================================ */
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{
  background:var(--bg);
  color:var(--text);
  font-family:var(--fb);
  font-size:16px;
  line-height:1.6;
  -webkit-font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
  overflow-x:hidden;
}
h1,h2,h3,h4{font-family:var(--fh);color:var(--ink);font-weight:800;line-height:1.12;letter-spacing:-.02em}
a{color:var(--primary);text-decoration:none}
button{font-family:inherit;cursor:pointer;border:none;background:none;color:inherit}
img,svg{display:block;max-width:100%}
::selection{background:rgba(0,86,255,.16)}

/* ============================================================
   Layout primitives
   ============================================================ */
.wrap{width:100%;max-width:1120px;margin:0 auto;padding:0 20px}
section{padding:64px 0}
section + section{border-top:1px solid var(--border)}

.eyebrow{
  display:inline-flex;align-items:center;gap:7px;
  background:var(--surface);
  border:1px solid var(--border);
  color:var(--muted);
  font-family:var(--fm);
  font-size:11px;font-weight:500;
  letter-spacing:.08em;text-transform:uppercase;
  padding:6px 13px;border-radius:var(--r-pill);
  margin-bottom:16px;
}
.eyebrow::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--primary)}
.sec-head{text-align:center;max-width:680px;margin:0 auto 44px}
.sec-head h2{font-size:clamp(27px,4.6vw,40px);font-weight:800}
.sec-head p{color:var(--muted);font-size:15px;margin-top:12px}

/* ============================================================
   Buttons
   ============================================================ */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  min-height:46px;padding:0 22px;
  border-radius:var(--r-pill);
  font-weight:600;font-size:14px;
  white-space:nowrap;
  transition:transform .2s ease,background .2s ease,box-shadow .2s ease,border-color .2s ease,color .2s ease;
}
.btn:active{transform:translateY(0)}
.btn-primary{background:var(--primary);color:#000;box-shadow:0 6px 18px -8px rgba(56, 189, 248,.4)}
.btn-primary:hover{background:var(--primary-hover);transform:translateY(-2px)}
.btn-dark{background:var(--surface-2);color:var(--ink);border:1px solid var(--border-2)}
.btn-dark:hover{background:var(--border-2);transform:translateY(-2px)}
.btn-ghost{background:transparent;color:var(--text);border:1px solid var(--border-2)}
.btn-ghost:hover{border-color:var(--primary);color:var(--primary);background:rgba(255,255,255,0.05);transform:translateY(-2px)}
.btn-yt{background:#ff2d2d;color:#fff}
.btn-yt:hover{background:#e00000;transform:translateY(-2px)}
.btn-cancel{background:#ff2d2d;color:#fff}
.btn-cancel:hover{background:#e00000;transform:translateY(-2px)}

/* ============================================================
   Header
   ============================================================ */
.hdr{
  position:sticky;top:0;z-index:60;
  background:rgba(8,8,8,.8);
  backdrop-filter:saturate(160%) blur(14px);
  border-bottom:1px solid transparent;
  transition:border-color .3s,box-shadow .3s,background .3s;
  animation:dropIn .6s ease both;
}
.hdr.scrolled{border-bottom-color:var(--border);box-shadow:0 6px 22px -16px rgba(0,0,0,.6);background:rgba(8,8,8,.95)}
@keyframes dropIn{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:none}}
.hdr-in{max-width:1120px;margin:0 auto;padding:13px 20px;display:flex;align-items:center;gap:14px}
.logo{
  display:inline-flex;align-items:center;gap:9px;
  font-family:var(--fh);font-weight:800;font-size:18px;color:var(--ink);
  letter-spacing:-.02em;flex-shrink:0;
}
.logo-mark{
  width:30px;height:30px;border-radius:9px;flex-shrink:0;
  background:linear-gradient(140deg,var(--primary),var(--primary-hover));
  display:grid;place-items:center;color:#000;
  box-shadow:0 6px 14px -6px rgba(56, 189, 248,.4);
}
.logo-mark svg{width:17px;height:17px}
.hdr-nav{display:none;gap:4px;margin-left:8px}
.hdr-nav a{
  color:var(--text);font-weight:500;font-size:14.5px;
  padding:8px 12px;border-radius:9px;
  transition:color .2s,background .2s;
}
.hdr-nav a:hover{color:var(--primary);background:var(--surface)}
.hdr-right{display:flex;align-items:center;gap:8px;margin-left:auto}
.hdr-right .btn{min-height:42px;padding:0 16px;font-size:13.5px}

.lang-sw{display:none;align-items:center;gap:2px;background:var(--surface-2);border:1px solid var(--border-2);border-radius:var(--r-pill);padding:3px}
.lang-btn{
  font-family:var(--fh);font-size:12px;font-weight:600;color:var(--text);
  padding:6px 11px;border-radius:var(--r-pill);transition:color .2s,background .2s;cursor:pointer;text-decoration:none
}
.lang-btn:hover{color:var(--ink)}
.lang-btn.active{color:#000;background:var(--primary)}

.burger{
  display:inline-flex;flex-direction:column;justify-content:center;align-items:center;gap:5px;
  width:44px;height:44px;flex-shrink:0;
  border:1px solid var(--border-2);border-radius:11px;background:var(--surface-2);
}
.burger span{width:19px;height:2px;background:var(--ink);border-radius:2px;transition:transform .35s,opacity .25s}
.burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.burger.open span:nth-child(2){opacity:0}
.burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* Mobile slide-in nav */
.nav-overlay{position:fixed;inset:0;z-index:65;background:rgba(15,23,41,.4);opacity:0;pointer-events:none;transition:opacity .3s}
.nav-overlay.open{opacity:1;pointer-events:auto}
.mob-nav{
  position:fixed;top:0;right:0;z-index:70;
  height:100dvh;width:82%;max-width:340px;
  background:var(--surface);border-left:1px solid var(--border);
  box-shadow:-24px 0 50px -24px rgba(0,0,0,.8);
  display:flex;flex-direction:column;padding:84px 24px 28px;gap:6px;
  transform:translateX(100%);transition:transform .4s cubic-bezier(.2,.8,.2,1);
  overflow-y:auto;
}
.mob-nav.open{transform:none}
.mob-nav a.mob-link{
  font-family:var(--fh);font-weight:600;font-size:18px;color:var(--ink);
  padding:15px 6px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
}
.mob-nav a.mob-link:hover{color:var(--primary)}
.mob-nav a.mob-link::after{content:"→";color:var(--faint);font-size:15px}
.mob-bottom{margin-top:auto;padding-top:22px;display:flex;flex-direction:column;gap:14px}
.mob-lang{display:flex;gap:8px}
.mob-lang .lang-btn{flex:1;text-align:center;justify-content:center;border:1px solid var(--border);background:var(--surface);padding:11px 0}
.mob-lang .lang-btn.active{background:var(--primary);border-color:var(--primary);color:#fff}

/* ============================================================
   Hero
   ============================================================ */
.hero{position:relative;padding:48px 0 18px;overflow:hidden}
.hero::before{
  content:"";position:absolute;inset:0;z-index:-1;pointer-events:none;
  background:
    radial-gradient(circle at 1px 1px,rgba(255,255,255,.03) 1px,transparent 0);
  background-size:26px 26px;
  -webkit-mask-image:linear-gradient(180deg,#000,transparent 78%);
          mask-image:linear-gradient(180deg,#000,transparent 78%);
}
.hero-grid{display:grid;grid-template-columns:1fr;gap:36px;align-items:center}
.hero h1{font-size:clamp(40px,10vw,68px);font-weight:900;letter-spacing:-.035em;line-height:1.02;margin-bottom:18px}
.hero h1 .accent{color:var(--primary)}
.hero-lead{font-size:17px;color:var(--text);max-width:480px;margin-bottom:26px}
.hero-cta{display:flex;flex-wrap:wrap;gap:10px}
.hero-meta{display:flex;flex-wrap:wrap;gap:10px 22px;margin-top:24px;font-size:13px;color:var(--muted)}
.hero-meta span{display:inline-flex;align-items:center;gap:7px;font-family:var(--fm)}
.hero-meta span::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--green)}

/* Terminal mock */
.terminal{
  background:#0e1422;border:1px solid #1d2740;border-radius:var(--r-lg);
  box-shadow:var(--sh-lg);overflow:hidden;
  font-family:var(--fm);
  max-width:460px;width:100%;margin-inline:auto;
  animation:floatY 7s ease-in-out infinite;
}
@keyframes floatY{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
.term-bar{display:flex;align-items:center;gap:8px;padding:13px 16px;background:#111a2e;border-bottom:1px solid #1d2740}
.term-dots{display:flex;gap:6px}
.term-dots i{width:11px;height:11px;border-radius:50%;display:block}
.term-dots i:nth-child(1){background:#ff5f57}
.term-dots i:nth-child(2){background:#febc2e}
.term-dots i:nth-child(3){background:#28c840}
.term-title{color:#7e8aa6;font-size:12px;margin-left:6px}
.term-body{padding:18px 18px 20px;font-size:12.5px;line-height:1.85;color:#c4cde0}
.term-body .t-mut{color:#5c6786}
.term-body .t-ok{color:#3fd07f}
.term-body .t-warn{color:#f4b740}
.term-bar-prog{height:7px;border-radius:6px;background:#1d2740;overflow:hidden;margin:10px 0}
.term-bar-prog i{display:block;height:100%;width:0;border-radius:6px;background:linear-gradient(90deg,var(--primary),#28c840);animation:fill 2.6s 1s cubic-bezier(.4,0,.2,1) forwards}
@keyframes fill{to{width:100%}}
/* YouTube modal */
.modal{
  position:fixed;inset:0;z-index:100;display:none;place-items:center;
  background:rgba(15,23,41,.55);backdrop-filter:blur(8px);padding:18px;
  animation:fadeIn .2s ease;
}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal.open{display:grid}
.modal-box{
  background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-xl);
  width:100%;max-width:900px;max-height:90vh;overflow:hidden;
  display:flex;flex-direction:column;box-shadow:var(--sh-lg);
  animation:popUp .28s cubic-bezier(.2,.8,.2,1);
}
@keyframes popUp{from{opacity:0;transform:translateY(18px) scale(.98)}to{opacity:1;transform:none}}
.modal-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 20px;border-bottom:1px solid var(--border);
}
.modal-head h3{
  font-family:var(--fh);font-weight:800;font-size:18px;color:var(--ink);display:flex;align-items:center;gap:10px;
}
.yt-dot{
  width:10px;height:10px;border-radius:50%;background:#ff2d2d;flex-shrink:0;
}
.modal-close{
  width:44px;height:44px;min-width:44px;min-height:44px;border-radius:var(--r-md);background:var(--surface);border:1px solid var(--border-2);
  display:grid;place-items:center;color:var(--text);transition:background .2s,border-color .2s,color .2s;cursor:pointer;
}
.modal-close:hover{background:var(--primary);border-color:var(--primary);color:#000}
.videos-grid{
  display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;
  padding:20px;overflow-y:auto;
}
.video-item{
  display:flex;flex-direction:column;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);padding:12px;
}
.video-label{
  font-family:var(--fm);font-size:12px;color:var(--muted);
}
.video-frame{
  aspect-ratio:16/9;border-radius:var(--r-sm);
}
/* Warning band */
.warn-band{padding:0 0 8px;border-top:none!important}
#hardware{border-top:none!important}
.warning{
  max-width:760px;margin:0 auto;
  background:var(--red-bg);border:1px solid var(--red-line);
  border-radius:var(--r-md);padding:18px 20px;
  display:flex;gap:14px;align-items:flex-start;
}
.warn-icon{
  flex-shrink:0;width:34px;height:34px;border-radius:9px;
  background:var(--red-bg);color:var(--red);
  display:grid;place-items:center;font-size:17px;
}
.warning strong{display:block;color:var(--red);font-family:var(--fm);font-size:11.5px;text-transform:uppercase;letter-spacing:.07em;margin-bottom:5px}
.warning p{font-size:14px;color:var(--text);line-height:1.55}

/* ============================================================
   Stats strip
   ============================================================ */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;max-width:720px;margin:0 auto}
.stat{
  background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-md);
  padding:22px 16px;text-align:center;transition:transform .25s,box-shadow .25s;
}
.stat:hover{transform:translateY(-3px);box-shadow:var(--sh-md)}
.stat-num{font-family:var(--fh);font-weight:900;font-size:clamp(30px,7vw,42px);color:var(--ink);letter-spacing:-.03em;line-height:1}
.stat-lbl{margin-top:7px;font-size:12.5px;color:var(--muted)}

/* ============================================================
   Models
   ============================================================ */
.models-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px}
.model{
  background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-md);
  padding:16px 16px 14px;position:relative;
  transition:transform .22s,box-shadow .22s,border-color .22s;
}
.model:hover{transform:translateY(-3px);box-shadow:var(--sh-md);border-color:var(--border-2)}
.model.pending{opacity:.72}
.model-top{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:10px}
.model-name{font-family:var(--fh);font-weight:700;font-size:15px;color:var(--ink)}
.model-con{font-family:var(--fm);font-size:10.5px;font-weight:600;color:var(--primary);background:var(--primary-dim);border:1px solid var(--primary-line);padding:2px 7px;border-radius:6px;flex-shrink:0}
.model-foot{display:flex;align-items:center;justify-content:space-between;gap:8px}
.model-mfr{font-family:var(--fm);font-size:11px;color:var(--faint)}
.model-status{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600}
.model-status::before{content:"";width:7px;height:7px;border-radius:50%}
.model-status.ok{color:var(--green)}
.model-status.ok::before{background:var(--green);box-shadow:0 0 0 3px var(--green-bg)}
.model-status.tba{color:var(--muted)}
.model-status.tba::before{background:var(--faint)}

/* ============================================================
   Hardware cards
   ============================================================ */
.hw-grid{display:grid;grid-template-columns:1fr;gap:14px}
.hw-card{
  background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);
  padding:22px;display:flex;gap:15px;align-items:flex-start;
  box-shadow:var(--sh-sm);transition:transform .25s,box-shadow .25s,border-color .25s;
}
.hw-card:hover{transform:translateY(-3px);box-shadow:var(--sh-md);border-color:var(--border-2)}
.hw-icon{
  flex-shrink:0;width:46px;height:46px;border-radius:12px;
  background:var(--surface-2);border:1px solid var(--border);
  display:grid;place-items:center;font-size:21px;
  box-shadow:var(--sh-sm);
}
.hw-icon svg{width:20px;height:20px;color:var(--primary)}
.hw-card h4{font-size:15.5px;font-weight:700;margin-bottom:4px}
.hw-card p{font-size:13.5px;color:var(--muted);line-height:1.55}

/* ============================================================
   Steps
   ============================================================ */
.steps{display:flex;flex-direction:column;gap:14px;max-width:820px;margin:0 auto}
.step{
  position:relative;overflow:hidden;
  background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-lg);
  padding:24px 22px;box-shadow:var(--sh-sm);
  transition:transform .25s,box-shadow .25s,border-color .25s;
}
.step:hover{transform:translateY(-3px);box-shadow:var(--sh-md);border-color:var(--border-2)}
/* Green hover effect removed */
.step-ghost{
  position:absolute;right:14px;top:-14px;
  font-family:var(--fh);font-weight:900;font-size:96px;line-height:1;
  color:var(--ink);opacity:.035;user-select:none;pointer-events:none;
}
.step-head{display:flex;align-items:center;gap:14px;margin-bottom:12px}
.step-num{
  flex-shrink:0;width:42px;height:42px;border-radius:12px;
  background:var(--primary-dim);border:1px solid var(--primary-line);
  color:var(--primary);font-family:var(--fh);font-weight:800;font-size:15px;
  display:grid;place-items:center;
}
.step-head h3{font-size:17px;font-weight:700}
.step-body p{font-size:14.5px;color:var(--text);line-height:1.65}
.step-note{
  margin-top:14px;background:var(--amber-bg);border:1px solid var(--amber-line);
  border-radius:var(--r-sm);padding:11px 14px;
  display:flex;gap:9px;align-items:flex-start;
  font-size:13px;color:var(--amber);line-height:1.5;
}
.step-note-icon{flex-shrink:0;font-weight:800;font-family:var(--fm)}
.step-links{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
.step-link{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 13px;background:var(--surface);border:1px solid var(--border-2);
  border-radius:9px;font-family:var(--fm);font-size:12px;font-weight:500;color:var(--primary);
  transition:background .2s,border-color .2s,transform .2s;
}
.step-link:hover{background:var(--primary-dim);border-color:var(--primary);transform:translateY(-1px)}
.step-link-arrow{opacity:.7;font-size:11px}

/* ============================================================
   FAQ
   ============================================================ */
.faq-wrap{display:grid;grid-template-columns:1fr;gap:30px;align-items:start;max-width:900px;margin:0 auto}
.faq-intro h2{font-size:clamp(26px,4.4vw,38px)}
.faq-intro p{color:var(--muted);font-size:14.5px;margin-top:12px}
.faq-list{display:flex;flex-direction:column;gap:10px}
details.faq{
  background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-md);
  overflow:hidden;transition:border-color .2s,box-shadow .2s;
}
details.faq:hover{border-color:var(--border-2)}
details.faq[open]{border-color:var(--primary-line);box-shadow:var(--sh-sm)}
details.faq summary{
  list-style:none;cursor:pointer;user-select:none;
  padding:17px 20px;font-family:var(--fh);font-weight:600;font-size:15px;color:var(--ink);
  display:flex;align-items:center;justify-content:space-between;gap:14px;
}
details.faq summary::-webkit-details-marker{display:none}
.faq-icon{
  flex-shrink:0;width:26px;height:26px;border-radius:8px;
  background:var(--surface);border:1px solid var(--border);
  position:relative;transition:transform .3s,color .2s,background .2s,border-color .2s;
  cursor:pointer;color:var(--muted);
}
.faq-icon::before,
.faq-icon::after{
  content:"";position:absolute;background:currentColor;transition:transform .3s;
}
.faq-icon::before{top:12px;left:7px;width:12px;height:2px;border-radius:1px}
.faq-icon::after{top:7px;left:12px;width:2px;height:12px;border-radius:1px}
details.faq[open] .faq-icon{transform:rotate(45deg);color:#fff;background:var(--primary);border-color:var(--primary)}
.faq-ans{padding:0 20px 18px;font-size:14px;color:var(--text);line-height:1.65}
details.faq[open] .faq-ans{animation:faqIn .35s ease}
@keyframes faqIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

/* ============================================================
   Footer
   ============================================================ */
footer{background:var(--surface);border-top:1px solid var(--border);padding:54px 0 28px;margin-top:0}
.foot-grid{display:grid;grid-template-columns:1fr;gap:30px}
.foot-brand .logo{margin-bottom:14px}
.foot-brand p{font-size:13.5px;color:var(--muted);max-width:300px;line-height:1.6}
.foot-col h4{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink);margin-bottom:14px}
.foot-col ul{list-style:none;display:flex;flex-direction:column;gap:9px}
.foot-col a{font-size:13.5px;color:var(--muted);position:relative;display:inline-block;transition:color .2s}
.foot-col a:hover{color:var(--primary)}
.foot-col a::after{content:"";position:absolute;left:0;bottom:-2px;width:100%;height:1.5px;background:var(--primary);transform:scaleX(0);transform-origin:left;transition:transform .3s}
.foot-col a:hover::after{transform:scaleX(1)}
.foot-bottom{
  margin-top:40px;padding-top:22px;border-top:1px solid var(--border);
  display:flex;flex-direction:column;gap:12px;align-items:flex-start;
}
.foot-made{font-family:var(--fm);font-size:12.5px;color:var(--muted)}
.foot-made a{color:var(--primary);font-weight:500}
.foot-made a:hover{text-decoration:underline}
.foot-disc{font-size:12px;color:var(--faint);line-height:1.5;max-width:560px}


/* ============================================================
   Reveal animation
   ============================================================ */
.reveal{opacity:0;transform:translateY(26px);transition:opacity .7s cubic-bezier(.2,.8,.2,1),transform .7s cubic-bezier(.2,.8,.2,1)}
.reveal.in{opacity:1;transform:none}
.no-js .reveal{opacity:1;transform:none}

/* ============================================================
   Responsive, mobile first scaling up
   ============================================================ */
@media (min-width:640px){
  .wrap{padding:0 28px}
  .hdr-in{padding:14px 28px}
  .hw-grid{grid-template-columns:1fr 1fr}
  .videos-grid{grid-template-columns:1fr 1fr}
  .step{padding:28px 30px}
  .step-head h3{font-size:18px}
  .step-ghost{font-size:118px;right:22px}
  .foot-grid{grid-template-columns:1.6fr 1fr 1fr}
  .foot-bottom{flex-direction:row;justify-content:space-between;align-items:center}
}
@media (min-width:980px){
  section{padding:84px 0}
  .hero{padding:72px 0 28px}
  .hero-grid{grid-template-columns:1.05fr .95fr;gap:48px}
  .hero h1{font-size:clamp(56px,5.4vw,72px)}
  .hdr-nav{display:flex}
  .lang-sw{display:flex}
  .burger{display:none}
  .faq-wrap{grid-template-columns:.85fr 1.15fr;gap:48px}
  .faq-intro{position:sticky;top:96px}
  .foot-grid{grid-template-columns:1.8fr 1fr 1fr 1fr}
}
@media (max-width:979px){
  .hdr-right .btn-buy{display:none}
}
@media (max-width:400px){
  .hdr-right .btn-yt span{display:none}
  .hdr-right .btn-yt{padding:0 12px}
}
@media (hover:none){
  .step:hover,.hw-card:hover,.model:hover,.stat:hover{transform:none}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation:none!important;transition:none!important}
  .reveal{opacity:1;transform:none}
  .term-bar-prog i{width:100%}
}
</style>
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
</head>
<body class="no-js">
<script>document.body.classList.remove('no-js');</script>

<!-- Header -->
<header class="hdr" id="hdr">
  <div class="hdr-in">
    <a href="?lang=<?= e($lang) ?>" class="logo" aria-label="visoire">
      <img src="assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
      visoire
    </a>

    <nav class="hdr-nav" aria-label="Main navigation">
      <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= e($tx['nav_wiki']) ?></a>
      <a href="hardware/?lang=<?= e($lang) ?>"><?= e($tx['nav_hw']) ?></a>
      <a href="#steps"><?= e($tx['nav_steps']) ?></a>
      <a href="patcher/"><?= e($tx['nav_patcher']) ?></a>
      <a href="flasher/"><?= e($tx['nav_flasher']) ?></a>
      <a href="changelog/"><?= e($tx['nav_changelog']) ?></a>
    </nav>

    <div class="hdr-right">
      <div class="lang-sw" aria-label="Language switcher">
        <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
        <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
      </div>


      <a href="hardware/" class="btn btn-dark btn-buy">
        <?= e($tx['btn_buy']) ?>
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
  <a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener" class="mob-link"><?= e($tx['nav_wiki']) ?></a>
  <a href="hardware/?lang=<?= e($lang) ?>" class="mob-link"><?= e($tx['nav_hw']) ?></a>
  <a href="#steps" class="mob-link"><?= e($tx['nav_steps']) ?></a>
  <a href="patcher/" class="mob-link"><?= e($tx['nav_patcher']) ?></a>
  <a href="flasher/" class="mob-link"><?= e($tx['nav_flasher']) ?></a>
  <a href="changelog/" class="mob-link"><?= e($tx['nav_changelog']) ?></a>
  <div class="mob-bottom">
    <button class="btn btn-yt" id="btnYtMob" style="width:100%">
      <svg width="15" height="11" viewBox="0 0 16 12" fill="currentColor" aria-hidden="true"><path d="M15.665 1.88A2.006 2.006 0 0 0 14.26.465C13.016.13 8 .13 8 .13S2.984.13 1.74.465A2.006 2.006 0 0 0 .335 1.88C0 3.13 0 5.74 0 5.74s0 2.61.335 3.86a2.006 2.006 0 0 0 1.405 1.415C2.984 11.35 8 11.35 8 11.35s5.016 0 6.26-.335a2.006 2.006 0 0 0 1.405-1.415C16 8.35 16 5.74 16 5.74s0-2.61-.335-3.86zM6.4 8.2V3.28l4.18 2.46L6.4 8.2z"/></svg>
      <?= e($tx['btn_yt']) ?>
    </button>
    <a href="hardware/" class="btn btn-dark" style="width:100%"><?= e($tx['btn_buy']) ?></a>
    <div class="mob-lang">
      <a href="?lang=de" class="lang-btn <?= $lang === 'de' ? 'active' : '' ?>" lang="de" onclick="localStorage.setItem('lang','de')">DE</a>
      <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" lang="en" onclick="localStorage.setItem('lang','en')">EN</a>
      <a href="?lang=pl" class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" lang="pl" onclick="localStorage.setItem('lang','pl')">PL</a>
    </div>
  </div>
</nav>

<main>

<!-- Hero -->
<section class="hero" id="home">
  <div class="wrap">
    <div class="hero-grid">
      <div class="hero-copy">
        <h1><?= e($tx['h1']) ?></h1>
        <p class="hero-lead"><?= e($tx['sub']) ?></p>
        <div class="hero-cta">
          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="setup/" class="btn btn-primary"><?= $L('Starten', 'Start', 'Start') ?></a>
            <button class="btn btn-yt" id="btnYtHero">
              <svg width="16" height="12" viewBox="0 0 16 12" fill="currentColor" aria-hidden="true"><path d="M15.665 1.88A2.006 2.006 0 0 0 14.26.465C13.016.13 8 .13 8 .13S2.984.13 1.74.465A2.006 2.006 0 0 0 .335 1.88C0 3.13 0 5.74 0 5.74s0 2.61.335 3.86a2.006 2.006 0 0 0 1.405 1.415C2.984 11.35 8 11.35 8 11.35s5.016 0 6.26-.335a2.006 2.006 0 0 0 1.405-1.415C16 8.35 16 5.74 16 5.74s0-2.61-.335-3.86zM6.4 8.2V3.28l4.18 2.46L6.4 8.2z"/></svg>
              <?= e($tx['btn_yt']) ?>
            </button>
          </div>
        </div>
        <div class="hero-meta">
          <span><?= $nSteps ?> <?= $L('Schritte', 'steps', 'kroków') ?></span>
          <span><?= $L('UART Flash', 'UART flash', 'flash UART') ?></span>
        </div>
      </div>

      <div class="hero-visual reveal">
        <div class="warning" role="alert" style="background: var(--red-bg); border: 1px solid var(--red-line); border-radius: var(--r-lg); padding: 18px 20px; max-width: 460px; width: 100%; display: flex; gap: 14px; align-items: flex-start;">
          <span class="warn-icon" aria-hidden="true" style="flex-shrink: 0; width: 34px; height: 34px; border-radius: 9px; background: var(--red-bg); color: var(--red); display: grid; place-items: center; font-size: 17px;">!</span>
          <div>
            <strong style="display: block; color: var(--red); font-family: var(--fm); font-size: 11.5px; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px;"><?= e($tx['warn_title']) ?></strong>
            <p style="font-size: 14px; color: var(--text); line-height: 1.55; margin:0;"><?= e($tx['warn_text']) ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- Hardware -->
<section id="hardware">
  <div class="wrap">
    <div class="sec-head reveal">
      <h2><?= e($tx['sec_hw']) ?></h2>
    </div>
    <div class="hw-grid">
      <?php foreach ($tx['hw'] as $hw): ?>
      <div class="hw-card reveal">
        <div class="hw-icon" aria-hidden="true"><i data-lucide="<?= e($hw['icon']) ?>"></i></div>
        <div>
          <h4><?= e($hw['title']) ?></h4>
          <p><?= e($hw['desc']) ?></p>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- Steps -->
<section id="steps">
  <div class="wrap">
    <div class="sec-head reveal">
      <h2><?= e($tx['sec_steps']) ?></h2>
    </div>
    <ol class="steps">
      <?php foreach ($tx['steps'] as $i => $step): $n = $i + 1; $pad = str_pad((string)$n, 2, '0', STR_PAD_LEFT); ?>
      <li class="step reveal" id="step-<?= $n ?>">
        <span class="step-ghost" aria-hidden="true"><?= $pad ?></span>
        <div class="step-head">
          <span class="step-num" aria-hidden="true"><?= $pad ?></span>
          <h3><?= e($step['title']) ?></h3>
        </div>
        <div class="step-body">
          <p><?= e($step['text']) ?></p>

          <?php if (!empty($step['note'])): ?>
          <div class="step-note">
            <span class="step-note-icon" aria-hidden="true">!</span>
            <span><?= e($step['note']) ?></span>
          </div>
          <?php endif ?>

          <?php if (!empty($step['links'])): ?>
          <div class="step-links" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <?php foreach ($step['links'] as $lnk): ?>
              <a href="<?= e($lnk['url']) ?>" target="_blank" rel="noopener" class="step-link">
                <span><?= e($lnk['label']) ?></span>
                <span class="step-link-arrow" aria-hidden="true">↗</span>
              </a>
              <?php endforeach ?>
            </div>
            <?php if ($n === 1): ?>
            <a href="mcu/?lang=<?= e($lang) ?>" class="btn btn-primary" style="box-shadow: 0 0 15px rgba(56, 189, 248, 0.6); font-weight: 700; height: 38px; min-height: 38px; padding: 0 16px; border-radius: 9px; font-size: 12.5px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px; margin: 0; display: inline-block; vertical-align: middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
              <span style="vertical-align: middle; margin-left: 6px;"><?= $L('Hier herunterladen', 'Download here', 'Pobierz tutaj') ?></span>
            </a>
            <?php endif ?>
            <?php if ($n === 2): ?>
            <a href="patcher/?lang=<?= e($lang) ?>" class="btn btn-primary" style="box-shadow: 0 0 15px rgba(56, 189, 248, 0.6); font-weight: 700; height: 38px; min-height: 38px; padding: 0 16px; border-radius: 9px; font-size: 12.5px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px; margin: 0; display: inline-block; vertical-align: middle;"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3"/></svg>
              <span style="vertical-align: middle; margin-left: 6px;"><?= $L('Hier patchen', 'Patch here', 'Patchuj tutaj') ?></span>
            </a>
            <?php endif ?>
            <?php if ($n === 4): ?>
            <a href="CDM2123620_Setup.zip" download class="btn btn-primary" style="box-shadow: 0 0 15px rgba(56, 189, 248, 0.6); font-weight: 700; height: 38px; min-height: 38px; padding: 0 16px; border-radius: 9px; font-size: 12.5px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px; margin: 0; display: inline-block; vertical-align: middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
              <span style="vertical-align: middle; margin-left: 6px;"><?= $L('Hier herunterladen', 'Download here', 'Pobierz tutaj') ?></span>
            </a>
            <?php endif ?>
            <?php if ($n === 5): ?>
            <a href="flasher/?lang=<?= e($lang) ?>" class="btn btn-primary" style="box-shadow: 0 0 15px rgba(56, 189, 248, 0.6); font-weight: 700; height: 38px; min-height: 38px; padding: 0 16px; border-radius: 9px; font-size: 12.5px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px; margin: 0; display: inline-block; vertical-align: middle;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              <span style="vertical-align: middle; margin-left: 6px;"><?= $L('Hier flashen', 'Flash here', 'Flashuj tutaj') ?></span>
            </a>
            <?php endif ?>
          </div>
          <?php endif ?>
        </div>
      </li>
      <?php endforeach ?>
    </ol>
  </div>
</section>

<!-- FAQ -->
<section id="help">
  <div class="wrap">
    <div class="faq-wrap">
      <div class="faq-intro reveal">
        <h2><?= e($tx['sec_help']) ?></h2>
        <p><?= $L('Die häufigsten Stolpersteine beim Flashen und wie du sie löst.', 'The most common snags during flashing and how to fix them.', 'Najczęstsze problemy podczas flashowania i jak je rozwiązać.') ?></p>
      </div>
      <div class="faq-list">
        <?php foreach ($tx['faq'] as $k => $item): ?>
        <details class="faq reveal">
          <summary>
            <?= e($item['q']) ?>
            <span class="faq-icon" aria-hidden="true"></span>
          </summary>
          <p class="faq-ans"><?= nl2br(e($item['a'])) ?></p>
        </details>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</section>

</main>

<!-- Footer -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a href="?lang=<?= e($lang) ?>" class="logo">
          <img src="assets/img/visoire/logo.png" alt="visoire logo" style="height: 28px; width: auto; filter: invert(74%) sepia(27%) saturate(5221%) hue-rotate(167deg) brightness(98%) contrast(99%); flex-shrink: 0; margin-right: 8px;">
          visoire
        </a>
        <p><?= $L('Firmware-Flashing für Brightway und LEQI Roller. Klar dokumentiert, ohne Umwege.', 'Firmware flashing for Brightway and LEQI scooters. Clearly documented, no detours.', 'Flashowanie firmware do hulajnóg Brightway i LEQI. Jasno opisane, bez zbędnych kroków.') ?></p>
      </div>
      <div class="foot-col">
        <h4><?= $L('Anleitung', 'Guide', 'Instrukcja') ?></h4>
        <ul>
          <li><a href="https://wiki.bastelpichi.de/brightway" target="_blank" rel="noopener"><?= e($tx['nav_wiki']) ?></a></li>
          <li><a href="hardware/?lang=<?= e($lang) ?>"><?= e($tx['nav_hw']) ?></a></li>
          <li><a href="#steps"><?= e($tx['nav_steps']) ?></a></li>
          <li><a href="mailto:visoire@proton.me"><?= $L('Kontakt', 'Contact', 'Kontakt') ?></a></li>
        </ul>
      </div>
      <div class="foot-col">
        <h4><?= $L('Werkzeuge', 'Tools', 'Narzędzia') ?></h4>
        <ul>
          <li><a href="patcher/">BW Patcher</a></li>
          <li><a href="flasher/">BW Flasher</a></li>
          <li><a href="mcu/">MCU Firmwares</a></li>
          <li><a href="changelog/">Changelog</a></li>
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
      <span class="foot-made"><?= $L('made by', 'made by', 'made by') ?> <a href="https://github.com/visoire" target="_blank" rel="noopener">visoire</a> · <?= e($tx['credit_scooterteam']) ?> <a href="https://github.com/scooterteam" target="_blank" rel="noopener">scooterteam</a> · <?= e($tx['credit_bastelpichi']) ?> <a href="https://wiki.bastelpichi.de/" target="_blank" rel="noopener">Bastelpichi</a></span>
    </div>
  </div>
</footer>

<!-- YouTube modal -->
<div class="modal" id="ytModal" role="dialog" aria-modal="true" aria-label="<?= e($tx['modal_title']) ?>">
  <div class="modal-box">
    <div class="modal-head">
      <h3><span class="yt-dot" aria-hidden="true"></span><?= e($tx['modal_title']) ?></h3>
      <button class="modal-close" id="closeModal" aria-label="Close">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display: block; width: 14px; height: 14px;">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="videos-grid">
      <?php foreach ($videos as $vi => $v): ?>
      <div class="video-item">
        <span class="video-label"><?= e($tx['vid_titles'][$vi]) ?></span>
        <iframe
          class="video-frame"
          data-src="https://www.youtube.com/embed/<?= e($v['id']) ?>?rel=0&playsinline=1"
          src="about:blank"
          title="<?= e($tx['vid_titles'][$vi]) ?>"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen
          loading="lazy"
        ></iframe>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</div>




<script>
(function(){
  'use strict';
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Header scroll state */
  var hdr = document.getElementById('hdr');
  window.addEventListener('scroll', function(){
    hdr.classList.toggle('scrolled', window.scrollY > 16);
  }, { passive:true });

  /* Mobile nav */
  var burger  = document.getElementById('burger');
  var mobNav  = document.getElementById('mobNav');
  var overlay = document.getElementById('navOverlay');

  function openMob(){
    burger.classList.add('open'); mobNav.classList.add('open'); overlay.classList.add('open');
    burger.setAttribute('aria-expanded','true'); document.body.style.overflow='hidden';
  }
  function closeMob(){
    burger.classList.remove('open'); mobNav.classList.remove('open'); overlay.classList.remove('open');
    burger.setAttribute('aria-expanded','false'); document.body.style.overflow='';
  }
  burger.addEventListener('click', function(){
    burger.classList.contains('open') ? closeMob() : openMob();
  });
  overlay.addEventListener('click', closeMob);
  mobNav.querySelectorAll('.mob-link').forEach(function(a){ a.addEventListener('click', closeMob); });

  /* YouTube modal */
  var modal = document.getElementById('ytModal');
  var closeBtn = document.getElementById('closeModal');
  var loaded = false;

  function openModal(){
    if(!loaded){
      modal.querySelectorAll('iframe[data-src]').forEach(function(f){ 
        var src = f.dataset.src;
        // Add origin parameter for YouTube Error 153 fix
        if (src.indexOf('origin=') === -1) {
          var sep = src.indexOf('?') === -1 ? '?' : '&';
          src += sep + 'origin=' + encodeURIComponent(window.location.origin);
        }
        f.src = src; 
      });
      loaded = true;
    }
    modal.classList.add('open'); document.body.style.overflow='hidden'; closeBtn.focus();
  }
  function closeModal(){ modal.classList.remove('open'); document.body.style.overflow=''; }

  ['btnYt','btnYtHero'].forEach(function(id){
    var el = document.getElementById(id);
    if(el) el.addEventListener('click', openModal);
  });
  var btnMob = document.getElementById('btnYtMob');
  if(btnMob) btnMob.addEventListener('click', function(){ closeMob(); openModal(); });
  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ closeMob(); closeModal(); }
  });

  /* Count up animation for stats */
  function countUp(el){
    if(!el.dataset.count) return;
    var target = parseFloat(el.dataset.count);
    var suffix = el.dataset.suffix || '';
    if(reduced){ el.textContent = target + suffix; return; }
    var dur = 1400, start = performance.now();
    function tick(now){
      var p = Math.min((now - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased) + suffix;
      if(p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }



  /* Intersection Observer reveal */
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(en){
      if(en.isIntersecting){
        en.target.classList.add('in');
        if(en.target.classList.contains('stats')){
          en.target.querySelectorAll('.stat-num').forEach(countUp);
        }
        io.unobserve(en.target);
      }
    });
  }, { threshold:0.12, rootMargin:'0px 0px -40px 0px' });

  document.querySelectorAll('.reveal').forEach(function(el, i){
    el.style.transitionDelay = (i % 5 * 55) + 'ms';
    io.observe(el);
  });

  /* Smooth scroll for anchor links */
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click', function(e){
      var target = document.querySelector(a.getAttribute('href'));
      if(target){ e.preventDefault(); target.scrollIntoView({ behavior:'smooth', block:'start' }); }
    });
  });

  /* Initialize Lucide icons */
  function initIcons() {
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }
  initIcons();
  window.addEventListener('load', initIcons);
})();
</script>
</body>
</html>
