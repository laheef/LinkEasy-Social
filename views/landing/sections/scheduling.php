<?php
$week = [
    'Mon' => [['tiktok', 'Reel · 9:00 AM'], ['facebook', 'Post · 1:00 PM']],
    'Tue' => [['instagram', 'Carousel · 11:30 AM']],
    'Wed' => [['linkedin', 'Article · 8:45 AM'], ['instagram', 'Story · 5:00 PM']],
    'Thu' => [['youtube', 'Video · 4:00 PM']],
    'Fri' => [['tiktok', 'Reel · 10:00 AM'], ['pinterest', 'Pin · 2:00 PM']],
];
?>
<section data-motion="publish" class="motion-section section feature-section">
    <div class="container feature-grid">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-calendar"/></svg>Smart scheduling</span>
            <h2>Plan your content. Stay consistent.</h2>
            <p>Drop posts into a weekly queue, pick date and time, and let LinkEasy Social publish
               them automatically. Each post shows its platform, status and next scheduled slot.</p>
            <ul class="check-list">
                <li>Weekly and monthly calendar views</li>
                <li>Queued, scheduled, published and failed statuses</li>
                <li>Move slots around without rebuilding posts</li>
                <li>Post even when you’re away from the desk</li>
            </ul>
            <a class="text-link" href="<?= e(url('signup')) ?>">Start scheduling free <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame schedule-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-calendar"/></svg> This week</span>
                    <div class="mock-segmented"><span>Week</span><span>Month</span></div>
                </div>
                <div class="schedule-grid">
                    <?php foreach ($week as $day => $posts): ?>
                    <div class="schedule-col">
                        <span class="schedule-day"><?= e($day) ?></span>
                        <?php foreach ($posts as $i => [$platform, $label]): ?>
                        <div class="schedule-post schedule-post--<?= e($platform) ?>" style="--i: <?= $i ?>">
                            <svg class="icon icon-fill"><use href="#i-<?= e($platform) ?>"/></svg>
                            <span><?= e($label) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="schedule-queue">
                    <span class="schedule-queue-label"><svg class="icon"><use href="#i-clock"/></svg> Next in queue</span>
                    <div class="schedule-queue-item">
                        <span class="platform-avatar platform-avatar--x"><svg class="icon icon-fill"><use href="#i-x"/></svg></span>
                        <span>Thread — 3 posts</span>
                        <span class="schedule-time">Mon 6:00 PM</span>
                    </div>
                    <div class="schedule-queue-item">
                        <span class="platform-avatar platform-avatar--google-business"><svg class="icon icon-fill"><use href="#i-google"/></svg></span>
                        <span>Business update</span>
                        <span class="schedule-time">Tue 9:30 AM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
