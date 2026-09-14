<?php
// September-style 35-cell month grid with scheduled posts
$calCells = [
    [null], [null], [1, 'instagram'], [2], [3, 'facebook'], [4, 'tiktok'], [5],
    [6], [7, 'linkedin'], [8, 'instagram'], [9], [10, 'youtube'], [11, 'instagram'], [12, 'pinterest'],
    [13], [14, 'tiktok'], [15], [16, 'facebook'], [17, 'instagram', 'linkedin'], [18], [19, 'tiktok'],
    [20], [21, 'instagram'], [22, 'x'], [23, 'youtube'], [24, 'instagram'], [25, 'facebook'], [26],
    [27], [28, 'linkedin'], [29, 'tiktok'], [30], [null], [null], [null],
];
?>
<section data-motion="publish" class="motion-section section section-soft feature-section">
    <div class="container feature-grid feature-grid--reverse">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-calendar-check"/></svg>Content calendar</span>
            <h2>See your entire content strategy at a glance</h2>
            <p>Switch between week and month views, see every scheduled post with its platform color,
               and drag content into open slots when plans change. Your whole content rhythm, visible.</p>
            <ul class="check-list">
                <li>Month, week and list calendar views</li>
                <li>Platform color-coding and post thumbnails</li>
                <li>Status: draft, scheduled, published</li>
                <li>Spot gaps before they become missed days</li>
            </ul>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame calendar-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-calendar"/></svg> September</span>
                    <div class="calendar-nav">
                        <span class="calendar-nav-btn"><svg class="icon icon-flip-x"><use href="#i-chevron-right"/></svg></span>
                        <span class="mock-status-pill">Month</span>
                        <span class="calendar-nav-btn"><svg class="icon"><use href="#i-chevron-right"/></svg></span>
                    </div>
                </div>
                <div class="calendar-weekdays">
                    <?php foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $d): ?><span><?= $d ?></span><?php endforeach; ?>
                </div>
                <div class="calendar-grid">
                    <?php foreach ($calCells as $cell):
                        [$day, $platform] = array_pad($cell, 2, null); ?>
                        <div class="calendar-cell <?= $day === null ? 'is-empty' : '' ?> <?= $day === 17 ? 'is-dragover' : '' ?>">
                            <?php if ($day !== null): ?>
                                <span class="calendar-date"><?= $day ?></span>
                                <?php if ($platform): ?>
                                    <span class="calendar-chip calendar-chip--<?= e($platform) ?>">
                                        <svg class="icon icon-fill"><use href="#i-<?= e($platform) ?>"/></svg>
                                        <?php if ($day === 17): ?><i class="calendar-extra">+1</i><?php endif; ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($day === 17): ?>
                                    <span class="calendar-dragchip">
                                        <svg class="icon"><use href="#i-grip"/></svg> Reel — sunset
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
