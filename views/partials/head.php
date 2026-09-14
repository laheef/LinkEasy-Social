<?php
/** @var array $seo */
$brand = config('brand');
$title = $seo['title'] ?? $brand['name'];
$description = $seo['description'] ?? $brand['description'];
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical = $brand['app_url'] . $path;
$ogImage = $brand['app_url'] . $brand['og_image'];
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta name="theme-color" content="#0e0e11">

    <link rel="icon" href="<?= e(asset('assets/img/favicon.ico')) ?>" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= e(asset('assets/img/favicon-32.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset('assets/img/favicon-180.png')) ?>">
    <link rel="manifest" href="<?= e(url('home')) ?>site.webmanifest">

    <link rel="preload" href="<?= e(asset('assets/fonts/inter-latin.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($brand['name']) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($title) ?>">
    <meta name="twitter:description" content="<?= e($description) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <style>
    /* Self-hosted variable Inter — unicode-range keeps latin-ext lazy */
    @font-face{font-family:'Inter';font-style:normal;font-weight:100 900;font-display:swap;
        src:url('<?= e(asset('assets/fonts/inter-latin-ext.woff2')) ?>') format('woff2');
        unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;}
    @font-face{font-family:'Inter';font-style:normal;font-weight:100 900;font-display:swap;
        src:url('<?= e(asset('assets/fonts/inter-latin.woff2')) ?>') format('woff2');
        unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}
    html{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;}
    </style>
    <link rel="preload" href="<?= e(asset('assets/css/landing.min.css')) ?>" as="style">
    <link rel="stylesheet" href="<?= e(asset('assets/css/landing.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/platforms.min.css')) ?>">
    <script nonce="<?= e(\App\Security::nonce()) ?>">document.documentElement.classList.add('js');</script>
</head>
<body class="<?= e($bodyClass) ?>">
<?php require __DIR__ . '/icons.php'; ?>
<?php require __DIR__ . '/icons-platforms.php'; ?>
<?php if ($path === '/'): ?>
<script nonce="<?= e(\App\Security::nonce()) ?>" type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            'name' => $brand['name'],
            'url' => $brand['app_url'],
            'logo' => $brand['app_url'] . $brand['logo'],
            'sameAs' => array_values(array_filter($brand['social'])),
        ],
        [
            '@type' => 'WebSite',
            'name' => $brand['name'],
            'url' => $brand['app_url'],
        ],
        [
            '@type' => 'SoftwareApplication',
            'name' => $brand['name'],
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => $brand['description'],
            'url' => $brand['app_url'],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ], config('faqs')),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
</script>
<?php endif; ?>
