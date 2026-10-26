<?php
declare(strict_types=1);

/**
 * fetch_info.php - Live firmware info proxy
 *
 * Fetches the latest firmware info for a given model from the community-
 * maintained mi-fw-info GitHub repository. This endpoint acts as a proxy
 * so that the frontend can get live CDN URLs, patch info, changelogs, etc.
 * without hitting CORS issues or exposing GitHub raw URLs directly.
 *
 * This is used by the mcu/index.php "Original Info" modal to show live
 * data (mcu_safe_url, changelogs, version info) fetched from GitHub rather
 * than relying solely on the static data.json cache.
 *
 * Usage: fetch_info.php?model=dreame.scooter.t2201
 *
 * Returns JSON:
 *   {
 *     "ok": true,
 *     "model": "dreame.scooter.t2201",
 *     "name": "Xiaomi Electric Scooter 4",
 *     "firmware": {
 *       "mcu_version": "0013",
 *       "mcu_md5": "ada2fc4bdfc90b1e1e3562f0e0a453c7",
 *       "mcu_safe_url": "https://...",
 *       "version": "2.5.0_0017",
 *       "safe_url": "https://...",
 *       "changeLog": "...",
 *       "diff_md5": "",
 *       "diff_safe_url": ""
 *     },
 *     "extra": {
 *       "imageMin": "https://..."
 *     }
 *   }
 *
 * Built by visoire.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=300'); // 5 min cache
header('X-Content-Type-Options: nosniff');

try {
    $model = isset($_GET['model']) ? trim((string) $_GET['model']) : '';

    if ($model === '') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Missing model parameter.'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Sanitize: only allow known safe characters in model names
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid model parameter.'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Fetch from the community-maintained mi-fw-info GitHub repo
    $sourceUrl = 'https://raw.githubusercontent.com/argusblack/mi-fw-info/main/data.json';
    // Local fallback file is now in fw/ subdirectory
    $localDataFile = __DIR__ . '/fw/data.json';

    $ctx = stream_context_create([
        'http' => [
            'method'         => 'GET',
            'timeout'        => 8,
            'header'         => "Accept: application/json\r\nUser-Agent: visoire-fw-info/1.0\r\n",
            'ignore_errors'  => false,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents($sourceUrl, false, $ctx);

    if ($response === false) {
        // GitHub fetch failed - fall back to local fw/data.json (flat array format)
        if (isset($localDataFile) && file_exists($localDataFile) && is_readable($localDataFile)) {
            $localData = json_decode((string) file_get_contents($localDataFile), true);
            if (is_array($localData)) {
                foreach ($localData as $item) {
                    if (is_array($item) && (string)($item['model'] ?? '') === $model) {
                        $fw    = $item['firmware'] ?? [];
                        $extra = $item['extra']    ?? [];
                        echo json_encode([
                            'ok'       => true,
                            'model'    => $model,
                            'name'     => $item['name'] ?? '',
                            'source'   => 'local',
                            'firmware' => [
                                'mcu_version'  => (string)($fw['mcu_version']  ?? ''),
                                'mcu_md5'      => (string)($fw['mcu_md5']      ?? ''),
                                'mcu_safe_url' => (string)($fw['mcu_safe_url'] ?? ''),
                                'version'      => (string)($fw['version']      ?? ''),
                                'safe_url'     => (string)($fw['safe_url']     ?? ''),
                                'changeLog'    => (string)($fw['changeLog']    ?? ''),
                            ],
                            'extra' => [
                                'imageMin' => (string)($extra['imageMin'] ?? ''),
                            ],
                        ], JSON_UNESCAPED_SLASHES);
                        exit;
                    }
                }
            }
        }

        http_response_code(502);
        echo json_encode([
            'ok'    => false,
            'error' => 'Could not fetch firmware info from remote source.',
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    $remoteData = json_decode($response, true);

    if (!is_array($remoteData)) {
        http_response_code(502);
        echo json_encode([
            'ok'    => false,
            'error' => 'Invalid response from firmware info source.',
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Find the matching model entry
    $found = null;
    foreach ($remoteData as $entry) {
        if (is_array($entry) && isset($entry['model']) && $entry['model'] === $model) {
            $found = $entry;
            break;
        }
    }

    if ($found === null) {
        http_response_code(404);
        echo json_encode([
            'ok'    => false,
            'error' => 'Model not found in firmware info source.',
            'model' => $model,
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Build the response with all relevant fields
    $result = [
        'ok'       => true,
        'model'    => $found['model'] ?? $model,
        'name'     => $found['name'] ?? '',
        'source'   => 'github',
    ];

    if (isset($found['firmware']) && is_array($found['firmware'])) {
        $result['firmware'] = [
            'mcu_version'    => (string) ($found['firmware']['mcu_version'] ?? ''),
            'mcu_md5'        => (string) ($found['firmware']['mcu_md5'] ?? ''),
            'mcu_safe_url'   => (string) ($found['firmware']['mcu_safe_url'] ?? ''),
            'version'        => (string) ($found['firmware']['version'] ?? ''),
            'safe_url'       => (string) ($found['firmware']['safe_url'] ?? ''),
            'changeLog'      => (string) ($found['firmware']['changeLog'] ?? ''),
            'diff_md5'       => (string) ($found['firmware']['diff_md5'] ?? ''),
            'diff_safe_url'  => (string) ($found['firmware']['diff_safe_url'] ?? ''),
            'url'            => (string) ($found['firmware']['url'] ?? ''),
            'md5'            => (string) ($found['firmware']['md5'] ?? ''),
            'upload_time'    => isset($found['firmware']['upload_time']) ? (int) $found['firmware']['upload_time'] : 0,
            'force'          => (bool) ($found['firmware']['force'] ?? false),
            'rec'            => (bool) ($found['firmware']['rec'] ?? false),
        ];
    } else {
        $result['firmware'] = [];
    }

    if (isset($found['extra']) && is_array($found['extra'])) {
        $result['extra'] = [
            'imageMin' => (string) ($found['extra']['imageMin'] ?? ''),
        ];
    } else {
        $result['extra'] = [];
    }

    echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Internal error: ' . $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES);
}
