<?php
declare(strict_types=1);

/**
 * prepare.php (setup)
 * Returns firmware bytes for the wizard, either the raw bundled stock firmware
 * or a patched build. The browser holds the bytes in memory and flashes them
 * over Web Serial, so this endpoint streams the binary directly.
 *
 *   GET  prepare.php?model=mi5            -> raw bundled stock firmware
 *   POST prepare.php (model, mode, patches[, firmware])
 *        no patches  -> raw firmware (stock for local, the upload for custom)
 *        patches     -> patched firmware built by the Python bwpatcher
 *
 * Setup by visoire, open source. Patch core by ScooterTeam, CC BY-NC-SA 4.0.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../inc/functions.php';

function prep_error(string $message, int $http = 400): void
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}

function stream_bin(string $path, string $name, int $changed): void
{
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: inline; filename="' . $name . '"');
    header('Content-Length: ' . (string) filesize($path));
    header('X-Changed-Bytes: ' . (string) $changed);
    header('Cache-Control: no-store');
    readfile($path);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$model = (string) ($_REQUEST['model'] ?? '');
if (!preg_match('/^[a-z0-9]+$/', $model)) {
    prep_error('Please choose a valid scooter model.');
}
$modelDef = setup_find_model($model);
if ($modelDef === null) {
    prep_error('The selected scooter model is not recognized. Please choose your model again.');
}

// ── GET: raw bundled stock firmware (used for the version check and no-patch flow)
if ($method === 'GET') {
    if (empty($modelDef['localAvailable'])) {
        prep_error('Bundled firmware is not available for this model. Please provide your own firmware file.');
    }
    $stock = SETUP_DATA_ROOT . '/' . $modelDef['filename'];
    if (!is_file($stock)) {
        prep_error('Original firmware is temporarily unavailable on the server.', 500);
    }
    stream_bin($stock, 'stock_' . $model . '.bin', 0);
}

if ($method !== 'POST') {
    prep_error('Invalid request method. Please reload the wizard.', 405);
}

// Apply rate limiting
if (!checkRateLimit('setup', 10, 60)) {
    prep_error('Too many requests. Please wait a moment and try again.', 429);
}

// Verify Cloudflare Turnstile if token is present
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';
if (!empty($turnstileToken) && !verifyTurnstile($turnstileToken)) {
    prep_error('Security verification failed. Please refresh the page and try again.', 403);
}

$mode = (string) ($_POST['mode'] ?? 'local');
if ($mode !== 'local' && $mode !== 'custom') {
    prep_error('Please select either bundled stock firmware or a custom firmware file.');
}
$advanced = isset($_POST['advanced']) && $_POST['advanced'] === '1';
$requested = json_decode((string) ($_POST['patches'] ?? '[]'), true);
if (!is_array($requested)) {
    prep_error('Please choose at least one modification before preparing firmware.');
}

$models = setup_models();
$defs = $models['patchDefs'];
$allowed = $modelDef['patches'] ?? [];

// Build a validated, ordered token list (mirrors patch.php).
$byId = [];
foreach ($requested as $item) {
    if (!is_array($item) || !isset($item['id'])) {
        prep_error('Invalid modification settings. Please reselect your patch options.');
    }
    $pid = (string) $item['id'];
    if (!in_array($pid, $allowed, true) || !isset($defs[$pid])) {
        prep_error('This modification is not available for the selected model.');
    }
    $def = $defs[$pid];
    if ($def['kind'] === 'toggle') {
        $byId[$pid] = $pid;
    } elseif ($def['kind'] === 'speed') {
        $val = isset($item['value']) ? (float) $item['value'] : (float) $def['def'];
        if ($val < (float) $def['min'] || $val > (float) $def['max']) {
            prep_error('Speed value is outside the allowed range for this model.');
        }
        $byId[$pid] = $pid . '=' . number_format($val, 1, '.', '');
    } elseif ($def['kind'] === 'fdv') {
        $fdv = isset($item['value']) ? (string) $item['value'] : (string) $def['def'];
        if (!preg_match('/^\d{4}$/', $fdv)) {
            prep_error('Firmware version number must be exactly 4 digits (e.g. 9999).');
        }
        $byId['fdv'] = 'fdv=' . $fdv;
    }
}

// ── Resolve the input and output firmware files.
$tmpDir = rtrim(SETUP_TMP, '/');
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0777, true);
}
if (!is_dir($tmpDir) || !is_writable($tmpDir)) {
    prep_error('Server temporary storage is not writable. Please contact support or try again later.', 500);
}
$inFile  = $tmpDir . '/setup_in_'  . bin2hex(random_bytes(8)) . '.bin';
$outFile = $tmpDir . '/setup_out_' . bin2hex(random_bytes(8)) . '.bin';

if ($mode === 'custom') {
    if (!isset($_FILES['firmware'])) {
        prep_error('Please select a firmware file (.bin) to upload.');
    }
    // Validate firmware file
    $fileValidationErrors = validateFirmwareFile($_FILES['firmware']);
    if (!empty($fileValidationErrors)) {
        prep_error(implode(' ', $fileValidationErrors));
    }
    if (!is_uploaded_file($_FILES['firmware']['tmp_name']) || !move_uploaded_file($_FILES['firmware']['tmp_name'], $inFile)) {
        prep_error('File upload failed. Please choose your file again.', 500);
    }
} else {
    if (empty($modelDef['localAvailable'])) {
        prep_error('Bundled firmware is not available for this model. Please use the custom firmware option.');
    }
    // filename is relative to mcu/ root, e.g. 'fw/mi4/ada2fc4...bin'
    $stock = SETUP_DATA_ROOT . '/' . $modelDef['filename'];
    if (!is_file($stock) || !copy($stock, $inFile)) {
        prep_error('Original firmware is temporarily unavailable on the server.', 500);
    }
}

// ── No patches selected: return the input firmware unchanged.
if (empty($byId)) {
    stream_bin($inFile, 'firmware_' . $model . '.bin', 0);
}

// ── Patches selected: build the ordered token list and run the patcher.
$canonical = ['dms', 'sld', 'slp', 'sls', 'mss', 'cce', 'rfm', 'fdv'];
$tokens = [];
foreach ($canonical as $pid) {
    if (isset($byId[$pid])) {
        $tokens[] = $byId[$pid];
    }
}
if (!$advanced) {
    $tokens[] = 'chk';
}
if (in_array($model, ['mi5elite', 'mi6', 'mi6lite', 'mi6pro'], true)) {
    $tokens[] = 'img';
}

// ── Run the patcher ───────────────────────────────────────────────────────────
$pythonBin = PYTHON_BIN;
$tokensArg = implode(',', $tokens);

if (!is_file($pythonBin) || !is_executable($pythonBin)) {
    prep_error('Server Python environment is misconfigured. Please contact support.', 500);
}
if (!is_file(SETUP_PATCHER_CLI)) {
    prep_error('Patcher CLI script is missing on the server. Please contact support.', 500);
}

$cmd =
    escapeshellarg($pythonBin) . ' ' .
    escapeshellarg(SETUP_PATCHER_CLI) . ' ' .
    escapeshellarg($model) . ' ' .
    escapeshellarg($inFile) . ' ' .
    escapeshellarg($outFile) . ' ' .
    escapeshellarg($tokensArg);

$descriptor = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];
$pipes = null;
$proc = proc_open($cmd, $descriptor, $pipes, SETUP_PATCHER_DIR, null, ['bypass_shell' => true]);

$stdout = '';
$stderr = '';
$exitCode = -1;
if (is_resource($proc)) {
    fclose($pipes[0]);
    stream_set_blocking($pipes[1], true);
    stream_set_blocking($pipes[2], true);

    $timeout = (defined('PATCHER_TIMEOUT') ? PATCHER_TIMEOUT : 30);
    $startTime = time();
    $stdoutDone = false;
    $stderrDone = false;

    while (!($stdoutDone && $stderrDone) && (time() - $startTime) < $timeout) {
        if (!$stdoutDone) {
            $line = fgets($pipes[1]);
            if ($line === false) { $stdoutDone = true; } else { $stdout .= $line; }
        }
        if (!$stderrDone) {
            $line = fgets($pipes[2]);
            if ($line === false) { $stderrDone = true; } else { $stderr .= $line; }
        }
        if (!$stdoutDone || !$stderrDone) {
            usleep(5000);
        }
    }

    if (!$stdoutDone) {
        $stdout .= stream_get_contents($pipes[1]);
    }
    if (!$stderrDone) {
        $stderr .= stream_get_contents($pipes[2]);
    }

    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);
} else {
    $stderr = 'Failed to spawn patcher process with ' . $pythonBin . ' (cmd=' . $cmd . ')';
}

$result = json_decode(trim($stdout), true);
@unlink($inFile);

if (!is_array($result) || empty($result['ok'])) {
    @unlink($outFile);
    $msg = '';
    if (is_array($result) && !empty($result['error'])) {
        $raw = (string) $result['error'];
        if (stripos($raw, 'pattern') !== false || stripos($raw, 'match') !== false || stripos($raw, 'model') !== false) {
            $msg = 'Patching failed: ' . $raw . ' Please verify that your firmware matches the selected scooter model.';
        } else {
            $msg = 'Patching failed: ' . $raw;
        }
    } elseif (!empty(trim($stderr))) {
        $cleanStderr = trim($stderr);
        if (strlen($cleanStderr) > 600) {
            $cleanStderr = substr($cleanStderr, 0, 600) . '... (truncated)';
        }
        $msg = 'Patching error: ' . $cleanStderr;
    } elseif ($exitCode !== 0) {
        $msg = 'Patching failed with exit code ' . $exitCode . '. Please verify that your firmware matches the selected scooter model, or try again.';
    } else {
        // Exit code 0 but no valid JSON output → server-side spawn or I/O failure
        $msg = 'Server could not complete the patching request (empty response from patcher, exit 0). '
             . 'This is typically caused by a temporary server resource issue. '
             . 'Please retry in a moment, or try selecting fewer patches.';
    }
    prep_error($msg, 422);
}
if (!is_file($outFile) || filesize($outFile) < 4096) {
    @unlink($outFile);
    prep_error('Could not generate a valid patched firmware file. Please check your patch options and try again.', 500);
}

stream_bin($outFile, 'firmware_' . $model . '.bin', (int) ($result['changed_bytes'] ?? 0));
@unlink($outFile);
