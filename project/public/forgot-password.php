<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/PasswordResetController.php';

if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Forgot Password - SkillLink';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passwordResetController = new PasswordResetController($pdo);
    $result = $passwordResetController->requestReset($_POST);
    
    if ($result['success']) {
        $success = $result['message'];
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
    $title = 'Reset Your Password';
    $subtitle = 'Enter your email address and we\'ll send you a link to reset your password';
    $illustration = 'login.svg';
    $theme = 'dark';
    include '../src/includes/auth-sidebar.php';
    ?>

    <div class="w-full md:w-1/2 bg-gray-50 flex items-center justify-center overflow-y-auto py-8 md:py-0">
        <div class="w-full max-w-md px-6 py-8 md:p-10 lg:p-12">
            <div class="mb-8 md:mb-10 animate-fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-black mb-3">Forgot Password?</h2>
                <p class="text-gray-600 text-base md:text-lg">No worries, we'll send you reset instructions</p>
            </div>

            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-slide-up">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-green-900 mb-1">Email Sent!</h3>
                            <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="forgot-password.php" class="space-y-4 md:space-y-5 animate-slide-up" style="animation-delay: 150ms;">
                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input
                        class="form-input"
                        id="email"
                        type="email"
                        name="email"
                        placeholder="you@example.com"
                        required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                    <p class="mt-2 text-sm text-gray-600">
                        We'll send a password reset link to this email address
                    </p>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-6">Send Reset Link</button>
            </form>

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
<?php if ($error): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        notifications.error('<?php echo addslashes($error); ?>');
    });
</script>
<?php endif; ?>
</body>
</html>

