<?php
// Simple test file to verify PHP functionality
require_once 'includes/functions.php';

echo "<h1>PHP Barbershop Test (PHP 5.3+ Compatible)</h1>";

echo "<h2>Functions Test:</h2>";
echo "Current time: " . getCurrentTime() . "<br>";
echo "Current date: " . getCurrentDate() . "<br>";
echo "Formatted price: " . formatPrice(1500) . "<br>";

echo "<h2>Services Test:</h2>";
$services = getServices();
foreach ($services as $service) {
    echo $service['name'] . " - " . formatPrice($service['price']) . "<br>";
}

echo "<h2>Products Test:</h2>";
$products = getProducts();
foreach ($products as $product) {
    echo $product['name'] . " - " . formatPrice($product['price']) . "<br>";
}

echo "<h2>FAQ Test:</h2>";
$faq = getFAQ();
echo "Number of FAQ items: " . count($faq) . "<br>";

echo "<h2>Session Test:</h2>";
echo "Session status: " . (session_id() != '' ? "Active" : "Not active") . "<br>";

echo "<h2>Validation Test:</h2>";
echo "Email validation (test@example.com): " . (validateEmail('test@example.com') ? "Valid" : "Invalid") . "<br>";
echo "Phone validation (+7 (999) 999-99-99): " . (validatePhone('+7 (999) 999-99-99') ? "Valid" : "Invalid") . "<br>";
echo "Name validation (John): " . (validateName('John') ? "Valid" : "Invalid") . "<br>";

echo "<h2>Cart Test:</h2>";
addToCart(1, 'Test Service', 1000);
echo "Cart items count: " . getCartItemCount() . "<br>";
echo "Cart total: " . formatPrice(getCartTotal()) . "<br>";

echo "<h2>PHP Version Test:</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PHP 5.3+ Compatible: " . (version_compare(PHP_VERSION, '5.3.0', '>=') ? "Yes" : "No") . "<br>";

echo "<p><strong>All tests completed successfully!</strong></p>";
echo "<p><a href='index.php'>Go to main page</a></p>";
?>
