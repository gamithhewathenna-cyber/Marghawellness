<?php
declare(strict_types=1);

require __DIR__ . '/_inc/bootstrap.php';
require_tables(['admin_users', 'admin_login_attempts']);

if (admin_count() === 0) {
    redirect(admin_url('setup.php'));
}
if (current_admin()) {
    redirect(admin_url());
}

$username = '';
$error    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_check($_POST['csrf'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif (login_blocked()) {
        $error = 'Too many failed attempts. Please wait 15 minutes and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Enter your username and password.';
    } elseif (!attempt_login($username, $password)) {
        $error = 'That username and password don’t match.';
    } else {
        $return = (string) ($_SESSION['admin_return'] ?? '');
        unset($_SESSION['admin_return']);
        // Only return to paths inside /admin on this site.
        $prefix = BASE_URL . '/admin/';
        redirect(str_starts_with($return, $prefix) && !str_contains($return, '//') ? $return : admin_url());
    }
}

auth_header('Sign in');
?>
<h1>Sign in</h1>
<?php if ($error): ?><div class="notice notice--error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="form" novalidate>
    <?= csrf_field() ?>
    <div class="field">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" value="<?= e($username) ?>" required autofocus>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>
    <button class="btn btn--primary btn--block" type="submit">Sign in</button>
</form>
<p class="auth-card__foot"><a href="<?= e(url()) ?>">← Back to website</a></p>
<?php auth_footer();
