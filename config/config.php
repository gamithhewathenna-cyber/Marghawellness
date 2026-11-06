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
define('APP_ENV', 'production');          // 'development' shows errors
define('APP_NAME', 'Mārghā Wellness');
define('APP_TAGLINE', 'Purposeful journeys through Sri Lanka and the Maldives.');

// ---- Database ----------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'cpuser_margha');
define('DB_USER', 'cpuser_margha');
define('DB_PASS', 'change-me');
define('DB_CHARSET', 'utf8mb4');

// ---- Contact / social --------------------------------------------
define('CONTACT_EMAIL', 'hello@margha.com');   // enquiry notifications go here
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/margawellness');
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/');
define('SOCIAL_WHATSAPP', 'https://chat.whatsapp.com/');

// ---- Base URL (auto-detected, works in root or a sub-folder) -----
if (!defined('BASE_URL')) {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    // API scripts live one level down — strip it so links stay correct
    $dir = preg_replace('#/api$#', '', $dir);
    define('BASE_URL', $dir === '' ? '' : $dir);
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
