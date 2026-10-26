<?php
declare(strict_types=1);

/**
 * config.php (setup)
 * Configuration for the guided setup wizard. The wizard reuses the verified
 * patcher backend (Python bwpatcher and the bundled stock firmware) instead of
 * duplicating it, so there is a single source of truth for patching.
 *
 * Adjust PYTHON_BIN to your interpreter that has keystone-engine, capstone and
 * crcmod installed. Adjust SETUP_FW_INDEX_URL to the firmware index you keep in
 * sync with mi-fw-info.
 *
 * Setup by visoire, open source. Patch core by ScooterTeam, CC BY-NC-SA 4.0.
 */

require_once dirname(__DIR__) . '/inc/functions.php';

// Python 3 interpreter with the patch dependencies installed.
if (!defined('PYTHON_BIN')) {
    define('PYTHON_BIN', resolve_python_binary());
}

// The patcher module that holds the verified CLI, modules and stock firmware.
define('SETUP_PATCHER_DIR', dirname(__DIR__) . '/patcher');

// Python CLI wrapper inside the patcher module.
define('SETUP_PATCHER_CLI', SETUP_PATCHER_DIR . '/bwpatcher_cli.py');

// --- Single source of truth: mcu/fw/data.json ---
define('SETUP_DATA_ROOT', dirname(__DIR__) . '/mcu');

// Bundled stock firmware directory (fw/<model>/) inside the mcu directory
define('SETUP_FW_DIR', SETUP_DATA_ROOT . '/fw');

// Absolute path to the central firmware data file
define('SETUP_FW_DATA', SETUP_DATA_ROOT . '/fw/data.json');

// Temp working directory with fallback if system temp has access issues.
$sysTmp = sys_get_temp_dir();
$setupTmp = (is_dir($sysTmp) && is_writable($sysTmp)) ? $sysTmp : dirname(__DIR__) . '/patcher/tmp';
if (!is_dir($setupTmp)) {
    @mkdir($setupTmp, 0777, true);
}
define('SETUP_TMP', $setupTmp);

// Max accepted custom upload in bytes.
define('SETUP_MAX_UPLOAD', 4 * 1024 * 1024);

/**
 * Slug-to-firmwareId mapping (must match patcher/config.php).
 */
function setup_slug_map(): array
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
 * Which patches each model supports (must match patcher/config.php).
 */
function setup_model_patches(string $slug): array
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
 * Load and decode the bundled models from the central fw/data.json once.
 *
 * @return array{patchDefs: array<string,mixed>, models: array<int,array<string,mixed>>}
 */
function setup_models(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $slugMap     = setup_slug_map();
    $firmwareMap = array_flip($slugMap);
    $patchDefs   = [
        'sls' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>25.0,'step'=>0.5],
        'sld' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>20.0,'step'=>0.5],
        'slp' => ['kind'=>'speed','min'=>1.0, 'max'=>10.0,'def'=>6.0, 'step'=>0.5],
        'dms' => ['kind'=>'speed','min'=>10.0,'max'=>35.0,'def'=>25.0,'step'=>0.5],
        'mss' => ['kind'=>'speed','min'=>1.0, 'max'=>10.0,'def'=>3.0, 'step'=>0.5],
        'cce' => ['kind'=>'toggle'],
        'rfm' => ['kind'=>'toggle'],
        'fdv' => ['kind'=>'fdv','def'=>'0001'],
    ];
    $models = [];

    $raw  = @file_get_contents(SETUP_FW_DATA);
    $data = $raw !== false ? json_decode($raw, true) : null;

    if (is_array($data)) {
        foreach ($data as $item) {
            if (!is_array($item)) continue;
            $firmwareId = (string)($item['model'] ?? '');
            if (!isset($firmwareMap[$firmwareId])) continue;

            $slug  = $firmwareMap[$firmwareId];
            $fw    = $item['firmware'] ?? [];
            $extra = $item['extra']    ?? [];

            $localImgFile = SETUP_DATA_ROOT . '/fw/' . $slug . '/' . $slug . '.png';
            $image = file_exists($localImgFile)
                ? '../mcu/fw/' . $slug . '/' . $slug . '.png'
                : (string)($extra['imageMin'] ?? '');

            $fwDir    = SETUP_DATA_ROOT . '/fw/' . $slug . '/';
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
                'protocol'       => in_array($slug, ['mi5elite', 'mi6', 'mi6lite', 'mi6pro', 'mi4lite2nd'], true) ? 'LEQI' : 'Brightway',
                'connector'      => setup_connector($slug),
                'localAvailable' => $filename !== '',
                'patches'        => setup_model_patches($slug),
                'experimental'   => in_array($slug, ['mi6', 'mi6lite', 'mi6pro'], true),
            ];
        }
    }

    $cache = ['patchDefs' => $patchDefs, 'models' => $models];
    return $cache;
}

/**
 * Connector type per model slug.
 */
function setup_connector(string $slug): string
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
 * Find a model definition by id.
 *
 * @return array<string,mixed>|null
 */
function setup_find_model(string $id): ?array
{
    foreach (setup_models()['models'] as $m) {
        if (($m['id'] ?? '') === $id) {
            return $m;
        }
    }
    return null;
}
