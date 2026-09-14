<?php
$seo = ['title' => 'Confirming your subscription — ' . config('brand.name')];
require __DIR__ . '/../partials/head.php'; require __DIR__ . '/../partials/header.php';
?>
<main class="billing-shell">
    <div class="billing-card">
        <div class="billing-icon"><svg class="icon" style="width:32px;height:32px"><use href="#i-clock"/></svg></div>
        <h1>Finishing your subscription…</h1>
        <p>PayPal is confirming your payment. Your Managed plan activates automatically the moment our
           server receives and verifies PayPal’s confirmation — usually within a few seconds.</p>
        <ul class="billing-steps">
            <li><svg class="icon"><use href="#i-check-circle"/></svg> Payment authorized at PayPal</li>
            <li><svg class="icon"><use href="#i-shield"/></svg> Server-to-server webhook verification</li>
            <li><svg class="icon"><use href="#i-zap"/></svg> Managed features unlock in your workspace</li>
        </ul>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <a class="btn btn-primary" href="<?= e(url('dashboard')) ?>"><svg class="icon"><use href="#i-grid"/></svg> Go to dashboard</a>
            <a class="btn btn-outline" href="<?= e(url('pricing')) ?>">Back to pricing</a>
        </div>
        <p style="margin-top:18px;font-size:.84rem">Something looks wrong?
            <a href="<?= e(url('contact')) ?>" style="color:var(--red-deep);font-weight:650">Contact support</a></p>
    </div>
</main>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
