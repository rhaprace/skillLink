<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/controllers/AuthController.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Login - SkillLink';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController($pdo);
    $result = $authController->login($_POST);

    if ($result['success']) {
        header('Location: index.php');
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
    <link rel="stylesheet" href="../src/loader/index.css">
</head>
<body class="bg-white md:bg-black min-h-screen antialiased">

<div class="min-h-screen flex flex-col md:flex-row">
    <div class="hidden md:flex md:w-2/5 bg-black p-8 lg:p-12 items-center border-r border-gray-200 overflow-y-auto">
        <div class="w-full max-w-sm mx-auto">
            <div class="mb-8 lg:mb-12 animate-fade-in">
                <a href="index.php" class="text-2xl lg:text-3xl font-bold text-white transition-colors hover:opacity-80">SkillLink</a>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold mb-4 lg:mb-6 text-white animate-fade-in" style="animation-delay: 100ms;">Welcome Back</h1>
            <p class="text-base lg:text-lg text-gray-300 mb-6 lg:mb-8 animate-fade-in" style="animation-delay: 200ms;">
                Access your personalized learning dashboard. Track your progress, complete lessons, and test your knowledge with interactive quizzes.
            </p>
            <div class="space-y-3 lg:space-y-4 text-sm text-white">
                <div class="flex items-center gap-3 animate-slide-up" style="animation-delay: 300ms;">
                    <div class="w-6 h-6 border-2 border-white rounded-full flex items-center justify-center flex-shrink-0 text-xs">1</div>
                    <p class="leading-6">Learn at your own pace with engaging content</p>
                </div>
                <div class="flex items-center gap-3 animate-slide-up" style="animation-delay: 400ms;">
                    <div class="w-6 h-6 border-2 border-white rounded-full flex items-center justify-center flex-shrink-0 text-xs">2</div>
                    <p class="leading-6">Test your knowledge and track improvement</p>
                </div>
                <div class="flex items-center gap-3 animate-slide-up" style="animation-delay: 500ms;">
                    <div class="w-6 h-6 border-2 border-white rounded-full flex items-center justify-center flex-shrink-0 text-xs">3</div>
                    <p class="leading-6">Monitor your learning journey</p>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-3/5 bg-gray-50 flex items-center justify-center overflow-y-auto py-8 md:py-0">
        <div class="w-full max-w-md px-6 py-8 md:p-12">
            <div class="mb-6 md:mb-8 animate-fade-in">
                <a href="index.php" class="text-2xl md:text-3xl font-bold text-black transition-colors hover:opacity-80">SkillLink</a>
            </div>

            <div class="mb-6 md:mb-8 animate-fade-in" style="animation-delay: 100ms;">
                <h2 class="text-2xl md:text-3xl font-bold text-black mb-2">Sign In</h2>
                <p class="text-gray-600 text-sm md:text-base">Continue your learning journey</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error mb-6 animate-slide-up" style="animation-delay: 150ms;">
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-4 md:space-y-5 animate-slide-up" style="animation-delay: 200ms;">
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

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        class="form-input"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="form-checkbox">
                        <span class="text-gray-700">Remember me</span>
                    </label>
                    <a href="#" class="text-black font-medium hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-6">Sign In</button>
            </form>

            <div class="mt-6 text-center text-sm md:text-base animate-fade-in" style="animation-delay: 300ms;">
                <span class="text-gray-600">Don't have an account? </span>
                <a href="register.php" class="text-black font-semibold hover:underline">Create one</a>
            </div>

            <div class="md:hidden mt-8 flex justify-center">
                <?php include '../src/loader/index.html'; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
