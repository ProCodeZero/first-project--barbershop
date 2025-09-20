<?php
require_once 'includes/functions.php';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = (int)$_POST['product_id'];
        $products = getProducts();
        foreach ($products as $product) {
            if ($product['id'] == $product_id) {
                addToCart($product['id'], $product['name'], $product['price']);
                break;
            }
        }
    } elseif (isset($_POST['remove_from_cart'])) {
        $product_id = (int)$_POST['product_id'];
        removeFromCart($product_id);
    }
}

// Get filter parameters
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 300;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 1700;
$manufacturers = isset($_GET['manufacturers']) ? $_GET['manufacturers'] : array();
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'expensive';

// Filter products
$products = getProducts();
$filtered_products = array();
foreach ($products as $product) {
    if ($product['price'] >= $min_price && $product['price'] <= $max_price) {
        $filtered_products[] = $product;
    }
}

// Sort products
if ($sort === 'cheap') {
    usort($filtered_products, 'comparePricesAsc');
} else {
    usort($filtered_products, 'comparePricesDesc');
}

// Comparison functions for sorting
function comparePricesAsc($a, $b) {
    if ($a['price'] == $b['price']) {
        return 0;
    }
    return ($a['price'] < $b['price']) ? -1 : 1;
}

function comparePricesDesc($a, $b) {
    if ($a['price'] == $b['price']) {
        return 0;
    }
    return ($a['price'] > $b['price']) ? -1 : 1;
}

include 'includes/header.php';
?>

<main class="main main--shop">
    <div class="main-wrapper">
        <div class="main-breadcrumbs">
            <h2 class="main-breadcrumbs__title">Catalog</h2>
            <p class="main-breadcrumbs__pass breadcrumbs-pass">
                <a class="main-breadcrumbs__link" href="index.php">Home</a>
                <img class="main-breadcrumbs__img" src="pictures/icons/bullet.svg" alt="" />
                Catalog
            </p>
        </div>

        <div class="main__catalog catalog">
            <div class="catalog__wrapper">
                <aside class="catalog__settings settings">
                    <form method="GET" class="filter-form">
                        <div class="settings__price price-setting">
                            <h3 class="settings__subtitle">Price</h3>
                            <div class="price-setting__slider">
                                <div class="progress"></div>
                            </div>
                            <div class="range-input">
                                <input type="range" class="range-min" name="min_price" min="0" max="2000" value="<?php echo $min_price; ?>">
                                <input type="range" class="range-max" name="max_price" min="0" max="2000" value="<?php echo $max_price; ?>">
                            </div>
                            <p class="price-setting__watch">
                                from <span class="show-value"><?php echo $min_price; ?></span> ₽ to
                                <span class="show-value"><?php echo $max_price; ?></span> ₽
                            </p>
                        </div>
                        
                        <div class="settings__manufacturers manufacturers-setting">
                            <h3 class="settings__subtitle">Manufacturers</h3>
                            <ul class="manufacturers-setting__list">
                                <?php
                                $manufacturers_list = [
                                    'Bed Head for Men',
                                    'Homme Deep Cleansing Cool',
                                    'So Intense',
                                    'Sprekenhus',
                                    'Firm Store',
                                    'Winter Body'
                                ];
                                foreach ($manufacturers_list as $manufacturer): ?>
                                <li class="manufacturers-setting__item">
                                    <label class="check option">
                                        <input type="checkbox" class="check__input" name="manufacturers[]" value="<?php echo $manufacturer; ?>"
                                            <?php echo in_array($manufacturer, $manufacturers) ? 'checked' : ''; ?>>
                                        <span class="check__box"></span>
                                        <p class="check__text"><?php echo $manufacturer; ?></p>
                                    </label>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="setting__groups groups-setting">
                            <h3 class="settings__subtitle">Product Groups</h3>
                            <ul class="groups-setting__list">
                                <li class="groups-setting__item">
                                    <label class="radio">
                                        <input type="radio" name="category" value="shaving" class="radio__input" 
                                            <?php echo $category === 'shaving' ? 'checked' : ''; ?>>
                                        <span class="radio__box"></span>
                                        <p class="radio__text">Shaving Accessories</p>
                                    </label>
                                </li>
                                <li class="groups-setting__item">
                                    <label class="radio">
                                        <input type="radio" name="category" value="care" class="radio__input"
                                            <?php echo $category === 'care' ? 'checked' : ''; ?>>
                                        <span class="radio__box"></span>
                                        <p class="radio__text">Care Products</p>
                                    </label>
                                </li>
                                <li class="groups-setting__item">
                                    <label class="radio">
                                        <input type="radio" name="category" value="accessories" class="radio__input"
                                            <?php echo $category === 'accessories' ? 'checked' : ''; ?>>
                                        <span class="radio__box"></span>
                                        <p class="radio__text">Accessories</p>
                                    </label>
                                </li>
                            </ul>
                            <button class="settings__apply-btn shop-btn" type="submit">Show</button>
                        </div>
                    </form>
                </aside>
                
                <div class="catalog__head catalog-head">
                    <form method="GET" class="sort-form">
                        <input type="hidden" name="min_price" value="<?php echo $min_price; ?>">
                        <input type="hidden" name="max_price" value="<?php echo $max_price; ?>">
                        <input type="hidden" name="manufacturers" value="<?php echo implode(',', $manufacturers); ?>">
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        
                        <select name="sort" class="catalog-head__select" onchange="this.form.submit()">
                            <option value="expensive" <?php echo $sort === 'expensive' ? 'selected' : ''; ?>>Most Expensive First</option>
                            <option value="cheap" <?php echo $sort === 'cheap' ? 'selected' : ''; ?>>Cheapest First</option>
                        </select>
                    </form>
                    
                    <div class="catalog-head__btns">
                        <button class="catalog-head__btn catalog-first-btn shop-sec-btn" type="button">
                            <img src="pictures/icons/union.svg" alt="">
                        </button>
                        <button class="catalog-head__btn catalog-second-btn shop-btn" type="button">
                            <img src="pictures/icons/cards-white.svg" alt="">
                        </button>
                    </div>
                </div>
                
                <div class="catalog__body catalog-body">
                    <?php foreach ($filtered_products as $product): ?>
                    <div class="catalog-body__card catalog-card">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="catalog-card__img">
                        <div class="catalog-card__text-block">
                            <h4 class="catalog-card__title"><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="catalog-card__cost"><?php echo formatPrice($product['price']); ?></p>
                        </div>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="add_to_cart" value="1">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="catalog-card__btn shop-btn">Buy</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="catalog__bottom catalog-bottom">
                    <div class="catalog-bottom__pagination">
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-1" disabled>
                            <img src="pictures/icons/arrow-left.svg" alt="">
                        </button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-2">1</button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-2">2</button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-2">3</button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-2">4</button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-2">5</button>
                        <button type="button" class="shop-btn catalog-bottom__pagination-btn-type-1">
                            <img src="pictures/icons/arrow-right.svg" alt="">
                        </button>
                    </div>
                    <button type="button" class="catalog-bottom__show-more-btn shop-btn">Show More</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
