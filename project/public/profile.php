<?php
session_start();

require_once '../src/config/database.php';
require_once '../src/controllers/UserController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userController = new UserController($pdo);
$user = $userController->getProfile($_SESSION['user_id']);

if (!$user) {
    header('Location: logout.php');
    exit();
}

$pageTitle = 'My Profile - SkillLink';
require_once '../src/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container-custom">
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account settings and preferences</p>
        </div>

        <?php require_once '../src/includes/components/profile-stats.php'; ?>

        <div class="space-y-6">
            <?php
            $animationDelay = '200ms';
            require_once '../src/includes/components/profile-edit-form.php';
            ?>

            <?php
            $animationDelay = '250ms';
            require_once '../src/includes/components/password-change-form.php';
            ?>

            <?php
            $animationDelay = '300ms';
            require_once '../src/includes/components/danger-zone.php';
            ?>
        </div>
    </div>
</div>

<?php require_once '../src/includes/components/delete-account-modal.php'; ?>

<?php require_once '../src/includes/footer.php'; ?>

