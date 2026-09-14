<?php
$seo = ['title' => 'Sign up free — ' . config('brand.name'), 'description' => 'Create your free LinkEasy Social account. A hosted workspace for your social content.'];
require __DIR__ . '/../partials/head.php';
$old = $old ?? [];
$errors = $errors ?? [];
function auth_field_value(string $key, array $old): string {
    return e($old[$key] ?? '');
}
?>
<div class="auth-shell">
    <?php require __DIR__ . '/../partials/auth-aside.php'; ?>

    <main class="auth-main">
        <div class="auth-card">
            <a class="auth-mobile-brand brand" href="<?= e(url('home')) ?>">
                <img class="brand-mark" src="<?= e(asset(config('brand.logo'))) ?>" alt="" width="34" height="36">
                <span class="brand-name">LinkEasy<b>Social</b></span>
            </a>

            <?php foreach (flashes() as $flash): ?>
                <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:16px">
                    <svg class="icon"><use href="#i-info"/></svg>
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endforeach; ?>

            <h1>Create your account</h1>
            <p class="auth-sub">Start free — <?= (int) config('pricing.plans.free.limits.max_social_accounts') ?> social accounts in one hosted workspace.</p>

            <a class="auth-google" href="<?= e(url('google_login')) ?>">
                <svg class="icon icon-fill"><use href="#i-google"/></svg> Continue with Google
            </a>
            <div class="auth-divider"><span>or sign up with email</span></div>

            <form method="post" action="<?= e(url('signup')) ?>" novalidate>
                <?= csrf_field() ?>
                <!-- Honeypot: real users never fill this -->
                <div class="hp-field" aria-hidden="true">
                    <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <input type="hidden" name="form_started" value="<?= time() ?>">

                <div class="field">
                    <label for="name">Full name</label>
                    <input class="input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" type="text" id="name"
                           name="name" autocomplete="name" placeholder="Jordan Lee" value="<?= auth_field_value('name', $old) ?>" required>
                    <?php if (isset($errors['name'])): ?><p class="field-error"><?= e($errors['name']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="email">Email address</label>
                    <input class="input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" type="email" id="email"
                           name="email" autocomplete="email" placeholder="you@company.com" value="<?= auth_field_value('email', $old) ?>" required>
                    <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>
                </div>
                <div class="form-grid-2">
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input class="input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" type="password" id="password"
                                   name="password" autocomplete="new-password" placeholder="At least 8 characters" required>
                            <button type="button" class="input-toggle" data-password-toggle data-target="password"
                                    aria-label="Show password"><svg class="icon"><use href="#i-eye"/></svg></button>
                        </div>
                        <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
                    </div>
                    <div class="field">
                        <label for="password_confirm">Confirm password</label>
                        <div class="input-wrap">
                            <input class="input <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" type="password"
                                   id="password_confirm" name="password_confirm" autocomplete="new-password" placeholder="Repeat it" required>
                            <button type="button" class="input-toggle" data-password-toggle data-target="password_confirm"
                                    aria-label="Show password"><svg class="icon"><use href="#i-eye"/></svg></button>
                        </div>
                        <?php if (isset($errors['password_confirm'])): ?><p class="field-error"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
                    </div>
                </div>
                <label class="checkbox-field" style="margin:4px 0 20px">
                    <input type="checkbox" name="terms" value="1" required <?= (\App\Request::text($_POST,'terms') === '1') ? 'checked' : '' ?>> I agree to the
                    <a href="<?= e(url('terms')) ?>" style="color:var(--red-deep);font-weight:650">Terms</a> and
                    <a href="<?= e(url('privacy')) ?>" style="color:var(--red-deep);font-weight:650">Privacy Policy</a>
                </label>
                <?php if (isset($errors['terms'])): ?><p class="field-error"><?= e($errors['terms']) ?></p><?php endif; ?>
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Create account <svg class="icon"><use href="#i-arrow-right"/></svg>
                </button>
            </form>

            <p class="auth-foot">Already have an account? <a href="<?= e(url('login')) ?>">Log in</a></p>
        </div>
    </main>
</div>
<?php require __DIR__ . '/../partials/auth-foot.php'; ?>
