<?php
declare(strict_types=1);

/**
 * Admin bootstrap — loaded by every page in /admin.
 * Auth, database access, flash messages, form fields and image uploads.
 */
define('ADMIN', true);
require dirname(__DIR__, 2) . '/includes/bootstrap.php';
require __DIR__ . '/entities.php';
require __DIR__ . '/pages.php';
require __DIR__ . '/layout.php';

const ADMIN_IDLE_TIMEOUT  = 7200;            // seconds of inactivity before logout
const ADMIN_MAX_FAILS     = 5;               // failed logins …
const ADMIN_FAIL_WINDOW   = 900;             // … per IP within 15 minutes
const ADMIN_UPLOAD_DIR    = 'uploads';       // inside assets/images
const ADMIN_UPLOAD_MAX_MB = 8;

// Never cache or index admin pages; never allow framing.
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, no-cache, must-revalidate');

/* ------------------------------------------------------------------ */
/* Helpers                                                             */
/* ------------------------------------------------------------------ */

function admin_url(string $path = '', array $query = []): string
{
    $u = url('admin/' . ltrim($path, '/'));
    return $query ? $u . '?' . http_build_query($query) : $u;
}

function redirect(string $to): void
{
    header('Location: ' . $to, true, 303);
    exit;
}

/** Accept a "return to" URL only if it points inside this site's /admin. */
function safe_return(string $url): string
{
    $prefix = BASE_URL . '/admin/';
    return str_starts_with($url, $prefix) && !str_contains($url, '//') && !preg_match('/[\r\n]/', $url) ? $url : '';
}

/** Format one value for a list table (thumbnail, badge, date, …). */
function admin_cell(string $col, mixed $val, array $ent): string
{
    $def = $ent['fields'][$col] ?? ['type' => 'text'];
    if ($val === null || $val === '') {
        return '<span class="muted">—</span>';
    }
    if ($def['type'] === 'image') {
        return '<img class="thumb" src="' . e(img((string) $val)) . '" alt="" loading="lazy">';
    }
    if ($def['type'] === 'bool') {
        return $val ? '<span class="badge badge--yes">Yes</span>' : '<span class="badge badge--no">No</span>';
    }
    if ($def['type'] === 'select') {
        $label = $def['options'][$val] ?? $val;
        return '<span class="badge badge--' . e((string) $val) . '">' . e(explode(' — ', (string) $label)[0]) . '</span>';
    }
    if ($col === 'created_at' || $col === 'published_at' || $def['type'] === 'datetime') {
        return e(date('j M Y', strtotime((string) $val)));
    }
    if ($def['type'] === 'decimal') {
        return e(rtrim(rtrim(number_format((float) $val, 2, '.', ','), '0'), '.'));
    }
    if ($def['type'] === 'rich') {
        $val = strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], ' ', (string) $val));
    }
    $text = (string) $val;
    return e(mb_strlen($text) > 70 ? mb_substr($text, 0, 70) . '…' : $text);
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['admin_flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $f = $_SESSION['admin_flash'] ?? [];
    unset($_SESSION['admin_flash']);
    return $f;
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

/** PDO connection, or a friendly error page if the database is unreachable. */
function db(): PDO
{
    $pdo = Database::get();
    if (!$pdo) {
        admin_fatal('Can’t connect to the database',
            'Check DB_NAME, DB_USER and DB_PASS in config/config.php, and that the user has been added to the database in cPanel › MySQL® Databases.');
    }
    return $pdo;
}

function table_exists(string $table): bool
{
    try {
        db()->query('SELECT 1 FROM `' . $table . '` LIMIT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

function require_tables(array $tables): void
{
    $missing = array_values(array_filter($tables, fn($t) => !table_exists($t)));
    if ($missing) {
        admin_fatal('Database tables missing',
            'These tables don’t exist yet: ' . implode(', ', $missing) . '. Import database/schema.sql (new database) or database/admin.sql (admin tables only) in phpMyAdmin.');
    }
}

/** Reject POSTs without a valid CSRF token. */
function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? null)) {
        flash('Your session expired — please try again.', 'error');
        redirect(admin_url());
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/* ------------------------------------------------------------------ */
/* Authentication                                                      */
/* ------------------------------------------------------------------ */

function current_admin(): ?array
{
    static $user = false;
    if ($user !== false) {
        return $user;
    }
    $user = null;
    $id = $_SESSION['admin_id'] ?? null;
    if (!$id) {
        return null;
    }
    if (time() - (int) ($_SESSION['admin_seen'] ?? 0) > ADMIN_IDLE_TIMEOUT) {
        admin_logout();
        flash('You were signed out after a period of inactivity.', 'info');
        return null;
    }
    $stmt = db()->prepare('SELECT id, username, name, email, last_login_at FROM admin_users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch() ?: null;
    if ($user) {
        $_SESSION['admin_seen'] = time();
    } else {
        admin_logout();
    }
    return $user;
}

function require_admin(): array
{
    require_tables(['admin_users']);
    $user = current_admin();
    if (!$user) {
        $_SESSION['admin_return'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect(admin_url('login.php'));
    }
    return $user;
}

function admin_count(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
}

function login_blocked(): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM admin_login_attempts WHERE ip_address = ? AND attempted_at > ?');
    $stmt->execute([client_ip(), date('Y-m-d H:i:s', time() - ADMIN_FAIL_WINDOW)]);
    return (int) $stmt->fetchColumn() >= ADMIN_MAX_FAILS;
}

/** Returns the user on success, null on failure (failure is recorded). */
function attempt_login(string $username, string $password): ?array
{
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Hash a dummy value when the user doesn't exist, so response time doesn't reveal usernames.
    $hash = $user['password_hash'] ?? password_hash('timing-dummy', PASSWORD_DEFAULT);
    $ok   = password_verify($password, $hash);
    if (!$user || !$ok) {
        db()->prepare('INSERT INTO admin_login_attempts (ip_address, username, attempted_at) VALUES (?, ?, NOW())')
            ->execute([client_ip(), mb_substr($username, 0, 60)]);
        return null;
    }

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
    }
    db()->prepare('DELETE FROM admin_login_attempts WHERE ip_address = ? OR attempted_at < ?')
        ->execute([client_ip(), date('Y-m-d H:i:s', time() - 86400)]);
    db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);

    session_regenerate_id(true);
    $_SESSION['admin_id']   = (int) $user['id'];
    $_SESSION['admin_seen'] = time();
    unset($_SESSION['csrf']);  // fresh token for the new session
    return $user;
}

function admin_logout(): void
{
    unset($_SESSION['admin_id'], $_SESSION['admin_seen']);
    session_regenerate_id(true);
}

function password_problem(string $password, string $confirm): ?string
{
    if (mb_strlen($password) < 10) {
        return 'Password must be at least 10 characters.';
    }
    if ($password !== $confirm) {
        return 'The two passwords don’t match.';
    }
    return null;
}

/* ------------------------------------------------------------------ */
/* Form fields (shared by content records and page-text settings)      */
/* ------------------------------------------------------------------ */

/**
 * Read one submitted field. Adds a message to $errors on bad input.
 * Field definitions come from admin/_inc/entities.php or includes/settings.php.
 */
function read_field(string $name, array $def, array &$errors): mixed
{
    $label = $def['label'];
    $raw   = $_POST['f'][$name] ?? null;
    $type  = $def['type'];

    if ($type === 'bool') {
        return empty($raw) ? 0 : 1;
    }

    $value = is_string($raw) ? trim(str_replace("\r\n", "\n", $raw)) : '';

    if ($type === 'image') {
        $upload = handle_upload('file_' . $name, $name, $errors, $label);
        if ($upload !== null) {
            $value = $upload;
        } elseif ($value !== '' && (str_contains($value, '..') || !preg_match('#^(https?://\S+|[A-Za-z0-9][A-Za-z0-9._/-]*\.(jpe?g|png|webp|gif|svg))$#i', $value))) {
            $errors[$name] = "$label: enter an image filename such as hero.jpg, or upload a file.";
        }
        if (isset($errors[$name])) {
            return $value;   // keep the upload/filename message rather than a generic "required"
        }
    }

    $required = !empty($def['required']);
    if ($required && $value === '' && $type !== 'slug') {
        $errors[$name] = "$label is required.";
        return $value;
    }

    switch ($type) {
        case 'int':
            if ($value === '') return $def['default'] ?? 0;
            if (!preg_match('/^-?\d+$/', $value)) $errors[$name] = "$label must be a whole number.";
            return (int) $value;
        case 'decimal':
            if ($value === '') return $def['default'] ?? 0;
            if (!is_numeric($value) || (float) $value < 0) $errors[$name] = "$label must be a number, e.g. 210 or 199.50.";
            return round((float) $value, 2);
        case 'select':
            if (!array_key_exists($value, $def['options'])) $errors[$name] = "Choose a valid $label.";
            return $value;
        case 'datetime':
            if ($value === '') return null;
            $dt = DateTime::createFromFormat('Y-m-d\TH:i', $value) ?: DateTime::createFromFormat('Y-m-d H:i:s', $value);
            if (!$dt) { $errors[$name] = "$label is not a valid date."; return null; }
            return $dt->format('Y-m-d H:i:s');
        case 'email':
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) $errors[$name] = "$label must be a valid email address.";
            break;
        case 'url':
            if ($value !== '' && !preg_match('#^https?://\S+$#i', $value)) $errors[$name] = "$label must start with https://";
            break;
        case 'slug':
            if ($value !== '' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
                $errors[$name] = "$label may only use lowercase letters, numbers and hyphens.";
            }
            break;
    }

    $max = $def['max'] ?? 20000;
    if (is_string($value) && mb_strlen($value) > $max) {
        $errors[$name] = "$label is too long (" . mb_strlen($value) . " characters, maximum $max).";
    }
    return $value;
}

/** Render one form field. */
function render_field(string $name, array $def, mixed $value, ?string $error = null): string
{
    $id    = 'f_' . $name;
    $type  = $def['type'];
    $label = e($def['label']) . (!empty($def['required']) ? ' <span class="req" aria-hidden="true">*</span>' : '');
    $attr  = 'id="' . $id . '" name="f[' . e($name) . ']"' . (!empty($def['required']) && $type !== 'slug' && $type !== 'image' ? ' required' : '');
    if (isset($def['max']) && in_array($type, ['text', 'rich', 'textarea', 'paras', 'slug', 'url', 'email'], true)) {
        $attr .= ' maxlength="' . (int) $def['max'] . '"';
    }
    $help = $def['help'] ?? match ($type) {
        'rich'  => 'Formatting: <em>italic</em>, <strong>bold</strong>, <br> for a new line.',
        'paras' => 'Leave a blank line between paragraphs. You can use <em>italic</em> and <strong>bold</strong>.',
        'slug'  => 'Used in the web address. Leave empty to create it from the title.',
        default => '',
    };
    $value = $value ?? '';
    $html  = '<div class="field field--' . $type . ($error ? ' has-error' : '') . '">';

    if ($type === 'bool') {
        $html .= '<label class="check"><input type="checkbox" ' . $attr . ' value="1"' . ($value ? ' checked' : '') . '> ' . $label . '</label>';
    } else {
        $html .= '<label for="' . $id . '">' . $label . '</label>';
        switch ($type) {
            case 'readonly':
                $html .= '<div class="readonly">' . ($value === '' ? '<span class="muted">—</span>' : nl2br(e((string) $value))) . '</div>';
                break;
            case 'textarea':
            case 'paras':
                $rows = $def['rows'] ?? 6;
                $html .= '<textarea ' . $attr . ' rows="' . $rows . '">' . e((string) $value) . '</textarea>';
                break;
            case 'rich':
                $html .= '<textarea ' . $attr . ' rows="2" class="rich">' . e((string) $value) . '</textarea>';
                break;
            case 'select':
                $html .= '<select ' . $attr . '>';
                foreach ($def['options'] as $k => $opt) {
                    $html .= '<option value="' . e((string) $k) . '"' . ((string) $value === (string) $k ? ' selected' : '') . '>' . e($opt) . '</option>';
                }
                $html .= '</select>';
                break;
            case 'datetime':
                $v = $value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '';
                $html .= '<input type="datetime-local" ' . $attr . ' value="' . e($v) . '">';
                break;
            case 'int':
            case 'decimal':
                $html .= '<input type="number" ' . $attr . ' value="' . e((string) $value) . '"' . ($type === 'decimal' ? ' step="0.01" min="0"' : ' step="1"') . '>';
                break;
            case 'image':
                $html .= '<div class="image-field">';
                $html .= '<img class="image-field__preview" src="' . ($value !== '' ? e(img((string) $value)) : '') . '" data-base="' . e(url('assets/images/')) . '" alt=""' . ($value === '' ? ' hidden' : '') . '>';
                $html .= '<div class="image-field__inputs">';
                $html .= '<input type="file" name="file_' . e($name) . '" accept="image/jpeg,image/png,image/webp,image/gif" data-preview>';
                $html .= '<input type="text" ' . $attr . ' value="' . e((string) $value) . '" placeholder="or filename in assets/images, e.g. hero.jpg">';
                $html .= '<small class="help">JPG, PNG, WebP or GIF, up to ' . ADMIN_UPLOAD_MAX_MB . ' MB. '
                    . e($def['help'] ?? 'Use landscape photos about 2400px wide for full-width images.') . '</small>';
                $html .= '</div></div>';
                break;
            default:
                $inputType = in_array($type, ['email', 'url'], true) ? $type : 'text';
                $html .= '<input type="' . $inputType . '" ' . $attr . ' value="' . e((string) $value) . '">';
        }
    }
    if ($help !== '' && $type !== 'image') {
        $html .= '<small class="help">' . e($help) . '</small>';
    }
    if ($error) {
        $html .= '<small class="error-text">' . e($error) . '</small>';
    }
    return $html . '</div>';
}

/* ------------------------------------------------------------------ */
/* Image uploads                                                       */
/* ------------------------------------------------------------------ */

/**
 * Save an uploaded image to assets/images/uploads with a random name.
 * Returns the path to store (e.g. "uploads/ab12….jpg"), or null if no file was sent.
 */
function handle_upload(string $input, string $key, array &$errors, string $label): ?string
{
    $file = $_FILES[$input] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[$key] = "$label: upload failed" . (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? ' — the file is too large.' : '.');
        return null;
    }
    if ($file['size'] > ADMIN_UPLOAD_MAX_MB * 1024 * 1024) {
        $errors[$key] = "$label: the file is larger than " . ADMIN_UPLOAD_MAX_MB . ' MB.';
        return null;
    }
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime  = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
    if (!isset($types[$mime]) || @getimagesize($file['tmp_name']) === false) {
        $errors[$key] = "$label: please upload a JPG, PNG, WebP or GIF image.";
        return null;
    }
    $dir = ROOT_PATH . '/assets/images/' . ADMIN_UPLOAD_DIR;
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        $errors[$key] = "$label: the uploads folder could not be created (check folder permissions).";
        return null;
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $types[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        $errors[$key] = "$label: the file could not be saved (check folder permissions).";
        return null;
    }
    @chmod($dir . '/' . $name, 0644);
    return ADMIN_UPLOAD_DIR . '/' . $name;
}

/** Turn a title into a URL slug. */
function slugify(string $text): string
{
    $map = ['ā' => 'a', 'Ā' => 'a', 'ē' => 'e', 'ī' => 'i', 'ō' => 'o', 'ū' => 'u', '&' => ' and '];
    $text = strtr($text, $map);
    if (function_exists('iconv')) {
        $text = (string) @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    }
    $text = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $text));
    return trim(substr($text, 0, 120), '-') ?: 'item';
}
