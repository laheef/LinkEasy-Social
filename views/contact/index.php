<?php
$seo = ['title' => 'Contact & setup quotes — ' . config('brand.name'), 'description' => 'Tell LinkEasy Social about your channels, workflow and setup needs. Request a tailored one-time quote or ask a product question.'];
$old = $old ?? []; $errors = $errors ?? [];
$settings = \App\ContactInquiry::settings();
$value = static fn ($key) => is_string($old[$key] ?? null) ? $old[$key] : '';
$preselect = $old['subject'] ?? ($_GET['subject'] ?? '');
$preselect = is_string($preselect) ? $preselect : '';
if (str_starts_with($preselect, 'Managed subscription')) $preselect = 'Managed subscription';
if (!in_array($preselect, $settings['subjects'], true)) $preselect = '';
$isQuote = in_array($preselect, $settings['quote_subjects'], true);
$error = static function ($key) use ($errors) { if (isset($errors[$key])) echo '<p class="field-error" id="error-' . e($key) . '">' . e($errors[$key]) . '</p>'; };
$invalid = static fn ($key) => isset($errors[$key]) ? ' aria-invalid="true" aria-describedby="error-' . e($key) . '"' : '';
require __DIR__ . '/../partials/head.php'; require __DIR__ . '/../partials/header.php';
?>
<main id="main">
    <section class="subpage-hero contact-hero"><div class="container">
        <span class="eyebrow">LET’S TALK ABOUT YOUR WORKFLOW</span>
        <h1>A little context.<br><span>A better starting point.</span></h1>
        <p>Need a hand getting set up? Tell us what you want to connect and where you’re getting stuck. We’ll help you work out the next step.</p>
        <div class="contact-topic-links"><a class="text-link" href="#contact-form" data-choose-quote>Request a setup quote <svg class="icon"><use href="#i-chevron-down"/></svg></a><span>Or choose a topic below for a general question.</span></div>
    </div></section>
    <div class="container contact-grid contact-grid--expanded">
        <aside class="contact-info">
            <span class="eyebrow">A CLEAR SCOPE, FIRST</span><h2>Setup help,<br>without the guesswork.</h2>
            <p>One-Time Setup is custom-quoted around your accounts, API readiness and the help you need. It runs on LinkEasy Social’s hosted platform—not a server you have to maintain.</p>
            <ol class="contact-steps">
                <li><span>01</span><div><strong>Tell us what you’re planning</strong><p>Your channels, accounts and goals give us a useful starting point.</p></div></li>
                <li><span>02</span><div><strong>We review the details</strong><p>We may ask follow-up questions about permissions, approvals or the workflow.</p></div></li>
                <li><span>03</span><div><strong>Agree on the scope</strong><p>You receive a tailored quote to review before any paid setup work begins.</p></div></li>
            </ol>
            <div class="contact-safe-note"><svg class="icon"><use href="#i-shield-check"/></svg><div><strong>Keep your credentials private</strong><p>Never send passwords, API secrets, access tokens or payment details through this form.</p></div></div>
            <div class="contact-direct"><span>Prefer email?</span><a href="mailto:<?= e(config('brand.support_email')) ?>"><?= e(config('brand.support_email')) ?></a></div>
            <p class="contact-fine-print">Provider approvals, quotas and API fees still apply. A quote request is not a purchase or a guarantee of platform approval.</p>
        </aside>
        <div class="contact-form-card" id="contact-form">
            <div class="contact-form-heading"><span class="eyebrow">START A CONVERSATION</span><h2>How can we help?</h2><p>Fields marked <span aria-hidden="true">*</span> are required. Extra setup details appear when you choose a quote.</p></div>
            <?php if ($errors): ?><div class="contact-error-summary" tabindex="-1" data-error-summary role="alert"><strong>Please check the following:</strong><ul><?php foreach ($errors as $key => $text): ?><li><a href="#<?= e($key) ?>"><?= e($text) ?></a></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" action="<?= e(url('contact')) ?>" novalidate data-contact-form data-quote-subjects="<?= e(json_encode($settings['quote_subjects'])) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="request_key" value="<?= e(preg_match('/^[a-f0-9]{32}$/', \App\Request::text($_POST,'request_key')) ? $_POST['request_key'] : bin2hex(random_bytes(16))) ?>">
                <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <input type="hidden" name="form_started" value="<?= time() - ($errors ? 4 : 0) ?>">
                <div class="form-grid-2">
                    <div class="field"><label for="name">Your name *</label><input class="input" id="name" name="name" autocomplete="name" maxlength="100" value="<?= e($value('name')) ?>" placeholder="Full name" required<?= $invalid('name') ?>><?php $error('name'); ?></div>
                    <div class="field"><label for="email">Email address *</label><input class="input" type="email" id="email" name="email" autocomplete="email" maxlength="190" value="<?= e($value('email')) ?>" placeholder="you@company.com" required<?= $invalid('email') ?>><?php $error('email'); ?></div>
                </div>
                <div class="field"><label for="subject">What brings you here? *</label><select class="input" id="subject" name="subject" required aria-controls="quote-details"<?= $invalid('subject') ?>><option value="">Choose a topic…</option><?php foreach ($settings['subjects'] as $subject): ?><option value="<?= e($subject) ?>" <?= $preselect === $subject ? 'selected' : '' ?>><?= e($subject) ?></option><?php endforeach; ?></select><?php $error('subject'); ?></div>
                <fieldset class="quote-details" id="quote-details" data-quote-details <?= !$isQuote ? 'data-initial-hidden' : '' ?>>
                    <legend>Your setup brief</legend><p class="quote-intro">For One-Time Setup or quote requests only. Rough estimates are fine; we’ll confirm the details together.</p>
                    <noscript><p class="contact-safe-note">Only complete this section if you selected One-Time Setup or Request a quote.</p></noscript>
                    <div class="form-grid-2">
                        <?php foreach ($settings['fields'] as $key => $field): ?>
                        <div class="field"><label for="<?= $key ?>"><?= e($field['label']) ?> <?= $field['required'] ? '*' : '<span class="field-optional">(optional)</span>' ?></label>
                            <?php if ($field['type'] === 'select'): ?><select class="input" id="<?= $key ?>" name="<?= $key ?>" <?= $field['required'] ? 'data-quote-required' : '' ?><?= $invalid($key) ?>><option value="">Choose an option…</option><?php foreach ($field['options'] as $option): ?><option <?= $value($key) === $option ? 'selected' : '' ?> value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select>
                            <?php else: ?><input class="input" type="<?= $field['type'] ?>" id="<?= $key ?>" name="<?= $key ?>" value="<?= e($value($key)) ?>" <?= $field['required'] ? 'data-quote-required' : '' ?> <?= $field['type'] === 'number' ? 'min="1" max="9999" step="1" inputmode="numeric"' : 'maxlength="' . $field['max'] . '"' ?> <?= $key === 'organization' ? 'autocomplete="organization"' : ($key === 'phone' ? 'autocomplete="tel"' : '') ?><?= $invalid($key) ?>><?php endif; ?>
                            <?php if ($key === 'account_count'): ?><small class="field-help">Count each profile, Page or channel separately.</small><?php endif; ?>
                            <?php if ($key === 'budget'): ?><small class="field-help">An estimate or “not sure yet” is welcome.</small><?php endif; ?>
                            <?php $error($key); ?>
                        </div><?php endforeach; ?>
                    </div>
                    <?php foreach (['platforms' => ['Platforms to connect', \App\ContactInquiry::platforms()], 'services' => ['What would you like help with?', $settings['services']]] as $key => [$label, $options]): ?>
                    <fieldset class="quote-check-group" id="<?= $key ?>" tabindex="-1"<?= $invalid($key) ?>><legend><?= $label ?> *</legend><p class="field-help">Select all that apply.</p><div class="quote-checks <?= $key === 'services' ? 'quote-checks--services' : '' ?>"><?php foreach ($options as $i => $option): ?><label class="quote-check"><input type="checkbox" name="<?= $key ?>[]" value="<?= e($option) ?>" <?= in_array($option, $old[$key] ?? [], true) ? 'checked' : '' ?>><span><?= e($option) ?></span></label><?php endforeach; ?></div><?php $error($key); ?></fieldset>
                    <?php endforeach; ?>
                    <p class="quote-provider-note">Looking for another platform? Mention it below. Planned integrations are not a promise of availability.</p>
                </fieldset>
                <div class="field"><label for="message" data-message-label><?= $isQuote ? 'Goals & requirements *' : 'Your message *' ?></label><textarea class="input" id="message" name="message" minlength="10" maxlength="5000" rows="5" placeholder="What would a successful setup look like? Tell us about your content, team, migration needs or questions. Please leave out credentials and sensitive account data." required<?= $invalid('message') ?>><?= e($value('message')) ?></textarea><?php $error('message'); ?><small class="field-help">10–5,000 characters. No attachments or credentials needed.</small></div>
                <p class="contact-privacy">We’ll use these details to respond to your enquiry and assess your requested setup. Read our <a href="<?= e(url('privacy')) ?>">Privacy Policy</a>.</p>
                <button type="submit" class="btn btn-primary btn-lg btn-block"><span data-contact-submit><?= $isQuote ? 'Request my setup quote' : 'Send message' ?></span> <svg class="icon"><use href="#i-arrow-right"/></svg></button>
                <p class="contact-submit-note" data-contact-note><?= $isQuote ? 'No payment is taken. Scope and pricing are agreed with you first.' : 'We’ll reply to the email address you provide.' ?></p>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
