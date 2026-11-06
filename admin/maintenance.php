<?php
declare(strict_types=1);

/**
 * Maintenance mode: switch the public website to a "back soon" page.
 * Signed-in admins keep seeing the real site. ?preview=1 shows the visitor page.
 */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();
require_tables(['settings']);

$fields = settings_schema()['maintenance']['fields'];
$text   = ['maintenance_title' => $fields['maintenance_title'], 'maintenance_message' => $fields['maintenance_message']];

function save_setting(string $key, string $value): void
{
    db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                   ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)')
        ->execute([$key, $value]);
}

// Preview exactly what visitors will see.
if (isset($_GET['preview'])) {
    partial('maintenance');
    exit;
}

$errors = [];
$values = [];
foreach ($text as $key => $_) {
    $values[$key] = setting($key);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $do = (string) ($_POST['do'] ?? '');

    if ($do === 'on' || $do === 'off') {
        save_setting('maintenance_enabled', $do === 'on' ? '1' : '0');
        flash($do === 'on'
            ? 'Maintenance mode is ON. Visitors now see the maintenance page.'
            : 'Maintenance mode is OFF. The website is live again.');
        redirect(admin_url('maintenance.php'));
    }

    if ($do === 'message') {
        foreach ($text as $key => $def) {
            $values[$key] = (string) read_field($key, $def, $errors);
        }
        if (!$errors) {
            foreach ($values as $key => $val) {
                save_setting($key, $val);
            }
            flash('Maintenance message saved.');
            redirect(admin_url('maintenance.php'));
        }
    }
}

$on = setting('maintenance_enabled') === '1';

admin_header('Maintenance mode', 'maintenance');
?>
<div class="page-head">
    <div>
        <h1>Maintenance mode</h1>
        <p class="muted">Temporarily hide the website from visitors while you make changes. You stay signed in here and can still see the real site.</p>
    </div>
</div>

<section class="panel maint maint--<?= $on ? 'on' : 'off' ?>">
    <div class="maint__status">
        <span class="maint__dot" aria-hidden="true"></span>
        <div>
            <p class="maint__label">Website status</p>
            <p class="maint__state"><?= $on ? 'Under maintenance' : 'Live' ?></p>
            <p class="muted maint__note"><?= $on
                ? 'Visitors see the maintenance page below. The newsletter form is paused.'
                : 'Everyone can see the website normally.' ?></p>
        </div>
    </div>
    <form method="post" class="maint__action"
          data-confirm="<?= $on ? 'Turn maintenance mode off and make the website live again?' : 'Turn maintenance mode on? Visitors will see the maintenance page until you turn it off.' ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="<?= $on ? 'off' : 'on' ?>">
        <button class="btn <?= $on ? 'btn--success' : 'btn--primary' ?> btn--large" type="submit">
            <?= $on ? 'Turn off — make website live' : 'Turn on maintenance mode' ?>
        </button>
    </form>
</section>

<?php if ($errors): ?>
<div class="notice notice--error" role="alert">Please fix the highlighted fields.</div>
<?php endif; ?>

<section class="panel">
    <div class="panel__head">
        <h2>Message visitors see</h2>
        <a href="<?= e(admin_url('maintenance.php', ['preview' => 1])) ?>" target="_blank" rel="noopener">Preview page ↗</a>
    </div>
    <form class="form" method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="message">
        <?php foreach ($text as $key => $def): ?>
            <?= render_field($key, $def, $values[$key], $errors[$key] ?? null) ?>
        <?php endforeach; ?>
        <div class="form__actions">
            <button class="btn btn--primary" type="submit">Save message</button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>Good to know</h2>
    <ul class="tips">
        <li>To check what visitors see, use <strong>Preview page</strong> or open the site in a private/incognito window.</li>
        <li>Search engines are told the site is temporarily unavailable, so your Google ranking isn’t affected.</li>
        <li>While it’s on, a reminder bar shows at the bottom of the website and a badge at the top of the admin.</li>
    </ul>
</section>
<?php admin_footer();
