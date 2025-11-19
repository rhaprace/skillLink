<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

$pageTitle = 'Users Management - Admin - SkillLink';
$adminController = new AdminController($pdo);

$search = $_GET['search'] ?? null;
$users = $adminController->getAllUsers($search);

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-black mb-2">Users Management</h1>
    <p class="text-gray-600">Manage all registered users</p>
</div>

<div class="card mb-6">
    <div class="p-6">
        <form method="GET" action="" class="flex gap-3">
            <input
                type="text"
                name="search"
                placeholder="Search by username or email..."
                class="form-input flex-1"
                value="<?php echo htmlspecialchars($search ?? ''); ?>"
            >
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search): ?>
                <a href="users.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">
                            No users found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?php echo $user['id']; ?></td>
                            <td class="font-semibold"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="text-sm text-gray-500">
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="user-details.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        View
                                    </a>
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        Edit
                                    </a>
                                    <button 
                                        onclick="showDeleteUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')"
                                        class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('deleteUserModal', event)">
    <div class="card max-w-md w-full mx-4 animate-scale-up" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete User</h3>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete user <strong id="deleteUserName"></strong>? 
                This will permanently delete their account, progress, and bookmarks.
            </p>
            <form id="deleteUserForm" method="POST" action="delete-user.php">
                <input type="hidden" name="user_id" id="deleteUserId">
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1">
                        Yes, Delete User
                    </button>
                    <button type="button" onclick="Modal.hide('deleteUserModal')" class="btn btn-secondary flex-1">
                        Cancel
                    </button>
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

