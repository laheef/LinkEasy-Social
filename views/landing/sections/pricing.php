<?php
$pricing = config('pricing');
$plans = $pricing['plans'];
$planHrefs = [
    'opensource' => url('contact', ['subject' => 'Bring Your Own API']),
    'free'       => url('signup'),
    'setup'      => url('contact', ['subject' => 'One-Time Setup']),
    'managed'    => url('paypal_create', ['cycle' => 'monthly']),
];
?>
<section data-motion="measure" class="motion-section section pricing-section" id="pricing">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-key"/></svg>Pricing</span>
            <h2>Choose how you run LinkEasy Social</h2>
            <p>One hosted platform. Bring your own API credentials, start small for free, get setup help, or let us manage the technical side.</p>

            <div class="billing-toggle reveal" role="group" aria-label="Billing period" data-billing-toggle>
                <button type="button" class="is-active" data-billing="monthly" aria-pressed="true">Monthly</button>
                <button type="button" data-billing="yearly" aria-pressed="false">Yearly <span class="save-badge">Save <?= (int) $pricing['yearly_save_pct'] ?>%</span></button>
            </div>
        </div>

        <div class="pricing-grid">
            <?php foreach ($plans as $id => $plan): ?>
            <article class="price-card reveal <?= !empty($plan['highlight']) ? 'price-card--featured' : '' ?>" data-plan="<?= e($id) ?>">
                <?php if (!empty($plan['badge'])): ?>
                    <span class="price-badge"><?= e($plan['badge']) ?></span>
                <?php endif; ?>
                <header class="price-head">
                    <h3><?= e($plan['name']) ?></h3>
                    <p><?= e($plan['description']) ?></p>
                </header>

                <div class="price-amount">
                    <?php if ($id === 'managed'): ?>
                        <span class="price-currency"><?= e($pricing['currency']) ?></span>
                        <span class="price-value"
                              data-price-monthly="<?= (int) $pricing['managed_monthly'] ?>"
                              data-price-yearly="<?= e(number_format($pricing['managed_yearly'] / 12, 2, '.', '')) ?>"><?= (int) $pricing['managed_monthly'] ?></span>
                        <span class="price-cadence" data-cadence-monthly="/month" data-cadence-yearly="/month, billed yearly">/month</span>
                    <?php elseif ($plan['price'] === 0): ?>
                        <span class="price-value"><?= e($pricing['currency']) ?>0</span>
                        <span class="price-cadence">/month</span>
                    <?php elseif ($plan['price'] === null): ?>
                        <span class="price-value price-value--text"><?= e($plan['price_note']) ?></span><span class="price-cadence"><?= e($plan['cadence']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($id === 'managed'): ?>
                <p class="price-yearly-note" data-yearly-note hidden>
                    <?= e($pricing['currency'] . $pricing['managed_yearly']) ?> billed annually
                </p>
                <?php endif; ?>

                <a class="btn <?= !empty($plan['highlight']) ? 'btn-primary' : 'btn-outline' ?> btn-block price-cta <?= $id === 'managed' ? 'js-managed-cta' : '' ?>"
                   href="<?= e($planHrefs[$id]) ?>"
                   <?= $id === 'managed' ? 'data-href-monthly="' . e(url('paypal_create', ['cycle' => 'monthly'])) . '" data-href-yearly="' . e(url('paypal_create', ['cycle' => 'yearly'])) . '"' : '' ?>>
                    <?= e($plan['cta']) ?> <svg class="icon"><use href="#i-arrow-right"/></svg>
                </a>

                <ul class="price-features">
                    <?php foreach ($plan['features'] as $feature): ?>
                    <li>
                        <span class="price-check"><svg class="icon"><use href="#i-check"/></svg></span>
                        <?= e($feature) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
        </div>

        <p class="pricing-note reveal">
            <svg class="icon"><use href="#i-info"/></svg>
            Unlimited accounts and no platform posting quota apply to BYO API and One-Time Setup. Provider quotas, fees and approval rules still apply. Free and Managed have the limits shown above.
            Questions? <a href="<?= e(url('contact')) ?>">Talk to us</a>.
        </p>
    </div>
</section>
