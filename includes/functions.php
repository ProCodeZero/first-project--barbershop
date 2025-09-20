<?php
// Common PHP functions for the barbershop website

// Start session if not already started
if (session_id() == '') {
    session_start();
}

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

// Services data
function getServices() {
    return array(
        array(
            'id' => 1,
            'name' => 'Machine Haircut',
            'price' => 800
        ),
        array(
            'id' => 2,
            'name' => 'Mustache Trim',
            'price' => 1200
        ),
        array(
            'id' => 3,
            'name' => 'Grooming',
            'price' => 400
        ),
        array(
            'id' => 4,
            'name' => 'Complete Haircut',
            'price' => 2300
        )
    );
}

// Products data
function getProducts() {
    return array(
        array(
            'id' => 1,
            'name' => 'Brews Daily Men Shampoo',
            'price' => 744,
            'image' => 'pictures/card1.png'
        ),
        array(
            'id' => 2,
            'name' => 'Syoss Men Power & Strength Shampoo',
            'price' => 160,
            'image' => 'pictures/card2.png'
        ),
        array(
            'id' => 3,
            'name' => 'Schauma "Strength and Volume with Hops"',
            'price' => 94,
            'image' => 'pictures/card3.png'
        )
    );
}

// Gallery images
function getGalleryImages() {
    return array(
        'pictures/pictureOfWorking.jpg',
        'pictures/pictureOfWorking_2.jpg',
        'pictures/pictureOfWorking_4.jpeg',
        'pictures/pictureOfWorking_3.jpg'
    );
}

// FAQ data
function getFAQ() {
    return array(
        array(
            'question' => 'How often should I get my hair cut?',
            'answer' => 'This depends on your hair type and desired style. Generally, it is recommended to get a haircut every 4-6 weeks to maintain the desired look.'
        ),
        array(
            'question' => 'What hairstyles are popular now?',
            'answer' => 'Popular hairstyles change over time, but currently in fashion are styles like fades, undercuts, pompadours, and textured cuts. Our barbers are always up to date with the latest trends and can suggest options based on your preferences.'
        ),
        array(
            'question' => 'How to care for hair between haircuts?',
            'answer' => 'Regularly use shampoo and condition your hair, avoid excessive heat styling. Use quality hair products suitable for your hair type, and apply a moisturizing hair mask once a week.'
        ),
        array(
            'question' => 'How to choose a haircut based on face shape?',
            'answer' => 'Different face shapes suit different hairstyles. Our barbers know how to assess face shape and can recommend styles that will highlight your features. Don\'t hesitate to ask for advice during your visit.'
        ),
        array(
            'question' => 'I have thinning or balding hair. What options do I have?',
            'answer' => 'Our barbers have experience dealing with hair loss and can suggest suitable hairstyles or care methods that will help solve your problems. They can also recommend hair care products that promote hair growth.'
        ),
        array(
            'question' => 'Can you recommend hair products for my specific hair type?',
            'answer' => 'Absolutely! Our barbers are well-informed about various hair products and can suggest the best products for your specific hair type, whether it\'s dry, oily, curly, or straight.'
        ),
        array(
            'question' => 'What\'s the difference between a fade and a taper?',
            'answer' => 'A fade refers to a gradual transition from short to long hair, usually with fading on the sides and back to the crown. On the other hand, a taper involves gradually shortening hair towards the neckline.'
        ),
        array(
            'question' => 'How long does it take to get a haircut?',
            'answer' => 'The duration of a haircut varies depending on the complexity of the hairstyle and specific client requirements. On average, a haircut takes about 30 minutes, but it can be shorter or longer depending on circumstances.'
        ),
        array(
            'question' => 'Can I bring a photo of a hairstyle I like?',
            'answer' => 'Absolutely! If you bring a photo of your desired hairstyle, it will help communicate your preferences to the barber. They will work with you to achieve a similar look that will suit your unique features.'
        ),
        array(
            'question' => 'How do I maintain my beard?',
            'answer' => 'Regularly wash and care for your beard, and use beard oil or balm to keep it moisturized and soft. Trimming and shaping your beard is also crucial for maintaining its neat appearance. Our barbers can give beard care advice and recommend beard care products.'
        )
    );
}

// Process form submissions
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
            // Here you would typically save to database or send email
            $_SESSION['success_message'] = 'Form submitted successfully! We will contact you to confirm your booking.';
            return true;
        } else {
            $_SESSION['errors'] = $errors;
            return false;
        }
    }
    return false;
}

// Display messages
function displayMessages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['errors'])) {
        echo '<div class="error-messages">';
        foreach ($_SESSION['errors'] as $error) {
            echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        }
        echo '</div>';
        unset($_SESSION['errors']);
    }
}
?>
