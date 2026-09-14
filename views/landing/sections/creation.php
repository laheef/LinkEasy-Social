<?php
$creationFlow = ['Idea', 'Create', 'Customize', 'Preview', 'Schedule', 'Publish'];
$selectedPlatforms = ['instagram', 'facebook', 'linkedin', 'tiktok'];
?>
<section data-motion="create" class="motion-section section section-soft feature-section">
    <div class="container feature-grid feature-grid--reverse">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-edit"/></svg>Content creation</span>
            <h2>Create content without starting from scratch</h2>
            <p>One composer for every network. Add images and video, tailor the caption per platform,
               preview exactly how each post will look, then schedule it — no copy-pasting between apps.</p>
            <ul class="check-list">
                <li>Text, images &amp; video in one composer</li>
                <li>Platform-specific captions and previews</li>
                <li>Media library and reusable assets</li>
                <li>Draft, schedule or publish instantly</li>
            </ul>
            <a class="text-link" href="<?= e(url('signup')) ?>">Open the composer <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <ol class="creation-flow" aria-label="Content creation workflow">
                <?php foreach ($creationFlow as $i => $step): ?>
                <li class="creation-flow-step <?= $i === 2 ? 'is-active' : '' ?>" style="--i: <?= $i ?>"><small><?= sprintf('%02d', $i+1) ?></small><span><?= e($step) ?></span></li>
                <?php endforeach; ?>
            </ol>
            <div class="mock-frame composer-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-edit"/></svg> New post</span>
                    <span class="mock-status-pill">Draft</span>
                </div>
                <div class="composer-platforms">
                    <?php foreach ($selectedPlatforms as $slug): ?>
                    <span class="platform-chip platform-chip--<?= e($slug) ?> is-selected">
                        <svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg>
                    </span>
                    <?php endforeach; ?>
                    <span class="platform-chip platform-chip--add"><svg class="icon"><use href="#i-plus"/></svg></span>
                </div>
                <div class="composer-body">
                    <p class="composer-text">Sunset sessions hit different this time of year. New reel drops
                       tomorrow at 9 AM — turn on post notifications so you don’t miss it.</p>
                    <div class="composer-media">
                        <span class="composer-media-item is-img">
                            <img src="<?= e(asset('assets/img/media/media-7.jpg')) ?>" alt="Attached image from the media library" loading="lazy" decoding="async" width="104" height="104">
                            <i><svg class="icon"><use href="#i-image"/></svg></i>
                        </span>
                        <span class="composer-media-item is-video">
                            <img src="<?= e(asset('assets/img/media/media-4.jpg')) ?>" alt="Attached video from the media library" loading="lazy" decoding="async" width="104" height="104">
                            <i><svg class="icon"><use href="#i-play"/></svg></i>
                        </span>
                        <span class="composer-media-item is-add"><svg class="icon"><use href="#i-plus"/></svg></span>
                    </div>
                </div>
                <div class="composer-foot">
                    <button type="button" class="btn btn-ghost btn-sm"><svg class="icon"><use href="#i-clock"/></svg> Save draft</button>
                    <button type="button" class="btn btn-primary btn-sm"><svg class="icon"><use href="#i-send"/></svg> Schedule post</button>
                </div>
                <div class="composer-preview">
                    <span class="composer-preview-label"><svg class="icon icon-fill"><use href="#i-instagram"/></svg> Instagram preview</span>
                    <div class="composer-preview-card">
                        <span class="composer-preview-thumb">
                            <img src="<?= e(asset('assets/img/media/media-1.jpg')) ?>" alt="" loading="lazy" decoding="async" width="92" height="92">
                        </span>
                        <div>
                            <strong>@linkeasysocial</strong>
                            <span>Sunset sessions hit different this time of year…</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
