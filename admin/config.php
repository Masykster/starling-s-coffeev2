<?php
// Config untuk admin panel
// Enable error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include main config
require_once dirname(__DIR__) . '/config.php';

// Include admin auth
require_once __DIR__ . '/includes/auth.php';
?>

