<?php
// Common PHP functions for the barbershop website

// Start session if not already started
if (session_id() == '') {
    session_start();
}

// Include database connection
require_once 'dbh.inc.php';

// Ensure UTF-8 encoding for database results
$pdo->exec("SET NAMES utf8mb4");

// Make $pdo available globally for other scripts like header.php
global $pdo;

// Form validation functions
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    $phoneRegex = '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/';
    return preg_match($phoneRegex, $phone);
}

function validateName($name) {
    return strlen(trim($name)) >= 2;
}

// Price formatting
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' ₽';
}

// Date and time utilities
function getCurrentTime() {
    return date('H:i');
}

function getCurrentDate() {
    return date('d.m.Y');
}

// Cart management functions
function addToCart($id, $name, $price) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    $_SESSION['cart'][] = array(
        'id' => $id,
        'name' => $name,
        'price' => $price
    );
}

function removeFromCart($id) {
    if (isset($_SESSION['cart'])) {
        $new_cart = array();
        foreach ($_SESSION['cart'] as $item) {
            if ($item['id'] != $id) {
                $new_cart[] = $item;
            }
        }
        $_SESSION['cart'] = $new_cart;
    }
}

function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'];
    }
    return $total;
}

// Fetch services from database
function getServices() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name, price FROM services ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getServices: " . $e->getMessage());
        return array();
    }
}

// Fetch products from database
function getProducts() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name, price, image FROM products ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getProducts: " . $e->getMessage());
        return array();
    }
}

// Fetch FAQ from database
function getFAQ() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT question, answer FROM faq ORDER BY sort_order ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getFAQ: " . $e->getMessage());
        return array();
    }
}

// Gallery images (static)
function getGalleryImages() {
    return array(
        'pictures/pictureOfWorking.jpg',
        'pictures/pictureOfWorking_2.jpg',
        'pictures/pictureOfWorking_4.jpeg',
        'pictures/pictureOfWorking_3.jpg'
    );
}

// Process appointment form
function processAppointmentForm() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment'])) {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $date = isset($_POST['date']) ? trim($_POST['date']) : '';
        $time = isset($_POST['time']) ? trim($_POST['time']) : '';
        $errors = array();

        if (!validateName($name)) {
            $errors[] = 'Name must contain at least 2 characters';
        }
        if (!validatePhone($phone)) {
            $errors[] = 'Enter a valid phone number';
        }
        if (empty($date)) {
            $errors[] = 'Select a date';
        }
        if (empty($time)) {
            $errors[] = 'Select a time';
        }

        if (empty($errors)) {
            // Save to database (optional enhancement)
            try {
                global $pdo;
                $stmt = $pdo->prepare("
                    INSERT INTO appointments (name, phone, date, time, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute(array($name, $phone, $date, $time));
            } catch (PDOException $e) {
                error_log("Failed to save appointment: " . $e->getMessage());
                // Don't fail the user experience — still show success
            }
            $_SESSION['success_message'] = 'Form submitted successfully! We will contact you to confirm your booking.';
            return true;
        } else {
            $_SESSION['errors'] = $errors;
            return false;
        }
    }
    return false;
}

// Display success/error messages
function displayMessages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['errors'])) {
        echo '<div class="error-messages">';
        foreach ($_SESSION['errors'] as $error) {
            echo '<div class="error-message">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        echo '</div>';
        unset($_SESSION['errors']);
    }
}
?>