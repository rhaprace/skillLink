<?php
if (!isset($book) || !isset($reviewController)) {
    return;
}

try {
    $ratingStats = $reviewController->getBookRatingStats($book['id']);
    $reviews = $reviewController->getBookReviews($book['id'], 10);
    $userReview = $userId ? $reviewController->getUserReview($userId, $book['id']) : null;

    $averageRating = $ratingStats['average_rating'] ? round($ratingStats['average_rating'], 1) : 0;
    $totalReviews = $ratingStats['total_reviews'] ?? 0;
} catch (Exception $e) {
    error_log("Review section error: " . $e->getMessage());
    ?>
    <div class="card p-8 animate-slide-up" style="animation-delay: 150ms;">
        <h2 class="text-2xl font-bold text-black mb-6">Ratings & Reviews</h2>
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <p class="text-gray-700 mb-4">
                <strong>Error loading reviews:</strong>
            </p>
            <code class="bg-gray-100 px-4 py-2 rounded text-sm block mb-4"><?php echo htmlspecialchars($e->getMessage()); ?></code>
            <p class="text-xs text-gray-500">
                Check the error above. If tables are missing, run <code>database-reviews.sql</code>
            </p>
        </div>
    </div>
    <?php
    return;
}
?>

<div class="card animate-slide-up" style="animation-delay: 150ms;">
    <div class="card-header">
        <h2 class="heading-2">Ratings & Reviews</h2>
    </div>
    <?php if ($totalReviews > 0): ?>
    <div class="card-body border-b" style="border-color: var(--color-border-light);">
        <div class="flex flex-col md:flex-row gap-8 md:gap-12">
            <div class="flex flex-col items-center md:items-start gap-3 md:min-w-[180px]">
                <div class="text-6xl font-bold tracking-tight" style="color: var(--color-text-primary);">
                    <?php echo $averageRating; ?>
                </div>
                <div class="flex items-center gap-1">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg class="w-5 h-5 transition-colors <?php echo $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <div class="text-sm font-medium" style="color: var(--color-text-secondary);">
                    <?php echo $totalReviews; ?> review<?php echo $totalReviews != 1 ? 's' : ''; ?>
                </div>
            </div>
            <div class="flex-1 space-y-3">
                <?php for ($star = 5; $star >= 1; $star--): ?>
                    <?php
                    $starKey = ['one_star', 'two_star', 'three_star', 'four_star', 'five_star'][$star - 1];
                    $count = $ratingStats[$starKey] ?? 0;
                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                    ?>
                    <div class="flex items-center gap-3 group">
                        <span class="text-sm font-medium w-14 text-right" style="color: var(--color-text-secondary);">
                            <?php echo $star; ?> star
                        </span>
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden transition-all">
                            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-2 rounded-full transition-all duration-500 ease-out"
                                 style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <span class="text-sm font-medium w-10 text-right" style="color: var(--color-text-secondary);">
                            <?php echo $count; ?>
                        </span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($totalReviews === 0): ?>
        <div class="card-body text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <h3 class="heading-4 mb-2">No reviews yet</h3>
            <p class="text-base mb-8" style="color: var(--color-text-secondary);">
                Be the first to review this book!
            </p>

            <?php if ($userId): ?>
                <div class="max-w-2xl mx-auto text-left">
                    <form id="reviewForm" class="space-y-6">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>
                            <div class="flex gap-1 justify-center md:justify-start" id="ratingStars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button"
                                            class="rating-star text-gray-300 hover:text-yellow-400 transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 rounded"
                                            data-rating="<?php echo $i; ?>"
                                            aria-label="Rate <?php echo $i; ?> stars">
                                        <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="reviewText" class="form-label">Your Review (Optional)</label>
                            <textarea
                                id="reviewText"
                                name="review_text"
                                rows="4"
                                class="form-input form-textarea"
                                placeholder="Share your thoughts about this book..."
                            ></textarea>
                        </div>

                        <div class="flex justify-center md:justify-start">
                            <button type="submit" class="btn btn-primary">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Log In to Review</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card-body border-b" style="border-color: var(--color-border-light);">
            <?php if ($userId): ?>
                <h3 class="heading-4 mb-6">
                    <?php echo $userReview ? 'Update Your Review' : 'Write a Review'; ?>
                </h3>
                <div class="max-w-2xl">
                    <form id="reviewForm" class="space-y-6">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>
                            <div class="flex gap-1" id="ratingStars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button"
                                            class="rating-star text-gray-300 hover:text-yellow-400 transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 rounded"
                                            data-rating="<?php echo $i; ?>"
                                            aria-label="Rate <?php echo $i; ?> stars">
                                        <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="<?php echo $userReview['rating'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="reviewText" class="form-label">Your Review (Optional)</label>
                            <textarea
                                id="reviewText"
                                name="review_text"
                                rows="4"
                                class="form-input form-textarea"
                                placeholder="Share your thoughts about this book..."
                            ><?php echo $userReview['review_text'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <?php echo $userReview ? 'Update Review' : 'Submit Review'; ?>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-base mb-6" style="color: var(--color-text-secondary);">
                        Please log in to write a review
                    </p>
                    <a href="login.php" class="btn btn-primary">Log In</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($reviews)): ?>
    <div class="card-body" id="reviewsList">
        <h3 class="heading-4 mb-6">Reviews</h3>
        <div class="space-y-6">
            <?php foreach ($reviews as $review): ?>
                <?php require 'review-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

