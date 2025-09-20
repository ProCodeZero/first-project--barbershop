// Minimal JavaScript for essential client-side functionality
// Most functionality has been moved to PHP

// Swiper initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper for gallery
    if (document.querySelector('.gallery__swiper')) {
        new Swiper('.gallery__swiper', {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    // Phone number formatting
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach((input) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '+7 (' + value.substring(1, 4) + ') ' + 
                        value.substring(4, 7) + '-' + 
                        value.substring(7, 9) + '-' + 
                        value.substring(9, 11);
            }
            e.target.value = value;
        });
    });

    // Popup functionality
    const popupLinks = document.querySelectorAll('.popup-link');
    const popupClose = document.querySelectorAll('.close-popup');
    const popup = document.getElementById('popup');

    popupLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            popup.classList.add('open');
        });
    });

    popupClose.forEach(close => {
        close.addEventListener('click', (e) => {
            e.preventDefault();
            popup.classList.remove('open');
        });
    });

    // Popover functionality
    const popoverLinks = document.querySelectorAll('.popover-link');
    const popoverClose = document.querySelectorAll('.close-popover');
    const popover = document.getElementById('popover');

    popoverLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            popover.classList.add('open');
        });
    });

    popoverClose.forEach(close => {
        close.addEventListener('click', (e) => {
            e.preventDefault();
            popover.classList.remove('open');
        });
    });

    // Close popup/popover when clicking outside
    document.addEventListener('click', (e) => {
        if (popup && popup.classList.contains('open') && !e.target.closest('.popup__content')) {
            popup.classList.remove('open');
        }
        if (popover && popover.classList.contains('open') && !e.target.closest('.basket__popover')) {
            popover.classList.remove('open');
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (popup) popup.classList.remove('open');
            if (popover) popover.classList.remove('open');
        }
    });

    // Price slider functionality
    const rangeInputs = document.querySelectorAll('.range-input input');
    const progress = document.querySelector('.price-setting__slider .progress');
    const priceShows = document.querySelectorAll('.price-setting__watch .show-value');
    
    if (rangeInputs.length > 0 && progress && priceShows.length > 0) {
        let priceGap = 200;
        
        rangeInputs.forEach((input) => {
            input.addEventListener('input', (e) => {
                let minVal = parseInt(rangeInputs[0].value);
                let maxVal = parseInt(rangeInputs[1].value);
                
                if (maxVal - minVal < priceGap) {
                    if (e.target.className === 'range-min') {
                        rangeInputs[0].value = maxVal - priceGap;
                    } else {
                        rangeInputs[1].value = minVal + priceGap;
                    }
                } else {
                    priceShows[0].textContent = minVal;
                    priceShows[1].textContent = maxVal;
                    progress.style.left = (minVal / rangeInputs[0].max) * 100 + '%';
                    progress.style.right = 100 - (maxVal / rangeInputs[1].max) * 100 + '%';
                }
            });
        });
    }

    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.success-message, .error-message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 300);
        }, 5000);
    });
});

// Cart management functions
function removeFromCart(productId) {
    if (confirm('Удалить товар из корзины?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="remove_from_cart" value="1"><input type="hidden" name="product_id" value="' + productId + '">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Form validation
function validateForm(form) {
    const phoneInput = form.querySelector('input[type="tel"]');
    const nameInput = form.querySelector('input[name="name"]');
    
    let isValid = true;
    
    if (phoneInput && !validatePhone(phoneInput.value)) {
        showError(phoneInput, 'Введите корректный номер телефона');
        isValid = false;
    }
    
    if (nameInput && nameInput.value.length < 2) {
        showError(nameInput, 'Имя должно содержать минимум 2 символа');
        isValid = false;
    }
    
    return isValid;
}

function validatePhone(phone) {
    const phoneRegex = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
    return phoneRegex.test(phone);
}

function showError(input, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ff3434';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    
    input.parentNode.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}
