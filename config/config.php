<?php

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '8889');
define('DB_NAME', 'klinik_app');
define('DB_USER', 'root');
define('DB_PASS', 'root');

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$autoBaseUrl = ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $autoBaseUrl);

date_default_timezone_set('Asia/Jakarta');
                                                                                                                                                                                                
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => BASE_URL ?: '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}