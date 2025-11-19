document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');

    if (page && page !== '1') {
        const booksSection = document.querySelector('.grid, .space-y-4');
        if (booksSection) {
            setTimeout(() => {
                const offset = 100;
                const elementPosition = booksSection.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }, 100);
        }
    }

    const paginationLinks = document.querySelectorAll('.pagination-container a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const booksContainer = document.querySelector('.grid, .space-y-4');
            if (booksContainer) {
                booksContainer.style.opacity = '0.5';
                booksContainer.style.pointerEvents = 'none';
            }
        });
    });

    const bookCards = document.querySelectorAll('.card-hover');
    bookCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 50 + (index * 30));
    });

    document.addEventListener('keydown', function(e) {
        const currentPage = parseInt(urlParams.get('page')) || 1;
        const prevLink = document.querySelector('a[href*="page=' + (currentPage - 1) + '"]');
        const nextLink = document.querySelector('a[href*="page=' + (currentPage + 1) + '"]');

        if (e.key === 'ArrowLeft' && prevLink && !e.ctrlKey && !e.metaKey) {
            const activeElement = document.activeElement;
            if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                prevLink.click();
            }
        }

        if (e.key === 'ArrowRight' && nextLink && !e.ctrlKey && !e.metaKey) {
            const activeElement = document.activeElement;
            if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                nextLink.click();
            }
        }
    });

    const pageNumbers = document.querySelectorAll('.pagination-container a');
    pageNumbers.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
