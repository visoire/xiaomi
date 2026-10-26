<?php
// xiaomi/mcu/version_check.php
/**
 * version_check.php - Read-only version check endpoint
 *
 * Reads the local versions_cache.json (populated by the CLI cron script
 * fetch_versions.php) and compares the requested model's latest version
 * against a provided local version.
 *
 * This endpoint makes ZERO external HTTP calls. It always returns valid JSON.
 *
 * Legacy parameter support:
 *   ?firmwareId=...&local=...   (previous API format)
 *   ?model=...&local=...        (new format)
 *
 * Returns JSON:
 *   {
 *     "ok": true,
 *     "model": "dreame.scooter.t2201",
 *     "latest": "0017",
 *     "latest_md5": "ada2fc4bdfc90b1e1e3562f0e0a453c7",
 *     "local": "0013",
 *     "up_to_date": false,
 *     "cache_age_hours": 2.5
 *   }
 *
 * Built by visoire.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=300');
header('X-Content-Type-Options: nosniff');

try {
    // ── Load local fw/data.json for bundled version metadata ──────────────
    $dataFile  = __DIR__ . '/fw/data.json';
    $cacheFile = __DIR__ . '/fw/versions_cache.json';
    $model     = '';
    $local     = '';

    // Map the 8 patchable firmware IDs to their bundled MCU version from fw/data.json
    $bundledModels = [];
    if (file_exists($dataFile) && is_readable($dataFile)) {
        $parsed = json_decode((string) file_get_contents($dataFile), true);
        // flat array format: [{model, name, firmware:{mcu_version,...}, extra:{...}}, ...]
        if (is_array($parsed)) {
            foreach ($parsed as $m) {
                if (!is_array($m) || !isset($m['model'])) continue;
                $fwId = (string)$m['model'];
                $fw   = $m['firmware'] ?? [];
                // Store using firmwareId as key to keep same interface for rest of the code
                $bundledModels[$fwId] = [
                    'bundledVersion' => (string)($fw['mcu_version'] ?? ''),
                    'bundledMd5'     => (string)($fw['mcu_md5']     ?? ''),
                ];
            }
        }
    }

    // ── Parse request parameters ────────────────────────────────────────
    // Support both old firmwareId param and new model param
    $model = isset($_GET['model']) ? trim((string) $_GET['model']) : '';
    if ($model === '' && isset($_GET['firmwareId'])) {
        $model = trim((string) $_GET['firmwareId']);
    }

    $local = isset($_GET['local']) ? trim((string) $_GET['local']) : '';

    // ── Load cache file ────────────────────────────────────────────────
    if (!file_exists($cacheFile) || !is_readable($cacheFile)) {
        // Cache doesn't exist yet - return empty result (no error)
        if ($model === '') {
            echo json_encode([
                'ok'              => true,
                'models'          => [],
                'cache_age_hours' => null,
                'note'            => 'Version cache not yet populated. Run fetch_versions.php via cron.',
            ]);
        } else {
            echo json_encode([
                'ok'              => true,
                'model'           => $model,
                'latest'          => null,
                'latest_md5'      => null,
                'local'           => $local,
                'up_to_date'      => null,
                'cache_age_hours' => null,
                'note'            => 'Version cache not yet populated. Run fetch_versions.php via cron.',
            ]);
        }
        exit;
    }

    $cacheJson  = (string) file_get_contents($cacheFile);
    $cacheData  = (array) json_decode($cacheJson, true);

    if (!isset($cacheData['fetched_at']) || !isset($cacheData['models'])) {
        throw new RuntimeException('Invalid cache file structure');
    }

    $fetchedAt    = (int) $cacheData['fetched_at'];
    $cachedModels = (array) $cacheData['models'];
    $now          = time();
    $ageSeconds   = max(0, $now - $fetchedAt);
    $ageHours     = round($ageSeconds / 3600, 1);

    // ── "all" mode (no specific model requested) ───────────────────────
    if ($model === '') {
        $result = [];
        $allModelIds = array_keys($bundledModels);
        // Also include any models from cache that aren't in bundled
        $allModelIds = array_unique(array_merge($allModelIds, array_keys($cachedModels)));

        foreach ($allModelIds as $firmwareId) {
            $localVer = '';
            $localMd5 = '';
            if (isset($bundledModels[$firmwareId])) {
                $localVer = (string) ($bundledModels[$firmwareId]['bundledVersion'] ?? '');
                $localMd5 = (string) ($bundledModels[$firmwareId]['bundledMd5'] ?? '');
            }

            $latest    = null;
            $latestMd5 = null;
            if (isset($cachedModels[$firmwareId])) {
                $latest    = $cachedModels[$firmwareId]['mcu_version'] ?? null;
                $latestMd5 = $cachedModels[$firmwareId]['mcu_md5']   ?? null;
            }

            $upToDate = null;
            if ($latest !== null && $localVer !== '') {
                $upToDate = ((int) $localVer >= (int) $latest);
            }

            $result[$firmwareId] = [
                'latest'     => $latest,
                'latest_md5' => $latestMd5,
                'local'      => $localVer,
                'local_md5'  => $localMd5,
                'up_to_date' => $upToDate,
            ];
        }

        echo json_encode([
            'ok'              => true,
            'models'          => $result,
            'cache_age_hours' => $ageHours,
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // ── Single model lookup ─────────────────────────────────────────────
    // If local param is empty, try to get it from bundled data
    if ($local === '' && isset($bundledModels[$model]['bundledVersion'])) {
        $local = (string) $bundledModels[$model]['bundledVersion'];
    }

    $latest    = null;
    $latestMd5 = null;
    $note      = null;

    if (isset($cachedModels[$model])) {
        $latest    = $cachedModels[$model]['mcu_version'] ?? null;
        $latestMd5 = $cachedModels[$model]['mcu_md5']   ?? null;
    } else {
        $note = 'Model not found in version cache. Only bundled version available.';
    }

    // Compare versions as integers (4-digit MCU format, e.g. "0013")
    $upToDate = null;
    if ($latest !== null && $local !== '') {
        $upToDate = ((int) $local >= (int) $latest);
    }

    $response = [
        'ok'              => true,
        'model'           => $model,
        'latest'          => $latest,
        'latest_md5'      => $latestMd5,
        'local'           => $local,
        'up_to_date'      => $upToDate,
        'cache_age_hours' => $ageHours,
    ];

    if ($note !== null) {
        $response['note'] = $note;
    }

    echo json_encode($response, JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    // Last-resort fallback - always return valid JSON, never a 500 page
    echo json_encode([
        'ok'    => false,
        'error' => 'Internal error: ' . $e->getMessage(),
    ]);
}
