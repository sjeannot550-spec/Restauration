// MENU MOBILE
let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
};

// SEARCH
let searchIcon = document.querySelector('#search-icon');
let searchForm = document.querySelector('#search-form');
let closeBtn = document.querySelector('#close');

searchIcon.onclick = () => {
    searchForm.classList.toggle('active');
};

closeBtn.onclick = () => {
    searchForm.classList.remove('active');
};

// SCROLL RESET
window.onscroll = () => {
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
    searchForm.classList.remove('active');
};

// Slider des avis
var swiperReview = new Swiper(".review-slider", {
    loop: true,
    grabCursor: true,
    spaceBetween: 20,
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        0: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    },
});

// Votre slider home existant
var swiperHome = new Swiper(".home-slider", {
    loop: true,
    grabCursor: true,
    centeredSlides: true,
    autoplay: { delay: 3000, disableOnInteraction: false },
    pagination: { el: ".swiper-pagination", clickable: true },
});