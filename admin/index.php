<?php
declare(strict_types=1);

require __DIR__ . '/_inc/bootstrap.php';
$user = require_admin();

/** Count rows, or null if the table doesn't exist yet. */
function count_rows(string $sql): ?int
{
    try {
        return (int) db()->query($sql)->fetchColumn();
    } catch (Throwable) {
        return null;
    }
}

$cards = [
    ['Hero slides',  'hero_slides', count_rows('SELECT COUNT(*) FROM hero_slides WHERE is_active = 1'), 'shown'],
    ['Retreats',     'retreats',    count_rows('SELECT COUNT(*) FROM retreats WHERE is_featured = 1'), 'on homepage'],
    ['Journeys',     'journeys',    count_rows('SELECT COUNT(*) FROM journeys WHERE is_featured = 1'), 'on homepage'],
    ['Posts',        'posts',       count_rows("SELECT COUNT(*) FROM posts WHERE status = 'published'"), 'published'],
    ['Enquiries',    'enquiries',   count_rows("SELECT COUNT(*) FROM enquiries WHERE status = 'new'"), 'new'],
    ['Subscribers',  'subscribers', count_rows("SELECT COUNT(*) FROM subscribers WHERE status = 'subscribed'"), 'subscribed'],
];

$recentEnquiries = [];
$recentSubs      = [];
try {
    $recentEnquiries = db()->query('SELECT id, full_name, email, interest, status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 5')->fetchAll();
    $recentSubs      = db()->query('SELECT id, email, created_at FROM subscribers ORDER BY created_at DESC LIMIT 5')->fetchAll();
} catch (Throwable) {
    // Tables missing — the cards above already show it.
}

$missing = array_filter($cards, fn($c) => $c[2] === null);

admin_header('Dashboard', 'dashboard');
?>
<div class="page-head">
    <div>
        <h1>Welcome, <?= e(explode(' ', $user['name'])[0]) ?></h1>
        <p class="muted">Manage the text, images and listings on the Mārghā Wellness website.</p>
    </div>
</div>

<?php if ($missing): ?>
<div class="notice notice--error">Some content tables are missing (<?= e(implode(', ', array_column($missing, 1))) ?>). Import <code>database/schema.sql</code> in phpMyAdmin.</div>
<?php endif; ?>

<div class="stats">
    <?php foreach ($cards as [$label, $type, $count, $note]): ?>
    <a class="stat" href="<?= e(admin_url('manage.php', ['type' => $type])) ?>">
        <span class="stat__value"><?= $count === null ? '—' : $count ?></span>
        <span class="stat__label"><?= e($label) ?></span>
        <span class="stat__note"><?= e($note) ?></span>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid-2">
    <section class="panel">
        <div class="panel__head"><h2>Latest enquiries</h2><a href="<?= e(admin_url('manage.php', ['type' => 'enquiries'])) ?>">View all</a></div>
        <?php if (!$recentEnquiries): ?>
        <p class="muted">No enquiries yet.</p>
        <?php else: ?>
        <ul class="list">
            <?php foreach ($recentEnquiries as $q): ?>
            <li>
                <a href="<?= e(admin_url('manage.php', ['type' => 'enquiries', 'action' => 'edit', 'id' => $q['id']])) ?>"><strong><?= e($q['full_name']) ?></strong> <span class="muted"><?= e($q['email']) ?></span></a>
                <span class="badge badge--<?= e($q['status']) ?>"><?= e($q['status']) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>Quick links</h2></div>
        <ul class="list list--links">
            <li><a href="<?= e(admin_url('content.php')) ?>">Edit homepage text</a></li>
            <li><a href="<?= e(admin_url('manage.php', ['type' => 'hero_slides'])) ?>">Change the hero banner</a></li>
            <li><a href="<?= e(admin_url('manage.php', ['type' => 'posts', 'action' => 'new'])) ?>">Write a journal post</a></li>
            <li><a href="<?= e(admin_url('manage.php', ['type' => 'journeys', 'action' => 'new'])) ?>">Add a journey</a></li>
            <li><a href="<?= e(admin_url('export.php', ['type' => 'subscribers'])) ?>">Download subscribers (CSV)</a></li>
        </ul>
        <?php if ($recentSubs): ?>
        <h3 class="panel__sub">Newest subscribers</h3>
        <ul class="list">
            <?php foreach ($recentSubs as $s): ?>
            <li><span><?= e($s['email']) ?></span><span class="muted"><?= e(date('j M Y', strtotime($s['created_at']))) ?></span></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>
</div>
<?php admin_footer();
