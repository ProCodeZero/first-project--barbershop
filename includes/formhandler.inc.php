<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        require_once 'dbh.inc.php';

        if ($action === 'register') {
            // Check if email already exists
            $checkQuery = 'SELECT email FROM users WHERE email = :email';
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute(array(':email' => $email));

            if ($checkStmt->rowCount() > 0) {
                $_SESSION['message'] = array(
                    'type' => 'error',
                    'text' => 'This email is already registered!'
                );
                header("Location: ../index.php");
                die();
            }

            // ✅ Transform password to clean string
            $plainPassword = trim((string) $password);

            // Insert new user
            $insertQuery = 'INSERT INTO users (email, password, is_admin) VALUES (:email, :plainPassword, 0)';
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute(array(
                ':email' => $email,
                ':plainPassword' => $plainPassword
            ));

            $_SESSION['message'] = array(
                'type' => 'success',
                'text' => 'Registration successful! Welcome!'
            );

        } elseif ($action === 'login') {
            // Fetch user by email
            $query = 'SELECT email, password FROM users WHERE email = :email';
            $stmt = $pdo->prepare($query);
            $stmt->execute(array(':email' => $email));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['message'] = array(
                    'type' => 'error',
                    'text' => 'Invalid email or password!'
                );
                header("Location: ../index.php");
                die();
            }

            // ✅ Transform both passwords to clean strings
            $inputPassword = trim((string) $password);
            $storedPassword = trim((string) $user['password']);

            // Optional debug (remove in production)
            // error_log("Input: '$inputPassword' | Stored: '$storedPassword'");

            if ($inputPassword !== $storedPassword) {
                $_SESSION['message'] = array(
                    'type' => 'error',
                    'text' => 'Invalid email or password!'
                );
                header("Location: ../index.php");
                die();
            }

            // Login successful
            $_SESSION['user_email'] = $user['email'];

            $_SESSION['message'] = array(
                'type' => 'success',
                'text' => 'Login successful! Welcome back.'
            );

        } else {
            throw new Exception('Invalid action.');
        }

        $pdo = null;

    } catch (Exception $e) {
        $_SESSION['message'] = array(
            'type' => 'error',
            'text' => 'Error: ' . $e->getMessage()
        );
    }

    header("Location: ../index.php");
    die();
} else {
    header("Location: ../index.php");
}