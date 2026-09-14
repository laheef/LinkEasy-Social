<?php
$seo = ['title' => $code . ' — ' . config('brand.name')];
require __DIR__ . '/../partials/head.php'; require __DIR__ . '/../partials/header.php';
$user = \App\Auth::user();
?>
<main class="error-shell">
    <div class="error-card">
        <img src="<?= e(asset('assets/img/logo-mark.png')) ?>" alt="" width="72" style="margin:0 auto 22px">
        <div class="error-code"><?= e((string) $code) ?></div>
        <h1><?= e($message) ?></h1>
        <p>
            <?php if ($code === 404): ?>
                The page may have moved, or the link might be incorrect.
            <?php elseif ($code === 403): ?>
                You don’t currently have access to this resource.
            <?php elseif ($code === 419): ?>
                For your security, expired forms need a fresh start.
            <?php else: ?>
                We’ve been notified. Please try again in a moment.
            <?php endif; ?>
        </p>
        <div class="error-actions">
            <a class="btn btn-primary" href="<?= e(url('home')) ?>"><svg class="icon"><use href="#i-home"/></svg> Back home</a>
            <?php if ($user): ?>
                <a class="btn btn-outline" href="<?= e(url('dashboard')) ?>"><svg class="icon"><use href="#i-grid"/></svg> Go to dashboard</a>
            <?php else: ?>
                <a class="btn btn-outline" href="<?= e(url('contact')) ?>"><svg class="icon"><use href="#i-mail"/></svg> Contact support</a>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
