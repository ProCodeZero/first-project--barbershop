// Form validation utilities
function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePhone(phone) {
  const phoneRegex = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
  return phoneRegex.test(phone);
}

// UI feedback utilities
function showTooltip(element, message, type = 'error') {
  const tooltip = document.createElement('div');
  tooltip.className = `tooltip tooltip--${type}`;
  tooltip.textContent = message;
  element.parentNode.appendChild(tooltip);
  setTimeout(() => tooltip.remove(), 3000);
}

// Date and time utilities
function formatDateTime(date) {
  return date.toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

// Price formatting
function formatPrice(price) {
  return `${price.toLocaleString('ru-RU')} ₽`;
}

// Time utilities
function getCurrentTime() {
  const now = new Date();
  return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
}

function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Loading screen
function showLoadingScreen() {
  const loader = document.createElement('div');
  loader.className = 'page-loader';
  loader.innerHTML = `
        <div class="loader-content">
            <img src="pictures/icons/logo-short-dark.svg" alt="Loading..." />
            <p>Загрузка... ${getCurrentTime()}</p>
        </div>
    `;
  document.body.appendChild(loader);

  setTimeout(() => {
    loader.style.opacity = '0';
    setTimeout(() => loader.remove(), 500);
  }, 1500);
}

// String manipulation utilities
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

// Math utilities for animations
function easeInOutQuad(t) {
  return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
}

// Initialize all tooltips
document.addEventListener('DOMContentLoaded', () => {
  // Add tooltip listeners to all form inputs
  const formInputs = document.querySelectorAll('.full-form__input');
  formInputs.forEach((input) => {
    input.addEventListener('invalid', (e) => {
      e.preventDefault();
      showTooltip(input, input.validationMessage);
    });
  });

  // Add price formatting to all price elements
  const priceElements = document.querySelectorAll('.card__price');
  priceElements.forEach((el) => {
    const price = parseFloat(el.textContent);
    el.textContent = formatPrice(price);
  });
});

// Export utilities
window.utils = {
  getCurrentTime,
  getRandomInt,
  showLoadingScreen,
  capitalizeFirstLetter,
  easeInOutQuad,
};
