<?php
/**
 * Route table. Uses a dependency-free micro router so the scaffold has no
 * build step. In the existing application, map these same paths to its
 * router/controllers — the views and config carry over unchanged.
 */

use App\Auth;
use App\Database;
use App\PlanGate;
use App\RateLimiter;
use App\Services\GoogleOAuth;
use App\Services\Mailer;
use App\Services\PayPal;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    private function normalize(string $path): string
    {
        return rtrim($path, '/') ?: '/';
    }

    public function dispatch(string $method, string $path): void
    {
        $path = $this->normalize($path);
        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            // Try GET for HEAD
            if ($method === 'HEAD' && isset($this->routes['GET'][$path])) {
                $handler = $this->routes['GET'][$path];
            } else {
                les_render_error(404);
                return;
            }
        }
        $handler();
    }
}

$router = new Router();

/* ---------------------------------------------------------------------
 * Landing
 * ------------------------------------------------------------------- */
$router->get('/', function (): void {
    view('landing/index', [
        'seo' => config('seo.home'),
        'bodyClass' => 'is-home',
    ]);
});

$router->get('/contact', function (): void {
    view('contact/index', ['bodyClass' => 'is-subpage']);
});

$router->post('/contact', function (): void {
    csrf_verify();

    // Honeypot + minimum-fill-time spam traps
    if (!is_string($_POST['website'] ?? '') || trim($_POST['website'] ?? '') !== '') {
        flash('success', 'Thanks — your message has been received.');
        redirect(url('contact'));
    }
    $renderedAt = is_string($_POST['form_started'] ?? null) ? (int) $_POST['form_started'] : 0;

    if (!RateLimiter::check('contact', 6, 3600)) {
        RateLimiter::reject(RateLimiter::retryAfter('contact',3600));
    }

    [$data, $errors, $isQuote] = \App\ContactInquiry::validate($_POST);
    if (!$errors && $renderedAt > 0 && (time() - $renderedAt) < 3) {
        $errors['message'] = 'Please wait a moment, then submit your request again. Your details are still here.';
    }
    if ($errors) {
        http_response_code(422);
        view('contact/index', ['bodyClass' => 'is-subpage', 'errors' => $errors, 'old' => $data]);
        return;
    }
    $requestKey=\App\Request::text($_POST,'request_key');
    if (!preg_match('/^[a-f0-9]{32}$/',$requestKey)) \App\Request::reject(400,'Please reload the form and try again.');
    \App\Services\ContactService::submit($data,$isQuote,$requestKey);
    flash('success', $isQuote ? 'Your quote request has been saved. We’ll review the scope and reply by email before any work begins.' : 'Your message has been saved. Thanks for reaching out to LinkEasy Social.');
    redirect(url('contact'));
});

/* ---------------------------------------------------------------------
 * Authentication
 * ------------------------------------------------------------------- */
$router->get('/login', function (): void {
    if (Auth::check()) {
        redirect(url('dashboard'));
    }
    view('auth/login', ['bodyClass' => 'is-auth']);
});

$router->post('/login', function (): void {
    csrf_verify();

    if (!RateLimiter::check('login', 6, 900)) {
        RateLimiter::reject(RateLimiter::retryAfter('login'));
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (strlen($email)>190 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password)>72) {
        flash('error', 'Please enter a valid email and password.');
        view('auth/login', ['bodyClass' => 'is-auth', 'old' => ['email' => $email]]);
        return;
    }

    if (!Auth::attempt($email, $password)) {
        flash('error', 'Those credentials do not match an account. Please try again.');
        view('auth/login', ['bodyClass' => 'is-auth', 'old' => ['email' => $email]]);
        return;
    }

    redirect(safe_next($_POST['next'] ?? null));
});

$router->get('/signup', function (): void {
    if (Auth::check()) {
        redirect(url('dashboard'));
    }
    view('auth/signup', ['bodyClass' => 'is-auth']);
});

$router->post('/signup', function (): void {
    csrf_verify();

    if (!RateLimiter::check('register', 5, 3600)) {
        RateLimiter::reject(RateLimiter::retryAfter('register',3600));
    }
    if (!is_string($_POST['website'] ?? '') || trim($_POST['website'] ?? '') !== '') { // honeypot
        redirect(url('dashboard'));
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');

    $errors = [];
    if (($_POST['terms']??'') !== '1') $errors['terms']='Please read and accept the terms before creating an account.';
    if (mb_strlen($name) < 2 || mb_strlen($name)>100) {
        $errors['name'] = 'Please enter your name.';
    }
    if (strlen($email)>190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (Auth::emailExists($email)) {
        $errors['email'] = 'An account with this email already exists.';
    }
    if (!Auth::validPassword($password)) {
        $errors['password'] = 'Password must be 8–72 bytes (longer Unicode characters use multiple bytes).';
    }
    if ($password !== $confirm) {
        $errors['password_confirm'] = 'Passwords do not match.';
    }

    if ($errors) {
        flash('error', 'Please fix the highlighted fields.');
        view('auth/signup', ['bodyClass' => 'is-auth', 'errors' => $errors, 'old' => $_POST]);
        return;
    }

    try { $user = Auth::register($name, $email, $password); } catch (\PDOException $e) { if (!Database::duplicate($e)) throw $e; flash('error','An account with this email already exists. Please log in.'); redirect(url('login')); }
    flash('success', 'Welcome to ' . config('brand.name') . ', ' . explode(' ', $user['name'])[0] . '!');
    redirect(url('dashboard'));
});

$router->post('/logout', function (): void {
    csrf_verify();
    Auth::logout();
    flash('success', 'You have been logged out.');
    redirect(url('home'));
});

$router->get('/auth/google', function (): void {
    $google = new GoogleOAuth();
    if (!$google->configured()) {
        flash('warning', 'Google sign-in is not configured on this deployment yet.');
        redirect(url('login'));
    }
    redirect($google->authUrl());
});

$router->get('/auth/google/callback', function (): void {
    try {
        $google = new GoogleOAuth();
        if (!$google->configured()) {
            throw new \RuntimeException('Google sign-in is not configured.');
        }
        $code = (string) ($_GET['code'] ?? '');
        $state = (string) ($_GET['state'] ?? '');
        if ($code === '' || $state === '') {
            throw new \RuntimeException('Missing authorization code.');
        }
        $data = $google->handleCallback($code, $state);
        Auth::loginOrCreateFromProvider('google', $data['provider_id'], $data['email'], $data['name']);
        flash('success', 'Welcome to ' . config('brand.name') . '!');
        redirect($data['next']);
    } catch (\Throwable $e) {
        \App\Logger::exception($e,'google_oauth_failure');
        flash('error', 'Google sign-in could not be completed. Please try again.');
        redirect(url('login'));
    }
});

/** Polled by front-end JS to detect expired sessions gracefully. */
$router->get('/auth/session-check', function (): void {
    header('Content-Type: application/json');
    echo json_encode(['authenticated' => Auth::check(), 'login' => url('login')]);
});

/* ---------------------------------------------------------------------
 * Password reset
 * ------------------------------------------------------------------- */
$router->get('/forgot-password', function (): void {
    view('auth/forgot', ['bodyClass' => 'is-auth']);
});

$router->post('/forgot-password', function (): void {
    csrf_verify();
    if (!RateLimiter::check('pwreset', 4, 1800)) {
        RateLimiter::reject(RateLimiter::retryAfter('pwreset',1800));
    }
    $email = Auth::normalizeEmail(trim((string) ($_POST['email'] ?? '')));

    // Always answer identically — do not reveal which emails are registered.
    flash('success', 'If an account exists for that address, a password reset link will be emailed shortly.');

    if (RateLimiter::check('pwreset-account',3,1800,$email)) \App\Services\PasswordReset::request($email);
    redirect(url('forgot_password'));
});

$router->get('/reset-password', function (): void {
    [$token, $email] = [$_GET['token'] ?? '', Auth::normalizeEmail((string) ($_GET['email'] ?? ''))];
    if (!reset_token_valid($token, $email)) {
        flash('error', 'This reset link is invalid or has expired.');
        redirect(url('forgot_password'));
    }
    view('auth/reset', ['bodyClass' => 'is-auth', 'token' => $token, 'email' => $email]);
});

$router->post('/reset-password', function (): void {
    csrf_verify();
    if (!RateLimiter::check('reset-confirm',10,900)) RateLimiter::reject();
    $token = (string) ($_POST['token'] ?? '');
    $email = Auth::normalizeEmail((string) ($_POST['email'] ?? ''));
    if (!reset_token_valid($token, $email)) {
        flash('error', 'This reset link is invalid or has expired.');
        redirect(url('forgot_password'));
    }
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');
    if (!Auth::validPassword($password) || $password !== $confirm) {
        flash('error', 'Enter a new password of 8–72 bytes and confirm it.');
        view('auth/reset', ['bodyClass' => 'is-auth', 'token' => $token, 'email' => $email]);
        return;
    }
    if (!\App\Services\PasswordReset::reset($token,$email,$password)) {
        flash('error','This reset link has already been used or expired.');redirect(url('forgot_password'));
    }
    flash('success', 'Password updated. Please log in with your new password.');
    redirect(url('login'));
});

/* ---------------------------------------------------------------------
 * Protected dashboard (integration point — the existing app mounts here)
 * ------------------------------------------------------------------- */
$router->get('/dashboard', function (): void {
    $user = Auth::requireLogin();
    view('dashboard/index', ['bodyClass' => 'is-app', 'user' => $user, 'subscription' => \App\Services\DashboardService::subscription($user)]);
});

/* ---------------------------------------------------------------------
 * Billing / PayPal
 * ------------------------------------------------------------------- */
$router->get('/billing/paypal/create', function (): void {
    Auth::requireLogin();
    $cycle = ($_GET['cycle'] ?? 'monthly') === 'yearly' ? 'yearly' : 'monthly';
    $paypal=new PayPal();
    if (!$paypal->configured() || !$paypal->planId($cycle)) {
        flash('warning','Managed checkout is not enabled yet. Contact us to discuss the available options.');
        redirect(url('contact',['subject'=>'Managed subscription']));
    }
    $_SESSION['paypal_request_keys'][$cycle] ??= bin2hex(random_bytes(16));
    view('billing/confirm',['bodyClass'=>'is-subpage','cycle'=>$cycle,'requestKey'=>$_SESSION['paypal_request_keys'][$cycle]]);
});
$router->post('/billing/paypal/create', function (): void {
    csrf_verify();$user=Auth::requireLogin();
    if(!RateLimiter::check('checkout',5,900,(string)$user['id']))RateLimiter::reject();
    $cycle=$_POST['cycle']??'';$key=$_POST['request_key']??'';
    if(!in_array($cycle,['monthly','yearly'],true)||!is_string($key)||empty($_SESSION['paypal_request_keys'][$cycle])||!hash_equals($_SESSION['paypal_request_keys'][$cycle],$key)) \App\Request::reject(400,'Reload checkout and try again.');
    try {
        $result=(new PayPal())->createSubscription((int)$user['id'],$cycle,$key);
        redirect($result['approve_url']);
    } catch (\Throwable $e) {
        \App\Logger::exception($e,'paypal_checkout_failure');
        flash('error','Checkout could not be started. Please try again or contact support.');redirect(url('pricing'));
    }
});

/** Server-to-server webhook. CSRF is not applicable; PayPal signatures are verified instead. */
$router->post('/billing/paypal/webhook', function (): void {
    if (!RateLimiter::check('paypal-webhook',120,60)) RateLimiter::reject(60);
    header('Content-Type: application/json');
    $raw = file_get_contents('php://input',false,null,0,262145) ?: '';
    try {
        $paypal = new PayPal();
        $id = $paypal->processWebhook($raw, function_exists('getallheaders') ? getallheaders() : []);
        echo json_encode(['received' => true, 'event_id' => $id]);
    } catch (\Throwable $e) {
        \App\Logger::exception($e,'paypal_webhook_failure');
        http_response_code($e instanceof \InvalidArgumentException || $e instanceof \JsonException ? 400 : 503);
        echo json_encode(['received' => false, 'error' => 'not_processed']);
    }
});

$router->get('/billing/paypal/return', function (): void {
    Auth::requireLogin();
    view('billing/return', ['bodyClass' => 'is-subpage']);
});

$router->get('/billing/paypal/cancel', function (): void {
    flash('warning', 'Subscription checkout was cancelled.');
    redirect(url('pricing'));
});

/* ---------------------------------------------------------------------
 * Legal
 * ------------------------------------------------------------------- */
$publicPages = require LES_BASE_PATH . '/config/public-pages.php';
foreach ($publicPages as $slug => $page) {
    $router->get('/' . $slug, function () use ($page): void {
        view('pages/policy', ['page' => $page, 'bodyClass' => 'is-subpage', 'seo' => [
            'title' => $page['title'] . ' — ' . config('brand.name'), 'description' => $page['summary'],
        ]]);
    });
}
foreach (['about' => ['About us', 'A calmer home for the work behind your social presence. Learn what guides LinkEasy Social.'], 'help' => ['Help & getting started', 'Guidance on hosted plans, API credentials, account connections and support.'], 'legal' => ['Trust & policies', 'Privacy, terms, data deletion, copyright and security information for LinkEasy Social.']] as $slug => [$title, $description]) {
    $router->get('/' . $slug, function () use ($slug, $title, $description, $publicPages): void {
        view('pages/' . $slug, ['pages' => $publicPages, 'bodyClass' => 'is-subpage', 'seo' => [
            'title' => $title . ' — ' . config('brand.name'), 'description' => $description,
        ]]);
    });
}
// Familiar aliases for visitors and platform review teams.
$router->get('/dmca', fn () => redirect(url('copyright')));
$router->get('/privacy-policy', fn () => redirect(url('privacy')));
$router->get('/terms-of-service', fn () => redirect(url('terms')));

/* ---------------------------------------------------------------------
 * SEO
 * ------------------------------------------------------------------- */
$router->get('/sitemap.xml', function (): void {
    header('Content-Type: application/xml; charset=utf-8');
    $paths = array_merge(['/', '/contact', '/about', '/help', '/legal'], array_map(fn ($slug) => '/' . $slug, array_keys(require LES_BASE_PATH . '/config/public-pages.php')));
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($paths as $p) {
        echo '  <url><loc>' . e(absolute_url($p)) . '</loc><changefreq>'
            . ($p === '/' ? 'weekly' : 'monthly') . '</changefreq></url>' . "\n";
    }
    echo '</urlset>';
});

/* ---------------------------------------------------------------------
 * Helpers local to routing
 * ------------------------------------------------------------------- */
function safe_next(mixed $next): string { return \App\Request::next($next); }
function reset_token_valid(string $token,string $email): bool { return \App\Services\PasswordReset::valid($token,$email); }
