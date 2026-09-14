<section data-motion="create" class="motion-section section usecases-section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-users"/></svg>Built for the way you work</span>
            <h2>One platform, every kind of social team</h2>
        </div>

        <div class="usecases reveal" data-tabs>
            <div class="usecase-tabs" role="tablist" aria-label="Use cases">
                <?php foreach (config('use_cases') as $i => $uc): ?>
                <button type="button" class="usecase-tab <?= $i === 0 ? 'is-active' : '' ?>" role="tab"
                        id="tab-<?= e($uc['slug']) ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                        aria-controls="panel-<?= e($uc['slug']) ?>" data-tab="<?= e($uc['slug']) ?>">
                    <svg class="icon"><use href="#i-<?= e($uc['icon']) ?>"/></svg>
                    <span><?= e($uc['name']) ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <div class="usecase-panels">
                <?php foreach (config('use_cases') as $i => $uc): ?>
                <div class="usecase-panel <?= $i === 0 ? 'is-active' : '' ?>" role="tabpanel"
                     id="panel-<?= e($uc['slug']) ?>" aria-labelledby="tab-<?= e($uc['slug']) ?>"
                     data-panel="<?= e($uc['slug']) ?>" <?= $i === 0 ? '' : 'hidden' ?>>
                    <div class="usecase-copy">
                        <h3><?= e($uc['title']) ?></h3>
                        <p><?= e($uc['description']) ?></p>
                        <ul class="check-list">
                            <?php foreach ($uc['points'] as $point): ?>
                            <li><?= e($point) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="text-link" href="<?= e(url('signup')) ?>">Start free for <?= e(strtolower($uc['name'])) ?> <svg class="icon"><use href="#i-arrow-right"/></svg></a>
                    </div>
                    <div class="usecase-visual usecase-visual--<?= e($uc['slug']) ?>">
                        <span class="usecase-visual-icon"><svg class="icon"><use href="#i-<?= e($uc['icon']) ?>"/></svg></span>
                        <div class="usecase-mini-card">
                            <?php foreach ($uc['points'] as $j => $point): ?>
                            <span style="--i: <?= $j ?>"><i class="mini-dot"></i><?= e($point) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
