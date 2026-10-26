<?php
declare(strict_types=1);

/**
 * fw_index.php
 * Serves the MCU firmware index (data.json) with proper caching and CORS
 * headers. The frontend version checkers (patcher / setup wizard) fetch this
 * to compare bundled MCU hashes against the latest CDN versions.
 *
 * Using a PHP proxy avoids HTTP 403 issues that can occur when static .json
 * files are served directly through Apache with the existing .htaccess rules.
 *
 * Built by visoire.
 */

$file = __DIR__ . '/fw/data.json';

if (!file_exists($file) || !is_readable($file)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Index file not found']);
    exit;
}

$json = file_get_contents($file);

if ($json === false) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Failed to read index file']);
    exit;
}

$data = json_decode($json, true);

if (!is_array($data)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Invalid JSON in index file']);
    exit;
}

// Cache for 15 minutes (client and proxy caches) to reduce load
$maxAge = 900;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=' . $maxAge . ', must-revalidate');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
header('X-Content-Type-Options: nosniff');

// Allow same-origin and the patcher/setup subdirectories
header('Access-Control-Allow-Origin: *');

echo $json;
