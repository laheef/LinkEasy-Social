<section data-motion="measure" class="motion-section section section-soft feature-section">
    <div class="container feature-grid feature-grid--reverse">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-file-text"/></svg>Reporting</span>
            <h2>Turn social data into useful reports</h2>
            <p>Generate clean monthly summaries with follower growth, engagement, reach and your
               best-performing platform — ready to share with clients, your team or your boss.</p>
            <ul class="check-list">
                <li>Automated monthly performance summaries</li>
                <li>Growth, engagement and reach at a glance</li>
                <li>Top posts and best platform highlighted</li>
                <li>Export-ready for client reporting</li>
            </ul>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame report-mock">
                <div class="report-head">
                    <div>
                        <span class="mock-eyebrow">Generated report</span>
                        <h3>Monthly report <span class="report-month">— August</span></h3>
                    </div>
                    <span class="report-download"><svg class="icon"><use href="#i-download"/></svg> PDF</span>
                </div>
                <div class="report-kpis">
                    <div class="report-kpi"><span>Followers</span><strong class="up">+18%</strong><i style="--w:72%"></i></div>
                    <div class="report-kpi"><span>Engagement</span><strong class="up">+24%</strong><i style="--w:84%"></i></div>
                    <div class="report-kpi"><span>Reach</span><strong class="up">+31%</strong><i style="--w:92%"></i></div>
                </div>
                <div class="report-bars">
                    <?php foreach ([['instagram', 92, 'IG'], ['facebook', 64, 'FB'], ['tiktok', 78, 'TT'], ['linkedin', 48, 'LI']] as [$slug, $w, $lbl]): ?>
                    <div class="report-bar-row">
                        <span class="platform-avatar platform-avatar--<?= e($slug) ?>"><svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg></span>
                        <span class="report-bar-track"><i class="report-bar-fill report-bar-fill--<?= e($slug) ?>" style="--w: <?= $w ?>%"></i></span>
                        <b><?= e($lbl) ?></b>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="report-best">
                    <svg class="icon"><use href="#i-trophy"/></svg>
                    Best platform this month: <b>Instagram</b>
                </div>
            </div>
        </div>
    </div>
</section>
