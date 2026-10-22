<?php
/**
 * WAM Tech POS & ERP - Central System Configuration
 * Secure Environment Settings
 */

defined('DB_HOST') or define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('DB_USER') or define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS') or define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
defined('DB_NAME') or define('DB_NAME', getenv('DB_NAME') ?: 'wamtech_db');
defined('DB_UPDATE_NAME') or define('DB_UPDATE_NAME', getenv('DB_UPDATE_NAME') ?: 'wamtech_db_update');

// Application Environment
defined('APP_ENV') or define('APP_ENV', getenv('APP_ENV') ?: 'local');
defined('APP_DEBUG') or define('APP_DEBUG', APP_ENV === 'local');
