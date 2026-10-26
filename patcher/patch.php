<?php
declare(strict_types=1);

/**
 * patch.php
 * Backend endpoint for the web patcher. Receives a model, a patch list and
 * either an uploaded firmware (mode=upload) or uses the bundled stock firmware
 * (mode=local). Runs the Python bwpatcher CLI and streams the patched .bin back
 * to the browser, or returns a JSON error.
 *
 * Patcher core: ScooterTeam, CC BY-NC-SA 4.0.
 * Web integration by visoire (open source).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../inc/functions.php';

/**
 * Send a JSON error and stop.
 */
function patch_error(string $message, int $http = 400): void
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    patch_error('Please submit your request using the patch form.', 405);
}

// Apply rate limiting
if (!checkRateLimit('patcher', 10, 60)) {
    patch_error('Too many requests. Please wait a moment and try again.', 429);
}

// Verify Cloudflare Turnstile if token is present
$turnstileToken = $_POST['cf-turnstile-response'] ?? '';
if (!empty($turnstileToken) && !verifyTurnstile($turnstileToken)) {
    patch_error('Security verification failed. Please refresh the page and try again.', 403);
}

// ── Inputs ──────────────────────────────────────────────────────────────────
$model    = isset($_POST['model']) ? (string) $_POST['model'] : '';
$mode     = isset($_POST['mode']) ? (string) $_POST['mode'] : 'local';
$advanced = isset($_POST['advanced']) && $_POST['advanced'] === '1';
$rawList  = isset($_POST['patches']) ? (string) $_POST['patches'] : '[]';

// Model id must be a known, safe token.
if (!preg_match('/^[a-z0-9]+$/', $model)) {
    patch_error('Please choose a valid scooter model.');
}
$modelDef = patcher_find_model($model);
if ($modelDef === null) {
    patch_error('The selected scooter model is not supported. Please choose your model again.');
}
if ($mode !== 'local' && $mode !== 'upload') {
    patch_error('Please select either bundled stock firmware or upload your own .bin file.');
}

// ── Validate the requested patches against the model definition ───────────────
$requested = json_decode($rawList, true);
if (!is_array($requested)) {
    patch_error('Please choose at least one modification before patching.');
}

$defs    = patcher_models()['patchDefs'];
$allowed = $modelDef['patches'] ?? [];
$byId    = [];

foreach ($requested as $item) {
    if (!is_array($item) || !isset($item['id'])) {
        patch_error('Invalid modification settings. Please reselect your patch options.');
    }
    $pid = (string) $item['id'];
    if (!in_array($pid, $allowed, true) || !isset($defs[$pid])) {
        patch_error('This modification is not available for the selected model.');
    }
    $def = $defs[$pid];

    if ($def['kind'] === 'toggle') {
        $byId[$pid] = $pid;
    } elseif ($def['kind'] === 'speed') {
        $val = isset($item['value']) ? (float) $item['value'] : (float) $def['def'];
        if ($val < (float) $def['min'] || $val > (float) $def['max']) {
            patch_error('Speed value is outside the allowed range for this model.');
        }
        // One decimal place, matching the desktop tool.
        $byId[$pid] = $pid . '=' . number_format($val, 1, '.', '');
    } elseif ($def['kind'] === 'fdv') {
        $fdv = isset($item['value']) ? (string) $item['value'] : (string) $def['def'];
        if (!preg_match('/^\d{4}$/', $fdv)) {
            patch_error('Firmware version number must be exactly 4 digits (e.g. 9999).');
        }
        $byId['fdv'] = 'fdv=' . $fdv;
    }
}

if (empty($byId)) {
    patch_error('Please select at least one modification before patching.');
}

// Apply patches in a canonical order. Several patches modify overlapping code
// regions (notably sls and sld), so the order is verified to avoid pattern
// search failures across every supported model.
$canonical = ['dms', 'sld', 'slp', 'sls', 'mss', 'cce', 'rfm', 'fdv'];
$tokens = [];
foreach ($canonical as $pid) {
    if (isset($byId[$pid])) {
        $tokens[] = $byId[$pid];
    }
}

// Append the checksum fix unless the user patches a raw full dump.
if (!$advanced) {
    $tokens[] = 'chk';
}

// LEQI models need a full image rebuild after the checksum fix.
if (in_array($model, ['mi5elite', 'mi6', 'mi6lite', 'mi6pro'], true)) {
    $tokens[] = 'img';
}

// ── Resolve the input firmware ────────────────────────────────────────────────
$tmpDir = rtrim(PATCHER_TMP, '/');
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0777, true);
}
if (!is_dir($tmpDir) || !is_writable($tmpDir)) {
    patch_error('Server temporary storage is not writable. Please contact support or try again later.', 500);
}
$inFile  = $tmpDir . '/bwp_in_'  . bin2hex(random_bytes(8)) . '.bin';
$outFile = $tmpDir . '/bwp_out_' . bin2hex(random_bytes(8)) . '.bin';

if ($mode === 'upload') {
    if (!isset($_FILES['firmware'])) {
        patch_error('Please select a firmware file (.bin) to upload.');
    }
    // Validate firmware file
    $fileValidationErrors = validateFirmwareFile($_FILES['firmware']);
    if (!empty($fileValidationErrors)) {
        patch_error(implode(' ', $fileValidationErrors));
    }
    if (!is_uploaded_file($_FILES['firmware']['tmp_name'])) {
        patch_error('File upload failed. Please choose your file again.');
    }
    if (!move_uploaded_file($_FILES['firmware']['tmp_name'], $inFile)) {
        patch_error('Could not save the uploaded file. Please try again.', 500);
    }
} else {
    if (empty($modelDef['localAvailable'])) {
        patch_error('Bundled firmware is not available for this model. Please upload your own .bin file.');
    }
    // filename in modelDef is relative to mcu/ root, e.g. 'fw/mi4/ada2fc4...bin'
    $stock = PATCHER_DATA_ROOT . '/' . $modelDef['filename'];
    if (!is_file($stock)) {
        patch_error('Original firmware is temporarily unavailable on the server. Please upload your .bin file directly.', 500);
    }
    if (!copy($stock, $inFile)) {
        patch_error('Could not prepare firmware for patching. Please try again.', 500);
    }
}

// ── Run the patcher ───────────────────────────────────────────────────────────
$pythonBin = PYTHON_BIN;
$tokensArg = implode(',', $tokens);

if (!is_file($pythonBin) || !is_executable($pythonBin)) {
    patch_error('Server Python environment is misconfigured. Please contact support.', 500);
}
if (!is_file(PATCHER_CLI)) {
    patch_error('Patcher CLI script is missing on the server. Please contact support.', 500);
}

$cmd =
    escapeshellarg($pythonBin) . ' ' .
    escapeshellarg(PATCHER_CLI) . ' ' .
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
$proc = proc_open($cmd, $descriptor, $pipes, PATCHER_ROOT, null, ['bypass_shell' => true]);

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
    patch_error($msg, 422);
}

if (!is_file($outFile) || filesize($outFile) < 4096) {
    @unlink($outFile);
    patch_error('Could not generate a valid patched firmware file. Please check your patch options and try again.', 500);
}

// ── Stream the patched firmware as a download ─────────────────────────────────
$downloadName = 'patched_' . $model . '_firmware.bin';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . (string) filesize($outFile));
header('X-Patch-Changed-Bytes: ' . (string) ($result['changed_bytes'] ?? 0));
header('X-Patch-List: ' . implode(',', $tokens));
header('Cache-Control: no-store');
readfile($outFile);
@unlink($outFile);
exit;
