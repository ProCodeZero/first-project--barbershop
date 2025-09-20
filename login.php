<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']);
    
    $errors = array();
    
    if (!validateEmail($email)) {
        $errors[] = 'Enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Enter password';
    }
    
    if (empty($errors)) {
        // Here you would typically validate against database
        // For demo purposes, we'll use simple validation
        if ($email === 'admin@borodinski.ru' && $password === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_email'] = $email;
            $_SESSION['success_message'] = 'Welcome!';
            
            if ($remember) {
                // Set remember me cookie
                setcookie('remember_token', 'demo_token', time() + (30 * 24 * 60 * 60), '/');
            }
        } else {
            $errors[] = 'Invalid email or password';
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}

// Redirect back to the page they came from
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header('Location: ' . $referer);
exit;
?>
