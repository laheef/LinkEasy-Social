<?php
$seo = ['title' => 'Reset password — ' . config('brand.name')];
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
            <h1>Forgot your password?</h1>
            <p class="auth-sub">Enter your email and we’ll send you a secure reset link valid for 60 minutes.</p>
            <form method="post" action="<?= e(url('forgot_password')) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="email">Email address</label>
                    <input class="input" type="email" id="email" name="email" autocomplete="email" placeholder="you@company.com" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Send reset link</button>
            </form>
        </div>
    </main>
</div>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
