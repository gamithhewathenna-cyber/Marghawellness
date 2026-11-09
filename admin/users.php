<?php
declare(strict_types=1);

/** Users (admins only): add users with an access level, change level, reset passwords, remove. */
require __DIR__ . '/_inc/bootstrap.php';
$me    = require_admin();
$roles = roles_enabled();

$errors = [];
$new    = ['name' => '', 'username' => '', 'email' => '', 'role' => $roles ? 'viewer' : 'admin'];

/** Number of full admins (only counts the role column once it exists). */
function full_admin_count(): int
{
    return roles_enabled()
        ? (int) db()->query("SELECT COUNT(*) FROM admin_users WHERE role = 'admin'")->fetchColumn()
        : admin_count();
}

function find_user(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE id = ?');
    $stmt->execute([$id]);
    $u = $stmt->fetch() ?: null;
    if ($u) {
        $u['role'] = isset(ADMIN_ROLES[$u['role'] ?? '']) ? $u['role'] : 'admin';
    }
    return $u;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $do = (string) ($_POST['do'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($do === 'add') {
        foreach ($new as $k => $_) {
            $new[$k] = trim((string) ($_POST[$k] ?? ''));
        }
        if (!isset(ADMIN_ROLES[$new['role']])) $new['role'] = 'viewer';
        $password = (string) ($_POST['password'] ?? '');
        if ($new['name'] === '' || mb_strlen($new['name']) > 120) $errors[] = 'Enter a name.';
        if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $new['username'])) $errors[] = 'Username must be 3–60 characters: letters, numbers, dot, dash or underscore.';
        if ($new['email'] !== '' && !filter_var($new['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address (or leave it empty).';
        if ($p = password_problem($password, (string) ($_POST['password_confirm'] ?? ''))) $errors[] = $p;
        if (!$roles && $new['role'] === 'viewer') $errors[] = 'Viewer accounts need the database update first (database/roles.sql) — see the note above.';
        if (!$errors) {
            try {
                if ($roles) {
                    db()->prepare('INSERT INTO admin_users (username, name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())')
                        ->execute([$new['username'], $new['name'], $new['email'] ?: null, $new['role'], password_hash($password, PASSWORD_DEFAULT)]);
                } else {
                    db()->prepare('INSERT INTO admin_users (username, name, email, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())')
                        ->execute([$new['username'], $new['name'], $new['email'] ?: null, password_hash($password, PASSWORD_DEFAULT)]);
                }
                flash($new['name'] . ' can now sign in as “' . $new['username'] . '” (' . ucfirst($new['role']) . ').');
                redirect(admin_url('users.php'));
            } catch (PDOException $e) {
                $errors[] = $e->getCode() === '23000' ? 'That username is already taken.' : 'The account could not be created.';
            }
        }
    }

    if ($do === 'role' && $roles) {
        $role = (string) ($_POST['role'] ?? '');
        $user = find_user($id);
        if (!$user || !isset(ADMIN_ROLES[$role])) {
            flash('User not found.', 'error');
        } elseif ($id === (int) $me['id']) {
            flash('You can’t change your own access level.', 'error');
        } elseif ($user['role'] === 'admin' && $role !== 'admin' && full_admin_count() <= 1) {
            flash('There must always be at least one Admin.', 'error');
        } else {
            db()->prepare('UPDATE admin_users SET role = ? WHERE id = ?')->execute([$role, $id]);
            flash($user['name'] . ' is now ' . ($role === 'admin' ? 'an Admin' : 'a Viewer') . '.');
        }
        redirect(admin_url('users.php'));
    }

    if ($do === 'password') {
        $user = find_user($id);
        $p = password_problem((string) ($_POST['password'] ?? ''), (string) ($_POST['password'] ?? ''));
        if (!$user) {
            flash('User not found.', 'error');
        } elseif ($p) {
            flash($p, 'error');
        } else {
            db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash((string) $_POST['password'], PASSWORD_DEFAULT), $id]);
            flash('New password set for ' . $user['name'] . '. Share it with them securely.');
        }
        redirect(admin_url('users.php'));
    }

    if ($do === 'delete') {
        $user = find_user($id);
        if ($id === (int) $me['id']) {
            flash('You can’t remove your own account while signed in.', 'error');
        } elseif ($user && $user['role'] === 'admin' && full_admin_count() <= 1) {
            flash('There must always be at least one Admin.', 'error');
        } elseif ($user) {
            db()->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
            flash($user['name'] . '’s access has been removed.');
        }
        redirect(admin_url('users.php'));
    }
}

$users = db()->query('SELECT * FROM admin_users ORDER BY name')->fetchAll();

admin_header('Users', 'users');
?>
<div class="page-head">
    <div>
        <h1>Users</h1>
        <p class="muted"><strong>Admins</strong> can edit everything. <strong>Viewers</strong> only see the dashboard, their account and the website — ideal for sharing a preview with a client.</p>
    </div>
</div>

<?php if (!$roles): ?>
<div class="notice notice--info">
    <strong>One-time database update needed for Viewer accounts.</strong> In phpMyAdmin, select the database, open the SQL tab and run:<br>
    <code>ALTER TABLE admin_users ADD COLUMN role ENUM('admin','viewer') NOT NULL DEFAULT 'admin' AFTER email;</code><br>
    Until then, every account has full Admin access.
</div>
<?php endif; ?>

<div class="panel panel--flush">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th scope="col">Name</th><th scope="col">Username</th><th scope="col">Access</th><th scope="col">Last sign-in</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u):
            $u['role'] = isset(ADMIN_ROLES[$u['role'] ?? '']) ? $u['role'] : 'admin';
            $isMe = (int) $u['id'] === (int) $me['id']; ?>
            <tr>
                <td data-label="Name"><strong><?= e($u['name']) ?></strong><?= $isMe ? ' <span class="badge">you</span>' : '' ?><?= $u['email'] ? '<br><span class="muted">' . e($u['email']) . '</span>' : '' ?></td>
                <td data-label="Username"><?= e($u['username']) ?></td>
                <td data-label="Access">
                    <?php if ($roles && !$isMe): ?>
                    <form method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="do" value="role">
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <label class="sr-only" for="role_<?= (int) $u['id'] ?>">Access level for <?= e($u['name']) ?></label>
                        <select id="role_<?= (int) $u['id'] ?>" name="role" data-autosubmit>
                            <option value="admin"<?= $u['role'] === 'admin' ? ' selected' : '' ?>>Admin</option>
                            <option value="viewer"<?= $u['role'] === 'viewer' ? ' selected' : '' ?>>Viewer</option>
                        </select>
                        <noscript><button class="btn btn--small" type="submit">Save</button></noscript>
                    </form>
                    <?php else: ?>
                    <span class="badge badge--<?= e($u['role']) ?>"><?= e(ucfirst($u['role'])) ?></span>
                    <?php endif; ?>
                </td>
                <td data-label="Last sign-in"><?= $u['last_login_at'] ? e(date('j M Y, H:i', strtotime($u['last_login_at']))) : '<span class="muted">never</span>' ?></td>
                <td class="actions">
                    <?php if (!$isMe): ?>
                    <details class="reset">
                        <summary class="btn btn--small">Reset password</summary>
                        <form method="post" class="reset__form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="do" value="password">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <input type="text" name="password" minlength="10" placeholder="New password (10+ characters)" autocomplete="off" required>
                            <button class="btn btn--small btn--primary" type="submit">Set</button>
                        </form>
                    </details>
                    <form method="post" data-confirm="Remove <?= e($u['name']) ?>’s access?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="do" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button class="btn btn--small btn--ghost-danger" type="submit">Remove</button>
                    </form>
                    <?php else: ?>
                    <a class="btn btn--small" href="<?= e(admin_url('account.php')) ?>">My account</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<section class="panel">
    <h2>Add a user</h2>
    <?php if ($errors): ?><div class="notice notice--error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
    <form class="form" method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="add">
        <div class="field">
            <label for="n_role">Access level</label>
            <select id="n_role" name="role">
                <?php foreach (ADMIN_ROLES as $key => $label): ?>
                <option value="<?= e($key) ?>"<?= $new['role'] === $key ? ' selected' : '' ?><?= !$roles && $key === 'viewer' ? ' disabled' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="help">Choose <strong>Viewer</strong> for a client who should only see the website and dashboard.</small>
        </div>
        <div class="field"><label for="n_name">Name</label><input id="n_name" name="name" value="<?= e($new['name']) ?>" maxlength="120" required></div>
        <div class="field"><label for="n_user">Username</label><input id="n_user" name="username" value="<?= e($new['username']) ?>" maxlength="60" autocomplete="off" required></div>
        <div class="field"><label for="n_email">Email <span class="muted">(optional)</span></label><input id="n_email" name="email" type="email" value="<?= e($new['email']) ?>" maxlength="190"></div>
        <div class="field"><label for="n_pw1">Password</label><input id="n_pw1" name="password" type="password" autocomplete="new-password" minlength="10" required></div>
        <div class="field"><label for="n_pw2">Confirm password</label><input id="n_pw2" name="password_confirm" type="password" autocomplete="new-password" required></div>
        <div class="form__actions"><button class="btn btn--primary" type="submit">Add user</button></div>
    </form>
</section>
<?php admin_footer();
