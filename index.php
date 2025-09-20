<?php
require_once 'includes/functions.php';

// Process appointment form
$formProcessed = processAppointmentForm();

include 'includes/header.php';
?>

<main class="main">
    <div class="main__background-container">
        <img
            src="pictures/backgroundjpg.jpg"
            alt=""
            class="main__background"
        />
    </div>
    
    <!-- Welcome Block #1 -->
    <div class="main__greeting greeting">
        <div class="greeting__logo">
            <img
                src="pictures/icons/logo-full-dark.svg"
                alt="Logo"
                class="logo-img"
            />
        </div>
        <div class="greeting__labels">
            <div class="greeting__text-block-1 label-block">
                <h2 class="block-1__title label-title">Fast</h2>
                <img
                    src="pictures/icons/bullet.svg"
                    alt="*"
                    class="block-1__img label-img"
                />
                <p class="block-1__paragraph label-paragraph">
                    We do our work quickly! Two hours will fly by unnoticed, and you
                    — a happy owner of a stylish haircut!
                </p>
            </div>
            <div class="greeting__text-block-2 label-block">
                <h2 class="block-2__title label-title">Cool</h2>
                <img
                    src="pictures/icons/bullet.svg"
                    alt="*"
                    class="block-2__img label-img"
                />
                <p class="block-2__paragraph label-paragraph">
                    Forget how you used to get your hair cut.<br />We'll make you a star
                    of football or cinema!<br />At least externally.
                </p>
            </div>
            <div class="greeting__text-block-3 label-block">
                <h2 class="block-3__title label-title">Expensive</h2>
                <img
                    src="pictures/icons/bullet.svg"
                    alt="*"
                    class="block-3__img label-img"
                />
                <p class="block-2__paragraph label-paragraph">
                    Our masters are professionals in their field and cannot cost
                    cheap. Besides, doesn't the price give a certain status?
                </p>
            </div>
        </div>
    </div>
    
    <!-- Services Block #2 -->
    <div class="main__servises servises">
        <!-- Popular Services -->
        <div class="servises__popular-servises popular-servises">
            <h2 class="popular-servises__title main-block-title">
                Popular Services
            </h2>
            <ul class="popular-servises__list list">
                <?php foreach (getServices() as $service): ?>
                <li>
                    <div class="list__card card">
                        <p class="card__name"><?php echo htmlspecialchars($service['name']); ?></p>
                        <p class="card__price"><?php echo formatPrice($service['price']); ?></p>
                        <div class="card__vector vector-<?php echo $service['id']; ?>"></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="shop.php" class="popular-servises__check-all-btn main-btn-type-1">
                <p>View All</p>
                <img src="pictures/icons/arrow-right.svg" alt="" />
            </a>
        </div>

        <!-- Gallery -->
        <div class="servises__gallery gallery">
            <h2 class="gallery__title main-block-title">Photo Gallery</h2>
            <div class="gallery__swiper swiper-container" tabindex="-1">
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach (getGalleryImages() as $image): ?>
                    <div class="swiper-slide">
                        <img
                            src="<?php echo $image; ?>"
                            alt=""
                            class="gallery__img"
                        />
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Navigation arrows -->
                <div class="swiper-button-prev my-prev"></div>
                <div class="swiper-button-next"></div>
                <!-- Pagination -->
                <div class="swiper-pagination my-pagination"></div>
            </div>
        </div>
    </div>
    
    <!-- Information Block #2 -->
    <div class="main__information information">
        <div class="information__contacts contacts">
            <h2 class="contacts__title main-block-title">Contacts</h2>
            <div class="contacts__block-1">
                <h3 class="contacts__subtitle main-block-subtitle">
                    Barbershop «Borodinski»
                </h3>
                <p class="contacts__info">
                    St. Petersburg, Karpovka River Embankment, 5, letter P.
                </p>
                <h2 class="contacts__phone-number">+7 800 555-35-35</h2>
            </div>
            <div class="contacts__block-2">
                <h3 class="contacts__subtitle">Working Hours</h3>
                <p class="contacts__time-of-working">
                    Mon—Fri: 10:00 to 22:00<br />Sat—Sun: 10:00 to 19:00
                </p>
                <div class="block-2__btns">
                    <button type="button" class="contacts__route main-btn-type-1">
                        How to Get Here
                    </button>
                    <button type="button" class="contacts__feedback">
                        Feedback
                    </button>
                </div>
            </div>
        </div>

        <!-- Book Appointment Block #2 -->
        <div class="information__appointment appointment">
            <h2 class="appointment__title main-block-title">Book Appointment</h2>
            <p class="appointment__paragraph">
                Specify your desired date and time and we will contact you to
                confirm your booking.
            </p>
            
            <?php displayMessages(); ?>
            
            <form action="index.php" method="POST" class="appointment__form full-form">
                <input type="hidden" name="appointment" value="1">
                <div class="top-form">
                    <div class="full-form__date">
                        <label class="full-form__label" for="date">
                            <p class="label__text">Date</p>
                            <div class="label__img-div"></div>
                        </label>
                        <input
                            class="full-form__input"
                            type="date"
                            name="date"
                            id="date"
                            value="<?php echo getCurrentDate(); ?>"
                            required
                        />
                    </div>
                    <div class="full-form__time">
                        <label class="full-form__label" for="time">
                            <p class="label__text">Time</p>
                            <div class="label__img-div"></div>
                        </label>
                        <input
                            class="full-form__input"
                            type="time"
                            name="time"
                            id="time"
                            value="<?php echo getCurrentTime(); ?>"
                            required
                        />
                    </div>
                    <div class="full-form__phone">
                        <label class="full-form__label" for="phone">
                            <p class="label__text">Phone</p>
                        </label>
                        <input
                            class="full-form__input"
                            type="tel"
                            name="phone"
                            id="phone"
                            placeholder="+7 (999) 999-99-99"
                            pattern="\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}"
                            required
                        />
                    </div>
                </div>
                <div class="bottom-form">
                    <div class="full-form__name">
                        <label class="full-form__label" for="name">
                            <p class="label__text">Your Name</p>
                        </label>
                        <input
                            class="full-form__input"
                            type="text"
                            name="name"
                            id="name"
                            placeholder="Enter your name..."
                            minlength="2"
                            required
                        />
                    </div>
                </div>
                <div class="full-form__subscribe-container">
                    <button class="full-form__subscribe main-btn-type-1" type="submit">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
