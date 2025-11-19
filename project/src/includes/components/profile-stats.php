<?php
if (!isset($user)) {
    return;
}
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card animate-slide-up" style="animation-delay: 50ms;">
        <div class="p-4">
            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Member Since</p>
            <p class="text-xl font-bold text-black">
                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
            </p>
        </div>
    </div>
    <div class="card animate-slide-up" style="animation-delay: 100ms;">
        <div class="p-4">
            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Last Updated</p>
            <p class="text-xl font-bold text-black">
                <?php echo date('M j, Y', strtotime($user['updated_at'])); ?>
            </p>
        </div>
    </div>
    <div class="card animate-slide-up" style="animation-delay: 150ms;">
        <div class="p-4">
            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">User ID</p>
            <p class="text-xl font-bold text-black">
                #<?php echo str_pad($user['id'], 6, '0', STR_PAD_LEFT); ?>
            </p>
        </div>
    </div>
</div>
