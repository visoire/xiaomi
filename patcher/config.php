<?php
declare(strict_types=1);

/**
 * config.php
 * Central configuration for the web patcher backend.
 * Adjust PYTHON_BIN if your server uses a different interpreter path
 * (for example a virtualenv at /var/www/venv/bin/python3).
 *
 * Patcher core: ScooterTeam, CC BY-NC-SA 4.0.
 * Web integration by visoire (open source).
 */

require_once dirname(__DIR__) . '/inc/functions.php';

// Absolute path to the Python 3 interpreter that has keystone-engine,
// capstone and crcmod installed (see bwpatcher/requirements.txt).
if (!defined('PYTHON_BIN')) {
    define('PYTHON_BIN', resolve_python_binary());
}

// Directory of this patcher module.
define('PATCHER_ROOT', __DIR__);

// Python CLI wrapper.
define('PATCHER_CLI', PATCHER_ROOT . '/bwpatcher_cli.py');

// Local stock firmware directory (fw/<model>/stock.bin).
define('PATCHER_FW_DIR', PATCHER_ROOT . '/fw');

// Temporary working directory with fallback if system temp has access issues.
$sysTmp = sys_get_temp_dir();
$patcherTmp = (is_dir($sysTmp) && is_writable($sysTmp)) ? $sysTmp : PATCHER_ROOT . '/tmp';
if (!is_dir($patcherTmp)) {
    @mkdir($patcherTmp, 0777, true);
}
define('PATCHER_TMP', $patcherTmp);

// Maximum accepted upload size in bytes (firmware images are < 1 MB).
define('PATCHER_MAX_UPLOAD', 4 * 1024 * 1024);

// Hard timeout for a single patch run in seconds.
define('PATCHER_TIMEOUT', 30);

// --- Single source of truth: fw/data.json in /mcu/ ---
define('PATCHER_DATA_ROOT', dirname(__DIR__) . '/mcu');
define('PATCHER_FW_DATA',   PATCHER_DATA_ROOT . '/fw/data.json');

/**
 * Slug-to-firmwareId mapping for the 8 supported models.
 * Key   = patcher model ID (short slug used by bwpatcher_cli.py)
 * Value = Xiaomi/mi-fw-info model string
 */
function patcher_slug_map(): array
{
    return [
        'mi4'       => 'dreame.scooter.t2201',
        'mi42'      => 'navee1.scooter.t2209',
        'mi43'      => 'navee1.scooter.t2209g',
        'mi4lite'   => 'navee1.scooter.t2210',
        'mi4pro2nd' => 'xiaomi.scooter.t2336',
        'ultra4'    => 'dreame.scooter.p2301',
        'mi5'       => 'xiaomi.scooter.0005',
        'mi5pro'    => 'xiaomi.scooter.5pro',
        'mi5max'    => 'xiaomi.scooter.5max',
        'mi5elite'  => 'xiaomi.scooter.elite',
        'mi6'       => 'xiaomi.scooter.6',
        'mi6lite'   => 'xiaomi.scooter.6lite',
        'mi6pro'    => 'xiaomi.scooter.6pro',
    ];
}

/**
 * Patch definitions shared across all models.
 */
function patcher_patch_defs(): array
{
    return [
        'sls' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>25.0,'step'=>0.5],
        'sld' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>20.0,'step'=>0.5],
        'slp' => ['kind'=>'speed','min'=>1.0, 'max'=>10.0,'def'=>6.0, 'step'=>0.5],
        'dms' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>25.0,'step'=>0.5],
        'mss' => ['kind'=>'speed','min'=>1.0, 'max'=>10.0,'def'=>3.0, 'step'=>0.5],
        'cce' => ['kind'=>'toggle'],
        'rfm' => ['kind'=>'toggle'],
        'fdv' => ['kind'=>'fdv','def'=>'0001'],
    ];
}

/**
 * Which patches each model supports.
 */
function patcher_model_patches(string $slug): array
{
    switch ($slug) {
        case 'mi4':
        case 'mi42':
        case 'mi43':
            return ['sls', 'sld', 'dms', 'cce', 'rfm', 'fdv'];
        case 'mi4lite':
            return ['sls', 'sld', 'cce', 'rfm', 'fdv'];
        case 'mi4pro2nd':
            return ['sls', 'sld', 'cce', 'rfm', 'mss'];
        case 'ultra4':
            return ['sls', 'sld', 'dms', 'mss', 'cce', 'rfm', 'fdv'];
        case 'mi5':
        case 'mi5max':
            return ['sls', 'sld', 'mss', 'cce', 'rfm', 'fdv'];
        case 'mi5pro':
            return ['sls', 'sld', 'cce', 'rfm', 'mss'];
        case 'mi5elite':
            return ['sls', 'sld', 'slp', 'mss'];
        case 'mi6':
        case 'mi6lite':
            return ['sls', 'sld', 'slp'];
        case 'mi6pro':
            // Stock firmware download is supported in MCU; custom patching is under research
            return [];
        default:
            return [];
    }
}

/**
 * Protocol string per model slug.
 */
function patcher_protocol(string $slug): string
{
    return in_array($slug, ['mi5elite', 'mi6', 'mi6lite', 'mi6pro', 'mi4lite2nd'], true) ? 'LEQI' : 'Brightway';
}

function patcher_connector(string $slug): string
{
    switch ($slug) {
        case 'mi5elite':
        case 'mi6':
        case 'mi6lite':
        case 'mi6pro':
        case 'mi4lite2nd':
            return 'M8 MALE';
        case 'mi5':
        case 'mi4pro2nd':
            return 'M7 FEMALE';
        default:
            return 'M7 MALE';
    }
}

/**
 * Load and decode models from the fw/data.json, synthesising the structure
 * that patcher.js / patch.php expect.
 *
 * @return array{patchDefs: array<string,mixed>, models: array<int,array<string,mixed>>}
 */
function patcher_models(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $slugMap   = patcher_slug_map();
    $firmwareMap = array_flip($slugMap); // firmwareId => slug
    $patchDefs = patcher_patch_defs();
    $models    = [];

    $raw  = @file_get_contents(PATCHER_FW_DATA);
    $data = $raw !== false ? json_decode($raw, true) : null;

    if (is_array($data)) {
        foreach ($data as $item) {
            if (!is_array($item)) continue;
            $firmwareId = (string)($item['model'] ?? '');
            if (!isset($firmwareMap[$firmwareId])) continue;

            $slug  = $firmwareMap[$firmwareId];
            $fw    = $item['firmware'] ?? [];
            $extra = $item['extra']    ?? [];

            // Local image
            $localImgFile = PATCHER_DATA_ROOT . '/fw/' . $slug . '/' . $slug . '.png';
            $image = file_exists($localImgFile)
                ? '../mcu/fw/' . $slug . '/' . $slug . '.png'
                : (string)($extra['imageMin'] ?? '');

            // Locate stock firmware binary in fw/<slug>/
            $fwDir    = PATCHER_DATA_ROOT . '/fw/' . $slug . '/';
            $filename = '';
            if (is_dir($fwDir)) {
                foreach (scandir($fwDir) as $f) {
                    if (str_ends_with((string)$f, '.bin')) { $filename = 'fw/' . $slug . '/' . $f; break; }
                }
            }

            $models[] = [
                'id'             => $slug,
                'firmwareId'     => $firmwareId,
                'name'           => $item['name'] ?? $firmwareId,
                'image'          => $image,
                'bundledVersion' => (string)($fw['mcu_version'] ?? ''),
                'bundledMd5'     => (string)($fw['mcu_md5']     ?? ''),
                'filename'       => $filename,
                'mcu_safe_url'   => (string)($fw['mcu_safe_url'] ?? ''),
                'protocol'       => patcher_protocol($slug),
                'connector'      => patcher_connector($slug),
                'localAvailable' => $filename !== '',
                'patches'        => patcher_model_patches($slug),
                'experimental'   => in_array($slug, ['mi6', 'mi6lite', 'mi6pro'], true),
            ];
        }
    }

    $cache = [
        'patchDefs' => $patchDefs,
        'models'    => $models,
    ];
    return $cache;
}

/**
 * Find a model definition by its slug id.
 *
 * @return array<string,mixed>|null
 */
function patcher_find_model(string $id): ?array
{
    foreach (patcher_models()['models'] as $m) {
        if (($m['id'] ?? '') === $id) {
            return $m;
        }
    }
    return null;
}
