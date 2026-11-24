<?php
declare(strict_types=1);

/**
 * /journeys/ — the Journeys page. Lives in its own folder so the address works
 * without any server rewrite rules. The page itself is pages/journeys.php.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';
maintenance_gate();

$page_slug = 'journeys';
require ROOT_PATH . '/pages/journeys.php';
