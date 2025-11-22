document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('ratingInput');

    if (ratingStars.length > 0 && ratingInput) {
        const initialRating = parseInt(ratingInput.value) || 0;
        updateStarDisplay(initialRating);

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                updateStarDisplay(rating);
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                updateStarDisplay(rating);
            });
        });
        
        document.getElementById('ratingStars').addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value) || 0;
            updateStarDisplay(currentRating);
        });
    }
    
    function updateStarDisplay(rating) {
        ratingStars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
    
    const reviewForm = document.getElementById('reviewForm');

    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const rating = formData.get('rating');

            if (!rating || rating < 1 || rating > 5) {
                notifications.warning('Please select a rating before submitting');
                return;
            }

            try {
                const response = await fetch('submit-review.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.success('Review submitted successfully!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    notifications.error(result.message || 'Failed to submit review');
                }
            } catch (error) {
                notifications.error('An error occurred while submitting your review');
            }
        });
    }
    
    const deleteButtons = document.querySelectorAll('.delete-review-btn');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete your review?')) {
                return;
            }

            const reviewId = this.dataset.reviewId;
            const formData = new FormData();
            formData.append('review_id', reviewId);

            try {
                const response = await fetch('delete-review.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    notifications.success('Review deleted successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    notifications.error(result.message || 'Failed to delete review');
                }
            } catch (error) {
                notifications.error('An error occurred while deleting your review');
            }
        });
    });

    const helpfulButtons = document.querySelectorAll('.helpful-btn');

    helpfulButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const reviewId = this.dataset.reviewId;

            const formData = new FormData();
            formData.append('review_id', reviewId);

            try {
                const response = await fetch('toggle-helpful.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    const countSpan = this.querySelector('.helpful-count');
                    let currentCount = parseInt(countSpan.textContent) || 0;

                    if (result.action === 'marked') {
                        countSpan.textContent = currentCount + 1;
                        this.classList.add('text-black');
                        this.classList.remove('text-gray-600');
                    } else {
                        countSpan.textContent = Math.max(0, currentCount - 1);
                        this.classList.remove('text-black');
                        this.classList.add('text-gray-600');
                    }
                } else {
                    notifications.error(result.message || 'Failed to update helpful status');
                }
            } catch (error) {
                notifications.error('An error occurred');
            }
        });
    });
});

