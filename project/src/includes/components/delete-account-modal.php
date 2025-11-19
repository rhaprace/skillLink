<?php
$modalId = $modalId ?? 'deleteModal';
$formAction = $formAction ?? 'delete-account.php';
?>
<div id="<?php echo htmlspecialchars($modalId); ?>" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" 
     onclick="hideDeleteModal(event)">
    <div class="card max-w-md w-full mx-4 animate-scale-up" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete Account</h3>
            <p class="text-gray-600 mb-6">
                Are you absolutely sure? This action cannot be undone. All your data will be permanently deleted.
            </p>

            <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST">
                <div class="mb-4">
                    <label for="delete_password" class="form-label">Enter your password to confirm</label>
                    <input
                        type="password"
                        id="delete_password"
                        name="password"
                        class="form-input"
                        required
                        placeholder="Your password"
                    >
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1">
                        Yes, Delete My Account
                    </button>
                    <button type="button" 
                            onclick="hideDeleteModal()" 
                            class="btn btn-secondary flex-1">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
