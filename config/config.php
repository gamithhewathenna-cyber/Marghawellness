<?php
/**
 * MĀRGHĀ Wellness — site configuration
 * --------------------------------------------------------------
 * Edit the database credentials below after creating the MySQL
 * database + user in cPanel (MySQL® Databases).
 * cPanel prefixes names with your account, e.g. "cpuser_margha".
 */

declare(strict_types=1);

// ---- Environment -------------------------------------------------
defined('APP_ENV') || define('APP_ENV', 'production');   // 'development' shows errors
define('APP_NAME', 'Mārghā Wellness');
define('APP_TAGLINE', 'Purposeful journeys through Sri Lanka and the Maldives.');

// ---- Database ----------------------------------------------------
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_NAME') || define('DB_NAME', 'margkihr_Marghawellnessmain');
defined('DB_USER') || define('DB_USER', 'margkihr_margamainadmin');
defined('DB_PASS') || define('DB_PASS', '7ZHb,[pd;+m$hh[P');
define('DB_CHARSET', 'utf8mb4');

// ---- Contact / social --------------------------------------------
define('CONTACT_EMAIL', 'hello@margha.com');   // enquiry notifications go here
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/margawellness');
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/');
define('SOCIAL_WHATSAPP', 'https://chat.whatsapp.com/');

// ---- Base URL (auto-detected, works in root or a sub-folder) -----
if (!defined('BASE_URL')) {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    // Scripts inside a sub-folder of the site (api/, admin/, wellness/ …): strip that folder
    // so every link still points at the site root.
    $root = defined('ROOT_PATH') ? rtrim(str_replace('\\', '/', (string) realpath(ROOT_PATH)), '/') : '';
    $file = str_replace('\\', '/', (string) realpath($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $sub  = ($root !== '' && $file !== '' && stripos($file, $root . '/') === 0)
        ? rtrim(str_replace('\\', '/', dirname(substr($file, strlen($root)))), '/')
        : '';
    if ($sub !== '' && strcasecmp(substr($dir, -strlen($sub)), $sub) === 0) {
        $dir = substr($dir, 0, -strlen($sub));
    } else {
        $dir = (string) preg_replace('#/(api|admin|wellness)$#', '', $dir);
    }
    define('BASE_URL', $dir === '' ? '' : $dir);
    unset($root, $file, $sub);
}

// ---- Error handling ----------------------------------------------
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED);
}

date_default_timezone_set('Asia/Colombo');
