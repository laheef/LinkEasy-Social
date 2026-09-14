<?php
$steps = [
    ['i-plug', 'Connect your social accounts', 'Securely link Instagram, Facebook, TikTok, LinkedIn and more with official OAuth — we never see your passwords.'],
    ['i-edit', 'Create your content', 'Write captions, add images and video, and organize everything in your shared media library.'],
    ['i-grid', 'Choose your platforms', 'Pick one or many networks and tailor the message, format and preview for each.'],
    ['i-calendar-check', 'Schedule or publish', 'Drop posts into your calendar queue, or publish immediately. LinkEasy Social takes it from there.'],
    ['i-bar-chart', 'Track performance', 'Watch reach, engagement and follower growth across every platform in one dashboard.'],
    ['i-refresh', 'Improve your strategy', 'Use the insights to refine content, timing and platform mix — and repeat.'],
];
?>
<section data-motion="publish" class="motion-section section section-dark steps-section" id="how-it-works">
    <div class="container">
        <div class="section-head section-head--light reveal">
            <span class="eyebrow eyebrow--light"><svg class="icon section-cue" aria-hidden="true"><use href="#i-rocket"/></svg>How it works</span>
            <h2>From idea to published post in minutes</h2>
            <p>No certification courses, no 50-tab setup — connect your accounts and publish your first post today.</p>
        </div>

        <div class="steps-timeline" data-timeline>
            <span class="steps-track" aria-hidden="true"><span class="steps-progress"></span></span>
            <ol class="steps-list">
            <?php foreach ($steps as $i => [$icon, $title, $text]): ?>
            <li class="step-item reveal">
                <div class="step-marker">
                    <span class="step-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="step-icon"><svg class="icon"><use href="#<?= e($icon) ?>"/></svg></span>
                </div>
                <div class="step-body">
                    <h3><?= e($title) ?></h3>
                    <p><?= e($text) ?></p>
                </div>
            </li>
            <?php endforeach; ?>
            </ol>
        </div>

        <div class="steps-cta reveal">
            <a class="btn btn-primary btn-lg" href="<?= e(url('signup')) ?>">Start free <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>
    </div>
</section>
