<?php
declare(strict_types=1);

/**
 * /journeys/journey.php?p=womens-circle-getaway — one Journey Package page.
 * Content: Admin › Journey Packages. Template: pages/journey-package.php.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
maintenance_gate();

$page_slug = 'journeys';                       // keeps "Journeys" highlighted in the menu
$journey   = get_journey_package((string) ($_GET['p'] ?? ''));

if (!$journey) {
    http_response_code(404);
    $page_title = 'Journey not found — ' . APP_NAME;
    $body_class = 'page-inner';
    partial('header', compact('page_title', 'body_class'));
    ?>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow eyebrow--tan">Journeys</p>
            <h1>This journey isn’t available</h1>
            <p class="lead">It may have been renamed or retired. Explore all our journeys instead.</p>
            <a class="btn btn--rust btn--arrow" href="<?= e(page_url('journeys') . '#journeys-list') ?>">See all journeys <?= arrow_icon() ?></a>
        </div>
    </section>
    <?php
    partial('footer');
    exit;
}

require ROOT_PATH . '/pages/journey-package.php';
