class BookSearch {
    constructor(options = {}) {
        this.searchInput = document.getElementById('searchInput');
        this.clearSearchBtn = document.getElementById('clearSearch');
        this.resetSearchBtn = document.getElementById('resetSearch');
        this.booksGrid = document.getElementById('booksGrid');
        this.booksCarousel = document.getElementById('booksCarousel');
        this.noResults = document.getElementById('noResults');
        this.resultsCount = document.getElementById('resultsCount');
        this.bookCount = document.getElementById('bookCount');
        this.bookLabel = document.getElementById('bookLabel');
        this.categoryFilters = document.querySelectorAll('.category-filter');

        this.currentCategory = options.initialCategory || 'all';
        this.allBookCards = Array.from(document.querySelectorAll('.book-card'));

        this.initializeFuse();
        this.attachEventListeners();
    }
    
    initializeFuse() {
        this.booksData = this.allBookCards.map(card => ({
            element: card,
            id: card.dataset.bookId,
            title: card.dataset.title,
            author: card.dataset.author,
            description: card.dataset.description,
            category: card.dataset.category,
            categoryId: card.dataset.categoryId
        }));
        
        const fuseOptions = {
            keys: [
                { name: 'title', weight: 0.4 },
                { name: 'author', weight: 0.3 },
                { name: 'description', weight: 0.2 },
                { name: 'category', weight: 0.1 }
            ],
            threshold: 0.4,
            includeScore: true,
            includeMatches: true,
            minMatchCharLength: 2,
            ignoreLocation: true
        };
        
        this.fuse = new Fuse(this.booksData, fuseOptions);
    }
    
    attachEventListeners() {
        this.searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value;
            
            if (searchTerm.length > 0) {
                this.clearSearchBtn.classList.remove('hidden');
            } else {
                this.clearSearchBtn.classList.add('hidden');
            }
            
            this.filterBooks(searchTerm);
        });
        
        this.clearSearchBtn.addEventListener('click', () => {
            this.clearSearch();
        });
        
        this.resetSearchBtn.addEventListener('click', () => {
            this.clearSearch();
            this.searchInput.focus();
        });
        
        this.categoryFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleCategoryFilter(filter);
            });
        });
    }
    
    clearSearch() {
        this.searchInput.value = '';
        this.clearSearchBtn.classList.add('hidden');
        this.filterBooks('');
    }
    
    handleCategoryFilter(clickedFilter) {
        this.categoryFilters.forEach(f => {
            f.classList.remove('bg-black', 'text-white');
            f.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        });
        
        clickedFilter.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        clickedFilter.classList.add('bg-black', 'text-white');
        
        this.currentCategory = clickedFilter.dataset.category;
        
        this.filterBooks(this.searchInput.value);
    }

    highlightText(text, indices) {
        if (!indices || indices.length === 0) return text;
        
        let result = '';
        let lastIndex = 0;
        
        indices.forEach(([start, end]) => {
            result += text.substring(lastIndex, start);
            result += '<mark class="bg-yellow-200 text-black px-0.5 rounded">' + 
                     text.substring(start, end + 1) + '</mark>';
            lastIndex = end + 1;
        });
        
        result += text.substring(lastIndex);
        return result;
    }
    
    applyHighlighting(book, matches, searchTerm) {
        if (!matches || !searchTerm) {
            this.resetHighlighting(book);
            return;
        }
        
        matches.forEach(match => {
            const key = match.key;
            const indices = match.indices;
            const value = match.value;
            
            if (key === 'title') {
                const titleEl = book.element.querySelector('.book-title');
                if (titleEl) titleEl.innerHTML = this.highlightText(value, indices);
            } else if (key === 'author') {
                const authorEl = book.element.querySelector('.book-author');
                if (authorEl) authorEl.innerHTML = this.highlightText(value, indices);
            } else if (key === 'description') {
                const descEl = book.element.querySelector('.book-description');
                if (descEl) descEl.innerHTML = this.highlightText(value, indices);
            } else if (key === 'category') {
                const catEl = book.element.querySelector('.book-category');
                if (catEl) catEl.innerHTML = this.highlightText(value, indices);
            }
        });
    }
    resetHighlighting(book) {
        const titleEl = book.element.querySelector('.book-title');
        const authorEl = book.element.querySelector('.book-author');
        const descEl = book.element.querySelector('.book-description');
        const catEl = book.element.querySelector('.book-category');

        if (titleEl) titleEl.textContent = book.title;
        if (authorEl) authorEl.textContent = book.author;
        if (descEl) descEl.textContent = book.description;
        if (catEl) catEl.textContent = book.category;
    }

    updateUI(count) {
        this.bookCount.textContent = count;
        this.bookLabel.textContent = count === 1 ? 'book' : 'books';

        if (count === 0) {
            this.noResults.classList.remove('hidden');
            this.resultsCount.classList.add('hidden');
        } else {
            this.noResults.classList.add('hidden');
            this.resultsCount.classList.remove('hidden');
        }
    }

    filterBooks(searchTerm) {
        let filteredBooks = this.booksData;
        let matchedIndices = new Map();

        if (searchTerm && searchTerm.trim().length >= 2) {
            const results = this.fuse.search(searchTerm);
            filteredBooks = results.map(result => {
                if (result.matches) {
                    matchedIndices.set(result.item.id, result.matches);
                }
                return result.item;
            });
        }

        if (this.currentCategory !== 'all') {
            filteredBooks = filteredBooks.filter(book =>
                book.categoryId === this.currentCategory
            );
        }
        this.allBookCards.forEach(card => {
            card.classList.add('hidden');
            const swiperSlide = card.closest('.swiper-slide');
            if (swiperSlide) {
                swiperSlide.classList.add('hidden');
            }
        });

        filteredBooks.forEach(book => {
            book.element.classList.remove('hidden');
            const swiperSlide = book.element.closest('.swiper-slide');
            if (swiperSlide) {
                swiperSlide.classList.remove('hidden');
            }
            const matches = matchedIndices.get(book.id);
            this.applyHighlighting(book, matches, searchTerm);
        });

        this.updateUI(filteredBooks.length);
        if (typeof window.updateBooksCarousel === 'function') {
            window.updateBooksCarousel();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const initialCategory = urlParams.get('category') || 'all';

    new BookSearch({
        initialCategory: initialCategory
    });
});
