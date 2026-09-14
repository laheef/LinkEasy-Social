<section data-motion="connect" class="motion-section section section-soft">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-layers"/></svg>Before vs after</span>
            <h2>Less switching. More doing.</h2>
        </div>
        <div class="ba-grid">
            <article class="ba-card ba-card--before reveal">
                <header class="ba-card-head">
                    <span class="ba-tag">Before LinkEasy Social</span>
                    <h3>Context switching is the job</h3>
                </header>
                <ul class="ba-chaos">
                    <?php foreach (['instagram', 'facebook', 'tiktok', 'linkedin', 'youtube'] as $slug):
                        $p = array_values(array_filter(config('platforms'), fn ($x) => $x['slug'] === $slug))[0]; ?>
                        <li style="--platform-color: <?= e($p['color']) ?>">
                            <span class="platform-avatar platform-avatar--<?= e($slug) ?>"><svg class="icon icon-fill"><use href="#i-<?= e($slug) ?>"/></svg></span>
                            <?= e($p['name']) ?> app
                            <svg class="icon ba-spin"><use href="#i-refresh"/></svg>
                        </li>
                    <?php endforeach; ?>
                    <li><span class="ba-generic-icon"><svg class="icon"><use href="#i-grid"/></svg></span> Spreadsheets</li>
                    <li><span class="ba-generic-icon"><svg class="icon"><use href="#i-calendar"/></svg></span> Separate calendars</li>
                    <li><span class="ba-generic-icon"><svg class="icon"><use href="#i-bar-chart"/></svg></span> Screenshots for reports</li>
                </ul>
            </article>

            <div class="ba-vs reveal" data-reveal-delay="80" aria-hidden="true">
                <span><svg class="icon"><use href="#i-arrow-right"/></svg></span>
            </div>

            <article class="ba-card ba-card--after reveal" data-reveal-delay="160">
                <header class="ba-card-head">
                    <span class="ba-tag ba-tag--red">With LinkEasy Social</span>
                    <h3>One hub runs the whole workflow</h3>
                </header>
                <div class="ba-workspace">
                    <div class="ba-workspace-bar"><span><img src="<?= e(asset(config('brand.logo_light'))) ?>" alt="" width="28" height="28"> Your workspace</span><span class="ba-live">All together</span></div>
                    <div class="ba-workspace-intro"><span>YOUR NEXT MOVE</span><h4>From scattered to in sync.</h4><p>A clear place for your content, your channels and your next big idea.</p></div>
                    <div class="ba-actions">
                        <?php foreach ([['send', 'Publish with purpose', 'One calendar. Every connected channel.', 'rose'], ['bar-chart', 'See the bigger picture', 'Content performance, without the spreadsheets.', 'blue'], ['folder', 'Keep every brand in order', 'Projects that keep your work in its place.', 'amber']] as [$icon, $title, $text, $tone]): ?>
                        <div class="ba-action tone-<?= e($tone) ?>"><span class="module-icon"><svg class="icon"><use href="#i-<?= e($icon) ?>"/></svg></span><div><strong><?= e($title) ?></strong><p><?= e($text) ?></p></div><svg class="icon ba-action-check"><use href="#i-check"/></svg></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= e(url('signup')) ?>" class="ba-workspace-link">Make space for better work <svg class="icon"><use href="#i-arrow-right"/></svg></a>
                </div>
            </article>
        </div>
    </div>
</section>
