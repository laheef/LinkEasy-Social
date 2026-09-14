<?php
$seo = ['title' => 'Log in — ' . config('brand.name'), 'description' => 'Log in to your LinkEasy Social workspace.'];
require __DIR__ . '/../partials/head.php';
$old = $old ?? [];
$next = $_GET['next'] ?? ($old['next'] ?? url('dashboard'));
if (!is_string($next) || !str_starts_with($next, '/') || str_starts_with($next, '//')) {
    $next = url('dashboard');
}
?>
<div class="auth-shell">
    <?php require __DIR__ . '/../partials/auth-aside.php'; ?>

    <main class="auth-main">
        <div class="auth-card">
            <a class="auth-mobile-brand brand" href="<?= e(url('home')) ?>">
                <img class="brand-mark" src="<?= e(asset(config('brand.logo'))) ?>" alt="" width="34" height="36">
                <span class="brand-name">LinkEasy<b>Social</b></span>
            </a>

            <?php foreach (flashes() as $flash): ?>
                <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:16px">
                    <svg class="icon"><use href="#i-info"/></svg>
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endforeach; ?>

            <h1>Welcome back</h1>
            <p class="auth-sub">Log in to your LinkEasy Social workspace.</p>

            <a class="auth-google" href="<?= e(url('google_login', ['next' => $next])) ?>">
                <svg class="icon icon-fill"><use href="#i-google"/></svg> Continue with Google
            </a>
            <div class="auth-divider"><span>or continue with email</span></div>

            <form method="post" action="<?= e(url('login')) ?>" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="next" value="<?= e($next) ?>">
                <div class="field">
                    <label for="email">Email address</label>
                    <input class="input" type="email" id="email" name="email" autocomplete="email"
                           placeholder="you@company.com" required value="<?= e($old['email'] ?? '') ?>">
                </div>
                <div class="field">
                    <div class="field-row">
                        <label for="password">Password</label>
                        <a href="<?= e(url('forgot_password')) ?>" style="font-size:.84rem;font-weight:650;color:var(--red-deep)">Forgot password?</a>
                    </div>
                    <div class="input-wrap">
                        <input class="input" type="password" id="password" name="password"
                               autocomplete="current-password" placeholder="Your password" required>
                        <button type="button" class="input-toggle" data-password-toggle data-target="password"
                                aria-label="Show password"><svg class="icon"><use href="#i-eye"/></svg></button>
                    </div>
                </div>
                <p class="field-help" style="margin-bottom:20px">Sessions expire after inactivity for your security.</p>
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Log in <svg class="icon"><use href="#i-arrow-right"/></svg>
                </button>
            </form>

            <p class="auth-foot">Don’t have an account? <a href="<?= e(url('signup')) ?>">Create one free</a></p>
            <p class="auth-foot" style="margin-top:10px"><a href="<?= e(url('home')) ?>" style="display:inline-flex;align-items:center;gap:.35rem"><svg class="icon" style="width:15px;height:15px"><use href="#i-arrow-left"/></svg> Back to website</a></p>
        </div>
    </main>
</div>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
