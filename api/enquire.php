<?php
declare(strict_types=1);

/**
 * Enquiry form endpoint ("Begin your journey" — partials/enquire.php).
 * Saves to the `enquiries` table (Admin › Inbox › Enquiries) and emails the contact address.
 * Replies with JSON for the pop-up, or redirects back to the enquire page without JavaScript.
 */
require dirname(__DIR__) . '/includes/bootstrap.php';

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

/** Respond as JSON (pop-up) or redirect to the enquire page (no JavaScript). */
function enquire_respond(bool $ok, string $message, int $status, bool $isAjax, array $errors = []): void
{
    if ($isAjax) {
        json_response(['ok' => $ok, 'message' => $message, 'errors' => $errors], $status);
    }
    $_SESSION['enquire_notice'] = ['ok' => $ok, 'message' => $message];
    if (!$ok) {
        $_SESSION['enquire_old'] = array_intersect_key($_POST, array_flip(['full_name', 'email', 'country', 'city', 'journey_stage', 'looking_for', 'travel_month', 'dates_flexible']));
    }
    header('Location: ' . url('page.php?slug=enquire'), true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enquire_respond(false, 'Method not allowed.', 405, $isAjax);
}
if (maintenance_on() && !is_admin_session()) {
    enquire_respond(false, 'The website is under maintenance. Please try again soon.', 503, $isAjax);
}
// Honeypot: bots fill the hidden "company" field — pretend it worked.
if (!empty($_POST['company'])) {
    enquire_respond(true, 'Thank you.', 200, $isAjax);
}
if (!csrf_check($_POST['csrf'] ?? null)) {
    enquire_respond(false, 'Your session expired. Please refresh the page and try again.', 419, $isAjax);
}

// Rate limit: 5 enquiries per 10 minutes per visitor session.
$now = time();
$_SESSION['enq_hits'] = array_values(array_filter($_SESSION['enq_hits'] ?? [], fn($t) => $t > $now - 600));
if (count($_SESSION['enq_hits']) >= 5) {
    enquire_respond(false, 'Too many attempts. Please try again in a few minutes.', 429, $isAjax);
}

/* ---------- Read + validate ---------- */
// Collapse whitespace and trim. Invalid UTF-8 (only sent by bots) becomes an empty value instead of an error.
$clean = fn(string $k, int $max): string => mb_substr(trim((string) preg_replace('/\s+/u', ' ', is_string($_POST[$k] ?? null) ? $_POST[$k] : '')), 0, $max);
$data = [
    'full_name'      => $clean('full_name', 120),
    'email'          => strtolower($clean('email', 190)),
    'country'        => $clean('country', 80),
    'city'           => $clean('city', 80),
    'journey_stage'  => $clean('journey_stage', 200),
    'interest'       => $clean('looking_for', 120),     // "What are you looking for?"
    'travel_month'   => $clean('travel_month', 40),
    'dates_flexible' => empty($_POST['dates_flexible']) ? 0 : 1,
];

$errors = [];
if ($data['full_name'] === '') $errors['full_name'] = 'Please tell us your name.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
// Choices must be ones offered on the form (they're edited in the admin).
if ($data['journey_stage'] !== '' && !in_array($data['journey_stage'], setting_lines('eq_stages'), true)) $data['journey_stage'] = '';
if ($data['interest'] !== '' && !in_array($data['interest'], setting_lines('eq_looking'), true)) $data['interest'] = '';
if ($data['travel_month'] !== '' && !in_array($data['travel_month'], enquiry_months(), true)) $data['travel_month'] = '';

if ($errors) {
    enquire_respond(false, reset($errors), 422, $isAjax, $errors);
}
$_SESSION['enq_hits'][] = $now;

/* ---------- Save ---------- */
$pdo = Database::get();
if (!$pdo) {
    error_log('[margha] Enquiry not saved — DB unavailable: ' . $data['email']);
    enquire_respond(false, 'We couldn’t send your enquiry right now. Please try again shortly, or email us at ' . setting('contact_email') . '.', 503, $isAjax);
}

try {
    // Columns added by the database update (city, journey_stage, dates_flexible) are used when present;
    // otherwise those answers are kept in the message so nothing is lost.
    $have  = array_column($pdo->query('SHOW COLUMNS FROM enquiries')->fetchAll(), 'Field');
    $row   = [];
    $extra = [];
    foreach ($data as $col => $val) {
        if (in_array($col, $have, true)) {
            $row[$col] = $val === '' ? null : $val;
        } elseif ($val !== '' && $val !== 0) {
            $extra[] = ['city' => 'City', 'journey_stage' => 'Wellness journey', 'dates_flexible' => 'Dates flexible'][$col] . ': ' . ($col === 'dates_flexible' ? 'Yes' : $val);
        }
    }
    if ($extra) {
        $row['message'] = implode("\n", $extra);
    }
    $cols = array_keys($row);
    $pdo->prepare('INSERT INTO enquiries (`' . implode('`, `', $cols) . '`, created_at) VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ', NOW())')
        ->execute(array_values($row));
} catch (Throwable $e) {
    error_log('[margha] Enquiry insert failed: ' . $e->getMessage());
    enquire_respond(false, 'Something went wrong. Please try again, or email us at ' . setting('contact_email') . '.', 500, $isAjax);
}

/* ---------- Email notification (best effort — the enquiry is already saved) ---------- */
$to = setting('contact_email');
if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $host  = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $host  = preg_replace('/^www\./i', '', $host) ?: 'localhost';
    $name  = str_replace(["\r", "\n", '"'], '', $data['full_name']);
    $lines = [
        'New enquiry from the website',
        '',
        'Name:             ' . $data['full_name'],
        'Email:            ' . $data['email'],
        'Country / city:   ' . trim($data['country'] . ', ' . $data['city'], ', '),
        'Wellness journey: ' . $data['journey_stage'],
        'Looking for:      ' . $data['interest'],
        'Travel:           ' . $data['travel_month'] . ($data['dates_flexible'] ? ' (dates flexible)' : ''),
        '',
        'Reply directly to this email to answer ' . $data['full_name'] . '.',
        'All enquiries: ' . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '') . url('admin/manage.php?type=enquiries'),
    ];
    $headers = implode("\r\n", [
        'From: ' . APP_NAME . ' website <no-reply@' . $host . '>',
        'Reply-To: "' . $name . '" <' . $data['email'] . '>',
        'Content-Type: text/plain; charset=UTF-8',
        'MIME-Version: 1.0',
    ]);
    $subject = '=?UTF-8?B?' . base64_encode('New enquiry — ' . $data['full_name']) . '?=';
    if (!@mail($to, $subject, implode("\n", $lines), $headers)) {
        error_log('[margha] Enquiry saved but notification email failed for ' . $data['email']);
    }
}

unset($_SESSION['enquire_old']);
enquire_respond(true, setting('eq_success_title'), 200, $isAjax);
