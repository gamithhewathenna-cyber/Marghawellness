<?php
declare(strict_types=1);

/** Edit the homepage text and site settings defined in includes/settings.php. */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();
require_tables(['settings']);

$requested = (string) ($_POST['group'] ?? $_GET['group'] ?? '');
if ($requested === 'maintenance') {
    redirect(admin_url('maintenance.php'));
}
// Groups with their own admin page are left out of these tabs.
$schema = array_filter(settings_schema(), fn($g) => empty($g['own_page']));
$group  = $requested !== '' ? $requested : array_key_first($schema);
if (!isset($schema[$group])) {
    $group = array_key_first($schema);
}
$fields = $schema[$group]['fields'];
$errors = [];
$values = [];
foreach ($fields as $key => $def) {
    $values[$key] = setting($key);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $reset = (string) ($_POST['reset'] ?? '');

    if ($reset !== '' && isset($fields[$reset])) {
        db()->prepare('DELETE FROM settings WHERE setting_key = ?')->execute([$reset]);
        flash('“' . $fields[$reset]['label'] . '” was reset to the original text.');
        redirect(admin_url('content.php', ['group' => $group]) . '#f_' . $reset);
    }

    $data = [];
    foreach ($fields as $key => $def) {
        $data[$key] = (string) read_field($key, $def, $errors);
    }
    $values = $data;

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                               ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($data as $key => $val) {
            $stmt->execute([$key, $val]);
        }
        flash($schema[$group]['label'] . ' saved. The website now shows the new text.');
        redirect(admin_url('content.php', ['group' => $group]));
    }
}

$saved    = settings_saved(true);
$defaults = settings_defaults();

admin_header('Page text', 'content');
?>
<div class="page-head">
    <div>
        <h1>Page text</h1>
        <p class="muted">Edit the wording and images of each homepage section. Changes appear on the website as soon as you save.</p>
    </div>
</div>

<nav class="tabs" aria-label="Sections">
    <?php foreach ($schema as $key => $g): ?>
    <a href="<?= e(admin_url('content.php', ['group' => $key])) ?>"<?= $key === $group ? ' aria-current="page"' : '' ?>><?= e($g['label']) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($errors): ?>
<div class="notice notice--error" role="alert">Please fix the highlighted fields.</div>
<?php endif; ?>

<form class="form panel" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="group" value="<?= e($group) ?>">
    <?php foreach ($fields as $key => $def): ?>
        <?= render_field($key, $def, $values[$key], $errors[$key] ?? null) ?>
        <?php if (isset($saved[$key]) && $saved[$key] !== $defaults[$key]): ?>
        <button class="link-btn" type="submit" form="resetForm" name="reset" value="<?= e($key) ?>" data-confirm="Reset “<?= e($def['label']) ?>” to the original text?">Reset to original</button>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="form__actions">
        <button class="btn btn--primary" type="submit">Save <?= e($schema[$group]['label']) ?></button>
        <a class="btn btn--ghost" href="<?= e(url()) ?>" target="_blank" rel="noopener">View homepage</a>
    </div>
</form>
<!-- "Reset to original" buttons submit this form (via form="resetForm"), so pressing Enter in a field never triggers a reset. -->
<form id="resetForm" method="post" hidden>
    <?= csrf_field() ?>
    <input type="hidden" name="group" value="<?= e($group) ?>">
</form>
<?php admin_footer();
