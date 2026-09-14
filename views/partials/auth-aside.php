<?php
/** Brand panel shown on the left of split-screen auth pages. */
$asidePlatforms = ['instagram', 'facebook', 'tiktok', 'linkedin'];
?>
<aside class="auth-aside">
    <a class="auth-aside-brand" href="<?= e(url('home')) ?>">
        <img class="brand-mark" src="<?= e(asset('assets/img/logo-mark-light.png')) ?>" alt="" width="34" height="36">
        <span class="brand-name">LinkEasy<b>Social</b></span>
    </a>

    <div>
        <h2>Your entire social workflow, in one workspace.</h2>
        <p>Create, schedule, publish and analyze content for every platform — without leaving the page.</p>
        <ul class="auth-bullets">
            <li><svg class="icon"><use href="#i-plug"/></svg> Connect each account in seconds with secure OAuth</li>
            <li><svg class="icon"><use href="#i-calendar-check"/></svg> Plan a month of content from one calendar</li>
            <li><svg class="icon"><use href="#i-trending-up"/></svg> Understand reach and growth at a glance</li>
        </ul>
        <div class="auth-aside-mock">
            <?php foreach ([
                ['instagram', 'Reel published', 'Instagram · just now'],
                ['facebook', 'Post scheduled', 'Facebook · tomorrow, 1:00 PM'],
                ['tiktok', 'Reach +18% this week', 'TikTok · analytics'],
                ['linkedin', '3 new comments', 'LinkedIn · inbox'],
            ] as [$slug, $title, $sub]): ?>
            <div class="auth-aside-mock-row">
                <span class="platform-avatar platform-avatar--<?= e($slug) ?>">
                    <svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg>
                </span>
                <div><strong><?= e($title) ?></strong><span><?= e($sub) ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <p class="auth-aside-foot">© <?= date('Y') ?> <?= e(config('brand.name')) ?> · <a href="<?= e(url('privacy')) ?>" style="color:#838693">Privacy</a> · <a href="<?= e(url('terms')) ?>" style="color:#838693">Terms</a></p>
</aside>
