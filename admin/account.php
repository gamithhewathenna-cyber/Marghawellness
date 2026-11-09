<?php
declare(strict_types=1);

/** My account — every signed-in user (admin or viewer): see details, change password. */
require __DIR__ . '/_inc/bootstrap.php';
$me = require_login();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $stmt = db()->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
    $stmt->execute([$me['id']]);
    if (!password_verify((string) ($_POST['current'] ?? ''), (string) $stmt->fetchColumn())) {
        $error = 'Your current password is not correct.';
    } elseif ($p = password_problem((string) ($_POST['password'] ?? ''), (string) ($_POST['password_confirm'] ?? ''))) {
        $error = $p;
    } else {
        db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash((string) $_POST['password'], PASSWORD_DEFAULT), $me['id']]);
        session_regenerate_id(true);
        flash('Your password has been changed.');
        redirect(admin_url('account.php'));
    }
}

admin_header('My account', 'account');
?>
<div class="page-head">
    <div>
        <h1>My account</h1>
        <p class="muted">Your sign-in details.</p>
    </div>
</div>

<div class="grid-2">
    <section class="panel">
        <h2>Details</h2>
        <ul class="list">
            <li><span class="muted">Name</span><strong><?= e($me['name']) ?></strong></li>
            <li><span class="muted">Username</span><span><?= e($me['username']) ?></span></li>
            <li><span class="muted">Email</span><span><?= $me['email'] ? e($me['email']) : '<span class="muted">—</span>' ?></span></li>
            <li><span class="muted">Access level</span><span class="badge badge--<?= e($me['role']) ?>"><?= e(ucfirst($me['role'])) ?></span></li>
            <li><span class="muted">Last sign-in</span><span><?= $me['last_login_at'] ? e(date('j M Y, H:i', strtotime($me['last_login_at']))) : '—' ?></span></li>
        </ul>
        <?php if (!is_full_admin($me)): ?>
        <p class="muted panel__sub">Your account can view the dashboard and the website (including while it’s in maintenance mode). To change names or details, contact the site administrator.</p>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Change password</h2>
        <?php if ($error): ?><div class="notice notice--error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form class="form" method="post" novalidate>
            <?= csrf_field() ?>
            <input type="text" name="username" value="<?= e($me['username']) ?>" autocomplete="username" hidden>
            <div class="field"><label for="current">Current password</label><input id="current" name="current" type="password" autocomplete="current-password" required></div>
            <div class="field"><label for="pw1">New password</label><input id="pw1" name="password" type="password" autocomplete="new-password" minlength="10" required><small class="help">At least 10 characters.</small></div>
            <div class="field"><label for="pw2">Confirm new password</label><input id="pw2" name="password_confirm" type="password" autocomplete="new-password" required></div>
            <div class="form__actions"><button class="btn btn--primary" type="submit">Change password</button></div>
        </form>
    </section>
</div>
<?php admin_footer();
