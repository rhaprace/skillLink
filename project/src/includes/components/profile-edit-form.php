<?php
if (!isset($user)) {
    return;
}

$animationDelay = $animationDelay ?? '200ms';
?>
<div class="card animate-slide-up" style="animation-delay: <?php echo htmlspecialchars($animationDelay); ?>;">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-black mb-6">Profile Information</h2>

        <form action="edit-profile.php" method="POST" class="space-y-5">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?php echo htmlspecialchars($user['username']); ?>"
                    class="form-input"
                    required
                    pattern="[a-zA-Z0-9_]{3,50}"
                    title="3-50 characters, letters, numbers, and underscores only"
                >
                <p class="text-xs text-gray-500 mt-1">3-50 characters, letters, numbers, and underscores only</p>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    class="form-input"
                    required
                >
            </div>

            <div class="form-group">
                <label for="current_password" class="form-label">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="form-input"
                    required
                    placeholder="Enter your current password to save changes"
                >
                <p class="text-xs text-gray-500 mt-1">Required to update your profile</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
                <a href="index.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
