<?php require __DIR__ . '/../partials/head.php'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main id="main">
<?php
$sections = [
    'hero', 'platforms', 'value-strip', 'workspace',
    'accounts', 'creation', 'scheduling', 'publishing',
    'analytics', 'calendar', 'media', 'projects', 'inbox', 'reports', 'ai',
    'how-it-works', 'workflow-loop', 'before-after',
    'use-cases', 'integrations', 'pricing', 'comparison',
    'faq', 'security', 'final-cta',
];
foreach ($sections as $section) {
    require __DIR__ . '/sections/' . $section . '.php';
}
?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
