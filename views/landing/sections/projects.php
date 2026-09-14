<?php
$projects = [
    ['name' => 'Brand A', 'tone' => 1, 'accounts' => [['instagram', 'Instagram'], ['facebook', 'Facebook'], ['tiktok', 'TikTok']]],
    ['name' => 'Brand B', 'tone' => 2, 'accounts' => [['instagram', 'Instagram'], ['linkedin', 'LinkedIn'], ['youtube', 'YouTube']]],
    ['name' => 'Client C', 'tone' => 3, 'accounts' => [['facebook', 'Facebook'], ['instagram', 'Instagram'], ['x', 'X']]],
];
?>
<section data-motion="connect" class="motion-section section section-soft feature-section">
    <div class="container feature-grid feature-grid--reverse">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-folder"/></svg>Brands &amp; projects</span>
            <h2>Manage multiple brands and projects</h2>
            <p>Keep each brand or client in its own project workspace with its own accounts,
               media, calendar and reports. Perfect for agencies and freelancers — and tidy for growing teams.</p>
            <ul class="check-list">
                <li>A dedicated workspace per brand or client</li>
                <li>Project-level accounts, media and schedules</li>
                <li>Fast switching without logging out</li>
                <li>Plan limits control projects and members</li>
            </ul>
            <a class="text-link" href="<?= e(url('signup')) ?>">Create your first project <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="projects-mock">
                <div class="projects-sidebar">
                    <span class="projects-sidebar-title"><svg class="icon"><use href="#i-briefcase"/></svg> My projects</span>
                    <?php foreach ($projects as $i => $proj): ?>
                    <span class="projects-tab <?= $i === 0 ? 'is-active' : '' ?>">
                        <i class="projects-dot projects-dot--<?= $proj['tone'] ?>"></i> <?= e($proj['name']) ?>
                    </span>
                    <?php endforeach; ?>
                    <span class="projects-tab projects-tab--new"><svg class="icon"><use href="#i-plus"/></svg> New project</span>
                </div>
                <div class="projects-content mock-frame">
                    <div class="mock-frame-head">
                        <span class="mock-frame-title">Brand A</span>
                        <span class="mock-status-pill">3 accounts</span>
                    </div>
                    <div class="projects-accounts">
                        <?php foreach ($projects[0]['accounts'] as [$slug, $label]): ?>
                        <div class="project-account-card">
                            <span class="platform-avatar platform-avatar--<?= e($slug) ?>">
                                <svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg>
                            </span>
                            <div><strong><?= e($label) ?></strong><span>@brand.a</span></div>
                            <span class="account-flow-check"><svg class="icon"><use href="#i-check"/></svg></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="projects-meta">
                        <span><b>12</b> scheduled</span>
                        <span><b>4</b> in review</span>
                        <span><b>+8.2%</b> growth</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
