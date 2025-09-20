<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (validateEmail($email)) {
        // Here you would typically save to database or send to email service
        $_SESSION['success_message'] = 'Thank you for subscribing! You will receive our best offers.';
    } else {
        $_SESSION['errors'] = array('Enter a valid email address');
    }
}

// Redirect back to the page they came from
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header('Location: ' . $referer);
exit;
?>
