<?php $dashPlatforms = ['instagram'=>'Instagram','facebook'=>'Facebook','linkedin'=>'LinkedIn','tiktok'=>'TikTok','youtube'=>'YouTube']; ?>
<section class="hero hero--command motion-section" id="top" data-motion="measure">
    <div class="container">
        <div class="hero-copy-scene">
            <div class="hero-social-orbit" aria-hidden="true">
                <?php foreach (['instagram','youtube','linkedin','tiktok','facebook','pinterest'] as $i => $slug): ?>
                <span class="hero-orbit-slot hero-orbit-slot--<?= $i+1 ?>"><span class="hero-orbit-float" style="--float-delay: -<?= $i*1.2 ?>s"><span class="platform-avatar platform-avatar--<?= $slug ?>"><svg class="icon icon-fill"><use href="#i-<?= $slug ?>"/></svg></span></span></span>
                <?php endforeach; ?>
                <span class="hero-orbit-dot hero-orbit-dot--one"></span><span class="hero-orbit-dot hero-orbit-dot--two"></span>
            </div>
        <div class="command-intro">
            <span class="command-eyebrow"><svg class="icon section-cue"><use href="#i-layers"/></svg> LESS TAB-SWITCHING. MORE MOMENTUM.</span>
            <h1>Make time for the ideas.<br><span>Not the busywork.</span></h1>
            <p>Turn a spark of inspiration into a week of content.<br class="command-break"> Plan your posts, stay present across your channels, and see what connects—all without the daily scramble.</p>
            <div class="command-ctas"><a class="btn btn-primary btn-lg" href="<?= e(url('signup')) ?>">Find your flow <svg class="icon"><use href="#i-arrow-right"/></svg></a><a class="btn btn-outline btn-lg" href="#command-preview">See it in action <svg class="icon"><use href="#i-chevron-down"/></svg></a></div>
            <span class="command-note">Your content. Your rhythm. A clearer way to keep it moving.</span>
        </div>

        </div>
        <div id="command-preview" class="command-stage" aria-label="Illustrative LinkEasy Social dashboard">
            <div class="command-stage-caption"><span><span class="command-signal"></span> YOUR SOCIAL COMMAND CENTER</span><div><span>DEMO WORKSPACE · SAMPLE DATA</span><button class="motion-toggle" type="button" data-motion-toggle aria-pressed="false" hidden><svg class="icon"><use href="#i-pause"/></svg><span>Pause motion</span></button></div></div>
            <div class="command-window">
                <aside class="command-sidebar" aria-label="Illustrative dashboard navigation">
                    <div class="command-sidebar-brand"><img src="<?= e(asset(config('brand.logo_light'))) ?>" alt="" width="28" height="28"><strong>LinkEasy Social</strong></div>
                    <div class="command-workspace"><span>S</span><div><b>Studio workspace</b><small>Personal workspace</small></div><svg class="icon"><use href="#i-chevron-down"/></svg></div>
                    <span class="command-sidebar-label">WORKSPACE</span>
                    <?php foreach ([['grid','Overview'],['edit','Create a post'],['calendar','Content calendar'],['bar-chart','Analytics'],['inbox','Inbox'],['folder','Media library']] as $i=>[$icon,$name]): ?><span class="command-nav-item <?= !$i?'is-current':'' ?>"><svg class="icon"><use href="#i-<?= $icon ?>"/></svg><span><?= $name ?></span><?php if(!$i): ?><i></i><?php endif; ?></span><?php endforeach; ?>
                    <div class="command-sidebar-bottom"><svg class="icon"><use href="#i-plug"/></svg><span>Your channels.<br>Working together.</span></div>
                </aside>
                <div class="command-main">
                    <div class="command-topbar"><span>Workspace <span>/</span> <strong>Overview</strong></span><span class="command-topbar-right"><svg class="icon"><use href="#i-bell"/></svg><span class="command-avatar">S</span></span></div>
                    <div class="command-greeting"><div><span class="command-overline">A CLEARER PICTURE OF YOUR CONTENT</span><h2>Your week, working together.</h2></div><a class="command-new" href="<?= e(url('signup')) ?>"><svg class="icon"><use href="#i-plus"/></svg> New post</a></div>
                    <div class="command-columns">
                        <div class="command-primary">
                            <section class="command-analytics" aria-label="Sample performance analytics">
                                <div class="command-card-heading"><h3><svg class="icon"><use href="#i-bar-chart"/></svg> Performance overview</h3><span>Last 7 days · Demo</span></div>
                                <div class="command-metrics" role="tablist" aria-label="Sample analytics metric">
                                    <?php foreach ([['reach','Total reach','24.8K','12.6%'],['engagement','Engagement','5.2%','8.4%'],['audience','Audience','8,420','4.2%']] as $i=>[$key,$name,$value,$change]): ?><button type="button" class="command-metric <?= !$i?'is-active':'' ?>" role="tab" id="metric-<?= $key ?>" aria-controls="command-chart-panel" aria-selected="<?= !$i?'true':'false' ?>" tabindex="<?= !$i?'0':'-1' ?>" data-command-metric="<?= $key ?>"><span><?= $name ?></span><strong><?= $value ?></strong><small><svg class="icon"><use href="#i-trending-up"/></svg> <?= $change ?></small></button><?php endforeach; ?>
                                </div>
                                <div class="command-chart-panel" id="command-chart-panel" role="tabpanel" aria-labelledby="metric-reach" tabindex="0">
                                    <div class="command-chart-key"><span><i></i><span data-command-series>Total reach</span></span><span><i></i> Previous period</span></div>
                                    <svg class="command-chart" viewBox="0 0 540 142" preserveAspectRatio="none" role="img" aria-label="Illustrative reach trend over seven days, not live account data">
                                        <defs><linearGradient id="commandArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fc2428" stop-opacity=".14"/><stop offset="1" stop-color="#fc2428" stop-opacity="0"/></linearGradient></defs>
                                        <g stroke="#eeeaf0" stroke-width="1"><path d="M0 20H540M0 60H540M0 100H540M0 140H540"/></g>
                                        <path d="M0 111C40 110 51 89 84 98S138 74 175 85S224 50 265 68S323 55 354 69S420 37 447 45S501 39 540 28" fill="none" stroke="#c9c3cf" stroke-width="2" stroke-dasharray="5 5"/>
                                        <path class="command-chart-area" d="M0 115C35 115 56 86 84 90S142 116 175 78S224 91 265 51S321 76 354 45S417 70 447 31S505 42 540 12V142H0Z" fill="url(#commandArea)"/>
                                        <path class="command-chart-line" d="M0 115C35 115 56 86 84 90S142 116 175 78S224 91 265 51S321 76 354 45S417 70 447 31S505 42 540 12" fill="none" stroke="#fc2428" stroke-width="2.5" stroke-linecap="round" pathLength="1"/>
                                    </svg>
                                    <div class="command-chart-days"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div>
                                </div>
                            </section>
                            <section class="command-queue" aria-label="Sample upcoming posts"><div class="command-card-heading"><h3><svg class="icon"><use href="#i-calendar"/></svg> Coming up next</h3><span>Your content rhythm</span></div>
                                <?php foreach ([['media-7.jpg','instagram','A slower kind of Sunday','Today','09:00'],['media-3.jpg','linkedin','A fresh perspective','Tomorrow','11:30']] as [$img,$slug,$title,$day,$time]): ?><div class="command-queue-row"><img src="<?= e(asset('assets/img/media/'.$img)) ?>" width="40" height="40" loading="lazy" alt="Sample scheduled post"><div><strong><?= $title ?></strong><span><svg class="icon icon-fill"><use href="#i-<?= $slug ?>"/></svg> <?= $dashPlatforms[$slug] ?> · Scheduled</span></div><time><b><?= $day ?></b><?= $time ?></time></div><?php endforeach; ?>
                            </section>
                        </div>
                        <div class="command-secondary">
                            <section class="command-social"><div class="command-card-heading"><h3>Connected accounts</h3><svg class="icon"><use href="#i-plug"/></svg></div><div class="command-platforms"><?php foreach($dashPlatforms as $slug=>$name): ?><span class="platform-avatar platform-avatar--<?= $slug ?>" aria-label="<?= $name ?>"><svg class="icon icon-fill"><use href="#i-<?= $slug ?>"/></svg><i></i></span><?php endforeach; ?></div><p>Different channels. One workspace.</p></section>
                            <section class="command-post"><div class="command-card-heading"><h3>Ready for your audience</h3><svg class="icon"><use href="#i-send"/></svg></div><div class="command-post-photo"><img src="<?= e(asset('assets/img/media/media-7.jpg')) ?>" width="300" height="190" fetchpriority="high" decoding="async" alt="Beach photograph in an illustrative social media post"><span><svg class="icon icon-fill"><use href="#i-instagram"/></svg> POST PREVIEW</span></div><div class="command-post-copy"><strong>A slower kind of Sunday.</strong><p>A little salt air. A little room for the moments in between.</p><div><span><svg class="icon"><use href="#i-heart"/></svg><svg class="icon"><use href="#i-message"/></svg><svg class="icon"><use href="#i-send"/></svg></span><span>Made in your studio</span></div></div></section>
                            <div class="command-insight"><span><svg class="icon"><use href="#i-calendar-check"/></svg></span><div><strong>A little planning. More possibility.</strong><p>Keep your next idea moving.</p></div></div>
                        </div>
                    </div>
                    <div class="command-foot"><span><span></span> Sample workspace—not live account activity</span><a href="<?= e(url('signup')) ?>">Make it yours <svg class="icon"><use href="#i-arrow-up-right"/></svg></a></div>
                </div>
            </div>
        </div>
    </div>
</section>
