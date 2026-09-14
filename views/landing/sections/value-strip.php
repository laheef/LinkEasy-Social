<?php
$values = [
    ['i-plug', 'Multiple social accounts', 'Connect Instagram, Facebook, TikTok, LinkedIn and more to one workspace.'],
    ['i-grid', 'One unified dashboard', 'Composer, calendar, inbox and analytics in a single consistent view.'],
    ['i-calendar-check', 'Scheduled content', 'Plan and queue posts so your channels stay active without the daily grind.'],
    ['i-trending-up', 'Performance analytics', 'See reach, engagement and growth for every platform in one place.'],
];
?>
<section data-motion="connect" class="motion-section value-strip section">
    <div class="container">
        <div class="value-grid">
            <?php foreach ($values as [$icon, $title, $text]): ?>
            <article class="value-card reveal">
                <span class="icon-tile icon-tile--soft"><svg class="icon"><use href="#<?= e($icon) ?>"/></svg></span>
                <h3><?= e($title) ?></h3>
                <p><?= e($text) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <p class="value-slogan reveal">
            One platform. <b>One workflow.</b> Less switching. <b>More consistency.</b>
        </p>
    </div>
</section>
