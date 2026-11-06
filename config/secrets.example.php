<?php
/**
 * MĀRGHĀ Wellness — secrets template (safe to commit: contains no real values)
 * --------------------------------------------------------------
 * Production (cPanel):
 *   Save a copy as  /home/<account>/margha-secrets.php
 *   i.e. one folder ABOVE public_html, so it can never be opened in a browser
 *   and is never touched by GitHub deployments. Set its permissions to 600.
 *
 * Local development:
 *   Save a copy as  config/secrets.local.php  (git-ignored).
 *
 * Never commit the real file.
 */
return [
    'APP_ENV' => 'production',        // 'development' shows PHP errors — never on the live site

    'DB_HOST' => 'localhost',
    'DB_NAME' => 'account_dbname',
    'DB_USER' => 'account_dbuser',
    'DB_PASS' => 'your-database-password',
];
