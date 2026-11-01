<?php

require_once __DIR__ . '/../models/User.php';

class AkunController extends Controller
{
    private $user;

    public function __construct()
    {
        require_login();
        $this->user = new User();
    }

    public function index()
    {
        $this->view('akun/index', ['title' => 'Keamanan Akun']);
    }

    public function password()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Metode tidak diizinkan.');
        }

        $fields = ['password_lama', 'password_baru', 'konfirmasi_password'];
        foreach ($fields as $field) {
            if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
                flash('error', 'Isi semua kolom password dengan benar.');
                $this->redirect('/index.php?url=akun');
            }
        }

        $current = $_POST['password_lama'];
        $new = $_POST['password_baru'];
        if (strlen($new) < 12 || strlen($new) > 72) {
            flash('error', 'Password baru harus terdiri dari 12 sampai 72 karakter.');
            $this->redirect('/index.php?url=akun');
        }
        if ($new !== $_POST['konfirmasi_password']) {
            flash('error', 'Konfirmasi password baru tidak cocok.');
            $this->redirect('/index.php?url=akun');
        }
        if ($new === $current) {
            flash('error', 'Password baru harus berbeda dari password lama.');
            $this->redirect('/index.php?url=akun');
        }
        if (!$this->user->changePassword(current_user()['id_user'], $current, $new)) {
            flash('error', 'Password lama tidak sesuai.');
            $this->redirect('/index.php?url=akun');
        }

        session_regenerate_id(true);
        audit('Mengubah password akun');
        flash('success', 'Password berhasil diperbarui.');
        $this->redirect('/index.php?url=akun');
    }
}