<?php
$animationDelay = $animationDelay ?? '300ms';
?>
<div class="card card-danger animate-slide-up" style="animation-delay: <?php echo htmlspecialchars($animationDelay); ?>;">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-red-600 mb-4">Danger Zone</h2>
        <p class="text-gray-600 mb-6">
            Once you delete your account, there is no going back. This will permanently delete your account,
            all your progress, and bookmarks.
        </p>

        <button
            type="button"
            onclick="showDeleteModal(event)"
            class="btn btn-danger">
            Delete Account
        </button>
    </div>
</div>
