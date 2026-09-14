<?php $accountPlatforms = ['instagram', 'facebook', 'tiktok', 'linkedin', 'youtube', 'pinterest']; ?>
<section data-motion="connect" class="motion-section section feature-section">
    <div class="container feature-grid">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-plug"/></svg>Social account management</span>
            <h2>All your social accounts. One workspace.</h2>
            <p>Connect each profile once with secure platform OAuth — no passwords shared — and
               organize them by brand or client into projects. Switching context takes seconds, not logins.</p>
            <ul class="check-list">
                <li>Connect multiple platforms and profiles securely</li>
                <li>Group accounts into brand &amp; client projects</li>
                <li>See every connection’s status at a glance</li>
                <li>Invite teammates to the right workspace</li>
            </ul>
            <a class="text-link" href="<?= e(url('signup')) ?>">Connect your first account <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="accounts-flow">
                <div class="accounts-flow-list">
                    <?php foreach ($accountPlatforms as $i => $slug):
                        $p = array_values(array_filter(config('platforms'), fn ($x) => $x['slug'] === $slug))[0]; ?>
                        <div class="account-flow-card" style="--i: <?= $i ?>; --platform-color: <?= e($p['color']) ?>">
                            <span class="platform-avatar platform-avatar--<?= e($slug) ?>">
                                <svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg>
                            </span>
                            <div>
                                <strong><?= e($p['name']) ?></strong>
                                <span>Connected</span>
                            </div>
                            <span class="account-flow-check"><svg class="icon"><use href="#i-check-circle"/></svg></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="accounts-flow-rail" aria-hidden="true">
                    <span class="flow-particle"></span>
                    <span class="flow-particle flow-particle--b"></span>
                </div>
                <div class="accounts-flow-hub">
                    <img src="<?= e(asset(config('brand.logo_light'))) ?>" alt="" width="40" height="42">
                    <strong>LinkEasy Social</strong>
                    <span class="hub-pulse">6 accounts synced</span>
                </div>
            </div>
        </div>
    </div>
</section>
