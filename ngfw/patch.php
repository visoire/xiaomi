<?php
declare(strict_types=1);

/**
 * ngfw/patch.php
 * Backend endpoint for the NGFW patcher. Accepts an uploaded DRV firmware,
 * a family (mi / nb), a model and a patch list. Runs ngfw_cli.py and answers
 * with JSON: the patch report and, unless simulation is on, the patched
 * firmware as base64 (DRV images are small).
 *
 * NGFW patcher core: Daljeet Nandha (AGPL-3.0). Web integration by visoire.
 */

require_once dirname(__DIR__) . '/inc/functions.php';

const NGFW_ROOT = __DIR__;
const NGFW_CLI = NGFW_ROOT . '/ngfw_cli.py';

function ngfw_error(string $message, int $http = 400): void
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ngfw_error('Please submit your request using the patch form.', 405);
}
if (!checkRateLimit('ngfw', 10, 60)) {
    ngfw_error('Too many requests. Please wait a moment and try again.', 429);
}

$family = (string)($_POST['family'] ?? '');
$model  = (string)($_POST['model'] ?? '');
$debug  = ($_POST['debug'] ?? '0') === '1';
$sim    = ($_POST['simulation'] ?? '0') === '1';

if ($family !== 'mi' && $family !== 'nb') {
    ngfw_error('Please choose a scooter family (Xiaomi or Ninebot).');
}
$familyModels = ['mi' => ['1s', 'pro2', 'lite', 'mi3', '4pro', '4proita'], 'nb' => ['4plus', '4max', 'f2pro', 'f2plus', 'f2', 'g2']];
if (!in_array($model, $familyModels[$family] ?? [], true)) {
    ngfw_error('Please choose a supported model.');
}

// Patch tokens: code or code=number. Validated again by the Python CLI.
$requested = json_decode((string)($_POST['patches'] ?? '[]'), true);
if (!is_array($requested) || !$requested) {
    ngfw_error('Please select at least one modification.');
}
$tokens = [];
foreach ($requested as $item) {
    if (!is_array($item) || !isset($item['id']) || !preg_match('/^[a-z]{3}$/', (string)$item['id'])) {
        ngfw_error('Invalid modification settings. Please reselect your options.');
    }
    $tok = (string)$item['id'];
    if (isset($item['value']) && $item['value'] !== '' && $item['value'] !== null) {
        if (!is_numeric($item['value'])) {
            ngfw_error('Modification values must be numbers.');
        }
        $tok .= '=' . (string)(float)$item['value'];
    }
    $tokens[] = $tok;
}

if (!isset($_FILES['firmware'])) {
    ngfw_error('Please select a firmware file (.bin) to patch.');
}
$errs = validateFirmwareFile($_FILES['firmware']);
if ($errs) {
    ngfw_error(implode(' ', $errs));
}

$sysTmp = sys_get_temp_dir();
$tmpDir = (is_dir($sysTmp) && is_writable($sysTmp)) ? $sysTmp : NGFW_ROOT . '/tmp';
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0777, true);
}
if (!is_dir($tmpDir) || !is_writable($tmpDir)) {
    ngfw_error('Server temporary storage is not writable.', 500);
}
$inFile  = $tmpDir . '/ngfw_in_'  . bin2hex(random_bytes(8)) . '.bin';
$outFile = $tmpDir . '/ngfw_out_' . bin2hex(random_bytes(8)) . '.bin';
if (!is_uploaded_file($_FILES['firmware']['tmp_name']) || !move_uploaded_file($_FILES['firmware']['tmp_name'], $inFile)) {
    ngfw_error('File upload failed. Please choose your file again.');
}

$python = resolve_python_binary();
if (!is_file($python) || !is_file(NGFW_CLI)) {
    @unlink($inFile);
    ngfw_error('Server Python environment is misconfigured.', 500);
}

$cmd = [$python, NGFW_CLI, $family, $model, $inFile, $outFile, implode(',', $tokens)];
if ($debug) { $cmd[] = '--debug'; }
if ($sim)   { $cmd[] = '--simulation'; }

$proc = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, NGFW_ROOT, null, ['bypass_shell' => true]);
$stdout = '';
$stderr = '';
if (is_resource($proc)) {
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($proc);
}
@unlink($inFile);

$result = json_decode(trim($stdout), true);
if (!is_array($result) || empty($result['ok'])) {
    @unlink($outFile);
    if (is_array($result) && !empty($result['error'])) {
        ngfw_error('Patching failed: ' . (string)$result['error'], 422);
    }
    $clean = trim($stderr);
    ngfw_error('Patching error: ' . ($clean !== '' ? substr($clean, 0, 600) : 'no response from the patcher'), 422);
}

if (!$sim) {
    if (!is_file($outFile) || filesize($outFile) < 4096) {
        @unlink($outFile);
        ngfw_error('Could not generate a valid patched firmware file.', 500);
    }
    $result['file_b64'] = base64_encode((string)file_get_contents($outFile));
    $result['filename'] = 'ngfw_' . $family . '_' . $model . '.bin';
    @unlink($outFile);
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode($result, JSON_UNESCAPED_SLASHES);
