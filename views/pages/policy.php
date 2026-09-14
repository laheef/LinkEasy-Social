<?php
require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header.php';
?>
<main id="main">
    <section class="subpage-hero policy-hero">
        <div class="container">
            <a class="policy-back" href="<?= e(url('legal')) ?>"><svg class="icon"><use href="#i-arrow-left"/></svg> Trust &amp; policies</a>
            <span class="eyebrow"><?= e($page['category']) ?></span>
            <h1><?= e($page['title']) ?></h1><p><?= e($page['summary']) ?></p>
            <span class="policy-date">Last updated: September 13, 2026</span>
        </div>
    </section>
    <div class="container policy-layout">
        <aside class="policy-nav"><strong>On this page</strong><nav aria-label="Page contents"><?php foreach ($page['sections'] as $i => [$heading, $text]): ?><a href="#section-<?= $i+1 ?>"><?= e($heading) ?></a><?php endforeach; ?></nav><a class="text-link" href="<?= e(url('contact')) ?>">Have a question? <svg class="icon"><use href="#i-arrow-up-right"/></svg></a></aside>
        <article class="policy-content">
            <?php if (!empty($page['review'])): ?><div class="policy-review"><svg class="icon"><use href="#i-info"/></svg><p><strong>Draft for operator review</strong>Legal identity, jurisdiction and operational details must be confirmed before this policy is adopted. This draft does not replace applicable statutory rights.</p></div><?php endif; ?>
            <?php foreach ($page['sections'] as $i => [$heading, $text]): ?>
            <section id="section-<?= $i+1 ?>"><h2><?= e($heading) ?></h2><p><?= e($text) ?></p></section>
            <?php endforeach; ?>
            <div class="policy-contact"><h2>Let’s get you to the right place.</h2><p>For questions about this page, contact <a href="mailto:<?= e(config('brand.support_email')) ?>"><?= e(config('brand.support_email')) ?></a>. Please do not include passwords, API keys or payment-card details.</p><a class="btn btn-dark btn-sm" href="<?= e(url('contact', ['subject' => $page['title'] === 'Data Access & Deletion' ? 'Privacy & data deletion' : 'General question'])) ?>">Contact the team <svg class="icon"><use href="#i-arrow-right"/></svg></a></div>
        </article>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
