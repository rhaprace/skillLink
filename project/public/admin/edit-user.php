<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../src/models/User.php';

$userId = $_GET['id'] ?? null;

if (!$userId) {
    header('Location: users.php');
    exit();
}

$userModel = new User($pdo);
$user = $userModel->findById($userId);

if (!$user) {
    header('Location: users.php?error=' . urlencode('User not found'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    $adminController = new AdminController($pdo);
    $result = $adminController->updateUser($userId, $username, $email);
    
    if ($result['success']) {
        header('Location: users.php?success=' . urlencode($result['message']));
        exit();
    } else {
        header('Location: edit-user.php?id=' . $userId . '&error=' . urlencode($result['message']));
        exit();
    }
}

$pageTitle = 'Edit User - Admin - SkillLink';
require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-6">
    <a href="users.php" class="text-gray-600 hover:text-black mb-4 inline-block">
        ← Back to Users
    </a>
    <h1 class="text-3xl font-bold text-black mb-2">Edit User</h1>
    <p class="text-gray-600">Update user information</p>
</div>

<div class="max-w-2xl">
    <div class="card">
        <div class="p-6">
            <form method="POST" action="" class="space-y-5">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        value="<?php echo htmlspecialchars($user['username']); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        required
                    >
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">
                        Update User
                    </button>
                    <a href="users.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

