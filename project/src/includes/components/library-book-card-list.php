<?php
if (!isset($item)) {
    return;
}
?>

<div class="card card-hover book-card animate-slide-up view-transition"
     style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-1">
                <?php if (!empty($item['category_name'])): ?>
                    <div class="mb-2">
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                            <?php echo htmlspecialchars($item['category_name']); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <h3 class="text-xl font-bold text-black mb-2">
                    <?php echo htmlspecialchars($item['title']); ?>
                </h3>

                <?php if (!empty($item['author'])): ?>
                    <p class="text-sm text-gray-600 mb-3">
                        by <?php echo htmlspecialchars($item['author']); ?>
                    </p>
                <?php endif; ?>

                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    <?php echo htmlspecialchars($item['description']); ?>
                </p>

                <div class="mb-3">
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

                <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                    <span>Last accessed: <?php echo date('M j, Y', strtotime($item['last_accessed'])); ?></span>
                    <span class="capitalize">Status: <?php echo str_replace('_', ' ', $item['status']); ?></span>
                    <?php if (!empty($item['completed_at'])): ?>
                        <span>Completed: <?php echo date('M j, Y', strtotime($item['completed_at'])); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <a href="book.php?id=<?php echo $item['book_id']; ?>"
                   class="btn btn-primary w-full text-sm whitespace-nowrap">
                    <?php echo $item['status'] === 'completed' ? 'Read Again' : 'Continue Reading'; ?>
                </a>
            </div>
        </div>
    </div>
</div>
