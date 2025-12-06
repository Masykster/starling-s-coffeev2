<?php
require_once 'config.php';

startSession();

// Clear all session data
session_unset();
session_destroy();

// Redirect based on where logout was called from
if (strpos($_SERVER['HTTP_REFERER'] ?? '', '/admin/') !== false) {
    header('Location: login.php');
} else {
    header('Location: index.php');
}
exit;
?>

