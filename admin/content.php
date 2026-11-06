<?php
declare(strict_types=1);

/**
 * Old "Page text" address — page text is now edited per page in pages.php.
 * Kept so existing bookmarks and links still land in the right place.
 */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();

$group = (string) ($_GET['group'] ?? '');
if ($group === 'maintenance') {
    redirect(admin_url('maintenance.php'));
}
$legacy = ['seo' => 'seo_home'];
$target = admin_page_for('settings', $legacy[$group] ?? $group);
redirect($target
    ? admin_url('pages.php', ['page' => $target['page'], 'section' => $target['section']])
    : admin_url('pages.php', ['page' => 'home']));
