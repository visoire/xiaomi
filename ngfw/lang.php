<?php
declare(strict_types=1);

/**
 * ngfw/lang.php
 * Strings for the NGFW patcher page (EN / DE / PL). Missing keys fall back to EN.
 */

$supported = ['en', 'de', 'pl'];
$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'en');
if (!in_array($lang, $supported, true)) {
    $lang = 'en';
}

$NG = [
    'en' => [
        'page_title' => 'NGFW Patcher | visoire',
        'page_desc' => 'Patch Xiaomi M365 family and Ninebot ESC firmware with NGFW mods. Debug, simulation, setup mode and serial options.',
        'hero_title' => 'NGFW Patcher',
        'hero_sub' => 'Next-gen firmware mods for Xiaomi M365 family and Ninebot controllers (DRV). Includes the Xiaomi 4 Pro Italian (4proita, STM32F4).',
        'warn_title' => 'ST-Link required',
        'warn_body' => 'NGFW images are flashed over SWD with an ST-Link (ScooterFlasher), not over the UART adapter used for Brightway or LEQI. Patch here, then flash with the generated ScooterFlasher command.',
        'step1' => 'Scooter', 'family' => 'Family', 'fam_mi' => 'Xiaomi (M365 family)', 'fam_nb' => 'Ninebot', 'model' => 'Model',
        'step2' => 'Firmware', 'drop' => 'Drop your DRV firmware here or click to select', 'drop_hint' => 'Raw .bin from your controller or a downloaded NGFW base image',
        'step3' => 'Modifications', 'val' => 'Value (optional)',
        'step4' => 'Options',
        'sim_label' => 'Simulation mode', 'sim_sub' => 'Runs every patch and shows the report, but creates no download. Safe dry run.',
        'debug_label' => 'Debug output', 'debug_sub' => 'Adds offsets, original and patched bytes and disassembly for every change.',
        'mode_label' => 'Setup mode', 'mode_setup' => 'Setup', 'mode_start' => 'Start',
        'mode_sub' => 'On = Setup mode (guided): runs a simulation first and asks you to confirm before the download is created. Off = Start mode: patches right away.',
        'step5' => 'Serial and flash command',
        'serial_sub' => 'Optional. Builds the ScooterFlasher command including the serial. LEQI dashboard serials (5 Elite, 6, 6 Lite) are set on the Flasher page.',
        'target' => 'Target', 'tg_esc' => 'ESC (motor controller)', 'tg_ble' => 'BLE (dashboard)',
        'prefix' => 'Prefix', 'serial' => 'Serial', 'ble_name' => 'BLE name',
        'serial_hint_esc' => 'ESC serial: 5 digit prefix + 8 digit serial, joined as 12345/00000000.',
        'serial_hint_ble' => 'BLE name as shown to the app, e.g. NBScooter0000.',
        'serial_ita' => 'The 4 Pro Italian keeps its serial in an I2C EEPROM. ScooterFlasher does not write it, so no --sn is added.',
        'chip' => 'Chip', 'cmd_label' => 'ScooterFlasher command', 'btn_copy' => 'Copy',
        'btn_patch' => 'Patch firmware', 'btn_dl' => 'Download patched firmware',
        'report' => 'Patch report', 'log' => 'Log',
        'need_fw' => 'Please select a firmware file first.', 'need_patch' => 'Select at least one modification.',
        'sim_done' => 'Simulation finished. No file was created.', 'patch_done' => 'Patched firmware ready',
        'setup_sim' => 'Setup mode: running a simulation first...', 'setup_confirm' => 'Simulation passed. Create the patched firmware now?',
        'setup_cancel' => 'Cancelled in setup mode. No file was created.',
        'failed' => 'Patches that did not match this firmware', 'changed' => 'Changed bytes',
        'license' => 'NGFW patcher core by turbojeet (AGPL-3.0), based on BotoX. Source: ngfwpatcher/ in this folder.',
    ],
    'de' => [
        'hero_sub' => 'Next-Gen-Firmware-Mods für Xiaomi M365-Familie und Ninebot-Controller (DRV). Inklusive Xiaomi 4 Pro Italien (4proita, STM32F4).',
        'warn_title' => 'ST-Link erforderlich',
        'warn_body' => 'NGFW-Images werden per SWD mit einem ST-Link (ScooterFlasher) geflasht, nicht über den UART-Adapter für Brightway oder LEQI. Hier patchen, dann mit dem erzeugten ScooterFlasher-Befehl flashen.',
        'step1' => 'Scooter', 'family' => 'Familie', 'model' => 'Modell', 'step2' => 'Firmware', 'step3' => 'Modifikationen', 'step4' => 'Optionen',
        'drop' => 'DRV-Firmware hier ablegen oder klicken', 'val' => 'Wert (optional)',
        'sim_label' => 'Simulationsmodus', 'sim_sub' => 'Führt alle Patches aus und zeigt den Bericht, erzeugt aber keinen Download. Sicherer Probelauf.',
        'debug_label' => 'Debug-Ausgabe', 'debug_sub' => 'Zeigt Offsets, Original- und gepatchte Bytes und Disassembly je Änderung.',
        'mode_label' => 'Setup-Modus',
        'mode_sub' => 'An = Setup-Modus (geführt): erst Simulation, dann Bestätigung vor dem Download. Aus = Start-Modus: patcht sofort.',
        'step5' => 'Seriennummer und Flash-Befehl', 'prefix' => 'Präfix', 'serial' => 'Seriennummer', 'cmd_label' => 'ScooterFlasher-Befehl', 'btn_copy' => 'Kopieren',
        'btn_patch' => 'Firmware patchen', 'btn_dl' => 'Gepatchte Firmware herunterladen', 'report' => 'Patch-Bericht',
        'need_fw' => 'Bitte zuerst eine Firmware-Datei wählen.', 'need_patch' => 'Mindestens eine Modifikation wählen.',
        'sim_done' => 'Simulation beendet. Es wurde keine Datei erzeugt.', 'patch_done' => 'Gepatchte Firmware bereit',
        'setup_sim' => 'Setup-Modus: zuerst Simulation...', 'setup_confirm' => 'Simulation erfolgreich. Gepatchte Firmware jetzt erzeugen?',
        'setup_cancel' => 'Im Setup-Modus abgebrochen. Keine Datei erzeugt.',
    ],
    'pl' => [
        'hero_sub' => 'Modyfikacje firmware nowej generacji dla rodziny Xiaomi M365 i sterowników Ninebot (DRV). Zawiera Xiaomi 4 Pro Italian (4proita, STM32F4).',
        'warn_title' => 'Wymagany ST-Link',
        'warn_body' => 'Obrazy NGFW wgrywa się przez SWD za pomoca ST-Link (ScooterFlasher), a nie adapterem UART używanym dla Brightway i LEQI. Zmodyfikuj tutaj, potem wgraj wygenerowanym poleceniem ScooterFlasher.',
        'step1' => 'Hulajnoga', 'family' => 'Rodzina', 'model' => 'Model', 'step2' => 'Firmware', 'step3' => 'Modyfikacje', 'step4' => 'Opcje',
        'drop' => 'Upuść firmware DRV tutaj lub kliknij', 'val' => 'Wartość (opcjonalnie)',
        'sim_label' => 'Tryb symulacji', 'sim_sub' => 'Wykonuje wszystkie patche i pokazuje raport, ale nie tworzy pliku. Bezpieczna próba.',
        'debug_label' => 'Wyjscie debugowania', 'debug_sub' => 'Dodaje offsety, bajty oryginalne i zmienione oraz disassembly dla każdej zmiany.',
        'mode_label' => 'Tryb Setup',
        'mode_sub' => 'Włączony = tryb Setup (z przewodnikiem): najpierw symulacja i potwierdzenie przed utworzeniem pliku. Wyłączony = tryb Start: patchuje od razu.',
        'step5' => 'Numer seryjny i polecenie flash', 'prefix' => 'Prefiks', 'serial' => 'Numer seryjny', 'cmd_label' => 'Polecenie ScooterFlasher', 'btn_copy' => 'Kopiuj',
        'btn_patch' => 'Zmodyfikuj firmware', 'btn_dl' => 'Pobierz zmodyfikowany firmware', 'report' => 'Raport patchy',
        'need_fw' => 'Najpierw wybierz plik firmware.', 'need_patch' => 'Wybierz co najmniej jedną modyfikację.',
        'sim_done' => 'Symulacja zakończona. Plik nie został utworzony.', 'patch_done' => 'Zmodyfikowany firmware gotowy',
        'setup_sim' => 'Tryb Setup: najpierw symulacja...', 'setup_confirm' => 'Symulacja udana. Utworzyć zmodyfikowany firmware teraz?',
        'setup_cancel' => 'Anulowano w trybie Setup. Plik nie został utworzony.',
    ],
];

function ng(string $key): string
{
    global $NG, $lang;
    return $NG[$lang][$key] ?? $NG['en'][$key] ?? $key;
}

/** All strings for the active language as JSON for the page controller. */
function ng_js_i18n(): string
{
    global $NG, $lang;
    $out = [];
    foreach ($NG['en'] as $k => $v) {
        $out[$k] = $NG[$lang][$k] ?? $v;
    }
    return json_encode($out, JSON_UNESCAPED_UNICODE);
}
