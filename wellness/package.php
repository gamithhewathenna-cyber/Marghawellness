<?php
declare(strict_types=1);

/**
 * /wellness/package.php?p=arogya — one Wellness Package page.
 * Content: Admin › Wellness Packages. Template: pages/wellness-package.php.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
maintenance_gate();

$page_slug = 'wellness';                       // keeps "Wellness" highlighted in the menu
$package   = get_wellness_package((string) ($_GET['p'] ?? ''));

if (!$package) {
    http_response_code(404);
    $page_title = 'Package not found — ' . APP_NAME;
    $body_class = 'page-inner';
    partial('header', compact('page_title', 'body_class'));
    ?>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow eyebrow--tan">Wellness</p>
            <h1>This package isn’t available</h1>
            <p class="lead">It may have been renamed or retired. Explore all our wellness programmes instead.</p>
            <a class="btn btn--rust btn--arrow" href="<?= e(page_url('wellness') . '#wp-packages') ?>">See all programmes <?= arrow_icon() ?></a>
        </div>
    </section>
    <?php
    partial('footer');
    exit;
}

require ROOT_PATH . '/pages/wellness-package.php';
