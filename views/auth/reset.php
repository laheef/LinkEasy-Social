<?php
$seo = ['title' => 'Choose a new password — ' . config('brand.name')];
require __DIR__ . '/../partials/head.php';
?>
<div class="auth-shell">
    <?php require __DIR__ . '/../partials/auth-aside.php'; ?>
    <main class="auth-main">
        <div class="auth-card">
            <a class="auth-mobile-brand brand" href="<?= e(url('home')) ?>">
                <img class="brand-mark" src="<?= e(asset(config('brand.logo'))) ?>" alt="" width="34" height="36">
                <span class="brand-name">LinkEasy<b>Social</b></span>
            </a>
            <a class="auth-back" href="<?= e(url('login')) ?>"><svg class="icon"><use href="#i-arrow-left"/></svg> Back to log in</a>
            <?php foreach (flashes() as $flash): ?>
                <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:16px">
                    <svg class="icon"><use href="#i-info"/></svg><span><?= e($flash['message']) ?></span>
                </div>
            <?php endforeach; ?>
            <h1>Choose a new password</h1>
            <p class="auth-sub">Use at least 8 characters — a password manager is the easiest way to make it strong.</p>
            <form method="post" action="<?= e(url('reset_password')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <input type="hidden" name="email" value="<?= e($email) ?>">
                <div class="field">
                    <label for="password">New password</label>
                    <div class="input-wrap">
                        <input class="input" type="password" id="password" name="password" autocomplete="new-password" required>
                        <button type="button" class="input-toggle" data-password-toggle data-target="password" aria-label="Show password">
                            <svg class="icon"><use href="#i-eye"/></svg></button>
                    </div>
                </div>
                <div class="field">
                    <label for="password_confirm">Confirm new password</label>
                    <div class="input-wrap">
                        <input class="input" type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" required>
                        <button type="button" class="input-toggle" data-password-toggle data-target="password_confirm" aria-label="Show password">
                            <svg class="icon"><use href="#i-eye"/></svg></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Update password</button>
            </form>
        </div>
    </main>
</div>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
