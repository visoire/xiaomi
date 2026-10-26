<?php
declare(strict_types=1);

require_once __DIR__ . '/lang.php';

// ---------------------------------------------------------------------------
// Scooter data
// ---------------------------------------------------------------------------

function getScooterOptions(): array
{
    return [
        'M7_MALE' => [
            ['name' => 'Xiaomi Scooter 3 Lite', 'flashable' => false],
            ['name' => 'Xiaomi Scooter 4', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 4 Lite', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 4 Pro', 'flashable' => false],
            ['name' => 'Xiaomi Scooter 4 Pro Max', 'flashable' => false],
            ['name' => 'Xiaomi Scooter 4 Pro Plus', 'flashable' => false],
            ['name' => 'Xiaomi Scooter 4 Ultra', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 5 Max', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 5 Pro', 'flashable' => true],
        ],
        'M7_FEMALE' => [
            ['name' => 'Xiaomi Scooter 4 Pro (2nd Gen)', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 5', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 5 Plus', 'flashable' => false],
        ],
        'M8_MALE' => [
            ['name' => 'Xiaomi Scooter 4 Lite (2nd Gen)', 'flashable' => false],
            ['name' => 'Xiaomi Scooter Elite', 'flashable' => true],
            ['name' => 'Xiaomi Scooter 6', 'flashable' => false, 'testing' => true],
            ['name' => 'Xiaomi Scooter 6 Lite', 'flashable' => false, 'testing' => true],
            ['name' => 'Xiaomi Scooter 6 Pro', 'flashable' => false, 'testing' => true],
            ['name' => 'Xiaomi Scooter 6 Max', 'flashable' => false],
            ['name' => 'Xiaomi Scooter 6 Ultra', 'flashable' => false],
        ],
    ];
}

function getFlashableStatus(string $model): bool
{
    $opts = getScooterOptions();
    foreach ($opts as $group) {
        foreach ($group as $m) {
            if ($m['name'] === $model) {
                return $m['flashable'];
            }
        }
    }
    return true;
}

function getConnectorType(string $model): string
{
    $opts = getScooterOptions();
    foreach ($opts['M7_FEMALE'] as $m) {
        if ($m['name'] === $model) {
            return 'FEMALE';
        }
    }
    foreach ($opts['M7_MALE'] as $m) {
        if ($m['name'] === $model) {
            return 'MALE';
        }
    }
    foreach ($opts['M8_MALE'] as $m) {
        if ($m['name'] === $model) {
            return 'MALE';
        }
    }
    return '';
}

function getConnectorLabel(string $model): string
{
    $opts = getScooterOptions();
    foreach ($opts['M7_MALE'] as $m) {
        if ($m['name'] === $model) {
            return 'M7 MALE';
        }
    }
    foreach ($opts['M7_FEMALE'] as $m) {
        if ($m['name'] === $model) {
            return 'M7 FEMALE';
        }
    }
    foreach ($opts['M8_MALE'] as $m) {
        if ($m['name'] === $model) {
            return 'M8 MALE';
        }
    }
    return '';
}

function isValidModel(string $model): bool
{
    return getConnectorType($model) !== '';
}

// ---------------------------------------------------------------------------
// Security & Utilities
// ---------------------------------------------------------------------------

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// ---------------------------------------------------------------------------
// Rate Limiting & Captcha (Cloudflare Turnstile)
// ---------------------------------------------------------------------------

define('TURNSTILE_SITE_KEY', '0x4AAAAAABu4yov2SkEUYDfh');
define('TURNSTILE_SECRET_KEY', '0x4AAAAAABu4yh7e7JmXZedfFTMQWomuHh0');

function getClientIp(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    return $ip;
}

function checkRateLimit(string $endpoint, int $limit, int $period): bool
{
    // Skip rate limiting for endpoints that need stable streaming connections
    if (in_array($endpoint, ['flash_stream', 'firmware_download'], true)) {
        return true;
    }

    // Skip rate limiting for local development / testing
    $ip = getClientIp();
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost', '0.0.0.0'], true)) {
        return true;
    }

    try {
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vivalet_rate_limits';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        $file = $cacheDir . DIRECTORY_SEPARATOR . md5($endpoint . '_' . $ip) . '.json';
        $now = time();
        $cutoff = $now - $period;

        $timestamps = [];
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if ($content !== false) {
                $data = json_decode($content, true);
                if (is_array($data)) {
                    foreach ($data as $ts) {
                        if (is_int($ts) && $ts >= $cutoff) {
                            $timestamps[] = $ts;
                        }
                    }
                }
            }
        }

        if (count($timestamps) >= $limit) {
            return false;
        }

        $timestamps[] = $now;
        @file_put_contents($file, json_encode($timestamps), LOCK_EX);
        return true;
    } catch (Throwable $e) {
        return true;
    }
}

function verifyTurnstile(string $token): bool
{
    $secret = defined('TURNSTILE_SECRET_KEY') ? TURNSTILE_SECRET_KEY : '';
    if (empty($secret) || $secret === 'YOUR_SECRET_KEY_HERE') {
        return true;
    }

    $ip = getClientIp();
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $ip,
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 5,
        ],
    ];

    try {
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }
        $response = json_decode($result, true);
        return isset($response['success']) && $response['success'] === true;
    } catch (Throwable $e) {
        return false;
    }
}

// ---------------------------------------------------------------------------
// File Upload Validation
// ---------------------------------------------------------------------------

function validateFirmwareFile(array $file): array
{
    $errors = [];

    // 1. Check upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'The file exceeds the upload limit (maximum 4MB). Please select a smaller file.',
            UPLOAD_ERR_FORM_SIZE  => 'The file exceeds the form upload limit. Please select a file under 4MB.',
            UPLOAD_ERR_PARTIAL    => 'File upload was interrupted. Please select your file and try again.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected. Please choose a .bin firmware file.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temporary folder is missing. Please contact support.',
            UPLOAD_ERR_CANT_WRITE => 'Could not save the file to server storage. Please try again.',
            UPLOAD_ERR_EXTENSION  => 'Upload was blocked by a server security rule. Please check your file.',
        ];
        $errors[] = $errorMessages[$file['error']] ?? 'Upload failed with error code: ' . $file['error'];
        return $errors;
    }

    // 2. Check file size
    $maxSize = 4 * 1024 * 1024; // 4MB
    if ($file['size'] > $maxSize) {
        $maxMb = $maxSize / (1024 * 1024);
        $errors[] = "The file is too large. Maximum allowed size is {$maxMb}MB.";
    }

    // 3. Check file extension
    $allowedExtensions = ['bin', 'zip', 'enc'];
    $fileName = strtolower($file['name']);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    if (!in_array($fileExt, $allowedExtensions, true)) {
        $errors[] = 'Unsupported file type. Please upload a valid .bin firmware file.';
    }

    // 4. Check for ELF header (system executables disguised as firmware)
    if ($file['tmp_name'] && file_exists($file['tmp_name'])) {
        $firstBytes = file_get_contents($file['tmp_name'], false, null, 0, 16);
        if ($firstBytes !== false && strlen($firstBytes) >= 4) {
            if ($firstBytes[0] === "\x7f" && $firstBytes[1] === 'E' && $firstBytes[2] === 'L' && $firstBytes[3] === 'F') {
                $errors[] = 'Executable system file detected. Please upload an official .bin MCU firmware file.';
            }
        }
    }

    // 5. Block dangerous extensions
    $dangerousExtensions = ['exe', 'bat', 'sh', 'js', 'py', 'php', 'msi', 'elf', 'html', 'htm', 'svg', 'jsp', 'war', 'jar'];
    $originalName = strtolower($file['name']);
    $parts = explode('.', $originalName);
    foreach ($parts as $part) {
        if (in_array($part, $dangerousExtensions, true)) {
            $errors[] = 'Invalid file extension detected. Please upload a standard .bin firmware file.';
            break;
        }
    }

    return $errors;
}

function getFirmwareValidationRules(): array
{
    return [
        'maxSize' => 4 * 1024 * 1024,
        'allowedExtensions' => ['bin', 'zip', 'enc'],
        'dangerousExtensions' => ['exe', 'bat', 'sh', 'js', 'py', 'php', 'msi', 'elf', 'html', 'htm', 'svg', 'jsp', 'war', 'jar'],
    ];
}

/**
 * Dynamically discover a working Python 3 binary on the system.
 */
function resolve_python_binary(): string
{
    if (defined('PYTHON_BIN') && @is_file(PYTHON_BIN) && @is_executable(PYTHON_BIN)) {
        return PYTHON_BIN;
    }
    $envBin = getenv('PYTHON_BIN');
    if ($envBin && @is_file($envBin) && @is_executable($envBin)) {
        return $envBin;
    }

    $candidates = [];

    if (PHP_OS_FAMILY === 'Windows') {
        $username = getenv('USERNAME') ?: get_current_user();
        if ($username) {
            $candidates[] = "C:\\Users\\{$username}\\AppData\\Local\\Python\\bin\\python.exe";
            $candidates[] = "C:\\Users\\{$username}\\AppData\\Local\\Programs\\Python\\Python314\\python.exe";
            $candidates[] = "C:\\Users\\{$username}\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
            $candidates[] = "C:\\Users\\{$username}\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";
            $candidates[] = "C:\\Users\\{$username}\\AppData\\Local\\Programs\\Python\\Python311\\python.exe";
        }
        $candidates[] = 'C:\\Users\\user\\AppData\\Local\\Python\\bin\\python.exe';
        $candidates[] = 'C:\\xampp\\python\\python.exe';
        $candidates[] = 'C:\\Python314\\python.exe';
        $candidates[] = 'C:\\Python313\\python.exe';
        $candidates[] = 'C:\\Python312\\python.exe';
        $candidates[] = 'C:\\Python311\\python.exe';

        $where = @shell_exec('where.exe python 2>NUL');
        if ($where) {
            foreach (explode("\n", trim($where)) as $line) {
                $line = trim($line);
                if ($line && !str_contains($line, 'WindowsApps')) {
                    $candidates[] = $line;
                }
            }
        }
    } else {
        $candidates[] = '/var/www/html/visoire/xiaomi/venv/bin/python3';
        $candidates[] = '/usr/bin/python3';
        $candidates[] = '/usr/local/bin/python3';
        $which = @shell_exec('which python3 2>/dev/null');
        if ($which) {
            $candidates[] = trim($which);
        }
    }

    foreach ($candidates as $cand) {
        if (@is_file($cand) && @is_executable($cand)) {
            return $cand;
        }
    }

    return PHP_OS_FAMILY === 'Windows'
        ? 'C:\\Users\\user\\AppData\\Local\\Python\\bin\\python.exe'
        : '/var/www/html/visoire/xiaomi/venv/bin/python3';
}

