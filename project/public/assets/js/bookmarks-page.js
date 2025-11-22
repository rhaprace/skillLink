document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const booksGrid = document.getElementById('booksGrid');
    const noResults = document.getElementById('noResults');
    const bookCards = document.querySelectorAll('.book-card');

    if (!searchInput || !sortSelect || !booksGrid) return;

    let currentSearchTerm = '';
    let currentSort = 'recent';

    function filterAndSortBooks() {
        let visibleCount = 0;
        const cards = Array.from(bookCards);

        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const author = card.getAttribute('data-author') || '';
            const category = card.getAttribute('data-category') || '';
            
            const matchesSearch = !currentSearchTerm || 
                title.includes(currentSearchTerm) || 
                author.includes(currentSearchTerm) || 
                category.includes(currentSearchTerm);

            if (matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const visibleCards = cards.filter(card => card.style.display !== 'none');
        
        visibleCards.sort((a, b) => {
            if (currentSort === 'title') {
                const titleA = a.getAttribute('data-title') || '';
                const titleB = b.getAttribute('data-title') || '';
                return titleA.localeCompare(titleB);
            } else if (currentSort === 'author') {
                const authorA = a.getAttribute('data-author') || '';
                const authorB = b.getAttribute('data-author') || '';
                return authorA.localeCompare(authorB);
            } else {
                const dateA = parseInt(a.getAttribute('data-bookmarked-at')) || 0;
                const dateB = parseInt(b.getAttribute('data-bookmarked-at')) || 0;
                return dateB - dateA;
            }
        });

        visibleCards.forEach(card => {
            booksGrid.appendChild(card);
        });

        if (visibleCount === 0) {
            booksGrid.classList.add('hidden');
            if (noResults) noResults.classList.remove('hidden');
        } else {
            booksGrid.classList.remove('hidden');
            if (noResults) noResults.classList.add('hidden');
        }

        if (window.booksCarousel && typeof window.booksCarousel.update === 'function') {
            setTimeout(() => {
                window.booksCarousel.update();
            }, 100);
        }
    }

    searchInput.addEventListener('input', function(e) {
        currentSearchTerm = e.target.value.toLowerCase().trim();
        
        if (currentSearchTerm) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
        
        filterAndSortBooks();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        currentSearchTerm = '';
        clearSearchBtn.classList.add('hidden');
        filterAndSortBooks();
        searchInput.focus();
    });

    sortSelect.addEventListener('change', function(e) {
        currentSort = e.target.value;
        filterAndSortBooks();
    });

    bookCards.forEach(card => {
        const bookmarkBtn = card.querySelector('.bookmark-btn');
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function() {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        
                        const remainingCards = document.querySelectorAll('.book-card');
                        if (remainingCards.length === 0) {
                            window.location.reload();
                        } else {
                            filterAndSortBooks();
                        }
                    }, 300);
                }, 500);
            });
        }
    });
});

