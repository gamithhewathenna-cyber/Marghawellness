<?php
declare(strict_types=1);

require __DIR__ . '/_inc/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? null)) {
    admin_logout();
    flash('You have been signed out.', 'info');
}
redirect(admin_url('login.php'));
