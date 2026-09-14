<?php
$user = \App\Auth::user();
$nav = [
    ['label' => 'Features', 'url' => url('features'), 'id' => 'features'],
    ['label' => 'How it works', 'url' => url('how_it_works'), 'id' => 'how-it-works'],
    ['label' => 'Integrations', 'url' => url('integrations'), 'id' => 'integrations'],
    ['label' => 'About', 'url' => url('about'), 'id' => 'about'],
    ['label' => 'Pricing', 'url' => url('pricing'), 'id' => 'pricing'],
];
?>
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a class="brand" href="<?= e(url('home')) ?>" aria-label="<?= e(config('brand.name')) ?> — home">
            <img class="brand-mark" src="<?= e(asset(config('brand.logo'))) ?>" alt="" width="36" height="38">
            <span class="brand-name">LinkEasy<b>Social</b></span>
        </a>

        <nav class="main-nav" aria-label="Primary">
            <?php foreach ($nav as $item): ?>
                <a href="<?= e($item['url']) ?>" data-nav-id="<?= e($item['id']) ?>" <?= $item['url'] === parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ? 'aria-current="page" class="is-active"' : '' ?>><?= e($item['label']) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="header-actions">
            <?php if ($user): ?>
                <a class="btn btn-ghost btn-sm header-login" href="<?= e(url('dashboard')) ?>">
                    <svg class="icon"><use href="#i-grid"/></svg> Dashboard</a>
                <form method="post" action="<?= e(url('logout')) ?>" class="header-logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-sm">Log out</button>
                </form>
            <?php else: ?>
                <a class="btn btn-ghost btn-sm header-login" href="<?= e(url('login')) ?>">Log in</a>
                <a class="btn btn-primary btn-sm header-cta" href="<?= e(url('signup')) ?>">Get started free</a>
            <?php endif; ?>
            <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
                <svg class="icon"><use href="#i-menu"/></svg>
            </button>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobileNav" hidden>
        <nav class="mobile-nav-links" aria-label="Mobile">
            <?php foreach ($nav as $item): ?>
                <a href="<?= e($item['url']) ?>" class="mobile-nav-link"><span><?= e($item['label']) ?></span>
                    <svg class="icon"><use href="#i-chevron-right"/></svg></a>
            <?php endforeach; ?>
        </nav>
        <div class="mobile-nav-actions">
            <?php if ($user): ?>
                <a class="btn btn-dark btn-block" href="<?= e(url('dashboard')) ?>">Go to dashboard</a>
                <form method="post" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline btn-block" type="submit">Log out</button>
                </form>
            <?php else: ?>
                <a class="btn btn-primary btn-block" href="<?= e(url('signup')) ?>">Get started free</a>
                <a class="btn btn-outline btn-block" href="<?= e(url('login')) ?>">
                    <svg class="icon"><use href="#i-log-in"/></svg> Log in</a>
            <?php endif; ?>
        </div>
</div>

<div class="nav-backdrop" id="navBackdrop" hidden></div>

<?php $flashes = flashes(); ?>
<?php if ($flashes): ?>
<div class="flash-region container" role="status" aria-live="polite">
    <?php foreach ($flashes as $flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>">
            <svg class="icon">
                <use href="#i-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'warning' ? 'alert' : 'info') ?>"/>
            </svg>
            <span><?= e($flash['message']) ?></span>
            <button type="button" class="flash-close" aria-label="Dismiss"><svg class="icon"><use href="#i-close"/></svg></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
