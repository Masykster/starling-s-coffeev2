<?php
require_once 'config.php';

// Redirect ke login utama
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
} else {
    // Redirect ke login utama
    header('Location: ../login.php');
    exit;
}
?>

