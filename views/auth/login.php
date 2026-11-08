<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staf - Klinik Sehat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-header-action">
        <a class="login-back" href="<?= route('beranda') ?>">
            <?= icon('arrow-left', 'back-icon', 16) ?>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <div class="login-brand">
        <span class="login-logo-badge"><?= icon('shield-plus', '', 28) ?></span>
        <h1>Klinik Sehat</h1>
        <p class="muted">Portal Masuk Staf &amp; Tenaga Medis</p>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= route('auth/login') ?>" class="login-form">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="login-username">Username</label>
            <input id="login-username" type="text" name="username" placeholder="Masukkan username" required autofocus>
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <input id="login-password" type="password" name="password" placeholder="Masukkan kata sandi" required>
        </div>
        <div class="form-actions-centered">
            <button type="submit" class="btn btn-primary btn-block">Masuk ke Sistem</button>
        </div>
    </form>
</div>
</body>
</html>
