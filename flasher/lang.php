<?php
declare(strict_types=1);

/**
 * lang.php
 * Self contained i18n for the flasher. English is the default language.
 * Language is selected via ?lang=en|de|pl and remembered in a cookie.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'de', 'pl'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + 86400 * 365, '/');
}

$lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';
if (!in_array($lang, ['en', 'de', 'pl'], true)) {
    $lang = 'en';
}

$T = [
    'en' => [
        'nav_wiki' => 'Wiki', 'nav_hw' => 'Hardware', 'nav_tut' => 'Tutorial',
        'nav_status' => 'Status', 'nav_patcher' => 'Patcher', 'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'nav_setup' => 'Setup',
        'page_title' => 'Firmware Flasher | Xiaomi Scooter | visoire',
        'page_desc' => 'Browser based firmware flasher for Xiaomi Electric Scooters. Flash Brightway and LEQI firmware directly from Chrome, Edge or Opera. Works on desktop and Android.',
        'hero_badge' => 'Open source flasher',
        'hero_title' => 'Firmware Flasher',
        'hero_sub' => 'Flash your scooter firmware straight from the browser over Web Serial or WebUSB. No install, mobile friendly.',

        'disc_title' => 'Important notice',
        'disc_body' => 'You are modifying your own device. Changes can have technical and legal consequences, may void the warranty and can be illegal to operate in public traffic. A failed flash can brick the controller. You assume all responsibility. For research and educational use only. Licensed CC BY-NC-SA 4.0, no commercial use.',
        'disc_accept' => 'I understand and accept',
        'disc_decline' => 'Decline',

        'net_title' => 'Keep a stable connection',
        'net_body' => 'Do not unplug the cable, close the tab or let the device sleep while flashing runs. An interruption during the write can leave the controller in a broken state. Use a stable USB cable and a charged scooter.',

        'browser_warn' => 'Your browser does not expose Web Serial or WebUSB. Use a recent Chrome, Edge or Opera. On iPhone and iPad no browser supports this.',

        'step1_title' => 'Connect your device',
        'step1_sub' => 'Connect the UART adapter to the scooter, then choose a transport.',
        'btn_webserial' => 'Connect Web Serial',
        'btn_webserial_hint' => 'Chrome and Edge on desktop',
        'btn_webusb' => 'Connect WebUSB',
        'btn_webusb_hint' => 'Experimental, also Android',
        'btn_disconnect' => 'Disconnect',
        'btn_test' => 'Test connection',
        'conn_none' => 'Not connected',
        'conn_sim' => 'Simulation mode',
        'webusb_hint' => 'WebUSB drives CH340, CH341, FT232R, CP210x and PL2303 directly. On Android connect an OTG adapter first. On Windows switch the driver to WinUSB with Zadig if the device is not found.',

        'step2_title' => 'Load firmware',
        'step2_sub' => 'Drop a firmware file or pick one. Brightway and LEQI binaries are detected automatically.',
        'drop_label' => 'Drop firmware here or click to select',
        'drop_hint' => 'Accepts .bin, .zip or .enc',
        'fw_none' => 'No firmware loaded yet.',
        'fw_file' => 'File',
        'fw_type' => 'Type',
        'fw_size' => 'Size',
        'fw_protocol' => 'Protocol',
        'fw_signature' => 'Signature',
        'fw_encryption' => 'Encryption',
        'fw_keyoffset' => 'Key offset',

        'step3_title' => 'Options',
        'sim_label' => 'Simulation mode',
        'sim_sub' => 'Runs the full flow without a real device. Good for a dry run.',
        'debug_label' => 'Debug output',
        'debug_sub' => 'Logs every TX and RX packet. Can slow older adapters.',
        'mode_label' => 'Setup mode',
        'mode_setup' => 'Setup',
        'mode_start' => 'Start',
        'mode_sub' => 'On = Setup mode (guided): tests the connection and asks you to confirm before anything is written. Off = Start mode: flashes right away.',
        'dash_title' => 'Dashboard serial (LEQI only)',
        'dash_sub' => 'Sets the scooter serial stored in the dashboard BLE module. For LEQI scooters only (5 Elite, 6, 6 Lite). Connect the adapter to the dashboard, not the motor controller.',
        'dash_prefix' => 'Prefix',
        'dash_serial' => 'Serial',
        'dash_hint' => 'Prefix: 5 characters (example 05393). Serial: 14 characters. Together exactly 19.',
        'dash_preview' => 'Full serial',
        'btn_dash_write' => 'Write serial',
        'dash_note' => 'The serial is stored permanently in the BLE module. There is no documented undo, so double check it.',

        'step4_title' => 'Flash',
        'btn_flash' => 'Flash firmware',
        'btn_abort' => 'Abort',
        'baud_info' => '19200 baud, 8N1, no flow control',
        'progress_ready' => 'Ready',
        'state_idle' => 'Idle',
        'state_flashing' => 'Flashing',
        'state_done' => 'Done',
        'state_error' => 'Error',

        'log_title' => 'Log',
        'btn_clear' => 'Clear',
        'btn_export' => 'Export',

        'foot_made' => 'Built by visoire. Open source, licensed CC BY-NC-SA 4.0.',
        'foot_legal' => 'Legal notice',
        'foot_contact' => 'Contact',
        'btn_back' => 'Back to Tutorial',

        // dynamic strings used by the controller
        'js_serial_ok' => 'Web Serial connected at 19200 baud',
        'js_usb_ok' => 'WebUSB connected at 19200 baud',
        'js_serial_unsupported' => 'Web Serial is not supported in this browser',
        'js_usb_unsupported' => 'WebUSB is not supported in this browser',
        'js_conn_fail' => 'Connection failed. Please check your USB cable, COM port, and 5V connection.',
        'js_disconnected' => 'Disconnected',
        'js_loading' => 'Loading firmware',
        'js_fw_detected' => 'Firmware detected',
        'js_fw_error' => 'Could not read firmware file. Please choose a valid .bin file.',
        'js_need_fw' => 'Please select a valid firmware file before flashing.',
        'js_test_pass' => 'Connection test passed',
        'js_test_fail' => 'Connection test failed. Ensure your scooter is powered on with the taillight lit.',
        'js_flash_start' => 'Starting flash',
        'js_flash_done' => 'Flash completed successfully',
        'js_flash_error' => 'Flash error',
        'js_abort' => 'Abort requested',
        'js_unknown_type' => 'Unrecognized firmware format. Please use an official .bin MCU firmware file.',
        'js_api_serial' => 'Web Serial API available',
        'js_api_usb' => 'WebUSB API available',
        'js_leave_warn' => 'A flash is in progress. If you leave now the scooter controller could be left in a broken state. Stay on this page?',
        'disc_wait' => 'Please wait %s seconds...',
        'js_fw_crc_fail' => 'Firmware checksum check failed. The file is damaged or incomplete. Please re-download it.',
        'js_fw_crc_ok' => 'Firmware CRC verified',
        'js_dev_info' => 'Controller firmware version',
        'js_dev_region' => 'Region',
        'js_setup_testing' => 'Setup mode: testing the connection first...',
        'js_setup_confirm' => 'Setup mode: start writing now?',
        'js_setup_cancel' => 'Cancelled in setup mode. Nothing was written.',
        'js_dash_invalid' => 'Invalid serial',
        'js_dash_confirm' => 'Write this serial to the dashboard BLE module? It is stored permanently.',
        'js_dash_ok' => 'Serial written to the dashboard',
        'js_dash_sim_ok' => 'Simulation: serial would be written',
        'js_dash_fail' => 'Writing the serial failed. Check that the adapter is on the dashboard UART and the scooter is on.',
        'fw_bodysize' => 'Body size',
        'fw_sizesource' => 'Size source',
    ],

    'de' => [
        'nav_wiki' => 'Wiki', 'nav_hw' => 'Hardware', 'nav_tut' => 'Anleitung',
        'nav_status' => 'Status', 'nav_patcher' => 'Patcher', 'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Changelog',
        'nav_setup' => 'Setup',
        'page_title' => 'Firmware Flasher | Xiaomi Scooter | visoire',
        'page_desc' => 'Browserbasierter Firmware Flasher für Xiaomi Scooter. Flasht Brightway und LEQI Firmware direkt aus Chrome, Edge oder Opera. Funktioniert am Desktop und unter Android.',
        'hero_badge' => 'Open Source Flasher',
        'hero_title' => 'Firmware Flasher',
        'hero_sub' => 'Flashe deine Scooter Firmware direkt im Browser über Web Serial oder WebUSB. Ohne Installation, mobiltauglich.',

        'disc_title' => 'Wichtiger Hinweis',
        'disc_body' => 'Du veränderst dein eigenes Gerät. Änderungen können technische und rechtliche Folgen haben, die Garantie erlöschen lassen und im Straßenverkehr illegal sein. Ein fehlgeschlagener Flash kann den Controller unbrauchbar machen. Du trägst die volle Verantwortung. Nur für Forschung und Bildung. Lizenziert unter CC BY-NC-SA 4.0, keine kommerzielle Nutzung.',
        'disc_accept' => 'Verstanden und akzeptiert',
        'disc_decline' => 'Ablehnen',

        'net_title' => 'Stabile Verbindung halten',
        'net_body' => 'Während des Flashens das Kabel nicht abziehen, den Tab nicht schließen und das Gerät nicht in den Ruhezustand lassen. Eine Unterbrechung beim Schreiben kann den Controller beschädigen. Nutze ein stabiles USB Kabel und einen geladenen Scooter.',

        'browser_warn' => 'Dein Browser bietet weder Web Serial noch WebUSB. Nutze ein aktuelles Chrome, Edge oder Opera. Auf iPhone und iPad unterstützt das kein Browser.',

        'step1_title' => 'Gerät verbinden',
        'step1_sub' => 'Verbinde den UART Adapter mit dem Scooter und wähle dann eine Übertragungsart.',
        'btn_webserial' => 'Web Serial verbinden',
        'btn_webserial_hint' => 'Chrome und Edge am Desktop',
        'btn_webusb' => 'WebUSB verbinden',
        'btn_webusb_hint' => 'Experimentell, auch Android',
        'btn_disconnect' => 'Trennen',
        'btn_test' => 'Verbindung testen',
        'conn_none' => 'Nicht verbunden',
        'conn_sim' => 'Simulationsmodus',
        'webusb_hint' => 'WebUSB steuert CH340, CH341, FT232R, CP210x und PL2303 direkt an. Unter Android zuerst einen OTG Adapter anschließen. Unter Windows bei Bedarf den Treiber mit Zadig auf WinUSB umstellen.',

        'step2_title' => 'Firmware laden',
        'step2_sub' => 'Ziehe eine Firmware Datei hierher oder wähle eine aus. Brightway und LEQI Binärdateien werden automatisch erkannt.',
        'drop_label' => 'Firmware hierher ziehen oder zum Auswählen klicken',
        'drop_hint' => 'Akzeptiert .bin, .zip oder .enc',
        'fw_none' => 'Noch keine Firmware geladen.',
        'fw_file' => 'Datei',
        'fw_type' => 'Typ',
        'fw_size' => 'Größe',
        'fw_protocol' => 'Protokoll',
        'fw_signature' => 'Signatur',
        'fw_encryption' => 'Verschlüsselung',
        'fw_keyoffset' => 'Key Offset',

        'step3_title' => 'Optionen',
        'sim_label' => 'Simulationsmodus',
        'sim_sub' => 'Führt den gesamten Ablauf ohne echtes Gerät aus. Gut für einen Probelauf.',
        'debug_label' => 'Debug Ausgabe',
        'debug_sub' => 'Protokolliert jedes TX und RX Paket. Kann ältere Adapter verlangsamen.',
        'mode_label' => 'Setup-Modus',
        'mode_setup' => 'Setup',
        'mode_start' => 'Start',
        'mode_sub' => 'An = Setup-Modus (geführt): testet die Verbindung und fragt vor dem Schreiben nach Bestätigung. Aus = Start-Modus: flasht sofort.',
        'dash_title' => 'Dashboard-Seriennummer (nur LEQI)',
        'dash_sub' => 'Setzt die Seriennummer des Scooters im BLE-Modul des Dashboards. Nur für LEQI-Scooter (5 Elite, 6, 6 Lite). Adapter an das Dashboard anschließen, nicht an den Motorcontroller.',
        'dash_prefix' => 'Präfix',
        'dash_serial' => 'Seriennummer',
        'dash_hint' => 'Präfix: 5 Zeichen (Beispiel 05393). Seriennummer: 14 Zeichen. Zusammen genau 19.',
        'dash_preview' => 'Komplette Seriennummer',
        'btn_dash_write' => 'Seriennummer schreiben',
        'dash_note' => 'Die Seriennummer wird dauerhaft im BLE-Modul gespeichert. Es gibt kein dokumentiertes Zurück, also bitte genau prüfen.',

        'step4_title' => 'Flashen',
        'btn_flash' => 'Firmware flashen',
        'btn_abort' => 'Abbrechen',
        'baud_info' => '19200 Baud, 8N1, keine Flusssteuerung',
        'progress_ready' => 'Bereit',
        'state_idle' => 'Bereit',
        'state_flashing' => 'Flasht',
        'state_done' => 'Fertig',
        'state_error' => 'Fehler',

        'log_title' => 'Protokoll',
        'btn_clear' => 'Leeren',
        'btn_export' => 'Export',

        'foot_made' => 'Erstellt von visoire. Open Source, lizenziert unter CC BY-NC-SA 4.0.',
        'foot_legal' => 'Impressum',
        'foot_contact' => 'Kontakt',
        'btn_back' => 'Zurück zur Anleitung',

        'js_serial_ok' => 'Web Serial verbunden mit 19200 Baud',
        'js_usb_ok' => 'WebUSB verbunden mit 19200 Baud',
        'js_serial_unsupported' => 'Web Serial wird in diesem Browser nicht unterstützt',
        'js_usb_unsupported' => 'WebUSB wird in diesem Browser nicht unterstützt',
        'js_conn_fail' => 'Verbindung fehlgeschlagen. Bitte USB-Kabel, COM-Port und 5V-Verbindung prüfen.',
        'js_disconnected' => 'Getrennt',
        'js_loading' => 'Firmware wird geladen',
        'js_fw_detected' => 'Firmware erkannt',
        'js_fw_error' => 'Firmware-Datei konnte nicht gelesen werden. Bitte eine gültige .bin-Datei wählen.',
        'js_need_fw' => 'Bitte wähle vor dem Flashen eine gültige Firmware-Datei aus.',
        'js_test_pass' => 'Verbindungstest bestanden',
        'js_test_fail' => 'Verbindungstest fehlgeschlagen. Stelle sicher, dass der Roller an ist und das Rücklicht leuchtet.',
        'js_flash_start' => 'Flashen wird gestartet',
        'js_flash_done' => 'Flashen erfolgreich abgeschlossen',
        'js_flash_error' => 'Flash Fehler',
        'js_abort' => 'Abbruch angefordert',
        'js_unknown_type' => 'Unbekanntes Firmware-Format. Bitte eine offizielle .bin MCU-Firmware-Datei verwenden.',
        'js_api_serial' => 'Web Serial API verfügbar',
        'js_api_usb' => 'WebUSB API verfügbar',
        'disc_wait' => 'Bitte warten Sie %s Sekunden...',
        'js_fw_crc_fail' => 'Prüfsummenfehler der Firmware. Die Datei ist beschädigt oder unvollständig. Bitte erneut herunterladen.',
        'js_fw_crc_ok' => 'Firmware CRC geprüft',
        'js_dev_info' => 'Controller Firmware Version',
        'js_dev_region' => 'Region',
        'js_setup_testing' => 'Setup-Modus: Verbindung wird zuerst getestet...',
        'js_setup_confirm' => 'Setup-Modus: Jetzt mit dem Schreiben beginnen?',
        'js_setup_cancel' => 'Im Setup-Modus abgebrochen. Es wurde nichts geschrieben.',
        'js_dash_invalid' => 'Ungültige Seriennummer',
        'js_dash_confirm' => 'Diese Seriennummer ins BLE-Modul des Dashboards schreiben? Sie wird dauerhaft gespeichert.',
        'js_dash_ok' => 'Seriennummer ins Dashboard geschrieben',
        'js_dash_sim_ok' => 'Simulation: Seriennummer würde geschrieben',
        'js_dash_fail' => 'Schreiben der Seriennummer fehlgeschlagen. Prüfe, ob der Adapter am Dashboard-UART hängt und der Scooter an ist.',
        'fw_bodysize' => 'Nutzdatengröße',
        'fw_sizesource' => 'Größenquelle',
    ],

    'pl' => [
        'nav_wiki' => 'Wiki', 'nav_hw' => 'Sprzęt', 'nav_tut' => 'Poradnik',
        'nav_status' => 'Status', 'nav_patcher' => 'Patcher', 'nav_flasher' => 'Flasher',
        'nav_changelog' => 'Dziennik zmian',
        'nav_setup' => 'Kreator',
        'page_title' => 'Firmware Flasher | Hulajnoga Xiaomi | visoire',
        'page_desc' => 'Flasher firmware w przeglądarce dla hulajnóg Xiaomi. Wgrywa firmware Brightway i LEQI bezpośrednio z Chrome, Edge lub Opery. Działa na komputerze i Androidzie.',
        'hero_badge' => 'Otwartoźródłowy flasher',
        'hero_title' => 'Firmware Flasher',
        'hero_sub' => 'Wgraj firmware hulajnogi prosto z przeglądarki przez Web Serial lub WebUSB. Bez instalacji, działa na telefonie.',

        'disc_title' => 'Ważna informacja',
        'disc_body' => 'Modyfikujesz własne urządzenie. Zmiany mogą mieć skutki techniczne i prawne, mogą unieważnić gwarancję i być nielegalne w ruchu publicznym. Nieudane wgranie może uszkodzić kontroler. Ponosisz pełną odpowiedzialność. Tylko do celów badawczych i edukacyjnych. Licencja CC BY-NC-SA 4.0, bez użytku komercyjnego.',
        'disc_accept' => 'Rozumiem i akceptuję',
        'disc_decline' => 'Odrzuć',

        'net_title' => 'Utrzymaj stabilne połączenie',
        'net_body' => 'Podczas wgrywania nie odłączaj kabla, nie zamykaj karty i nie pozwól urządzeniu zasnąć. Przerwanie podczas zapisu może uszkodzić kontroler. Użyj stabilnego kabla USB i naładowanej hulajnogi.',

        'browser_warn' => 'Twoja przeglądarka nie udostępnia Web Serial ani WebUSB. Użyj aktualnego Chrome, Edge lub Opery. Na iPhone i iPad żadna przeglądarka tego nie obsługuje.',

        'step1_title' => 'Podłącz urządzenie',
        'step1_sub' => 'Podłącz adapter UART do hulajnogi, a następnie wybierz sposób transmisji.',
        'btn_webserial' => 'Połącz Web Serial',
        'btn_webserial_hint' => 'Chrome i Edge na komputerze',
        'btn_webusb' => 'Połącz WebUSB',
        'btn_webusb_hint' => 'Eksperymentalne, także Android',
        'btn_disconnect' => 'Rozłącz',
        'btn_test' => 'Test połączenia',
        'conn_none' => 'Brak połączenia',
        'conn_sim' => 'Tryb symulacji',
        'webusb_hint' => 'WebUSB obsługuje bezpośrednio CH340, CH341, FT232R, CP210x i PL2303. Na Androidzie podłącz najpierw adapter OTG. Na Windows w razie potrzeby zmień sterownik na WinUSB za pomocą Zadig.',

        'step2_title' => 'Wczytaj firmware',
        'step2_sub' => 'Przeciągnij plik firmware lub wybierz go. Pliki Brightway i LEQI są wykrywane automatycznie.',
        'drop_label' => 'Przeciągnij firmware tutaj lub kliknij, aby wybrać',
        'drop_hint' => 'Akzeptuje .bin, .zip lub .enc',
        'fw_none' => 'Nie wczytano jeszcze firmware.',
        'fw_file' => 'Plik',
        'fw_type' => 'Typ',
        'fw_size' => 'Rozmiar',
        'fw_protocol' => 'Protokół',
        'fw_signature' => 'Sygnatura',
        'fw_encryption' => 'Szyfrowanie',
        'fw_keyoffset' => 'Offset klucza',

        'step3_title' => 'Opcje',
        'sim_label' => 'Tryb symulacji',
        'sim_sub' => 'Uruchamia cały proces bez prawdziwego urządzenia. Dobre do próby.',
        'debug_label' => 'Wyjście debugowania',
        'debug_sub' => 'Loguje każdy pakiet TX i RX. Może spowolnić starsze adaptery.',
        'mode_label' => 'Tryb Setup',
        'mode_setup' => 'Setup',
        'mode_start' => 'Start',
        'mode_sub' => 'Włączony = tryb Setup (z przewodnikiem): testuje połączenie i prosi o potwierdzenie przed zapisem. Wyłączony = tryb Start: wgrywa od razu.',
        'dash_title' => 'Numer seryjny wyświetlacza (tylko LEQI)',
        'dash_sub' => 'Ustawia numer seryjny hulajnogi zapisany w module BLE wyświetlacza. Tylko dla hulajnog LEQI (5 Elite, 6, 6 Lite). Podłącz adapter do wyświetlacza, nie do sterownika silnika.',
        'dash_prefix' => 'Prefiks',
        'dash_serial' => 'Numer seryjny',
        'dash_hint' => 'Prefiks: 5 znaków (przykład 05393). Numer: 14 znaków. Razem dokładnie 19.',
        'dash_preview' => 'Pełny numer seryjny',
        'btn_dash_write' => 'Zapisz numer seryjny',
        'dash_note' => 'Numer jest zapisywany na stałe w module BLE. Nie ma udokumentowanego cofania, więc sprawdź go dokładnie.',

        'step4_title' => 'Wgraj',
        'btn_flash' => 'Wgraj firmware',
        'btn_abort' => 'Przerwij',
        'baud_info' => '19200 baud, 8N1, bez kontroli przepływu',
        'progress_ready' => 'Gotowe',
        'state_idle' => 'Gotowe',
        'state_flashing' => 'Wgrywanie',
        'state_done' => 'Gotowe',
        'state_error' => 'Błąd',

        'log_title' => 'Dziennik',
        'btn_clear' => 'Wyczyść',
        'btn_export' => 'Eksport',

        'foot_made' => 'Stworzone przez visoire. Otwarte źródło, licencja CC BY-NC-SA 4.0.',
        'foot_legal' => 'Nota prawna',
        'foot_contact' => 'Kontakt',
        'btn_back' => 'Powrót do instrukcji',

        'js_serial_ok' => 'Web Serial połączony z prędkością 19200 baud',
        'js_usb_ok' => 'WebUSB połączony z prędkością 19200 baud',
        'js_serial_unsupported' => 'Web Serial nie jest obsługiwany w tej przeglądarce',
        'js_usb_unsupported' => 'WebUSB nie jest obsługiwany w tej przeglądarce',
        'js_conn_fail' => 'Połączenie nieudane. Sprawdź kabel USB, port COM i zasilanie 5V.',
        'js_disconnected' => 'Rozłączono',
        'js_loading' => 'Wczytywanie firmware',
        'js_fw_detected' => 'Wykryto firmware',
        'js_fw_error' => 'Nie można odczytać pliku firmware. Wybierz poprawny plik .bin.',
        'js_need_fw' => 'Wybierz prawidłowy plik firmware przed rozpoczęciem flashowania.',
        'js_test_pass' => 'Test połączenia zaliczony',
        'js_test_fail' => 'Test połączenia nieudany. Upewnij się, że hulajnoga jest włączona, a tylne światło świeci.',
        'js_flash_start' => 'Rozpoczynanie wgrywania',
        'js_flash_done' => 'Wgrywanie zakończone pomyślnie',
        'js_flash_error' => 'Błąd wgrywania',
        'js_abort' => 'Zażądano przerwania',
        'js_unknown_type' => 'Nierozpoznany format firmware. Użyj oficjalnego pliku MCU .bin.',
        'js_api_serial' => 'API Web Serial dostępne',
        'js_api_usb' => 'API WebUSB dostępne',
        'disc_wait' => 'Proszę czekać %s sekund...',
        'js_fw_crc_fail' => 'Błąd sumy kontrolnej firmware. Plik jest uszkodzony lub niekompletny. Pobierz go ponownie.',
        'js_fw_crc_ok' => 'CRC firmware zweryfikowane',
        'js_dev_info' => 'Wersja firmware kontrolera',
        'js_dev_region' => 'Region',
        'js_setup_testing' => 'Tryb Setup: najpierw test połączenia...',
        'js_setup_confirm' => 'Tryb Setup: rozpocząć zapis teraz?',
        'js_setup_cancel' => 'Anulowano w trybie Setup. Nic nie zapisano.',
        'js_dash_invalid' => 'Nieprawidłowy numer seryjny',
        'js_dash_confirm' => 'Zapisać ten numer w module BLE wyświetlacza? Zostanie zapisany na stałe.',
        'js_dash_ok' => 'Numer seryjny zapisany w wyświetlaczu',
        'js_dash_sim_ok' => 'Symulacja: numer seryjny zostałby zapisany',
        'js_dash_fail' => 'Zapis numeru seryjnego nie powiódł sie. Sprawdz, czy adapter jest na UART wyświetlacza i hulajnoga jest włączona.',
        'fw_bodysize' => 'Rozmiar danych',
        'fw_sizesource' => 'Źródło rozmiaru',
    ],
];

/**
 * Translate a key for the active language, falling back to English then the key.
 */
function ft(string $key): string
{
    global $T, $lang;
    return $T[$lang][$key] ?? $T['en'][$key] ?? $key;
}

/**
 * Return the full dynamic-string set for the active language as JSON,
 * so the JS controller can localise its log and status output.
 */
function flasher_js_i18n(): string
{
    global $T, $lang;
    $keys = array_filter(array_keys($T['en']), fn($k) => str_starts_with($k, 'js_'));
    $out = [];
    foreach ($keys as $k) {
        $short = substr($k, 3);
        $out[$short] = $T[$lang][$k] ?? $T['en'][$k];
    }
    return json_encode($out, JSON_UNESCAPED_UNICODE);
}
