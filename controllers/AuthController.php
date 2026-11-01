<?php

require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        $this->redirect('/index.php?url=auth/login');
    }

    public function login()
    {
        if (is_logged_in()) {
            $this->redirect('/index.php?url=dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if (!is_string($username) || !is_string($password)) {
                flash('error', 'Username atau password salah.');
                $this->redirect('/index.php?url=auth/login');
            }
            $username = trim($username);

            $found = $this->user->findByUsername($username);

            if (
                $found &&
                password_verify($password, $found['password']) &&
                $this->user->isCurrentlyActive($found)
            ) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id_user'  => $found['id_user'],
                    'nama'     => $found['nama'],
                    'username' => $found['username'],
                    'role'     => $found['role']
                ];
                audit('Login ke sistem');
                $this->redirect('/index.php?url=dashboard');
            }

            flash('error', 'Username atau password salah.');
            $this->redirect('/index.php?url=auth/login');
        }

        $this->view('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }
        if (is_logged_in()) {
            audit('Logout dari sistem');
        }

        $_SESSION = [];
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $cookie['path'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'] ?? 'Lax'
        ]);
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?url=auth/login');
        exit;
    }
}
