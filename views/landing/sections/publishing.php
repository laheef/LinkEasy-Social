<?php $publishTargets = ['instagram', 'facebook', 'linkedin', 'tiktok', 'youtube']; ?>
<section data-motion="publish" class="motion-section section section-soft feature-section">
    <div class="container feature-grid feature-grid--reverse">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-send"/></svg>Multi-platform publishing</span>
            <h2>Create once. Publish everywhere.</h2>
            <p>Write one piece of core content, adapt the caption and format per network, and publish
               to every connected profile from a single confirmation. Your accounts stay in sync — your browser tabs don’t.</p>
            <ul class="check-list">
                <li>One composer, every connected platform</li>
                <li>Per-network captions, formats and previews</li>
                <li>Publish now or schedule for later</li>
                <li>Delivery status tracked per platform</li>
            </ul>
            <a class="text-link" href="<?= e(url('pricing')) ?>">Compare publishing on each plan <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="publish-diagram">
                <svg class="publish-lines" viewBox="0 0 400 340" preserveAspectRatio="none" aria-hidden="true">
                    <?php foreach ([34, 100, 166, 232, 298] as $i => $y): ?>
                    <path class="publish-line" style="--i: <?= $i ?>" d="M118,170 C 200,170 240,<?= $y ?> 298,<?= $y ?>"/>
                    <?php endforeach; ?>
                </svg>
                <div class="publish-source">
                    <span class="publish-source-media">
                        <img src="<?= e(asset('assets/img/media/media-7.jpg')) ?>" alt="Landscape photo ready to publish" width="160" height="120" loading="lazy" decoding="async">
                    </span>
                    <div>
                        <strong>One piece of content</strong>
                        <span>Caption · media · timing</span>
                    </div>
                </div>
                <div class="publish-targets">
                    <?php foreach ($publishTargets as $i => $slug):
                        $p = array_values(array_filter(config('platforms'), fn ($x) => $x['slug'] === $slug))[0]; ?>
                    <div class="publish-target" style="--i: <?= $i ?>; --platform-color: <?= e($p['color']) ?>">
                        <span class="platform-avatar platform-avatar--<?= e($slug) ?>">
                            <svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg>
                        </span>
                        <span><?= e($p['name']) ?></span>
                        <span class="publish-status"><svg class="icon"><use href="#i-check"/></svg></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
