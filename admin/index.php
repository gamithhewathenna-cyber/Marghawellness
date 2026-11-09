<?php
declare(strict_types=1);

require __DIR__ . '/_inc/bootstrap.php';
$user = require_login();

/** Count rows, or null if the table doesn't exist yet. */
function count_rows(string $sql): ?int
{
    try {
        return (int) db()->query($sql)->fetchColumn();
    } catch (Throwable) {
        return null;
    }
}

/* ---------------- Viewer dashboard (client preview) ---------------- */
// No enquiries, subscribers or edit links — just the website and a content overview.
if (!is_full_admin($user)) {
    $overview = [
        ['Wellness retreats', count_rows('SELECT COUNT(*) FROM retreats WHERE is_featured = 1'), 'on the homepage'],
        ['Journeys',          count_rows('SELECT COUNT(*) FROM journeys WHERE is_featured = 1'), 'on the homepage'],
        ['Journal posts',     count_rows("SELECT COUNT(*) FROM posts WHERE status = 'published'"), 'published'],
    ];
    admin_header('Dashboard', 'dashboard');
    ?>
    <div class="page-head">
        <div>
            <h1>Welcome, <?= e(explode(' ', $user['name'])[0]) ?></h1>
            <p class="muted">You’re signed in with a viewer account for the Mārghā Wellness website.</p>
        </div>
    </div>

    <section class="panel preview-card">
        <div>
            <h2>Preview the website</h2>
            <?php if (maintenance_on()): ?>
            <p class="muted">The website is currently hidden from the public while work is in progress. Because you’re signed in, you can still see the latest version.</p>
            <?php else: ?>
            <p class="muted">See the website exactly as visitors see it.</p>
            <?php endif; ?>
        </div>
        <a class="btn btn--primary btn--large" href="<?= e(url()) ?>" target="_blank" rel="noopener"><?= admin_icon('ext') ?> Open the website</a>
    </section>

    <div class="stats">
        <?php foreach ($overview as [$label, $count, $note]): ?>
        <div class="stat stat--static">
            <span class="stat__value"><?= $count === null ? '—' : $count ?></span>
            <span class="stat__label"><?= e($label) ?></span>
            <span class="stat__note"><?= e($note) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <section class="panel">
        <div class="panel__head"><h2>Your account</h2><a href="<?= e(admin_url('account.php')) ?>">Manage</a></div>
        <p class="muted">Change your password any time from <a href="<?= e(admin_url('account.php')) ?>">My account</a>. You’ll be signed out automatically after 2 hours without activity.</p>
    </section>
    <?php
    admin_footer();
    exit;
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
    <a class="stat" href="<?= e($type === 'hero_slides' ? admin_url('pages.php', ['page' => 'home', 'section' => 'hero']) : admin_url('manage.php', ['type' => $type])) ?>">
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
            <li><a href="<?= e(admin_url('pages.php', ['page' => 'home'])) ?>">Edit the homepage</a></li>
            <li><a href="<?= e(admin_url('pages.php', ['page' => 'home', 'section' => 'hero'])) ?>">Change the hero banner</a></li>
            <li><a href="<?= e(admin_url('pages.php', ['page' => 'site', 'section' => 'footer'])) ?>">Edit the footer</a></li>
            <li><a href="<?= e(admin_url('manage.php', ['type' => 'posts', 'action' => 'new'])) ?>">Write a journal post</a></li>
            <li><a href="<?= e(admin_url('manage.php', ['type' => 'journeys', 'action' => 'new'])) ?>">Add a journey</a></li>
            <li><a href="<?= e(admin_url('export.php', ['type' => 'subscribers'])) ?>">Download subscribers (CSV)</a></li>
            <li><a href="<?= e(admin_url('users.php')) ?>">Give a client viewer access</a></li>
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
