<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    
    $errors = array();
    
    if (!validateEmail($email)) {
        $errors[] = 'Enter a valid email address';
    }
    
    if (!validateName($name)) {
        $errors[] = 'Name must contain at least 2 characters';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must contain at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Here you would typically save to database
        $_SESSION['success_message'] = 'Registration successful! You can now log in.';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
    }
}

include 'includes/header.php';
?>

<main class="main">
    <div class="main-wrapper">
        <h2 class="main__title">Registration</h2>
        
        <?php displayMessages(); ?>
        
        <form method="POST" class="register-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required minlength="2">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit" class="main-btn-type-1">Register</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
