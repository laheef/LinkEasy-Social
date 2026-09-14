<?php
$user = \App\Auth::user();
$brand = config('brand');
$footerCols = [
    'Product' => [
        ['Features', url('features')],
        ['Scheduling & calendar', url('features')],
        ['Analytics', url('features')],
        ['Integrations', url('integrations')],
        ['Pricing', url('pricing')],
    ],
    'Resources' => [
        ['Getting started', url('help')],
        ['Help center', url('help')],
        ['Contact support', url('contact')],
        ['Bring Your Own API', url('pricing')],
    ],
    'Company' => [
        ['About', url('about')],
        ['Contact', url('contact')],
        ['Privacy policy', url('privacy')],
        ['Terms of service', url('terms')],
        ['Cookie policy', url('cookies')],
        ['All policies', url('legal')],
        ['Security', url('security')],
        ['Data deletion', url('data_deletion')],
    ],
];
$socialIcons = ['instagram' => 'i-instagram', 'facebook' => 'i-facebook', 'tiktok' => 'i-tiktok',
    'linkedin' => 'i-linkedin', 'youtube' => 'i-youtube', 'x' => 'i-x'];
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand brand-footer" href="<?= e(url('home')) ?>">
                    <img class="brand-mark" src="<?= e(asset($brand['logo_light'])) ?>" alt="" width="36" height="38">
                    <span class="brand-name">LinkEasy<b>Social</b></span>
                </a>
                <p class="footer-tagline"><?= e($brand['tagline']) ?></p>
                <div class="footer-social" aria-label="LinkEasy Social on social media">
                    <?php foreach ($brand['social'] as $network => $href):
                        $valid = is_string($href) && filter_var($href, FILTER_VALIDATE_URL) && in_array(parse_url($href, PHP_URL_SCHEME), ['https', 'http'], true); ?>
                        <?php if ($valid): ?>
                        <a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkEasy Social on <?= e(ucfirst($network)) ?>">
                            <svg class="icon icon-fill"><use href="#<?= e($socialIcons[$network]) ?>"/></svg>
                        </a>
                        <?php else: ?>
                        <span class="footer-social-pending" title="<?= e(ucfirst($network)) ?> — profile link coming soon" aria-label="<?= e(ucfirst($network)) ?> profile link coming soon">
                            <svg class="icon icon-fill"><use href="#<?= e($socialIcons[$network]) ?>"/></svg>
                        </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <a class="footer-email" href="mailto:<?= e($brand['support_email']) ?>">
                    <svg class="icon"><use href="#i-mail"/></svg> <?= e($brand['support_email']) ?>
                </a>
            </div>

            <?php foreach ($footerCols as $heading => $links): ?>
            <nav class="footer-col" aria-label="<?= e($heading) ?>">
                <h3><?= e($heading) ?></h3>
                <ul>
                    <?php foreach ($links as [$label, $href]): ?>
                    <li><a href="<?= e($href) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <?php endforeach; ?>

            <nav class="footer-col" aria-label="Account">
                <h3>Account</h3>
                <ul>
                    <?php if ($user): ?>
                        <li><a href="<?= e(url('dashboard')) ?>">Dashboard</a></li>
                        <li>
                            <form method="post" action="<?= e(url('logout')) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="footer-logout">Log out</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= e(url('login')) ?>">Log in</a></li>
                        <li><a href="<?= e(url('signup')) ?>">Sign up free</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="footer-bottom">
            <button type="button" class="motion-toggle" data-motion-toggle aria-pressed="false" hidden><svg class="icon"><use href="#i-play"/></svg><span>Pause motion</span></button>
            <p>© <?= date('Y') ?> <?= e($brand['name']) ?>. All rights reserved.</p>
            <div class="footer-legal">
                <a href="<?= e(url('privacy')) ?>">Privacy</a>
                <a href="<?= e(url('terms')) ?>">Terms</a>
                <a href="<?= e(url('cookies')) ?>">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<!-- Session expiry modal -->
<div class="session-modal" id="sessionModal" hidden>
    <div class="session-modal-card" role="dialog" aria-modal="true" aria-labelledby="sessionModalTitle">
        <div class="session-modal-icon"><svg class="icon"><use href="#i-lock"/></svg></div>
        <h2 id="sessionModalTitle">Your session has expired</h2>
        <p>Please sign in again to continue managing your social media.</p>
        <div class="session-modal-actions">
            <a class="btn btn-primary" id="sessionLoginBtn" href="<?= e(url('login')) ?>">Log in</a>
            <a class="btn btn-outline" href="<?= e(url('home')) ?>">Return home</a>
        </div>
    </div>
</div>

<script nonce="<?= e(\App\Security::nonce()) ?>" src="<?= e(asset('assets/js/main.min.js')) ?>" defer></script>
</body>
</html>
