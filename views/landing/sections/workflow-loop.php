<?php
$loop = [
    ['i-edit', 'Create', 50, 7],
    ['i-palette', 'Customize', 88, 28],
    ['i-calendar', 'Schedule', 88, 72],
    ['i-send', 'Publish', 50, 93],
    ['i-bar-chart', 'Analyze', 12, 72],
    ['i-refresh', 'Optimize', 12, 28],
];
?>
<section data-motion="connect" class="motion-section section loop-section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-refresh"/></svg>The workflow</span>
            <h2>A content loop that keeps getting better</h2>
            <p>Every post flows through the same cycle — so every round of content is sharper than the last.</p>
        </div>

        <div class="loop-diagram reveal" data-loop>
            <svg class="loop-ring" viewBox="0 0 200 200" aria-hidden="true">
                <circle class="loop-ring-track" cx="100" cy="100" r="88"/>
                <circle class="loop-ring-moving" cx="100" cy="100" r="88"/>
            </svg>
            <div class="loop-center">
                <img src="<?= e(asset('assets/img/logo-mark.png')) ?>" alt="" width="40" height="42">
                <strong>Continuous</strong>
                <span>growth loop</span>
            </div>
            <?php foreach ($loop as $i => [$icon, $label, $x, $y]): ?>
            <div class="loop-node" style="left: <?= $x ?>%; top: <?= $y ?>%; --i: <?= $i ?>">
                <span class="loop-node-icon"><svg class="icon"><use href="#<?= e($icon) ?>"/></svg></span>
                <span class="loop-node-label"><?= sprintf('%02d', $i + 1) ?> · <?= e($label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
