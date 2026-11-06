<?php
/**
 * MĀRGHĀ Wellness — site configuration
 * --------------------------------------------------------------
 * This file is safe to commit: it contains NO passwords.
 *
 * Database credentials live in a separate "secrets" file that is never
 * stored in Git or deployed from GitHub. The first file found is used:
 *
 *   1. the path in the MARGHA_SECRETS environment variable (optional)
 *   2. margha-secrets.php ONE FOLDER ABOVE the website root
 *      — on cPanel: /home/<account>/margha-secrets.php (outside public_html)
 *   3. config/secrets.local.php — local development only (git-ignored)
 *
 * Create it from config/secrets.example.php.
 */

declare(strict_types=1);

// ---- Secrets -----------------------------------------------------
$__secrets = [];
foreach (array_filter([
    getenv('MARGHA_SECRETS') ?: null,
    dirname(__DIR__, 2) . '/margha-secrets.php',
    __DIR__ . '/secrets.local.php',
]) as $__file) {
    if (@is_file($__file) && @is_readable($__file)) {
        $__loaded = require $__file;
        if (is_array($__loaded)) {
            $__secrets = $__loaded;
        }
        break;
    }
}
if (!$__secrets) {
    error_log('[margha] No secrets file found — database is disabled. See config/config.php.');
}

// ---- Environment -------------------------------------------------
defined('APP_ENV') || define('APP_ENV', (string) ($__secrets['APP_ENV'] ?? 'production'));   // 'development' shows errors
define('APP_NAME', 'Mārghā Wellness');
define('APP_TAGLINE', 'Purposeful journeys through Sri Lanka and the Maldives.');

// ---- Database (values come from the secrets file) -----------------
defined('DB_HOST') || define('DB_HOST', (string) ($__secrets['DB_HOST'] ?? 'localhost'));
defined('DB_NAME') || define('DB_NAME', (string) ($__secrets['DB_NAME'] ?? ''));
defined('DB_USER') || define('DB_USER', (string) ($__secrets['DB_USER'] ?? ''));
defined('DB_PASS') || define('DB_PASS', (string) ($__secrets['DB_PASS'] ?? ''));
define('DB_CHARSET', 'utf8mb4');
unset($__secrets, $__loaded, $__file);

// ---- Contact / social --------------------------------------------
define('CONTACT_EMAIL', 'hello@margha.com');   // enquiry notifications go here
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/margawellness');
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/');
define('SOCIAL_WHATSAPP', 'https://chat.whatsapp.com/');

// ---- Base URL (auto-detected, works in root or a sub-folder) -----
if (!defined('BASE_URL')) {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    // API + admin scripts live one level down — strip it so links stay correct
    $dir = preg_replace('#/(api|admin)$#', '', $dir);
    define('BASE_URL', $dir === '' ? '' : $dir);
}

// ---- Error handling ----------------------------------------------
// Never show PHP/database error details to visitors on the live site; they go to the error log.
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL & ~E_DEPRECATED);
}

date_default_timezone_set('Asia/Colombo');
