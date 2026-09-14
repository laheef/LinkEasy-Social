<?php
$trust = [
    ['key', 'OAuth connections', 'Social accounts connect through official OAuth — we never store or see your platform passwords.'],
    ['lock', 'Credential care', 'Keep API credentials private, grant only necessary permissions and revoke unused platform connections.'],
    ['shield-check', 'Hardened sessions', 'Secure, HttpOnly, SameSite cookies with session regeneration and idle timeouts.'],
    ['shield', 'Access control', 'Every protected request verifies ownership server-side, so users can only reach their own projects.'],
    ['credit-card', 'Verified payments', 'PayPal subscription status is confirmed server-to-server over signed webhooks — never trusted from the browser.'],
    ['database', 'Defense in depth', 'Prepared statements, CSRF protection, output escaping, rate limiting and HTTPS-only handling throughout.'],
];
?>
<section data-motion="connect" class="motion-section section section-soft security-section" id="security">
    <div class="container">
        <div class="security-head reveal">
            <div>
                <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-shield"/></svg>Security &amp; trust</span>
                <h2>Your accounts. Your data. Your control.</h2>
            </div>
            <div class="security-badge">
                <svg class="icon"><use href="#i-shield-check"/></svg>
                <span>A security-minded foundation</span>
            </div>
        </div>

        <div class="trust-grid">
            <?php foreach ($trust as $i => [$icon, $title, $text]): ?>
            <article class="trust-card reveal" style="--i: <?= $i ?>">
                <span class="icon-tile icon-tile--red"><svg class="icon"><use href="#i-<?= e($icon) ?>"/></svg></span>
                <h3><?= e($title) ?></h3>
                <p><?= e($text) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <p class="security-footnote reveal">
            No system can promise to be “100% secure” — we follow defense-in-depth practices,
            validate sign-in state and PayPal webhook signatures server-side. <a href="<?= e(url('security')) ?>">Read our security guidance</a>.
        </p>
    </div>
</section>
