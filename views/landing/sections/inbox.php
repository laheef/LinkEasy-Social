<?php
$threads = [
    ['platform' => 'instagram', 'name' => 'Maya R.', 'time' => '2m', 'text' => 'Love this post! When is the next drop?', 'unread' => true],
    ['platform' => 'facebook', 'name' => 'Daniel K.', 'time' => '18m', 'text' => 'Can you tell me more about pricing?', 'unread' => true],
    ['platform' => 'linkedin', 'name' => 'Sara M.', 'time' => '1h', 'text' => 'Interested in a collaboration. DMing details.', 'unread' => false],
    ['platform' => 'tiktok', 'name' => 'leo_films', 'time' => '3h', 'text' => 'This is fire, keep them coming', 'unread' => false],
];
?>
<section data-motion="connect" class="motion-section section feature-section">
    <div class="container feature-grid">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-inbox"/></svg>Unified inbox</span>
            <h2>Stay connected with your audience</h2>
            <p>Comments, messages and mentions from across your networks land in one inbox,
               so nothing slips through. Reply quickly, filter by platform and keep every conversation in context.</p>
            <ul class="check-list">
                <li>Comments, messages and mentions in one stream</li>
                <li>Reply without opening each native app</li>
                <li>Unread, flagged and resolved states</li>
                <li>Filter by platform, project or status</li>
            </ul>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame inbox-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-inbox"/></svg> Inbox</span>
                    <span class="mock-status-pill mock-status-pill--red">2 unread</span>
                </div>
                <div class="inbox-filters">
                    <span class="is-active">All</span><span>Unread</span><span>Comments</span><span>Mentions</span>
                </div>
                <div class="inbox-list">
                    <?php foreach ($threads as $t): ?>
                    <div class="inbox-item <?= $t['unread'] ? 'is-unread' : '' ?>">
                        <span class="platform-avatar platform-avatar--<?= e($t['platform']) ?>">
                            <svg class="icon icon-fill"><use href="#i-<?= e($t['platform']) ?>"/></svg>
                        </span>
                        <div class="inbox-item-body">
                            <div class="inbox-item-top">
                                <strong><?= e($t['name']) ?></strong>
                                <span><?= e($t['time']) ?></span>
                            </div>
                            <p><?= e($t['text']) ?></p>
                        </div>
                        <?php if ($t['unread']): ?><span class="inbox-dot"></span><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="inbox-reply">
                    <span class="inbox-reply-input">Reply to Maya…</span>
                    <button type="button" class="mock-btn mock-btn--small"><svg class="icon"><use href="#i-smile"/></svg> <svg class="icon"><use href="#i-send"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</section>
