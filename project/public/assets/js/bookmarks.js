document.addEventListener('DOMContentLoaded', () => {
    const bookmarkButtons = document.querySelectorAll('.bookmark-btn');

    bookmarkButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const bookId = button.getAttribute('data-book-id');
            const isBookmarked = button.getAttribute('data-bookmarked') === 'true';

            button.disabled = true;

            try {
                const response = await fetch('toggle-bookmark.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: parseInt(bookId)
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const icon = button.querySelector('.bookmark-icon');
                    
                    if (result.bookmarked) {
                        button.setAttribute('data-bookmarked', 'true');
                        button.setAttribute('title', 'Remove bookmark');
                        icon.setAttribute('fill', 'currentColor');
                        button.classList.add('bookmarked');
                    } else {
                        button.setAttribute('data-bookmarked', 'false');
                        button.setAttribute('title', 'Add bookmark');
                        icon.setAttribute('fill', 'none');
                        button.classList.remove('bookmarked');
                    }

                    if (typeof notifications !== 'undefined') {
                        notifications.success(result.message, 3000);
                    }
                } else {
                    if (typeof notifications !== 'undefined') {
                        notifications.error(result.message || 'Failed to update bookmark', 3000);
                    } else {
                        alert(result.message || 'Failed to update bookmark');
                    }
                }
            } catch (error) {
                console.error('Error toggling bookmark:', error);
                if (typeof notifications !== 'undefined') {
                    notifications.error('An error occurred. Please try again.', 3000);
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                button.disabled = false;
            }
        });
    });
});

