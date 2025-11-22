<?php
if (!isset($book)) {
    return;
}

$hasProgress = isset($userProgressMap[$book['id']]);
$progress = $hasProgress ? $userProgressMap[$book['id']] : null;
?>

<div class="card card-hover book-card animate-slide-up view-transition overflow-hidden"
     data-book-id="<?php echo $book['id']; ?>"
     data-category-id="<?php echo $book['category_id'] ?? ''; ?>"
     data-title="<?php echo htmlspecialchars($book['title']); ?>"
     data-author="<?php echo htmlspecialchars($book['author'] ?? ''); ?>"
     data-description="<?php echo htmlspecialchars($book['description']); ?>"
     data-category="<?php echo htmlspecialchars($book['category_name'] ?? ''); ?>"
     style="animation-delay: <?php echo 200 + ($index * 50); ?>ms;">
    <?php
    if (!empty($book['cover_image']) && trim($book['cover_image']) !== '') {
        $coverImage = $book['cover_image'];
    } else {
        $placeholderNum = (($book['id'] - 1) % 4) + 1;
        $coverImage = 'placeholder-' . $placeholderNum . '.jpg';
    }
    ?>
    <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
        <img
            src="assets/images/book-covers/<?php echo htmlspecialchars($coverImage); ?>"
            alt="<?php echo htmlspecialchars($book['title']); ?> cover"
            class="w-full h-full object-cover"
            onerror="this.src='assets/images/book-covers/placeholder-1.jpg'"
        >
        <!-- DEBUG: Book ID: <?php echo $book['id']; ?>, Calc: (<?php echo $book['id']; ?> - 1) % 4 + 1 = <?php echo $placeholderNum; ?>, Image: <?php echo $coverImage; ?> -->
    </div>

    <div class="p-6 relative">
        <div class="mb-3 flex items-start justify-between gap-2" style="min-height: 28px;">
            <div class="flex flex-wrap items-center gap-2">
                <?php if (!empty($book['category_name'])): ?>
                    <span class="book-category inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                        <?php echo htmlspecialchars($book['category_name']); ?>
                    </span>
                <?php endif; ?>

                <?php if ($hasProgress): ?>
                    <?php if ($progress['status'] === 'completed'): ?>
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                            Read completed
                        </span>
                    <?php elseif ($progress['status'] === 'in_progress'): ?>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                            Learning in progress
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($userId): ?>
                <button
                    class="bookmark-btn flex-shrink-0 p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                    data-book-id="<?php echo $book['id']; ?>"
                    data-bookmarked="<?php echo isset($userBookmarkIds) && in_array($book['id'], $userBookmarkIds) ? 'true' : 'false'; ?>"
                    title="<?php echo isset($userBookmarkIds) && in_array($book['id'], $userBookmarkIds) ? 'Remove bookmark' : 'Add bookmark'; ?>">
                    <svg class="w-5 h-5 bookmark-icon" fill="<?php echo isset($userBookmarkIds) && in_array($book['id'], $userBookmarkIds) ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>
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
                    <span class="text-gray-600">Progress</span>
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
                <?php echo $book['estimated_duration']; ?> mins
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
                    Read
                </a>
            <?php else: ?>
                <a href="start-book.php?id=<?php echo $book['id']; ?>"
                   class="btn btn-primary btn-sm w-full">
                    Learn
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php"
               class="btn btn-secondary btn-sm w-full">
                Login to Read
            </a>
        <?php endif; ?>
    </div>
</div>

