<?php
declare(strict_types=1);

/**
 * Generic list / create / edit / delete screen for every content type
 * defined in admin/_inc/entities.php.  ?type=retreats&action=edit&id=3
 */
require __DIR__ . '/_inc/bootstrap.php';
require_admin();

$ent = admin_entity((string) ($_POST['type'] ?? $_GET['type'] ?? ''));
if (!$ent) {
    flash('That section doesn’t exist.', 'error');
    redirect(admin_url());
}
require_tables([$ent['table']]);

$table     = '`' . $ent['table'] . '`';
$canCreate = $ent['can_create'] ?? true;
$action    = (string) ($_POST['action'] ?? $_GET['action'] ?? 'list');
$id        = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
$singular  = $ent['singular'];
$self      = fn(array $q = []): string => admin_url('manage.php', ['type' => $ent['key']] + $q);

// Where to go after saving/deleting: back to the page section it was opened from (e.g. Home › Hero banner).
$return  = safe_return((string) ($_POST['return'] ?? $_GET['return'] ?? ''));
$back    = $return ?: $self();
$owner   = admin_page_for('list', $ent['key']);
$navKey  = !empty($ent['hidden']) && $owner ? 'page_' . $owner['page'] : $ent['key'];
$crumbs  = $return && $owner
    ? '<a href="' . e($return) . '">' . e($owner['label']) . '</a> ›'
    : '<a href="' . e($self()) . '">' . e($ent['label']) . '</a> ›';

function find_row(string $table, int $id): ?array
{
    $stmt = db()->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

/* ---------------- Delete ---------------- */
if ($action === 'delete') {
    require_post_csrf();
    db()->prepare("DELETE FROM $table WHERE id = ?")->execute([$id]);
    flash(ucfirst($singular) . ' deleted.');
    redirect($back);
}

/* ---------------- Save ---------------- */
$errors = [];
$values = null;

if ($action === 'save') {
    require_post_csrf();
    $row = $id ? find_row($table, $id) : null;
    if (($id && !$row) || (!$id && !$canCreate)) {
        flash(ucfirst($singular) . ' not found.', 'error');
        redirect($self());
    }

    $data = [];
    foreach ($ent['fields'] as $name => $def) {
        if ($def['type'] !== 'readonly') {
            $data[$name] = read_field($name, $def, $errors);
        }
    }
    // Empty URL names are generated from the title.
    foreach ($ent['fields'] as $name => $def) {
        if ($def['type'] === 'slug' && ($data[$name] ?? '') === '' && !isset($errors[$name])) {
            $data[$name] = slugify((string) ($data[$def['from']] ?? ''));
        }
    }
    if ($ent['key'] === 'posts' && $data['status'] === 'published' && empty($data['published_at'])) {
        $data['published_at'] = date('Y-m-d H:i:s');
    }
    $values = array_merge($row ?? [], $data);

    if (!$errors) {
        try {
            $params = [];
            foreach ($data as $col => $val) {
                $params[':' . $col] = $val;
            }
            if ($id) {
                $set = implode(', ', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
                db()->prepare("UPDATE $table SET $set WHERE id = :__id")->execute($params + [':__id' => $id]);
            } else {
                $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
                $vals = implode(', ', array_keys($params));
                db()->prepare("INSERT INTO $table ($cols) VALUES ($vals)")->execute($params);
                $id = (int) db()->lastInsertId();
            }
            flash(ucfirst($singular) . ' saved.');
            redirect(isset($_POST['stay']) ? $self(['action' => 'edit', 'id' => $id] + ($return ? ['return' => $return] : [])) : $back);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000' && isset($ent['fields']['slug'])) {
                $errors['slug'] = 'This URL name is already used by another ' . $singular . '. Choose a different one.';
            } else {
                error_log('[margha-admin] save failed: ' . $e->getMessage());
                $errors['_'] = 'The ' . $singular . ' could not be saved because of a database error. Check that database/schema.sql has been imported.';
            }
        }
    }
    $action = 'edit';
}

/* ---------------- Edit / new form ---------------- */
if ($action === 'edit' || $action === 'new') {
    if ($values === null) {
        if ($action === 'edit') {
            $values = find_row($table, $id);
            if (!$values) {
                flash(ucfirst($singular) . ' not found.', 'error');
                redirect($self());
            }
        } else {
            if (!$canCreate) redirect($self());
            $values = [];
            foreach ($ent['fields'] as $name => $def) {
                $values[$name] = $def['default'] ?? ($def['type'] === 'select' ? array_key_first($def['options']) : '');
            }
        }
    }
    $isNew = !$id;
    $heading = $isNew ? 'New ' . $singular : 'Edit ' . $singular;
    admin_header($heading, $navKey);
    ?>
    <div class="page-head">
        <div>
            <p class="crumbs"><?= $crumbs ?></p>
            <h1><?= e($isNew ? $heading : (string) ($values[$ent['title']] ?? $heading)) ?></h1>
        </div>
        <?php if ($ent['key'] === 'enquiries' && !empty($values['email'])): ?>
        <a class="btn" href="mailto:<?= e($values['email']) ?>?subject=<?= rawurlencode('Your Mārghā Wellness enquiry') ?>">Reply by email</a>
        <?php endif; ?>
    </div>

    <?php if ($errors): ?>
    <div class="notice notice--error" role="alert">
        <?= isset($errors['_']) ? e($errors['_']) : 'Please fix the highlighted fields.' ?>
    </div>
    <?php endif; ?>

    <form class="form panel" method="post" action="<?= e(admin_url('manage.php')) ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="<?= e($ent['key']) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <input type="hidden" name="return" value="<?= e($return) ?>">
        <?php foreach ($ent['fields'] as $name => $def): ?>
            <?= render_field($name, $def, $values[$name] ?? null, $errors[$name] ?? null) ?>
        <?php endforeach; ?>
        <div class="form__actions">
            <button class="btn btn--primary" type="submit">Save</button>
            <button class="btn" type="submit" name="stay" value="1">Save and keep editing</button>
            <a class="btn btn--ghost" href="<?= e($back) ?>">Cancel</a>
        </div>
    </form>

    <?php if (!$isNew): ?>
    <form class="danger-zone" method="post" action="<?= e(admin_url('manage.php')) ?>" data-confirm="Delete this <?= e($singular) ?>? This can’t be undone.">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="<?= e($ent['key']) ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <input type="hidden" name="return" value="<?= e($return) ?>">
        <button class="btn btn--danger" type="submit">Delete <?= e($singular) ?></button>
    </form>
    <?php endif;
    admin_footer();
    exit;
}

/* ---------------- List ---------------- */
$perPage = 50;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$q       = trim((string) ($_GET['q'] ?? ''));
$where   = '';
$params  = [];
if ($q !== '' && !empty($ent['search'])) {
    $like  = '%' . addcslashes($q, '%_\\') . '%';
    $parts = [];
    foreach ($ent['search'] as $i => $col) {
        $parts[] = "`$col` LIKE :q$i";
        $params[":q$i"] = $like;
    }
    $where = 'WHERE ' . implode(' OR ', $parts);
}
$countStmt = db()->prepare("SELECT COUNT(*) FROM $table $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);

$stmt = db()->prepare("SELECT * FROM $table $where ORDER BY {$ent['order']} LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$rows = $stmt->fetchAll();

admin_header($ent['label'], $navKey);
?>
<div class="page-head">
    <div>
        <h1><?= e($ent['label']) ?></h1>
        <?php if (!empty($ent['intro'])): ?><p class="muted"><?= e($ent['intro']) ?></p><?php endif; ?>
    </div>
    <div class="page-head__actions">
        <?php if (!empty($ent['export'])): ?>
        <a class="btn" href="<?= e(admin_url('export.php', ['type' => $ent['key']])) ?>">Download CSV</a>
        <?php endif; ?>
        <?php if ($canCreate): ?>
        <a class="btn btn--primary" href="<?= e($self(['action' => 'new'])) ?>">+ Add <?= e($singular) ?></a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($ent['search'])): ?>
<form class="search" method="get" action="<?= e(admin_url('manage.php')) ?>" role="search">
    <input type="hidden" name="type" value="<?= e($ent['key']) ?>">
    <label class="sr-only" for="q">Search</label>
    <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Search <?= e(strtolower($ent['label'])) ?>…">
    <button class="btn" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn btn--ghost" href="<?= e($self()) ?>">Clear</a><?php endif; ?>
</form>
<?php endif; ?>

<div class="panel panel--flush">
<?php if (!$rows): ?>
    <div class="empty">
        <p><?= $q !== '' ? 'Nothing matches “' . e($q) . '”.' : 'Nothing here yet.' ?></p>
        <?php if ($canCreate && $q === ''): ?><a class="btn btn--primary" href="<?= e($self(['action' => 'new'])) ?>">+ Add the first <?= e($singular) ?></a><?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <?php foreach ($ent['list'] as $col => $label): ?><th scope="col"><?= e($label) ?></th><?php endforeach; ?>
                <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): $edit = $self(['action' => 'edit', 'id' => $r['id']]); ?>
            <tr>
                <?php foreach (array_keys($ent['list']) as $i => $col): ?>
                <td data-label="<?= e($ent['list'][$col]) ?>"><?= $col === $ent['title'] ? '<a class="row-link" href="' . e($edit) . '">' . admin_cell($col, $r[$col] ?? null, $ent) . '</a>' : admin_cell($col, $r[$col] ?? null, $ent) ?></td>
                <?php endforeach; ?>
                <td class="actions">
                    <a class="btn btn--small" href="<?= e($edit) ?>"><?= $canCreate ? 'Edit' : 'Open' ?></a>
                    <form method="post" action="<?= e(admin_url('manage.php')) ?>" data-confirm="Delete this <?= e($singular) ?>? This can’t be undone.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="<?= e($ent['key']) ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <button class="btn btn--small btn--ghost-danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
</div>

<?php if ($pages > 1): ?>
<nav class="pager" aria-label="Pages">
    <?php if ($page > 1): ?><a class="btn btn--small" href="<?= e($self(['q' => $q, 'page' => $page - 1])) ?>">← Previous</a><?php endif; ?>
    <span class="muted">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
    <?php if ($page < $pages): ?><a class="btn btn--small" href="<?= e($self(['q' => $q, 'page' => $page + 1])) ?>">Next →</a><?php endif; ?>
</nav>
<?php endif;
admin_footer();
