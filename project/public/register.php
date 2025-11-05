<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/AuthController.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Register - SkillLink';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController($pdo);
    $result = $authController->register($_POST);

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
    <link rel="stylesheet" href="../src/loader/index.css">
</head>
<body class="bg-white md:bg-black min-h-screen antialiased">

<div class="min-h-screen flex flex-col md:flex-row">
    <div class="hidden md:flex md:w-2/5 bg-white p-8 lg:p-12 items-center border-r border-gray-200 overflow-y-auto">
        <div class="w-full max-w-sm mx-auto">
            <div class="mb-6 lg:mb-8 animate-fade-in">
                <a href="index.php" class="text-2xl lg:text-3xl font-bold text-black transition-colors hover:opacity-80">SkillLink</a>
            </div>
            <div class="mb-6 lg:mb-8 animate-fade-in" style="animation-delay: 100ms;">
                <h1 class="text-3xl lg:text-4xl font-bold text-black mb-2">Join SkillLink</h1>
                <p class="text-gray-600 text-base lg:text-lg">Start your learning journey today</p>
            </div>

            <div class="space-y-4 lg:space-y-6 text-sm">
                <div class="border-l-2 border-black pl-4 animate-slide-up" style="animation-delay: 200ms;">
                    <p class="font-semibold text-black mb-1">500+ Lessons</p>
                    <p class="text-gray-600">Access comprehensive learning materials</p>
                </div>
                <div class="border-l-2 border-black pl-4 animate-slide-up" style="animation-delay: 300ms;">
                    <p class="font-semibold text-black mb-1">Interactive Quizzes</p>
                    <p class="text-gray-600">Test and reinforce your knowledge</p>
                </div>
                <div class="border-l-2 border-black pl-4 animate-slide-up" style="animation-delay: 400ms;">
                    <p class="font-semibold text-black mb-1">Track Progress</p>
                    <p class="text-gray-600">Monitor your learning achievements</p>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-3/5 bg-gray-50 flex items-center justify-center overflow-y-auto py-8 md:py-0">
        <div class="w-full max-w-md px-6 py-8 md:p-12">
            <div class="mb-6 md:mb-8 animate-fade-in">
                <a href="index.php" class="text-2xl md:text-3xl font-bold text-black transition-colors hover:opacity-80">SkillLink</a>
            </div>

            <div class="mb-6 md:mb-8 animate-fade-in">
                <h2 class="text-2xl md:text-3xl font-bold text-black mb-2">Create Account</h2>
                <p class="text-gray-600 text-sm md:text-base">Start your learning journey today</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error mb-6 animate-slide-up" style="animation-delay: 150ms;">
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success mb-6 animate-slide-up">
                    <div>
                        <span><?php echo htmlspecialchars($success); ?></span>
                        <a href="login.php" class="underline font-semibold ml-1">Login here</a>
                    </div>
                </div>
            <?php endif; ?>

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
                    <input type="checkbox" id="terms" class="form-checkbox mt-0.5" required>
                    <label for="terms" class="text-xs sm:text-sm text-gray-600">
                        I agree to the <a href="#" class="text-black font-medium hover:underline">Terms of Service</a> and <a href="#" class="text-black font-medium hover:underline">Privacy Policy</a>
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

</body>
</html>
