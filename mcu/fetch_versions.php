<?php
// xiaomi/mcu/fetch_versions.php
/**
 * fetch_versions.php - CLI-only cron script
 *
 * Fetches the latest firmware version data from the mi-fw-info GitHub raw
 * source and writes the result to versions_cache.json. If the fetch fails,
 * the existing cache is preserved unchanged.
 *
 * This script is designed to run as a cron job every 4 hours:
 *
 * Only runs in CLI mode; blocks web access via php_sapi_name() guard.
 *
 * Output files (in same directory):
 *   versions_cache.json  - Cached version data with fetched_at timestamp
 *   fetch_error.log      - Appended error log (only on failures)
 *
 * Built by visoire.
 */

declare(strict_types=1);

// ── Guard: CLI only ──────────────────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script is CLI-only.\n";
    exit(1);
}

// ── Configuration ────────────────────────────────────────────────────────
$dataDir = __DIR__ . '/fw';

// GitHub raw URL for the mi-fw-info firmware index (community-maintained)
$sourceUrl = 'https://raw.githubusercontent.com/argusblack/mi-fw-info/main/data.json';

// Local cache file path
$cacheFile = $dataDir . '/versions_cache.json';
$errorLog  = $dataDir . '/fetch_error.log';

// HTTP context for fetching (6 second timeout, suitable for 24/7 VPS)
$ctx = stream_context_create([
    'http' => [
        'method'         => 'GET',
        'timeout'        => 6,
        'header'         => "Accept: application/json\r\nUser-Agent: visoire-fw-cron/1.0\r\n",
        'ignore_errors'  => false,
    ],
    'ssl' => [
        'verify_peer'      => true,
        'verify_peer_name' => true,
    ],
]);

// ── Fetch remote data ────────────────────────────────────────────────────
$response = @file_get_contents($sourceUrl, false, $ctx);

if ($response === false) {
    $error = error_get_last();
    $msg  = '[' . date('Y-m-d H:i:s') . "] Failed to fetch remote data from {$sourceUrl}\n";
    $msg .= 'Error: ' . ($error['message'] ?? 'Unknown error') . "\n";
    file_put_contents($errorLog, $msg, FILE_APPEND | LOCK_EX);
    echo "ERROR: Could not fetch remote data. Existing cache preserved.\n";
    exit(1);
}

// ── Parse remote JSON ────────────────────────────────────────────────────
$remoteData = json_decode($response, true);

if (!is_array($remoteData) || count($remoteData) === 0) {
    $msg  = '[' . date('Y-m-d H:i:s') . "] Remote data is empty or invalid JSON\n";
    file_put_contents($errorLog, $msg, FILE_APPEND | LOCK_EX);
    echo "ERROR: Remote data is empty or invalid JSON. Existing cache preserved.\n";
    exit(1);
}

// ── Build model version map ──────────────────────────────────────────────
// The remote data is an array of objects with:
//   { "model": "dreame.scooter.t2201", "name": "...", "firmware": { "mcu_version": "0013", ... } }
// Some entries may not have mcu_version (BLE-only firmwares).
$models = [];
foreach ($remoteData as $entry) {
    if (!is_array($entry) || !isset($entry['model'])) {
        continue;
    }
    $firmwareId = (string) $entry['model'];

    if (isset($entry['firmware']['mcu_version'])) {
        $mcuVersion = (string) $entry['firmware']['mcu_version'];
        $mcuMd5     = isset($entry['firmware']['mcu_md5']) ? (string) $entry['firmware']['mcu_md5'] : '';
        $bleVersion = isset($entry['firmware']['version']) ? (string) $entry['firmware']['version'] : '';
        $bleMd5     = isset($entry['firmware']['md5']) ? (string) $entry['firmware']['md5'] : '';

        $models[$firmwareId] = [
            'mcu_version'   => $mcuVersion,
            'mcu_md5'       => $mcuMd5,
            'ble_version'   => $bleVersion,
            'ble_md5'       => $bleMd5,
        ];
    } else {
        // BLE-only firmware (no MCU) - still store with null MCU
        $models[$firmwareId] = [
            'mcu_version'   => null,
            'mcu_md5'       => null,
            'ble_version'   => isset($entry['firmware']['version']) ? (string) $entry['firmware']['version'] : '',
            'ble_md5'       => isset($entry['firmware']['md5']) ? (string) $entry['firmware']['md5'] : '',
        ];
    }
}

// ── Write cache file atomically ──────────────────────────────────────────
$cacheData = [
    'fetched_at'  => time(),
    'models'      => $models,
];

$tmpFile = $cacheFile . '.tmp';
if (file_put_contents($tmpFile, json_encode($cacheData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX) === false) {
    $msg  = '[' . date('Y-m-d H:i:s') . "] Failed to write temporary cache file\n";
    file_put_contents($errorLog, $msg, FILE_APPEND | LOCK_EX);
    echo "ERROR: Failed to write temporary cache file.\n";
    exit(1);
}

// Atomic rename to prevent partial reads
if (!rename($tmpFile, $cacheFile)) {
    $msg  = '[' . date('Y-m-d H:i:s') . "] Failed to rename temporary cache file\n";
    file_put_contents($errorLog, $msg, FILE_APPEND | LOCK_EX);
    @unlink($tmpFile);
    echo "ERROR: Failed to finalize cache file.\n";
    exit(1);
}

$count = count($models);
echo "OK: Fetched version data for {$count} models at " . date('Y-m-d H:i:s') . "\n";
echo "Cache written to: {$cacheFile}\n";
exit(0);
