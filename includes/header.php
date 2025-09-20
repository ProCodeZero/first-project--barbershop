<?php
// Common header include file
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Track visitor statistics
require_once 'classes/VisitorStats.php';
$visitorStats = new VisitorStats();
$visitorStats->trackVisit($current_page);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php echo getPageMetaTags($current_page); ?>
    <link type="Image/x-icon" href="pictures/icons/fav-ico.ico" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Lilita+One&family=PT+Sans+Narrow:wght@400;700&family=Raleway:wght@100&display=swap"
        rel="stylesheet"
    />
    <link rel="stylesheet" href="css/style.css" />
    <title><?php echo getPageTitle($current_page); ?></title>
</head>
<body>
    <div class="wrapper">
        <header class="header <?php echo ($current_page !== 'index') ? 'header-shop' : ''; ?>">
            <div class="header__groups">
                <nav class="header__navigation">
                    <?php if ($current_page !== 'index'): ?>
                    <a href="index.php" class="header__btn btn-main">
                        <img src="pictures/icons/logo-short-dark.svg" alt="" />
                    </a>
                    <?php endif; ?>
                    <a href="about-us.php" class="header__btn btn-us <?php echo ($current_page === 'about-us') ? 'active' : ''; ?>">About Us</a>
                    <a href="shop.php" class="header__btn btn-catalog <?php echo ($current_page === 'shop') ? 'active' : ''; ?>">Catalog</a>
                    <a href="questions.php" class="header__btn btn-questions <?php echo ($current_page === 'questions') ? 'active' : ''; ?>">FAQ</a>
                    <a href="statistics.php" class="header__btn btn-stats <?php echo ($current_page === 'statistics') ? 'active' : ''; ?>">Statistics</a>
                </nav>
                <div class="header__group-2">
                    <a href="#" class="header__btn btn-find">
                        <img src="pictures/icons/search.svg" alt="SEARCH" />
                    </a>
                    <div class="header__basket basket">
                        <a href="#popover" class="header__btn btn-basket popover-link">
                            <img
                                class="btn-basket__icon"
                                src="pictures/icons/cart.svg"
                                alt="CART"
                            />
                            <p class="basket__goods-number"><?php echo getCartItemCount(); ?></p>
                        </a>
                        <div id="popover" class="basket__popover basket-popover popover">
                            <div class="popover__content">
                                <div class="close-popover">
                                    <a type="button" class="close-popover__btn">
                                        <img src="pictures/icons/cart-black.svg" alt="" />
                                        <p class="basket__goods-number"><?php echo getCartItemCount(); ?></p>
                                    </a>
                                </div>
                                <h2 class="basket-popover__title">Cart</h2>
                                <div class="basket-popover__list">
                                    <?php echo getCartItems(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#popup" type="button" class="header__btn btn-enter popup-link">Login</a>
                </div>
            </div>
        </header>

<?php
function getPageTitle($page) {
    $titles = array(
        'index' => 'Borodinski',
        'shop' => 'Borodinski-shop',
        'questions' => 'Borodinski-questions',
        'about-us' => 'Borodinski-about-us',
        'statistics' => 'Borodinski-statistics'
    );
    return isset($titles[$page]) ? $titles[$page] : 'Borodinski';
}

function getPageMetaTags($page) {
    $metaTags = array(
        'index' => array(
            'description' => 'Professional barbershop services in St. Petersburg. Fast, cool, and expensive haircuts. Book your appointment today!',
            'keywords' => 'barbershop, haircut, St. Petersburg, grooming, men\'s haircut, beard trim',
            'author' => 'Borodinski Barbershop'
        ),
        'shop' => array(
            'description' => 'Shop for premium men\'s grooming products and accessories at Borodinski. Quality hair care products and shaving accessories.',
            'keywords' => 'men\'s grooming, hair products, shaving accessories, barbershop products, hair care',
            'author' => 'Borodinski Barbershop'
        ),
        'questions' => array(
            'description' => 'Frequently asked questions about our barbershop services, haircuts, and grooming tips. Get answers to common questions.',
            'keywords' => 'barbershop FAQ, haircut questions, grooming tips, barber services',
            'author' => 'Borodinski Barbershop'
        ),
        'about-us' => array(
            'description' => 'Learn about Borodinski Barbershop\'s story, our master barbers, and the transformation we bring to our clients.',
            'keywords' => 'about borodinski, barbershop story, master barbers, transformation',
            'author' => 'Borodinski Barbershop'
        ),
        'statistics' => array(
            'description' => 'View detailed visitor statistics and analytics for the Borodinski Barbershop website.',
            'keywords' => 'website statistics, visitor analytics, barbershop analytics, website traffic',
            'author' => 'Borodinski Barbershop'
        )
    );
    
    $currentMeta = isset($metaTags[$page]) ? $metaTags[$page] : $metaTags['index'];
    
    $html = '';
    $html .= '<meta name="description" content="' . htmlspecialchars($currentMeta['description']) . '" />' . "\n";
    $html .= '<meta name="keywords" content="' . htmlspecialchars($currentMeta['keywords']) . '" />' . "\n";
    $html .= '<meta name="author" content="' . htmlspecialchars($currentMeta['author']) . '" />' . "\n";
    
    return $html;
}

function getCartItemCount() {
    if (session_id() == '') {
        session_start();
    }
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}

function getCartItems() {
    if (session_id() == '') {
        session_start();
    }
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return '<p>Cart is empty</p>';
    }
    
    $html = '';
    foreach ($_SESSION['cart'] as $item) {
        $html .= '<div class="basket-popover__good-wrapper">';
        $html .= '<div class="basket-popover__good-info">';
        $html .= '<h3>' . htmlspecialchars($item['name']) . '</h3>';
        $html .= '<p>' . number_format($item['price']) . ' ₽</p>';
        $html .= '</div>';
        $html .= '<div class="basket-popover__delete-btn">';
        $html .= '<button type="button" onclick="removeFromCart(' . $item['id'] . ')">';
        $html .= '<img src="pictures/icons/trash.svg" alt="" />';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    return $html;
}
?>
