<?php
$allPlatforms = config('platforms');
$available = array_values(array_filter($allPlatforms, fn ($p) => !empty($p['available'])));
$planned   = array_values(array_filter($allPlatforms, fn ($p) => empty($p['available'])));

// Available connections sit on the ring (evenly distributed)
$ring = [];
$count = count($available);
foreach ($available as $i => $p) {
    $angle = deg2rad(-90 + $i * (360 / $count));
    $ring[] = ['slug' => $p['slug'], 'name' => $p['name'], 'color' => $p['color'],
        'x' => round(50 + 44 * cos($angle), 1), 'y' => round(50 + 44 * sin($angle), 1)];
}
?>
<section data-motion="connect" class="motion-section section section-soft integrations-section" id="integrations">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-plug"/></svg>Integrations</span>
            <h2>Connect the platforms you already use</h2>
            <p>LinkEasy Social plugs into the social networks your audience lives on — no new habits required.</p>
        </div>

        <div class="integrations-network reveal" data-draw-lines>
            <svg class="integrations-lines" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <?php foreach ($ring as $n): ?>
                <line class="ecosystem-line" x1="50" y1="50" x2="<?= $n['x'] ?>" y2="<?= $n['y'] ?>"/>
                <?php endforeach; ?>
            </svg>
            <div class="integrations-hub">
                <img src="<?= e(asset(config('brand.logo_light'))) ?>" alt="LinkEasy Social" width="34" height="36">
                <strong>LinkEasy<br>Social</strong>
            </div>
            <?php foreach ($ring as $i => $n): ?>
            <div class="integration-node" style="left: <?= $n['x'] ?>%; top: <?= $n['y'] ?>%; --platform-color: <?= e($n['color']) ?>; --i: <?= $i ?>">
                <span class="integration-icon platform-avatar--<?= e($n['slug']) ?>">
                    <svg class="icon icon-fill"><use href="#i-<?= e($n['slug']) ?>"/></svg>
                </span>
                <span class="integration-name"><?= e($n['name']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="integrations-note reveal">
            <svg class="icon"><use href="#i-plug"/></svg>
            Available connections depend on each platform’s API access and your plan.
        </p>

        <div class="platform-matrix reveal">
            <div class="platform-matrix-head">
                <h3>One workspace, every network</h3>
                <p><?= count($allPlatforms) ?> platforms supported today or on the roadmap — each with native publishing, scheduling or reporting where the platform’s API allows.</p>
            </div>

            <p class="matrix-group-label">
                <span class="matrix-dot matrix-dot--live"></span>
                Available connections
            </p>
            <div class="matrix-grid">
                <?php foreach ($available as $p): ?>
                <span class="matrix-item" title="<?= e($p['name']) ?>">
                    <span class="matrix-tile platform-avatar--<?= e($p['slug']) ?>">
                        <svg class="icon icon-fill"><use href="#i-<?= e($p['slug']) ?>"/></svg>
                    </span>
                    <span class="matrix-name"><?= e($p['name']) ?></span>
                    <svg class="icon matrix-live"><use href="#i-check-circle"/></svg>
                </span>
                <?php endforeach; ?>
            </div>

            <p class="matrix-group-label">
                <span class="matrix-dot matrix-dot--soon"></span>
                On the roadmap
            </p>
            <div class="matrix-grid">
                <?php foreach ($planned as $p): ?>
                <span class="matrix-item is-planned" title="<?= e($p['name']) ?> — planned connection">
                    <span class="matrix-tile matrix-tile--<?= e($p['slug']) ?>">
                        <svg class="icon icon-fill"><use href="#i-<?= e($p['slug']) ?>"/></svg>
                    </span>
                    <span class="matrix-name"><?= e($p['name']) ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
