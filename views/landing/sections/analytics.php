<section data-motion="measure" class="motion-section section feature-section">
    <div class="container feature-grid">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-bar-chart"/></svg>Analytics</span>
            <h2>Know what’s working</h2>
            <p>Reach, engagement, likes, comments, shares and follower growth — unified across platforms.
               Spot trends early, double down on what resonates and prove the impact of every post.</p>
            <ul class="check-list">
                <li>Impressions, reach &amp; engagement trends</li>
                <li>Follower growth across every network</li>
                <li>Best-performing posts at a glance</li>
                <li>Export-ready reporting for stakeholders</li>
            </ul>
            <a class="text-link" href="<?= e(url('pricing')) ?>">See analytics in every plan <svg class="icon"><use href="#i-arrow-right"/></svg></a>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame analytics-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-bar-chart"/></svg> Performance overview</span>
                    <span class="mock-status-pill mock-status-pill--green"><span class="pulse-dot"></span> Live</span>
                </div>
                <div class="analytics-kpis">
                    <div class="analytics-kpi">
                        <span>Reach</span>
                        <strong data-count="24.8" data-decimals="1" data-suffix="K">0</strong>
                        <em class="up">▲ 18%</em>
                    </div>
                    <div class="analytics-kpi">
                        <span>Engagement</span>
                        <strong data-count="8.4" data-decimals="1" data-suffix="%">0</strong>
                        <em class="up">▲ 2.1%</em>
                    </div>
                    <div class="analytics-kpi">
                        <span>Followers</span>
                        <strong data-prefix="+" data-count="1240">0</strong>
                        <em class="up">▲ 12.7%</em>
                    </div>
                    <div class="analytics-kpi">
                        <span>Shares</span>
                        <strong data-count="3.2" data-decimals="1" data-suffix="K">0</strong>
                        <em class="down">▼ 0.4%</em>
                    </div>
                </div>
                <svg class="analytics-chart" viewBox="0 0 480 170" preserveAspectRatio="none" aria-label="Engagement trend chart">
                    <defs>
                        <linearGradient id="analyticsArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#FC2428" stop-opacity=".25"/>
                            <stop offset="100%" stop-color="#FC2428" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <?php foreach ([34, 68, 102, 136] as $gy): ?>
                    <line x1="0" y1="<?= $gy ?>" x2="480" y2="<?= $gy ?>" class="chart-grid-line"/>
                    <?php endforeach; ?>
                    <path class="chart-area" d="M0 130 C 40 124 60 96 96 100 C 140 105 160 60 200 56 C 245 52 270 78 310 60 C 355 40 390 48 420 30 C 445 17 465 20 480 14 L480 170 L0 170 Z" fill="url(#analyticsArea)"/>
                    <path class="chart-line" d="M0 130 C 40 124 60 96 96 100 C 140 105 160 60 200 56 C 245 52 270 78 310 60 C 355 40 390 48 420 30 C 445 17 465 20 480 14" fill="none" stroke="#FC2428" stroke-width="2.5" stroke-linecap="round"/>
                    <circle class="chart-dot" cx="420" cy="30" r="4"/>
                </svg>
                <div class="analytics-bars" aria-hidden="true">
                    <?php foreach ([40, 65, 48, 80, 58, 92, 70] as $i => $h): ?>
                    <span class="analytics-bar <?= $i === 5 ? 'is-high' : '' ?>" style="--h: <?= $h ?>%; --i: <?= $i ?>"></span>
                    <?php endforeach; ?>
                </div>
                <div class="analytics-bottom">
                    <span class="analytics-legend"><i class="legend-dot legend-dot--red"></i> Engagement</span>
                    <span class="analytics-best">
                        <svg class="icon"><use href="#i-star"/></svg> Top post: <b>Summer launch reel</b>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
