<?php $marqueePlatforms = array_values(array_filter(config('platforms'), fn ($p) => !empty($p['available']))); ?>
<section data-motion="connect" class="motion-section platform-strip" id="platforms" aria-label="Supported social platforms">
    <div class="container">
        <p class="platform-strip-label">Works with the platforms you already use — with more added as APIs open up</p>
    </div>
    <div class="marquee" data-marquee>
        <div class="marquee-track">
            <?php for ($i = 0; $i < 2; $i++): ?>
            <ul class="marquee-group" aria-hidden="<?= $i ? 'true' : 'false' ?>">
                <?php foreach ($marqueePlatforms as $p): ?>
                <li class="marquee-item" style="--platform-color: <?= e($p['color']) ?>">
                    <svg class="icon icon-fill"><use href="#i-<?= e($p['slug']) ?>"/></svg>
                    <span><?= e($p['name']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endfor; ?>
        </div>
    </div>
</section>
