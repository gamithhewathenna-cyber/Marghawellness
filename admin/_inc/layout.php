<?php
declare(strict_types=1);

defined('ADMIN') || exit;

function admin_icon(string $name): string
{
    $paths = [
        'home'   => '<path d="M3 11 12 4l9 7v9h-6v-6H9v6H3z"/>',
        'text'   => '<path d="M5 6h14M5 12h14M5 18h9"/>',
        'slides' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 16 5-5 4 4 3-3 6 6"/>',
        'leaf'   => '<path d="M5 19C5 10 11 5 20 4c0 9-5 15-14 15zM5 19l7-7"/>',
        'map'    => '<path d="m3 6 6-2 6 2 6-2v14l-6 2-6-2-6 2zM9 4v14M15 6v14"/>',
        'book'   => '<path d="M4 5h6a2 2 0 0 1 2 2v12a2 2 0 0 0-2-2H4zM20 5h-6a2 2 0 0 0-2 2v12a2 2 0 0 1 2-2h6z"/>',
        'mail'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'users'  => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.5-4 3-6 6.5-6s6 2 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18 14c2 .7 3.3 2.7 3.5 6"/>',
        'key'    => '<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9M17 6l3 3M15 8l2 2"/>',
        'out'    => '<path d="M14 4h5v16h-5M10 8l-4 4 4 4M6 12h10"/>',
        'ext'    => '<path d="M14 4h6v6M20 4l-9 9M18 14v6H4V6h6"/>',
    ];
    return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

function admin_head(string $title): void
{
    ?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> — <?= e(APP_NAME) ?> admin</title>
    <link rel="icon" href="<?= e(url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>?v=<?= e((string) @filemtime(ROOT_PATH . '/admin/assets/admin.css')) ?>">
</head>
<?php
}

function admin_flashes(): void
{
    foreach (take_flashes() as $f) {
        echo '<div class="notice notice--' . e($f['type']) . '" role="status">' . e($f['message']) . '</div>';
    }
}

/** Page header + sidebar for signed-in screens. $active = nav key. */
function admin_header(string $title, string $active = ''): void
{
    $user = current_admin();
    admin_head($title);
    $nav = [
        'Overview' => ['dashboard' => ['Dashboard', admin_url(), 'home']],
        'Homepage' => ['content' => ['Page text', admin_url('content.php'), 'text']],
        'Content'  => [],
        'Inbox'    => [],
        'Account'  => ['users' => ['Admin users', admin_url('users.php'), 'key']],
    ];
    foreach (admin_entities() as $key => $ent) {
        $group = ($ent['can_create'] ?? true) ? 'Content' : 'Inbox';
        $nav[$group][$key] = [$ent['label'], admin_url('manage.php', ['type' => $key]), $ent['icon']];
    }
    ?>
<body class="admin">
<a class="skip-link" href="#content">Skip to content</a>
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <a class="sidebar__brand" href="<?= e(admin_url()) ?>">MĀRGHĀ <span>admin</span></a>
        <nav aria-label="Admin">
            <?php foreach ($nav as $group => $items): if (!$items) continue; ?>
            <p class="sidebar__group"><?= e($group) ?></p>
            <ul>
                <?php foreach ($items as $key => [$label, $href, $icon]): ?>
                <li><a href="<?= e($href) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= admin_icon($icon) ?><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php endforeach; ?>
        </nav>
    </aside>
    <div class="main">
        <header class="topbar">
            <button class="topbar__menu" type="button" aria-controls="sidebar" aria-expanded="false" data-menu>Menu</button>
            <a class="topbar__link" href="<?= e(url()) ?>" target="_blank" rel="noopener"><?= admin_icon('ext') ?>View website</a>
            <span class="topbar__user"><?= e($user['name'] ?? '') ?></span>
            <form method="post" action="<?= e(admin_url('logout.php')) ?>">
                <?= csrf_field() ?>
                <button class="topbar__link" type="submit"><?= admin_icon('out') ?>Sign out</button>
            </form>
        </header>
        <main class="content" id="content">
            <?php admin_flashes(); ?>
<?php
}

function admin_footer(): void
{
    ?>
        </main>
    </div>
</div>
<script src="<?= e(url('admin/assets/admin.js')) ?>?v=<?= e((string) @filemtime(ROOT_PATH . '/admin/assets/admin.js')) ?>" defer></script>
</body>
</html>
<?php
}

/** Centered card layout for login / setup / errors. */
function auth_header(string $title): void
{
    admin_head($title);
    ?>
<body class="admin admin--auth">
<main class="auth-card">
    <p class="auth-card__brand">MĀRGHĀ <span>admin</span></p>
    <?php admin_flashes(); ?>
<?php
}

function auth_footer(): void
{
    echo "</main>\n</body>\n</html>\n";
}

function admin_fatal(string $title, string $message): void
{
    http_response_code(500);
    auth_header($title);
    echo '<h1>' . e($title) . '</h1><p class="muted">' . e($message) . '</p>';
    auth_footer();
    exit;
}
