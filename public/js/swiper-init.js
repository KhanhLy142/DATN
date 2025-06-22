new Swiper(".banner-swiper", {
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },
});

document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: false,
        centeredSlides: false,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            320: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var newSwiper = new Swiper(".myNewSwiper", {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: false,
        centeredSlides: false,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            320: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const thumbs = document.querySelectorAll(".thumb-img");
    const mainImg = document.getElementById("main-image");

    thumbs.forEach((thumb) => {
        thumb.addEventListener("click", function () {
            mainImg.src = this.src;
        });
    });
});


