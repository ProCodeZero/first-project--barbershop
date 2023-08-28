// Swiper
new Swiper(".gallery__swiper", {
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});

// Popover
const popoverLinks = document.querySelectorAll(".popover-link")

// Popup
const popupLinks = document.querySelectorAll(".popup-link");
const body = document.querySelector("body");
// Для выравнивания фиксированных объектов
const lockPadding = document.querySelectorAll("lock-padding");
//
let unlock = true;
const timeout = 800;

// Popover code
if (popoverLinks.length > 0) {
  for (let index = 0; index < popoverLinks.length; index++) {
    const popoverLink = popoverLinks[index];
    popoverLink.addEventListener("click", function (e) {
      const popoverName = popoverLink.getAttribute("href").replace("#", "");
      const curentPopover = document.getElementById(popoverName);
      popoverOpen(curentPopover);
      e.preventDefault();
    });
  }
}
const popoverCloseIcon = document.querySelectorAll(".close-popover");
if (popoverCloseIcon.length > 0) {
  for (let i = 0; i < popoverCloseIcon.length; i++) {
    const el = popoverCloseIcon[i];
    el.addEventListener("click", function (e) {
      popoverClose(el.closest(".popover"));
      e.preventDefault();
    });
  }
}
function popoverOpen(curentPopover) {
  if (curentPopover && unlock) {
    const popoverActive = document.querySelector(".popover.open");
    if (popoverActive) {
      popoverClose(popoverActive, false);
    }
    curentPopover.classList.add("open");
    document.querySelector('main').addEventListener("click", function (e) {
      if (!e.target.closest(".basket__popover")) {
        popoverClose(document.querySelector(".popover"));
      }
    });
  }
}

function popoverClose(popoverActive) {
  if (unlock) {
    popoverActive.classList.remove("open");
  }
}
// End popover code


// Popup code

if (popupLinks.length > 0) {
  for (let index = 0; index < popupLinks.length; index++) {
    const popupLink = popupLinks[index];
    popupLink.addEventListener("click", function (e) {
      const popupName = popupLink.getAttribute("href").replace("#", "");
      const curentPopup = document.getElementById(popupName);
      popupOpen(curentPopup);
      e.preventDefault();
    });
  }
}
const popupCloseIcon = document.querySelectorAll(".close-popup");
if (popupCloseIcon.length > 0) {
  for (let i = 0; i < popupCloseIcon.length; i++) {
    const el = popupCloseIcon[i];
    el.addEventListener("click", function (e) {
      popupClose(el.closest(".popup"));
      e.preventDefault();
    });
  }
}
function popupOpen(curentPopup) {
  if (curentPopup && unlock) {
    const popupActive = document.querySelector(".popup.open");
    if (popupActive) {
      popupClose(popupActive, false);
    } else {
      // Отключение скрола основной страницы + убирание ползунка
      // bodyLock();
    }
    curentPopup.classList.add("open");
    curentPopup.addEventListener("click", function (e) {
      if (!e.target.closest(".popup__content")) {
        popupClose(e.target.closest(".popup"));
      }
    });
  }
}

function popupClose(popupActive, doUnlock = true) {
  if (unlock) {
    popupActive.classList.remove("open");
    if (doUnlock) {
      // Возвращение скрола основной страницы + убирание ползунка
      // bodyUnlock();
    }
  }
}
// Отключение скрола основной страницы + убирание ползунка
function bodyLock() {
  const lockPaddingValue =
    window.innerWidth - document.querySelector(".wrapper").offsetWidth + "px";

  if (lockPadding.length > 0) {
    for (let i = 0; i < lockPadding.length; i++) {
      const el = lockPadding[i];
      el.style.paddingRight = lockPaddingValue;
    }
  }
  body.style.paddingRight = lockPaddingValue;
  body.classList.add("lock");

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
        el.computedStyleMap.paddingRight = "0px";
      }
    }
    body.style.paddingRight = "0px";
    body.classList.remove("lock");
  }, timeout);

  unlock = false;
  setTimeout(() => {
    unlock = true;
  }, timeout);
}

document.addEventListener("keydown", function (e) {
  if (e.which === 27) {
    const popupActive = this.document.querySelector("popup.open");
    popupClose(popupActive);
  }
});
// End popup code

