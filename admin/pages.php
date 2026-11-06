<?php
declare(strict_types=1);

/**
 * Page editor: Pages › Home › (section). Each section shows its editable
 * text/images and, where the section shows cards, the list of those items.
 * Pages and sections are defined in admin/_inc/pages.php.
 */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();
require_tables(['settings']);

$pages   = admin_pages();
$pageKey = (string) ($_POST['page'] ?? $_GET['page'] ?? 'home');
if (!isset($pages[$pageKey])) {
    $pageKey = array_key_first($pages);
}
$page       = $pages[$pageKey];
$sectionKey = (string) ($_POST['section'] ?? $_GET['section'] ?? array_key_first($page['sections']));
if (!isset($page['sections'][$sectionKey])) {
    $sectionKey = array_key_first($page['sections']);
}
$section = $page['sections'][$sectionKey];
$self    = fn(?string $sec = null): string => admin_url('pages.php', ['page' => $pageKey, 'section' => $sec ?? $sectionKey]);

/* ---------------- Text & image settings ---------------- */
$fields = isset($section['settings']) ? settings_schema()[$section['settings']]['fields'] : [];
$errors = [];
$values = [];
foreach ($fields as $key => $_) {
    $values[$key] = setting($key);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fields) {
    require_post_csrf();
    $reset = (string) ($_POST['reset'] ?? '');

    if ($reset !== '' && isset($fields[$reset])) {
        db()->prepare('DELETE FROM settings WHERE setting_key = ?')->execute([$reset]);
        flash('“' . $fields[$reset]['label'] . '” was reset to the original text.');
        redirect($self() . '#f_' . $reset);
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
        flash($section['label'] . ' saved. The website now shows your changes.');
        redirect($self());
    }
}
$saved    = settings_saved(true);
$defaults = settings_defaults();

/* ---------------- Cards shown in this section ---------------- */
$ent  = isset($section['list']) ? admin_entity($section['list']) : null;
$rows = [];
if ($ent && table_exists($ent['table'])) {
    $rows = db()->query('SELECT * FROM `' . $ent['table'] . '` ORDER BY ' . $ent['order'] . ' LIMIT 60')->fetchAll();
} elseif ($ent) {
    $ent['missing'] = true;
}

$viewUrl = url($page['url']) . (isset($section['anchor']) ? '#' . $section['anchor'] : '');
$keys    = array_keys($page['sections']);
$pos     = array_search($sectionKey, $keys, true);
$prev    = $keys[$pos - 1] ?? null;
$next    = $keys[$pos + 1] ?? null;

admin_header($page['label'] . ' › ' . $section['label'], 'page_' . $pageKey);
?>
<div class="page-head">
    <div>
        <p class="crumbs">Pages ›</p>
        <h1><?= e($page['label']) ?></h1>
        <p class="muted"><?= e($page['intro']) ?></p>
    </div>
    <div class="page-head__actions">
        <a class="btn" href="<?= e(url($page['url'])) ?>" target="_blank" rel="noopener"><?= admin_icon('ext') ?> View page</a>
    </div>
</div>

<div class="page-editor">
    <nav class="section-nav" aria-label="<?= e($page['label']) ?> sections">
        <p class="section-nav__title">Sections <span>top to bottom</span></p>
        <ol>
            <?php foreach ($page['sections'] as $key => $s):
                $parts = array_filter([isset($s['settings']) ? 'Text' : null, isset($s['list']) ? 'Cards' : null]); ?>
            <li>
                <a href="<?= e($self($key)) ?>"<?= $key === $sectionKey ? ' aria-current="page"' : '' ?>>
                    <span class="section-nav__label"><?= e($s['label']) ?></span>
                    <span class="section-nav__meta"><?= e(implode(' · ', $parts)) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="section-body">
        <div class="section-head">
            <div>
                <h2><?= e($section['label']) ?></h2>
                <?php if (!empty($section['intro'])): ?><p class="muted"><?= e($section['intro']) ?></p><?php endif; ?>
            </div>
            <?php if (isset($section['anchor'])): ?>
            <a class="btn btn--small" href="<?= e($viewUrl) ?>" target="_blank" rel="noopener"><?= admin_icon('ext') ?> View on website</a>
            <?php endif; ?>
        </div>

        <?php if ($fields): ?>
            <?php if ($errors): ?><div class="notice notice--error" role="alert">Please fix the highlighted fields.</div><?php endif; ?>
            <form class="form panel" method="post" action="<?= e($self()) ?>" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>
                <h3 class="panel__title">Text &amp; images</h3>
                <?php foreach ($fields as $key => $def): ?>
                    <?= render_field($key, $def, $values[$key], $errors[$key] ?? null) ?>
                    <?php if (isset($saved[$key]) && $saved[$key] !== $defaults[$key]): ?>
                    <button class="link-btn" type="submit" form="resetForm" name="reset" value="<?= e($key) ?>" data-confirm="Reset “<?= e($def['label']) ?>” to the original text?">Reset to original</button>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="form__actions">
                    <button class="btn btn--primary" type="submit">Save <?= e($section['label']) ?></button>
                </div>
            </form>
            <!-- Reset buttons submit this separate form so pressing Enter in a field never resets anything. -->
            <form id="resetForm" method="post" action="<?= e($self()) ?>" hidden><?= csrf_field() ?></form>
        <?php endif; ?>

        <?php if ($ent): ?>
        <section class="panel panel--flush">
            <div class="list-head">
                <div>
                    <h3 class="panel__title"><?= e($ent['label']) ?> in this section</h3>
                    <?php if (!empty($section['list_note'])): ?><p class="muted"><?= e($section['list_note']) ?></p><?php endif; ?>
                </div>
                <div class="page-head__actions">
                    <a class="btn btn--small" href="<?= e(admin_url('manage.php', ['type' => $ent['key']])) ?>">Full list</a>
                    <a class="btn btn--small btn--primary" href="<?= e(admin_url('manage.php', ['type' => $ent['key'], 'action' => 'new', 'return' => $self()])) ?>">+ Add <?= e($ent['singular']) ?></a>
                </div>
            </div>
            <?php if (!empty($ent['missing'])): ?>
                <div class="empty"><p>The <code><?= e($ent['table']) ?></code> table is missing. Import <code>database/schema.sql</code> in phpMyAdmin.</p></div>
            <?php elseif (!$rows): ?>
                <div class="empty"><p>No <?= e(strtolower($ent['label'])) ?> yet.</p></div>
            <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr>
                        <?php foreach ($ent['list'] as $col => $label): ?><th scope="col"><?= e($label) ?></th><?php endforeach; ?>
                        <th scope="col"><span class="sr-only">Actions</span></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r):
                        $edit = admin_url('manage.php', ['type' => $ent['key'], 'action' => 'edit', 'id' => $r['id'], 'return' => $self()]); ?>
                        <tr>
                            <?php foreach ($ent['list'] as $col => $label): ?>
                            <td data-label="<?= e($label) ?>"><?= $col === $ent['title']
                                ? '<a class="row-link" href="' . e($edit) . '">' . admin_cell($col, $r[$col] ?? null, $ent) . '</a>'
                                : admin_cell($col, $r[$col] ?? null, $ent) ?></td>
                            <?php endforeach; ?>
                            <td class="actions"><a class="btn btn--small" href="<?= e($edit) ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <nav class="section-pager" aria-label="Previous and next section">
            <?php if ($prev): ?><a class="btn" href="<?= e($self($prev)) ?>">← <?= e($page['sections'][$prev]['label']) ?></a><?php else: ?><span></span><?php endif; ?>
            <?php if ($next): ?><a class="btn" href="<?= e($self($next)) ?>"><?= e($page['sections'][$next]['label']) ?> →</a><?php endif; ?>
        </nav>
    </div>
</div>
<?php admin_footer();
