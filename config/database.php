<?php
/**
 * Database Configuration
 * 
 * This file contains all database connection settings.
 * Update these values according to your environment.
 */

// Database Configuration Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'prod.unit3-local_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_ENV', 'development'); // development, staging, production
define('APP_DEBUG', true);

// Timezone
date_default_timezone_set('Asia/Manila');

// Error Reporting (disable in production)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
