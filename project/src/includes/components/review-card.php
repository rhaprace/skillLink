<?php
if (!isset($review)) {
    return;
}

$isOwnReview = $userId && $review['user_id'] == $userId;
?>

<div class="review-card group relative p-6 rounded-xl border transition-all duration-300 hover:shadow-md"
     style="background-color: var(--color-bg-primary); border-color: var(--color-border-light);"
     data-review-id="<?php echo $review['id']; ?>">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
                <span class="font-semibold text-base" style="color: var(--color-text-primary);">
                    <?php echo htmlspecialchars($review['username']); ?>
                </span>
                <?php if ($isOwnReview): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border border-blue-200">
                        You
                    </span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex gap-0.5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg class="w-4 h-4 transition-colors <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <span class="text-xs font-medium" style="color: var(--color-text-tertiary);">
                    <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                </span>
            </div>
        </div>
        <?php if ($isOwnReview): ?>
            <button
                class="delete-review-btn opacity-0 group-hover:opacity-100 transition-all duration-200 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                style="color: var(--color-danger);"
                data-review-id="<?php echo $review['id']; ?>"
                aria-label="Delete review">
                Delete
            </button>
        <?php endif; ?>
    </div>
    <?php if (!empty($review['review_text'])): ?>
        <p class="text-base leading-relaxed mb-4" style="color: var(--color-text-secondary);">
            <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
        </p>
    <?php endif; ?>
    <?php if ($userId && !$isOwnReview): ?>
        <button
            class="helpful-btn inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
            style="color: var(--color-text-secondary);"
            data-review-id="<?php echo $review['id']; ?>"
            aria-label="Mark as helpful">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
            </svg>
            <span class="helpful-count font-semibold"><?php echo $review['helpful_count']; ?></span>
            <span>Helpful</span>
        </button>
    <?php elseif ($review['helpful_count'] > 0): ?>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-50"
             style="color: var(--color-text-secondary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
            </svg>
            <span class="font-semibold"><?php echo $review['helpful_count']; ?></span>
            <span>found this helpful</span>
        </div>
    <?php endif; ?>
</div>

