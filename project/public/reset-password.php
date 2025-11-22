<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/PasswordResetController.php';

if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Reset Password - SkillLink';
$error = '';
$token = $_GET['token'] ?? '';

$passwordResetController = new PasswordResetController($pdo);
$tokenValid = false;

if ($token) {
    $tokenValid = $passwordResetController->validateToken($token);
    if (!$tokenValid) {
        $error = 'Invalid or expired reset link. Please request a new password reset.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    $result = $passwordResetController->resetPassword($_POST);
    
    if ($result['success']) {
        header('Location: login.php?success=' . urlencode($result['message']));
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/notifications.css">
    <link rel="stylesheet" href="../src/loader/index.css">
</head>
<body class="bg-gray-50 min-h-screen antialiased">
    <?php require_once '../src/includes/components/notification-drawer.php'; ?>

<div class="min-h-screen flex flex-col md:flex-row">
    <?php
    $title = 'Create New Password';
    $subtitle = 'Your new password must be different from previously used passwords';
    $illustration = 'login.svg';
    $theme = 'dark';
    include '../src/includes/auth-sidebar.php';
    ?>

    <div class="w-full md:w-1/2 bg-gray-50 flex items-center justify-center overflow-y-auto py-8 md:py-0">
        <div class="w-full max-w-md px-6 py-8 md:p-10 lg:p-12">
            <div class="mb-8 md:mb-10 animate-fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-black mb-3">Reset Password</h2>
                <p class="text-gray-600 text-base md:text-lg">Enter your new password below</p>
            </div>

            <?php if (!$token || !$tokenValid): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg animate-slide-up">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-red-900 mb-1">Invalid Reset Link</h3>
                            <p class="text-sm text-red-700 mb-3"><?php echo htmlspecialchars($error); ?></p>
                            <a href="forgot-password.php" class="text-sm font-medium text-red-900 hover:underline">Request a new reset link →</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" class="space-y-4 md:space-y-5 animate-slide-up" style="animation-delay: 150ms;">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input
                            class="form-input"
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Min. 6 characters"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                        <input
                            class="form-input"
                            id="confirm_password"
                            type="password"
                            name="confirm_password"
                            placeholder="Re-enter your password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-full mt-6">Reset Password</button>
                </form>
            <?php endif; ?>

            <div class="mt-6 text-center text-sm md:text-base animate-fade-in" style="animation-delay: 300ms;">
                <a href="login.php" class="flex items-center justify-center gap-2 text-gray-600 hover:text-black transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to login</span>
                </a>
            </div>

            <div class="md:hidden mt-8 flex justify-center">
                <?php include '../src/loader/index.html'; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/notifications.js"></script>
<script src="assets/js/password-toggle.js"></script>
<?php if ($error && $tokenValid): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        notifications.error('<?php echo addslashes($error); ?>');
    });
</script>
<?php endif; ?>
</body>
</html>

