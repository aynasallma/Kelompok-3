<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Icon.php';
require_once __DIR__ . '/vendor/autoload.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

/* ---------- Helper global ---------- */

function icon($name, $class = '', $size = 18, $strokeWidth = 2)
{
    return Icon::render($name, $class, $size, $strokeWidth);
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function route($path = '')
{
    return BASE_URL . '/index.php?url=' . $path;
}

function asset($path)
{
    $file = __DIR__ . '/public/' . $path;
    $version = is_file($file) ? '?v=' . filemtime($file) : '';
    return BASE_URL . '/public/' . $path . $version;
}

function rupiah($angka)
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

function tgl($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }
    return date('d/m/Y', strtotime($tanggal));
}

function is_logged_in()
{
    return isset($_SESSION['user']);
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function has_role(...$roles)
{
    return is_logged_in() && in_array($_SESSION['user']['role'], $roles, true);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: ' . route('auth/login'));
        exit;
    }

    $stmt = Database::connect()->prepare(
        "SELECT id_user, nama, username, role, is_active, aktif_mulai, aktif_sampai
         FROM users WHERE id_user = ? LIMIT 1"
    );
    $stmt->execute([$_SESSION['user']['id_user']]);
    $user = $stmt->fetch();
    $today = date('Y-m-d');

    if (
        !$user ||
        (int) $user['is_active'] !== 1 ||
        $user['aktif_mulai'] > $today ||
        $user['aktif_sampai'] < $today
    ) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . route('auth/login'));
        exit;
    }

    $_SESSION['user'] = [
        'id_user' => $user['id_user'],
        'nama' => $user['nama'],
        'username' => $user['username'],
        'role' => $user['role']
    ];
}

function require_role(...$roles)
{
    require_login();
    if (!has_role(...$roles)) {
        http_response_code(403);
        die('Akses ditolak. Anda tidak memiliki hak untuk halaman ini.');
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf'] ?? '';
        if (
            !is_string($token) ||
            !is_string($_SESSION['csrf'] ?? null) ||
            !hash_equals($_SESSION['csrf'], $token)
        ) {
            http_response_code(419);
            die('Token keamanan tidak valid. Silakan muat ulang halaman.');
        }
    }
}

function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function set_old(array $data)
{
    // Hindari menyimpan password ke session old
    unset($data['password'], $data['password_lama'], $data['password_baru'], $data['konfirmasi_password'], $data['csrf']);
    $_SESSION['old'] = $data;
}

function old($key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

function clear_old()
{
    unset($_SESSION['old']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_SESSION['flash']['error'])) {
    clear_old();
}

function audit($aktivitas)
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO audit_trail (id_user, aktivitas) VALUES (?, ?)"
        );
        $stmt->execute([$_SESSION['user']['id_user'] ?? null, $aktivitas]);
    } catch (Throwable $e) {
        // audit tidak boleh menghentikan proses utama
    }
}

/* ---------- Router sederhana ---------- */
/* Format: index.php?url=controller/method/param */

$url = trim($_GET['url'] ?? 'beranda', '/');
$parts = $url === '' ? ['beranda'] : explode('/', $url);

$name = preg_replace('/[^a-z_]/', '', strtolower($parts[0]));
$class = str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Controller';
$method = preg_replace('/[^a-zA-Z]/', '', $parts[1] ?? 'index');
$params = array_slice($parts, 2);

if ($method === '') {
    $method = 'index';
}

$file = __DIR__ . '/controllers/' . $class . '.php';

if ($name === '' || !file_exists($file)) {
    http_response_code(404);
    die('Halaman tidak ditemukan.');
}

require_once $file;

if (
    !class_exists($class) ||
    !method_exists($class, $method) ||
    !(new ReflectionMethod($class, $method))->isPublic()
) {
    http_response_code(404);
    die('Halaman tidak ditemukan.');
}

csrf_verify();

$controller = new $class();
call_user_func_array([$controller, $method], $params);
