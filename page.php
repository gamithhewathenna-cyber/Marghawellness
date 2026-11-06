<?php
declare(strict_types=1);

/**
 * Front controller for every non-home route (/journeys, /retreats …).
 * If /pages/{slug}.php exists it is rendered; otherwise a branded
 * "coming soon" page is shown so all navigation works from day one.
 */
require __DIR__ . '/includes/bootstrap.php';
maintenance_gate();

$slug = strtolower(trim((string) ($_GET['slug'] ?? ''), '/'));

if (!preg_match('/^[a-z0-9-]+$/', $slug) || !isset(PAGES[$slug])) {
    http_response_code(404);
    $page_slug  = '404';
    $page_title = 'Page not found — ' . APP_NAME;
    $heading    = 'This path doesn’t exist';
    $lead       = 'The page you were looking for may have moved. Let’s get you back on the way.';
} else {
    $page_slug  = $slug;
    $page_title = PAGES[$slug][1] . ' — ' . APP_NAME;
    $template   = __DIR__ . '/pages/' . $slug . '.php';
    if (is_file($template)) {
        require $template;   // Future page templates take over here.
        exit;
    }
    $heading = PAGES[$slug][1];
    $lead    = 'This part of the journey is being prepared. In the meantime, our team would love to hear where you are.';
}

$body_class = 'page-inner';
partial('header', compact('page_title', 'body_class'));
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow eyebrow--tan"><?= $page_slug === '404' ? 'Error 404' : 'Coming soon' ?></p>
        <h1><?= e($heading) ?></h1>
        <p class="lead"><?= e($lead) ?></p>
        <a class="btn btn--rust btn--arrow" href="<?= e(url()) ?>">Back to home <?= arrow_icon() ?></a>
    </div>
</section>
<?php partial('footer'); ?>
