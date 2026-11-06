<?php
declare(strict_types=1);

/** Admin accounts: change your password, add or remove other admins. */
require __DIR__ . '/_inc/bootstrap.php';
$me = require_admin();

$errors = [];
$new    = ['name' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $do = (string) ($_POST['do'] ?? '');

    if ($do === 'password') {
        $stmt = db()->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$me['id']]);
        if (!password_verify((string) ($_POST['current'] ?? ''), (string) $stmt->fetchColumn())) {
            $errors['password'] = 'Your current password is not correct.';
        } elseif ($p = password_problem((string) ($_POST['password'] ?? ''), (string) ($_POST['password_confirm'] ?? ''))) {
            $errors['password'] = $p;
        } else {
            db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash((string) $_POST['password'], PASSWORD_DEFAULT), $me['id']]);
            session_regenerate_id(true);
            flash('Your password has been changed.');
            redirect(admin_url('users.php'));
        }
    }

    if ($do === 'add') {
        foreach ($new as $k => $_) {
            $new[$k] = trim((string) ($_POST[$k] ?? ''));
        }
        $password = (string) ($_POST['password'] ?? '');
        if ($new['name'] === '' || mb_strlen($new['name']) > 120) $errors['add'][] = 'Enter a name.';
        if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $new['username'])) $errors['add'][] = 'Username must be 3–60 characters: letters, numbers, dot, dash or underscore.';
        if ($new['email'] !== '' && !filter_var($new['email'], FILTER_VALIDATE_EMAIL)) $errors['add'][] = 'Enter a valid email address (or leave it empty).';
        if ($p = password_problem($password, (string) ($_POST['password_confirm'] ?? ''))) $errors['add'][] = $p;
        if (empty($errors['add'])) {
            try {
                db()->prepare('INSERT INTO admin_users (username, name, email, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())')
                    ->execute([$new['username'], $new['name'], $new['email'] ?: null, password_hash($password, PASSWORD_DEFAULT)]);
                flash($new['name'] . ' can now sign in as “' . $new['username'] . '”.');
                redirect(admin_url('users.php'));
            } catch (PDOException $e) {
                $errors['add'][] = $e->getCode() === '23000' ? 'That username is already taken.' : 'The account could not be created.';
            }
        }
    }

    if ($do === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) $me['id']) {
            flash('You can’t delete your own account while signed in.', 'error');
        } else {
            db()->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
            flash('Admin account removed.');
        }
        redirect(admin_url('users.php'));
    }
}

$users = db()->query('SELECT id, username, name, email, last_login_at, created_at FROM admin_users ORDER BY name')->fetchAll();

admin_header('Admin users', 'users');
?>
<div class="page-head">
    <div>
        <h1>Admin users</h1>
        <p class="muted">Everyone listed here can sign in and edit the website.</p>
    </div>
</div>

<div class="panel panel--flush">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th scope="col">Name</th><th scope="col">Username</th><th scope="col">Email</th><th scope="col">Last sign-in</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td data-label="Name"><strong><?= e($u['name']) ?></strong><?= (int) $u['id'] === (int) $me['id'] ? ' <span class="badge">you</span>' : '' ?></td>
                <td data-label="Username"><?= e($u['username']) ?></td>
                <td data-label="Email"><?= $u['email'] ? e($u['email']) : '<span class="muted">—</span>' ?></td>
                <td data-label="Last sign-in"><?= $u['last_login_at'] ? e(date('j M Y, H:i', strtotime($u['last_login_at']))) : '<span class="muted">never</span>' ?></td>
                <td class="actions">
                    <?php if ((int) $u['id'] !== (int) $me['id']): ?>
                    <form method="post" data-confirm="Remove <?= e($u['name']) ?>’s admin access?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="do" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button class="btn btn--small btn--ghost-danger" type="submit">Remove</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<div class="grid-2">
    <section class="panel">
        <h2>Change your password</h2>
        <?php if (isset($errors['password'])): ?><div class="notice notice--error" role="alert"><?= e($errors['password']) ?></div><?php endif; ?>
        <form class="form" method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="do" value="password">
            <input type="text" name="username" value="<?= e($me['username']) ?>" autocomplete="username" hidden>
            <div class="field"><label for="current">Current password</label><input id="current" name="current" type="password" autocomplete="current-password" required></div>
            <div class="field"><label for="pw1">New password</label><input id="pw1" name="password" type="password" autocomplete="new-password" minlength="10" required><small class="help">At least 10 characters.</small></div>
            <div class="field"><label for="pw2">Confirm new password</label><input id="pw2" name="password_confirm" type="password" autocomplete="new-password" required></div>
            <div class="form__actions"><button class="btn btn--primary" type="submit">Change password</button></div>
        </form>
    </section>

    <section class="panel">
        <h2>Add an admin</h2>
        <?php if (!empty($errors['add'])): ?><div class="notice notice--error" role="alert"><?= implode('<br>', array_map('e', $errors['add'])) ?></div><?php endif; ?>
        <form class="form" method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="do" value="add">
            <div class="field"><label for="n_name">Name</label><input id="n_name" name="name" value="<?= e($new['name']) ?>" maxlength="120" required></div>
            <div class="field"><label for="n_user">Username</label><input id="n_user" name="username" value="<?= e($new['username']) ?>" maxlength="60" autocomplete="off" required></div>
            <div class="field"><label for="n_email">Email <span class="muted">(optional)</span></label><input id="n_email" name="email" type="email" value="<?= e($new['email']) ?>" maxlength="190"></div>
            <div class="field"><label for="n_pw1">Password</label><input id="n_pw1" name="password" type="password" autocomplete="new-password" minlength="10" required></div>
            <div class="field"><label for="n_pw2">Confirm password</label><input id="n_pw2" name="password_confirm" type="password" autocomplete="new-password" required></div>
            <div class="form__actions"><button class="btn btn--primary" type="submit">Add admin</button></div>
        </form>
    </section>
</div>
<?php admin_footer();
