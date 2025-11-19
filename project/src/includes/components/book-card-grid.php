<?php
if (!isset($book)) {
    return;
}

$hasProgress = isset($userProgressMap[$book['id']]);
$progress = $hasProgress ? $userProgressMap[$book['id']] : null;
?>

<div class="card card-hover book-card animate-slide-up view-transition"
     data-book-id="<?php echo $book['id']; ?>"
     data-category-id="<?php echo $book['category_id'] ?? ''; ?>"
     data-title="<?php echo htmlspecialchars($book['title']); ?>"
     data-author="<?php echo htmlspecialchars($book['author'] ?? ''); ?>"
     data-description="<?php echo htmlspecialchars($book['description']); ?>"
     data-category="<?php echo htmlspecialchars($book['category_name'] ?? ''); ?>"
     style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">
    <div class="p-6">
        <div class="mb-3 flex items-center justify-between gap-2" style="min-height: 28px;">
            <?php if (!empty($book['category_name'])): ?>
                <span class="book-category inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                    <?php echo htmlspecialchars($book['category_name']); ?>
                </span>
            <?php endif; ?>

            <?php if ($hasProgress): ?>
                <?php if ($progress['status'] === 'completed'): ?>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                        ✓ Completed
                    </span>
                <?php elseif ($progress['status'] === 'in_progress'): ?>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                        In Progress
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <h3 class="book-title text-lg font-bold text-black mb-2 line-clamp-2" style="min-height: 3.5rem;">
            <?php echo htmlspecialchars($book['title']); ?>
        </h3>

        <div class="mb-3" style="min-height: 20px;">
            <?php if (!empty($book['author'])): ?>
                <p class="text-sm text-gray-600">
                    by <span class="book-author"><?php echo htmlspecialchars($book['author']); ?></span>
                </p>
            <?php endif; ?>
        </div>

        <p class="book-description text-gray-600 text-sm mb-4 line-clamp-2" style="min-height: 2.5rem;">
            <?php echo htmlspecialchars($book['description']); ?>
        </p>

        <?php if ($hasProgress && $progress['progress_percentage'] > 0): ?>
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Your Progress</span>
                    <span class="font-semibold text-black">
                        <?php echo round($progress['progress_percentage']); ?>%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-black h-2 rounded-full transition-all"
                         style="width: <?php echo $progress['progress_percentage']; ?>%">
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="mb-4" style="min-height: 52px;"></div>
        <?php endif; ?>

        <div class="flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1">
                ⏱️ <?php echo $book['estimated_duration']; ?> min
            </span>
            <span class="book-difficulty capitalize px-2 py-1 bg-gray-50 rounded">
                <?php echo htmlspecialchars($book['difficulty_level']); ?>
            </span>
        </div>
    </div>

    <div class="p-4">
        <?php if ($userId): ?>
            <?php if ($hasProgress): ?>
                <a href="book.php?id=<?php echo $book['id']; ?>"
                   class="btn btn-primary btn-sm w-full">
                    Continue Reading
                </a>
            <?php else: ?>
                <a href="start-book.php?id=<?php echo $book['id']; ?>"
                   class="btn btn-primary btn-sm w-full">
                    Start Learning
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php"
               class="btn btn-secondary btn-sm w-full">
                Login to Start
            </a>
        <?php endif; ?>
    </div>
</div>

