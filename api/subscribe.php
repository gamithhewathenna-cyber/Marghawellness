<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

/** Respond as JSON for fetch(), or redirect back for no-JS form posts. */
function respond(bool $ok, string $message, int $status, bool $isAjax): void
{
    if ($isAjax) {
        json_response(['ok' => $ok, 'message' => $message], $status);
    }
    $_SESSION['flash'] = $message;
    header('Location: ' . url() . '#newsletter', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.', 405, $isAjax);
}

if (maintenance_on() && !is_admin_session()) {
    respond(false, 'The website is under maintenance. Please try again soon.', 503, $isAjax);
}

// Honeypot: bots fill the hidden "company" field.
if (!empty($_POST['company'])) {
    respond(true, 'Thank you for subscribing.', 200, $isAjax);
}

if (!csrf_check($_POST['csrf'] ?? null)) {
    respond(false, 'Your session expired. Please refresh the page and try again.', 419, $isAjax);
}

// Simple session rate-limit: 5 attempts per 10 minutes.
$now = time();
$_SESSION['sub_hits'] = array_values(array_filter($_SESSION['sub_hits'] ?? [], fn($t) => $t > $now - 600));
if (count($_SESSION['sub_hits']) >= 5) {
    respond(false, 'Too many attempts. Please try again in a few minutes.', 429, $isAjax);
}
$_SESSION['sub_hits'][] = $now;

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    respond(false, 'Please enter a valid email address.', 422, $isAjax);
}

$pdo = Database::get();
if (!$pdo) {
    error_log('[margha] Subscribe failed — DB unavailable for ' . $email);
    respond(false, 'We couldn’t save your details right now. Please try again shortly.', 503, $isAjax);
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO subscribers (email, source, ip_address, created_at)
         VALUES (:email, :source, :ip, NOW())
         ON DUPLICATE KEY UPDATE updated_at = NOW()'
    );
    $stmt->execute([
        ':email'  => $email,
        ':source' => 'homepage-footer',
        ':ip'     => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
    ]);
} catch (Throwable $e) {
    error_log('[margha] Subscribe insert failed: ' . $e->getMessage());
    respond(false, 'Something went wrong. Please try again.', 500, $isAjax);
}

respond(true, 'Thank you — you’re on the list.', 200, $isAjax);
