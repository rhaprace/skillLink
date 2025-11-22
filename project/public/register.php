<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/AuthController.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php?info=' . urlencode('You are already logged in.'));
    exit();
}

$pageTitle = 'Register - SkillLink';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController($pdo);
    $result = $authController->register($_POST);

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
    $title = 'Join SkillLink';
    $subtitle = 'Start your learning journey today and unlock your potential';
    $illustration = 'register.svg';
    $theme = 'light';
    include '../src/includes/auth-sidebar.php';
    ?>

    <div class="w-full md:w-1/2 bg-gray-50 flex items-center justify-center overflow-y-auto py-8 md:py-0">
        <div class="w-full max-w-md px-6 py-8 md:p-10 lg:p-12">
            <div class="mb-8 md:mb-10 animate-fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-black mb-3">Create Account</h2>
                <p class="text-gray-600 text-base md:text-lg">Start your learning journey today</p>
            </div>

            <form method="POST" action="register.php" class="space-y-4 md:space-y-5 animate-slide-up" style="animation-delay: 100ms;">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input
                            class="form-input"
                            id="username"
                            type="text"
                            name="username"
                            placeholder="johndoe"
                            required
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        >
                    </div>

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
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
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
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input
                            class="form-input"
                            id="confirm_password"
                            type="password"
                            name="confirm_password"
                            placeholder="Confirm password"
                            required
                        >
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-2">
                    <input type="checkbox" id="terms" name="terms" class="form-checkbox mt-0.5" required>
                    <label for="terms" class="text-xs sm:text-sm text-gray-600">
                        I agree to the <a href="terms.php" target="_blank" class="text-black font-medium hover:underline">Terms and Conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-6">Create Account</button>
            </form>

            <div class="mt-6 text-center animate-fade-in" style="animation-delay: 250ms;">
                <p class="text-sm md:text-base text-gray-600">
                    Already have an account?
                    <a href="login.php" class="text-black font-semibold hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/notifications.js"></script>
<script src="assets/js/password-toggle.js"></script>
<?php if ($error): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        notifications.error('<?php echo addslashes($error); ?>');
    });
</script>
<?php endif; ?>
<?php if ($success): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        notifications.success('<?php echo addslashes($success); ?> You can now login.', 7000);
    });
</script>
<?php endif; ?>
</body>
</html>
