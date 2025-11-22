document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const booksGrid = document.getElementById('booksGrid');
    const noResults = document.getElementById('noResults');
    const bookCards = document.querySelectorAll('.book-card');
    const modal = document.getElementById('readAgainModal');
    const modalBookTitle = document.getElementById('modalBookTitle');
    const cancelBtn = document.getElementById('cancelReadAgain');
    const confirmBtn = document.getElementById('confirmReadAgain');

    let currentSearchTerm = '';
    let currentSort = 'recent';
    let selectedBookId = null;
    let selectedBookCard = null;

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
                const authorB = a.getAttribute('data-author') || '';
                return authorA.localeCompare(authorB);
            } else {
                const dateA = parseInt(a.getAttribute('data-completed-at')) || 0;
                const dateB = parseInt(b.getAttribute('data-completed-at')) || 0;
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

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            
            if (currentSearchTerm) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }
            
            filterAndSortBooks();
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearchTerm = '';
            clearSearchBtn.classList.add('hidden');
            filterAndSortBooks();
            searchInput.focus();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function(e) {
            currentSort = e.target.value;
            filterAndSortBooks();
        });
    }

    const readAgainButtons = document.querySelectorAll('.read-again-btn');
    readAgainButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            selectedBookId = this.getAttribute('data-book-id');
            const bookTitle = this.getAttribute('data-book-title');
            selectedBookCard = this.closest('.book-card');
            
            modalBookTitle.textContent = bookTitle;
            modal.classList.remove('hidden');
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
            selectedBookId = null;
            selectedBookCard = null;
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', async function() {
            if (!selectedBookId) return;

            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Resetting...';

            try {
                const response = await fetch('reset-progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: parseInt(selectedBookId)
                    })
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof notifications !== 'undefined') {
                        notifications.success(result.message, 3000);
                    }
                    
                    if (selectedBookCard) {
                        selectedBookCard.style.opacity = '0';
                        selectedBookCard.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            selectedBookCard.remove();
                            
                            const remainingCards = document.querySelectorAll('.book-card');
                            if (remainingCards.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }
                    
                    modal.classList.add('hidden');
                } else {
                    if (typeof notifications !== 'undefined') {
                        notifications.error(result.message || 'Failed to reset progress', 3000);
                    } else {
                        alert(result.message || 'Failed to reset progress');
                    }
                }
            } catch (error) {
                console.error('Error resetting progress:', error);
                if (typeof notifications !== 'undefined') {
                    notifications.error('An error occurred. Please try again.', 3000);
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Yes, Read Again';
                selectedBookId = null;
                selectedBookCard = null;
            }
        });
    }

    const modalOverlay = modal?.querySelector('.modal-overlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function() {
            modal.classList.add('hidden');
            selectedBookId = null;
            selectedBookCard = null;
        });
    }
});

