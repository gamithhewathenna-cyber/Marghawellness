<?php
declare(strict_types=1);

/**
 * /wellness/ — the Wellness page. Lives in its own folder so the address works
 * without any server rewrite rules. The page itself is pages/wellness.php.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
maintenance_gate();

$page_slug = 'wellness';
require ROOT_PATH . '/pages/wellness.php';
