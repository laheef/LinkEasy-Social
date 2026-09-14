<section data-motion="connect" class="motion-section section faq-section" id="faq">
    <div class="container container--narrow">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-message"/></svg>FAQ</span>
            <h2>Questions, answered</h2>
            <p>Everything you need to know before getting started.</p>
        </div>

        <div class="faq-list reveal" data-accordion>
            <?php foreach (config('faqs') as $i => $faq): ?>
            <div class="faq-item">
                <h3>
                    <button type="button" class="faq-trigger"
                            id="faq-heading-<?= $i ?>"
                            aria-expanded="false" aria-controls="faq-panel-<?= $i ?>">
                        <span><?= e($faq['q']) ?></span>
                        <span class="faq-icon" aria-hidden="true"><svg class="icon"><use href="#i-plus"/></svg></span>
                    </button>
                </h3>
                <div class="faq-panel" id="faq-panel-<?= $i ?>" role="region"
                     aria-labelledby="faq-heading-<?= $i ?>" hidden>
                    <div class="faq-panel-inner"><p><?= e($faq['a']) ?></p></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="faq-more reveal">
            Still curious? <a href="<?= e(url('contact')) ?>">Talk to our team <svg class="icon"><use href="#i-arrow-up-right"/></svg></a>
        </p>
    </div>
</section>
