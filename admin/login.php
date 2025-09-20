<?php
// Simple redirect if already logged in as admin
session_start();
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: index.php");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $message = 'Please enter both email and password.';
    } else {
        try {
            require_once '../includes/dbh.inc.php';

            $query = 'SELECT email, password, is_admin FROM users WHERE email = :email';
            $stmt = $pdo->prepare($query);
            $stmt->execute(array(':email' => $email));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && trim((string)$password) === trim((string)$user['password']) && $user['is_admin'] == 1) {
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = true;
                header("Location: index.php");
                exit();
            } else {
                $message = 'Invalid credentials or not an administrator.';
            }
        } catch (Exception $e) {
            $message = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div style="max-width: 400px; margin: 100px auto; padding: 20px; background: #555555ff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center;">Admin Login</h2>
        <?php if ($message): ?>
            <p style="color: #dc3545; text-align: center;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
                <input type="email" name="email" id="email" required style="width: 100%; color: #000; padding: 8px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 5px;">Password:</label>
                <input type="password" name="password" id="password" required style="width: 100%; color: #000; padding: 8px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; padding: 10px; background: #000; color: #fff; border: none; cursor: pointer;">Login</button>
        </form>
    </div>
</body>
</html>