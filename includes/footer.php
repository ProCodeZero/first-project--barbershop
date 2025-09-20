        <!-- Footer -->
        <footer class="footer">
            <div class="footer__wrapper">
                <div class="footer__info footer-left">
                    <img
                        src="pictures/icons/logo-short-dark.svg"
                        alt="BORODINSKI"
                        class="footer-left__short-logo"
                    />
                    <p class="footer-left__paragraph">
                        St. Petersburg, Karpovka River<br />
                        Embankment, 5, letter P.
                    </p>
                    <h3 class="footer-left__phone">+7 800 555-86-28</h3>
                    <a href="" class="footer-left__link">How to Find Us?</a>
                </div>
                <div class="footer__offers footer-middle">
                    <h3 class="footer-middle__title footer-title">
                        Get the Best Offers
                    </h3>
                    <form class="footer__form" method="POST" action="subscribe.php">
                        <input
                            class="footer-middle__email-form"
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        />
                        <button class="submit-email-btn" type="submit">
                            <img src="pictures/icons/arrow-tail-right.svg" alt="" />
                        </button>
                    </form>
                </div>
                <div class="footer__social-media footer-right">
                    <h3 class="footer-right__title footer-title">Let's Be Friends</h3>
                    <a class="footer-right__btn btn-sotial-media" href="https://github.com/ProCodeZero" target="_blank">
                        <p>GitHub Profile</p>
                        <img src="pictures/icons/gitHub.png" alt="" width="auto%" />
                    </a>
                    <p class="footer-right__signature">
                        Created by: <span class="creator">Hollow_DS</span>
                    </p>
                </div>
            </div>
        </footer>
        
        <!-- Popup -->
        <div id="popup" class="popup">
            <div class="popup__body">
                <div class="popup__content">
                    <a href="#" class="popup__close close-popup">
                        <img src="pictures/icons/cross.svg" alt="" />
                    </a>
                    <div class="popup__title">Personal Account</div>
                    <form method="POST" action="includes/formhandler.inc.php">
                        <div class="popup__box-1">
                            <input 
                                class="popup__form-1 full-form__input" 
                                type="email" 
                                name="email"
                                placeholder="Enter email"
                                required
                            />
                            <input 
                                class="popup__form-2 full-form__input" 
                                type="password" 
                                name="password"
                                placeholder="Enter password"
                                required
                            />
                        </div>
                        <div class="popup__box-2">
                            <div class="popup__checkbox-container checkbox">
                                <label class="check option">
                                    <input class="check__input" type="checkbox" name="remember" value="1" />
                                    <span class="check__box"></span>
                                    Remember me
                                </label>
                            </div>
                            <a class="foggot-password" href="forgot-password.php">Forgot password</a>
                        </div>
                        <button name="action" value="login" class="popup-btn-enter" type="submit">Login</button>
                        <button name="action" value="register" class="popup-btn-registration" type="submit">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
