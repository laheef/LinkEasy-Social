<?php require __DIR__ . '/../partials/head.php'; require __DIR__ . '/../partials/header.php'; ?>
<main id="main">
    <section class="subpage-hero"><div class="container"><span class="eyebrow">Trust &amp; transparency</span><h1>Clear policies.<br>Fewer question marks.</h1><p>Understand your workspace, your data and what to expect from LinkEasy Social.</p></div></section>
    <section class="section"><div class="container"><div class="resource-grid">
        <?php foreach ($pages as $slug => $page): ?>
        <a class="resource-card" href="<?= e('/' . $slug) ?>"><span class="module-icon tone-rose"><svg class="icon"><use href="#i-<?= e($page['icon']) ?>"/></svg></span><span class="resource-category"><?= e($page['category']) ?></span><h2><?= e($page['title']) ?></h2><p><?= e($page['summary']) ?></p><span class="resource-read">Read <?= !empty($page['review']) ? 'draft' : 'more' ?> <svg class="icon"><use href="#i-arrow-up-right"/></svg></span></a>
        <?php endforeach; ?>
    </div><p class="resource-footnote">Some policy pages are marked as drafts pending operator and legal review. Each page makes its review status visible.</p></div></section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
