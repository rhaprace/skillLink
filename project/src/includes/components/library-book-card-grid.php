<?php
if (!isset($item)) {
    return;
}
?>

<div class="library-card-grid card card-hover animate-slide-up"
     style="animation-delay: <?php echo 150 + ($index * 30); ?>ms;">
    <div class="library-card-content">
        <div class="mb-3" style="min-height: 28px;">
            <?php if (!empty($item['category_name'])): ?>
                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                    <?php echo htmlspecialchars($item['category_name']); ?>
                </span>
            <?php endif; ?>
        </div>
        <h3 class="text-lg font-bold text-black mb-2 line-clamp-2" style="min-height: 3.5rem;">
            <?php echo htmlspecialchars($item['title']); ?>
        </h3>
        <div class="mb-3" style="min-height: 20px;">
            <?php if (!empty($item['author'])): ?>
                <p class="text-sm text-gray-600">
                    by <?php echo htmlspecialchars($item['author']); ?>
                </p>
            <?php endif; ?>
        </div>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2" style="min-height: 2.5rem;">
            <?php echo htmlspecialchars($item['description']); ?>
        </p>
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">Progress</span>
                <span class="font-semibold text-black">
                    <?php echo round($item['progress_percentage']); ?>%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-black h-2 rounded-full transition-all"
                     style="width: <?php echo $item['progress_percentage']; ?>%">
                </div>
            </div>
        </div>
        <div class="text-xs text-gray-500 space-y-1">
            <div>Last: <?php echo date('M j, Y', strtotime($item['last_accessed'])); ?></div>
            <div class="capitalize">Status: <?php echo str_replace('_', ' ', $item['status']); ?></div>
        </div>
    </div>

    <div class="library-card-footer">
        <a href="book.php?id=<?php echo $item['book_id']; ?>"
           class="btn btn-primary btn-sm w-full">
            <?php echo $item['status'] === 'completed' ? 'Read Again' : 'Continue Reading'; ?>
        </a>
    </div>
</div>
