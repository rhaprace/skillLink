let booksSwiper = null;

function initBooksCarousel() {
    if (window.innerWidth < 768 && typeof Swiper !== 'undefined') {
        if (booksSwiper) {
            booksSwiper.destroy(true, true);
        }

        booksSwiper = new Swiper('.books-swiper', {
            slidesPerView: 1.15,
            spaceBetween: 16,
            centeredSlides: true,
            grabCursor: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            a11y: {
                prevSlideMessage: 'Previous book',
                nextSlideMessage: 'Next book',
                paginationBulletMessage: 'Go to book {{index}}',
            },
            touchRatio: 1,
            touchAngle: 45,
            simulateTouch: true,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            speed: 400,
            effect: 'slide',
        });
    } else if (booksSwiper && window.innerWidth >= 768) {
        booksSwiper.destroy(true, true);
        booksSwiper = null;
    }
}

document.addEventListener('DOMContentLoaded', initBooksCarousel);

let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initBooksCarousel, 250);
});

window.updateBooksCarousel = function() {
    if (booksSwiper) {
        booksSwiper.update();
        booksSwiper.slideTo(0, 0);
    }
};
