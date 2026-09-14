<?php
$flowNodes = [
    ['edit','Content planning','Give every great idea a place to start.','planning'],
    ['calendar','Smart scheduling','Find your rhythm. Plan your next move.','scheduling'],
    ['plug','Social accounts','All your channels, without all the tabs.','accounts'],
    ['inbox','Engagement inbox','Keep the conversation moving.','inbox'],
    ['bar-chart','Analytics','See the story behind your content.','analytics'],
    ['file-text','Reports','Bring the bigger picture into focus.','reports'],
];
?>
<section data-motion="connect" class="motion-section section flow-section" id="features">
    <div class="container">
        <div class="section-head reveal"><span class="eyebrow"><svg class="icon section-cue"><use href="#i-grid"/></svg>EVERYTHING IN ONE PLACE</span><h2>Everything you need to manage<br class="flow-desktop-break"> your social presence</h2><p>Not another collection of tools. One connected flow—from your first idea to your next insight.</p></div>
        <div class="flow-map reveal" aria-label="Six connected tools around your LinkEasy Social workspace">
            <div class="flow-backbone" aria-hidden="true"></div>
            <div class="flow-center"><div class="flow-center-halo" aria-hidden="true"></div><span class="flow-center-status"><i></i> YOUR CONNECTED WORKSPACE</span><img class="flow-center-logo" src="<?= e(asset(config('brand.logo_light'))) ?>" width="62" height="62" alt=""><h3>LinkEasy <br>Social</h3><p>One place. <br>Everything moving.</p><div class="flow-center-platforms"><?php foreach(['instagram','linkedin','youtube'] as $slug): ?><span class="platform-avatar platform-avatar--<?= $slug ?>"><svg class="icon icon-fill"><use href="#i-<?= $slug ?>"/></svg></span><?php endforeach; ?></div></div>
            <?php foreach($flowNodes as $i=>[$icon,$title,$description,$kind]): ?>
            <article class="flow-node flow-node--<?= $i%2?'right':'left' ?> flow-node--<?= $kind ?>" style="--flow-row: <?= intdiv($i,2)+1 ?>; --flow-delay: <?= $i*.55 ?>s">
                <div class="flow-node-top"><span class="flow-icon"><svg class="icon"><use href="#i-<?= $icon ?>"/></svg></span><span class="flow-number"><?= sprintf('%02d',$i+1) ?></span></div>
                <h3><?= $title ?></h3><p><?= $description ?></p>
                <div class="flow-mini" aria-hidden="true">
                    <?php if($kind==='planning'): ?><div class="flow-mini-planning"><div class="flow-photo-stack"><img src="<?= e(asset('assets/img/media/media-7.jpg')) ?>" width="50" height="38" alt="" loading="lazy"><img src="<?= e(asset('assets/img/media/media-3.jpg')) ?>" width="50" height="38" alt="" loading="lazy"></div><span>A new idea, taking shape<span class="flow-mini-line"></span></span><svg class="icon"><use href="#i-edit"/></svg></div>
                    <?php elseif($kind==='scheduling'): ?><div class="flow-mini-week"><?php foreach(['M','T','W','T','F','S','S'] as $j=>$day): ?><span class="<?= in_array($j,[1,3,4])?'has-slot':'' ?>"><?= $day ?><i></i></span><?php endforeach; ?></div>
                    <?php elseif($kind==='accounts'): ?><div class="flow-mini-accounts"><?php foreach(['instagram','facebook','tiktok','linkedin','youtube'] as $slug): ?><span class="platform-avatar platform-avatar--<?= $slug ?>"><svg class="icon icon-fill"><use href="#i-<?= $slug ?>"/></svg></span><?php endforeach; ?><svg class="icon flow-check"><use href="#i-check-circle"/></svg></div>
                    <?php elseif($kind==='inbox'): ?><div class="flow-mini-inbox"><span><svg class="icon"><use href="#i-message"/></svg></span><div><b>Keep conversations together</b><i></i></div><em>Reply</em></div>
                    <?php elseif($kind==='analytics'): ?><svg class="flow-mini-chart" viewBox="0 0 290 42" preserveAspectRatio="none"><path d="M0 38H290M0 20H290" stroke="#ece8ef"/><path class="flow-trend" d="M0 34C25 34 25 24 48 25C70 26 65 36 88 28C105 21 113 12 135 16C154 20 155 32 177 22C194 14 199 14 220 10C243 6 263 19 290 4" fill="none" stroke="#fc2428" stroke-width="2" stroke-linecap="round" pathLength="1"/></svg>
                    <?php else: ?><div class="flow-mini-report"><span><svg class="icon"><use href="#i-file-text"/></svg> Your next insight</span><i></i><i></i><i></i><svg class="icon"><use href="#i-download"/></svg></div><?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="flow-bottom"><span><svg class="icon"><use href="#i-link"/></svg> Less switching. A more connected way to work.</span><a class="text-link" href="<?= e(url('signup')) ?>">Find your flow <svg class="icon"><use href="#i-arrow-right"/></svg></a></div>
        <p class="flow-disclosure">Illustrative workflows. Supported actions depend on your plan and each platform’s API.</p>
    </div>
</section>
