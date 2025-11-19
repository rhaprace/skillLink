<?php
if (!isset($title) || !isset($value)) {
    return;
}

$icon = $icon ?? '';
$color = $color ?? 'black';
$animationDelay = $animationDelay ?? '0ms';
?>

<div class="card animate-slide-up" style="animation-delay: <?php echo htmlspecialchars($animationDelay); ?>;">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1"><?php echo htmlspecialchars($title); ?></p>
                <p class="text-3xl font-bold text-<?php echo htmlspecialchars($color); ?>">
                    <?php echo htmlspecialchars($value); ?>
                </p>
            </div>
            <?php if ($icon): ?>
                <div class="text-gray-400">
                    <?php echo $icon; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

