<?php
session_start();

// Redirect to login if not authenticated as admin
if (!isset($_SESSION['user_email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}
?>