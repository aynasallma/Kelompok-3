<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Klinik App</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="login-page">
<div class="login-box">
    <h1>Klinik App</h1>
    <p class="muted">Silakan login untuk melanjutkan</p>

    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= route('auth/login') ?>">
        <?= csrf_field() ?>
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>

</div>
</body>
</html>
