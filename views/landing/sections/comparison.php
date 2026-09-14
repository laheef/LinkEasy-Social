<?php
$rows = config('comparison');
$columns = [
    'opensource' => [config('pricing.plans.opensource.name'), 'i-code'],
    'free'       => ['Free', 'i-zap'],
    'setup'      => ['One-Time Setup', 'i-rocket'],
    'managed'    => ['Managed', 'i-server'],
];
?>
<section data-motion="measure" class="motion-section section section-soft comparison-section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-grid"/></svg>Compare plans</span>
            <h2>What’s included in each option</h2>
            <p>Compare platform allowances side by side. Social networks set their own API access rules and rate limits.</p>
        </div>

        <div class="table-scroll reveal">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th scope="col" class="comparison-corner">
                            <span class="comparison-brand-mini">
                                <img src="<?= e(asset('assets/img/logo-mark.png')) ?>" alt="" width="22" height="23">
                                LinkEasy Social
                            </span>
                        </th>
                        <?php foreach ($columns as $key => [$label, $icon]): ?>
                        <th scope="col" class="<?= $key === 'managed' ? 'is-featured' : '' ?>">
                            <svg class="icon"><use href="#<?= e($icon) ?>"/></svg>
                            <span><?= e($label) ?></span>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <th scope="row"><?= e($row['label']) ?></th>
                        <?php foreach (array_keys($columns) as $key):
                            $value = $row[$key]; ?>
                            <td class="<?= $key === 'managed' ? 'is-featured' : '' ?>">
                                <?php if ($value === true): ?>
                                    <span class="table-check"><svg class="icon"><use href="#i-check"/></svg></span>
                                    <span class="sr-only">Included</span>
                                <?php elseif ($value === false): ?>
                                    <span class="table-no">—</span>
                                <?php else: ?>
                                    <?= e($value) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row"></th>
                        <?php foreach (array_keys($columns) as $key):
                            $hrefs = [
                                'opensource' => url('contact', ['subject' => 'Bring Your Own API']),
                                'free' => url('signup'),
                                'setup' => url('contact', ['subject' => 'One-Time Setup']),
                                'managed' => url('paypal_create', ['cycle' => 'monthly']),
                            ];
                            $labels = ['opensource' => 'Explore', 'free' => 'Start free', 'setup' => 'Request setup', 'managed' => 'Subscribe'];
                        ?>
                        <td class="<?= $key === 'managed' ? 'is-featured' : '' ?>">
                            <a class="btn <?= $key === 'managed' ? 'btn-primary' : 'btn-outline' ?> btn-sm btn-block" href="<?= e($hrefs[$key]) ?>"><?= e($labels[$key]) ?></a>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="comparison-hint">Tip: scroll the table sideways on small screens — the first column stays visible.</p>
    </div>
</section>
