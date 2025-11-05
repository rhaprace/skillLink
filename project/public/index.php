<?php
session_start();
$pageTitle = 'Home - SkillLink';
require_once '../src/includes/header.php';
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="h-screen overflow-hidden bg-white flex items-center">
        <div class="container-custom w-full">
            <div class="max-w-5xl mx-auto">
                <div class="mb-6 animate-fade-in">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-black mb-1">
                                Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </h1>
                            <p class="text-sm text-gray-600">Continue your learning journey</p>
                        </div>
                        <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="card animate-slide-up" style="animation-delay: 50ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Lessons Completed</p>
                            <p class="text-3xl font-bold text-black">0</p>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 100ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Quizzes Taken</p>
                            <p class="text-3xl font-bold text-black">0</p>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 150ms;">
                        <div class="p-4">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Average Score</p>
                            <p class="text-3xl font-bold text-black">0%</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="card animate-slide-up" style="animation-delay: 200ms;">
                        <div class="p-4">
                            <h3 class="font-semibold text-black mb-3">Account Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email</span>
                                    <span class="text-black font-medium"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Username</span>
                                    <span class="text-black font-medium"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card animate-slide-up" style="animation-delay: 250ms;">
                        <div class="p-4">
                            <h3 class="font-semibold text-black mb-3">Quick Actions</h3>
                            <div class="space-y-2">
                                <button class="w-full btn btn-primary btn-sm">Browse Lessons</button>
                                <button class="w-full btn btn-secondary btn-sm">Take a Quiz</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="h-screen overflow-hidden bg-white flex items-center">
        <div class="container-custom w-full">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div class="animate-fade-in">
                        <h1 class="text-5xl font-bold text-black mb-4 leading-tight">
                            Master New Skills with
                            <span class="block mt-2">SkillLink</span>
                        </h1>
                        <p class="text-lg text-gray-600 mb-8">
                            Access interactive lessons and quizzes designed to help you learn faster and retain more. Start your learning journey today.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="register.php" class="btn btn-primary">Get Started</a>
                            <a href="login.php" class="btn btn-secondary">Sign In</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 animate-slide-up">
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">500+</p>
                                <p class="text-sm text-gray-600">Lessons Available</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">200+</p>
                                <p class="text-sm text-gray-600">Practice Quizzes</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">10k+</p>
                                <p class="text-sm text-gray-600">Active Learners</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-6 text-center">
                                <p class="text-4xl font-bold text-black mb-2">95%</p>
                                <p class="text-sm text-gray-600">Success Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../src/includes/footer.php'; ?>
