<?php
if (!isset($currentPage) || !isset($totalPages) || $totalPages <= 1) {
    return;
}
?>

<div class="mt-8 flex justify-center animate-fade-in pagination-container">
    <div class="card p-4">
        <div class="flex items-center gap-2">
            <?php if ($currentPage > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                    Previous
                </a>
            <?php else: ?>
                <span class="px-4 py-2 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed font-medium">
                    Previous
                </span>
            <?php endif; ?>

            <div class="flex gap-1">
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                if ($startPage > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" 
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        1
                    </a>
                    <?php if ($startPage > 2): ?>
                        <span class="px-3 py-2 text-gray-400">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="px-3 py-2 bg-black text-white rounded-lg font-medium">
                            <?php echo $i; ?>
                        </span>
                    <?php else: ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                           class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="px-3 py-2 text-gray-400">...</span>
                    <?php endif; ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>" 
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <?php echo $totalPages; ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                    Next
                </a>
            <?php else: ?>
                <span class="px-4 py-2 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed font-medium">
                    Next
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
