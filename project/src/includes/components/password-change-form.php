<?php
$animationDelay = $animationDelay ?? '250ms';
?>
<div class="card animate-slide-up" style="animation-delay: <?php echo htmlspecialchars($animationDelay); ?>;">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-black mb-6">Change Password</h2>

        <form action="change-password.php" method="POST" class="space-y-5">
            <div class="form-group">
                <label for="old_password" class="form-label">Current Password</label>
                <input
                    type="password"
                    id="old_password"
                    name="current_password"
                    class="form-input"
                    required
                >
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">New Password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="form-input"
                    required
                    minlength="6"
                >
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-input"
                    required
                    minlength="6"
                >
            </div>

            <div class="pt-2">
                <button type="submit" class="btn btn-primary">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>
