<?php
declare(strict_types=1);

/**
 * First-run setup: creates the first admin account.
 * Only works while there are no admin accounts — afterwards it redirects to login.
 */
require __DIR__ . '/_inc/bootstrap.php';
require_tables(['admin_users', 'admin_login_attempts', 'settings']);

if (admin_count() > 0) {
    redirect(admin_url('login.php'));
}

$v      = ['name' => '', 'username' => '', 'email' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) {
        $v[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_check($_POST['csrf'] ?? null)) $errors[] = 'Your session expired. Please try again.';
    if ($v['name'] === '' || mb_strlen($v['name']) > 120) $errors[] = 'Enter your name.';
    if (!preg_match('/^[A-Za-z0-9._-]{3,60}$/', $v['username'])) $errors[] = 'Username must be 3–60 characters: letters, numbers, dot, dash or underscore.';
    if ($v['email'] !== '' && !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address (or leave it empty).';
    if ($p = password_problem($password, (string) ($_POST['password_confirm'] ?? ''))) $errors[] = $p;

    if (!$errors) {
        $pdo = db();
        $pdo->beginTransaction();
        // Re-check inside the transaction so two simultaneous submissions can't both succeed.
        if ((int) $pdo->query('SELECT COUNT(*) FROM admin_users FOR UPDATE')->fetchColumn() === 0) {
            $pdo->prepare('INSERT INTO admin_users (username, name, email, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())')
                ->execute([$v['username'], $v['name'], $v['email'] ?: null, password_hash($password, PASSWORD_DEFAULT)]);
        }
        $pdo->commit();
        attempt_login($v['username'], $password);
        flash('Welcome! Your admin account is ready.');
        redirect(admin_url());
    }
}

auth_header('Create admin account');
?>
<h1>Create the admin account</h1>
<p class="muted">No admin account exists yet. The account you create here can sign in and add others. This page stops working once an account exists.</p>
<?php if ($errors): ?>
<div class="notice notice--error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
<?php endif; ?>
<form method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <div class="field"><label for="name">Your name</label><input id="name" name="name" value="<?= e($v['name']) ?>" maxlength="120" required autofocus></div>
    <div class="field"><label for="username">Username</label><input id="username" name="username" value="<?= e($v['username']) ?>" autocomplete="username" maxlength="60" required></div>
    <div class="field"><label for="email">Email <span class="muted">(optional)</span></label><input id="email" name="email" type="email" value="<?= e($v['email']) ?>" maxlength="190"></div>
    <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="10" required><small class="help">At least 10 characters.</small></div>
    <div class="field"><label for="password_confirm">Confirm password</label><input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" required></div>
    <button class="btn btn--primary btn--block" type="submit">Create account</button>
</form>
<?php auth_footer();
