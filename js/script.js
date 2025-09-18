// Swiper
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

// Popover
const popoverLinks = document.querySelectorAll('.popover-link');

// Popup
const popupLinks = document.querySelectorAll('.popup-link');
const body = document.querySelector('body');
// Для выравнивания фиксированных объектов
const lockPadding = document.querySelectorAll('lock-padding');
//
let unlock = true;
const timeout = 800;

// Shop price slider
const rangeInput = document.querySelectorAll('.range-input input'),
  progress = document.querySelector('.price-setting__slider .progress');
let priceGap = 200;

// Popup list of goods
const btnsClose = document.querySelectorAll('.basket-popover__delete-btn'),
  goodsNumber = document.querySelectorAll('.basket__goods-number'),
  priceShow = document.querySelectorAll('.price-setting__watch .show-value');
let goodsList = document.querySelectorAll('.basket-popover__good-wrapper');

if (goodsNumber.length > 0) {
  for (let i = 0; i < goodsNumber.length; i++) {
    const el = goodsNumber[i];
    el.textContent = goodsList.length;
  }
}

// Delete good element
if (btnsClose.length > 0) {
  for (let i = 0; i < btnsClose.length; i++) {
    const el = btnsClose[i];
    el.addEventListener('click', function (e) {
      e.target.closest('.basket-popover__good-wrapper').remove();
      goodsList = document.querySelectorAll('.basket-popover__good-wrapper');
      for (let i = 0; i < goodsNumber.length; i++) {
        const el = goodsNumber[i];
        console.log('el :>> ', el);
        el.textContent = goodsList.length;
      }
    });
  }
}

// Popover code
if (popoverLinks.length > 0) {
  for (let index = 0; index < popoverLinks.length; index++) {
    const popoverLink = popoverLinks[index];
    popoverLink.addEventListener('click', function (e) {
      const popoverName = popoverLink.getAttribute('href').replace('#', '');
      const curentPopover = document.getElementById(popoverName);
      popoverOpen(curentPopover);
      e.preventDefault();
    });
  }
}
const popoverCloseIcon = document.querySelectorAll('.close-popover');
if (popoverCloseIcon.length > 0) {
  for (let i = 0; i < popoverCloseIcon.length; i++) {
    const el = popoverCloseIcon[i];
    el.addEventListener('click', function (e) {
      popoverClose(el.closest('.popover'));
      e.preventDefault();
    });
  }
}
function popoverOpen(curentPopover) {
  if (curentPopover && unlock) {
    const popoverActive = document.querySelector('.popover.open');
    if (popoverActive) {
      popoverClose(popoverActive, false);
    }
    curentPopover.classList.add('open');
    document.querySelector('main').addEventListener('click', function (e) {
      if (!e.target.closest('.basket__popover')) {
        popoverClose(document.querySelector('.popover'));
      }
    });
  }
}

function popoverClose(popoverActive) {
  if (unlock) {
    popoverActive.classList.remove('open');
  }
}
// End popover code

// Popup code

if (popupLinks.length > 0) {
  for (let index = 0; index < popupLinks.length; index++) {
    const popupLink = popupLinks[index];
    popupLink.addEventListener('click', function (e) {
      const popupName = popupLink.getAttribute('href').replace('#', '');
      const curentPopup = document.getElementById(popupName);
      popupOpen(curentPopup);
      e.preventDefault();
    });
  }
}
const popupCloseIcon = document.querySelectorAll('.close-popup');
if (popupCloseIcon.length > 0) {
  for (let i = 0; i < popupCloseIcon.length; i++) {
    const el = popupCloseIcon[i];
    el.addEventListener('click', function (e) {
      popupClose(el.closest('.popup'));
      e.preventDefault();
    });
  }
}
function popupOpen(curentPopup) {
  if (curentPopup && unlock) {
    const popupActive = document.querySelector('.popup.open');
    if (popupActive) {
      popupClose(popupActive, false);
    } else {
      // Отключение скрола основной страницы + убирание ползунка
      // bodyLock();
    }
    curentPopup.classList.add('open');
    curentPopup.addEventListener('click', function (e) {
      if (!e.target.closest('.popup__content')) {
        popupClose(e.target.closest('.popup'));
      }
    });
  }
}

function popupClose(popupActive, doUnlock = true) {
  if (unlock) {
    popupActive.classList.remove('open');
    if (doUnlock) {
      // Возвращение скрола основной страницы + убирание ползунка
      // bodyUnlock();
    }
  }
}
// Отключение скрола основной страницы + убирание ползунка
function bodyLock() {
  const lockPaddingValue =
    window.innerWidth - document.querySelector('.wrapper').offsetWidth + 'px';

  if (lockPadding.length > 0) {
    for (let i = 0; i < lockPadding.length; i++) {
      const el = lockPadding[i];
      el.style.paddingRight = lockPaddingValue;
    }
  }
  body.style.paddingRight = lockPaddingValue;
  body.classList.add('lock');

  unlock = false;
  setTimeout(() => {
    unlock = true;
  }, timeout);
}
// Возвращение скрола основной страницы + убирание ползунка
function bodyUnlock() {
  setTimeout(() => {
    if (lockPadding.length > 0) {
      for (let i = 0; i < lockPadding.length; i++) {
        const el = lockPadding[i];
        el.computedStyleMap.paddingRight = '0px';
      }
    }
    body.style.paddingRight = '0px';
    body.classList.remove('lock');
  }, timeout);

  unlock = false;
  setTimeout(() => {
    unlock = true;
  }, timeout);
}

document.addEventListener('keydown', function (e) {
  if (e.which === 27) {
    const popupActive = this.document.querySelector('popup.open');
    popupClose(popupActive);
  }
});
// End popup code

// Shop price slider
console.log('priceShow[0] :>> ', priceShow[0]);
rangeInput.forEach((input) => {
  input.addEventListener('input', (e) => {
    // * getting two ranges value and parsing them to number
    let minVal = parseInt(rangeInput[0].value),
      maxVal = parseInt(rangeInput[1].value);
    if (maxVal - minVal < priceGap) {
      if (e.target.className === 'range-min') {
        rangeInput[0].value = maxVal - priceGap;
      } else {
        rangeInput[1].value = minVal + priceGap;
      }
    } else {
      priceShow[0].textContent = minVal;
      priceShow[1].textContent = maxVal;
      progress.style.left = (minVal / rangeInput[0].max) * 100 + '%';
      progress.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + '%';
    }
  });
});

const appointmentForm = document.querySelector('.appointment__form');
if (appointmentForm) {
  appointmentForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const phone = appointmentForm.querySelector('input[name="phone"]').value;
    const name = appointmentForm.querySelector('input[name="name"]').value;

    if (!validatePhone(phone)) {
      showTooltip(
        appointmentForm.querySelector('input[name="phone"]'),
        'Введите корректный номер телефона'
      );
      return;
    }

    if (name.length < 2) {
      showTooltip(
        appointmentForm.querySelector('input[name="name"]'),
        'Имя должно содержать минимум 2 символа'
      );
      return;
    }

    // Show success message
    showTooltip(appointmentForm, 'Форма успешно отправлена!', 'success');
  });
}

// Add real-time phone formatting
const phoneInputs = document.querySelectorAll('input[type="tel"]');
phoneInputs.forEach((input) => {
  input.addEventListener('input', (e) => {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
      value =
        '+7 (' +
        value.substring(1, 4) +
        ') ' +
        value.substring(4, 7) +
        '-' +
        value.substring(7, 9) +
        '-' +
        value.substring(9, 11);
    }
    e.target.value = value;
  });
});

document.addEventListener('DOMContentLoaded', () => {
  // Form validation
  const appointmentForm = document.querySelector('.appointment__form');
  if (appointmentForm) {
    appointmentForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const phone = appointmentForm.querySelector('input[name="phone"]').value;
      const name = appointmentForm.querySelector('input[name="name"]').value;

      if (!validatePhone(phone)) {
        showTooltip(
          appointmentForm.querySelector('.full-form__phone'),
          'Введите корректный номер телефона'
        );
        return;
      }

      if (name.length < 2) {
        showTooltip(
          appointmentForm.querySelector('.full-form__name'),
          'Имя должно содержать минимум 2 символа'
        );
        return;
      }

      // Show success message
      alert('Форма успешно отправлена!');
    });
  }

  // Dynamic header effects
  const header = document.querySelector('.header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 100) {
      header.classList.add('header--scrolled');
    } else {
      header.classList.remove('header--scrolled');
    }
  });

  // Price hover effects
  const priceElements = document.querySelectorAll('.card__price');
  priceElements.forEach((el) => {
    el.addEventListener('mouseenter', () => {
      const price = parseFloat(el.textContent);
      el.dataset.originalPrice = el.textContent;
      el.textContent = formatPrice(price * 0.9); // Show 10% discount on hover
    });

    el.addEventListener('mouseleave', () => {
      el.textContent = el.dataset.originalPrice;
    });
  });

  // Live clock in footer
  const footerPhone = document.querySelector('.footer-left__phone');
  if (footerPhone) {
    setInterval(() => {
      footerPhone.setAttribute('data-time', getCurrentTime());
    }, 1000);
  }

  // Dropdown menu
  const menuItems = document.querySelectorAll('.header__btn');
  menuItems.forEach((item) => {
    if (item.classList.contains('btn-catalog')) {
      const dropdown = document.createElement('div');
      dropdown.className = 'dropdown-menu';
      dropdown.innerHTML = `
                <a href="#" class="dropdown-item">Стрижки</a>
                <a href="#" class="dropdown-item">Бритье</a>
                <a href="#" class="dropdown-item">Уход</a>
            `;
      item.appendChild(dropdown);

      item.addEventListener('mouseenter', () => {
        dropdown.style.display = 'block';
      });

      item.addEventListener('mouseleave', () => {
        dropdown.style.display = 'none';
      });
    }
  });

  // Add loading screen
  utils.showLoadingScreen();

  // Update clock every second
  setInterval(() => {
    const timeElements = document.querySelectorAll('.dynamic-time');
    timeElements.forEach((el) => {
      el.textContent = utils.getCurrentTime();
    });
  }, 1000);

  // Add scroll animation for elements
  const animateElements = document.querySelectorAll('.label-block');
  let observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-in');
      }
    });
  });

  animateElements.forEach((el) => observer.observe(el));

  // Random price animation
  setInterval(() => {
    const prices = document.querySelectorAll('.card__price');
    const randomPrice = prices[utils.getRandomInt(0, prices.length - 1)];
    if (randomPrice) {
      randomPrice.classList.add('price-pulse');
      setTimeout(() => randomPrice.classList.remove('price-pulse'), 1000);
    }
  }, 3000);

  // Add double-click handler for cards
  document.querySelectorAll('.list__card').forEach((card) => {
    card.addEventListener('dblclick', (e) => {
      e.preventDefault();
      card.classList.add('card-highlight');
      setTimeout(() => card.classList.remove('card-highlight'), 1000);
    });
  });

  // Add keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const activePopup = document.querySelector('.popup.open');
      if (activePopup) popupClose(activePopup);
    }
  });

  // Add touch events for mobile
  document.querySelectorAll('.card').forEach((card) => {
    let touchStartTime;
    card.addEventListener('touchstart', () => {
      touchStartTime = new Date().getTime();
    });
    card.addEventListener('touchend', () => {
      const touchEndTime = new Date().getTime();
      if (touchEndTime - touchStartTime > 500) {
        card.classList.add('long-touch');
        setTimeout(() => card.classList.remove('long-touch'), 500);
      }
    });
  });
});
