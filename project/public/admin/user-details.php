<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

$userId = $_GET['id'] ?? null;

if (!$userId) {
    header('Location: users.php');
    exit();
}

$adminController = new AdminController($pdo);
$userDetails = $adminController->getUserDetails($userId);

if (!$userDetails) {
    header('Location: users.php?error=' . urlencode('User not found'));
    exit();
}

$user = $userDetails['user'];
$pageTitle = htmlspecialchars($user['username']) . ' - User Details - Admin';

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-6">
    <a href="users.php" class="text-gray-600 hover:text-black mb-4 inline-block">
        ← Back to Users
    </a>
    <h1 class="text-3xl font-bold text-black mb-2">User Details</h1>
    <p class="text-gray-600">Viewing information for <?php echo htmlspecialchars($user['username']); ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-1">Books Started</p>
            <p class="text-3xl font-bold text-black"><?php echo $userDetails['progress_count']; ?></p>
        </div>
    </div>
    <div class="card">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-1">Bookmarks</p>
            <p class="text-3xl font-bold text-black"><?php echo $userDetails['bookmark_count']; ?></p>
        </div>
    </div>
    <div class="card">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-1">Member Since</p>
            <p class="text-xl font-bold text-black">
                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="p-6">
            <h2 class="text-xl font-bold text-black mb-4">Account Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">User ID</p>
                    <p class="font-semibold text-black">#<?php echo $user['id']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Username</p>
                    <p class="font-semibold text-black"><?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-semibold text-black"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="font-semibold text-black">
                        <?php echo date('M j, Y g:i A', strtotime($user['updated_at'])); ?>
                    </p>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                    Edit User
                </a>
                <button onclick="showDeleteUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" 
                        class="btn btn-danger btn-sm">
                    Delete User
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="p-6">
            <h2 class="text-xl font-bold text-black mb-4">Recent Activity</h2>
            <?php if (empty($userDetails['recent_progress'])): ?>
                <p class="text-gray-500 text-center py-8">No activity yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($userDetails['recent_progress'] as $progress): ?>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-black text-sm"><?php echo htmlspecialchars($progress['title']); ?></p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-600">
                                    <?php echo ucfirst($progress['status']); ?>
                                </span>
                                <span class="text-xs font-semibold text-black">
                                    <?php echo round($progress['progress_percentage']); ?>%
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('deleteUserModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete User</h3>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete user <strong id="deleteUserName"></strong>?
            </p>
            <form method="POST" action="delete-user.php">
                <input type="hidden" name="user_id" id="deleteUserId">
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1">Delete</button>
                    <button type="button" onclick="Modal.hide('deleteUserModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeleteUserModal(userId, username) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = username;
    Modal.show('deleteUserModal');
}
</script>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

