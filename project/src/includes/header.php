<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle ?? 'SkillLink'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/notifications.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
  </head>
  <body class="bg-white min-h-screen antialiased">
    <?php require_once __DIR__ . '/components/notification-drawer.php'; ?>
    <nav class="nav">
        <div class="container-custom">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <span class="text-xl font-bold text-black">SkillLink</span>
                    </a>
                </div>
                <div class="flex items-center gap-2 md:gap-4 lg:gap-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php" class="nav-link hidden sm:block">Dashboard</a>
                        <a href="books.php" class="nav-link">Books</a>
                        <a href="my-library.php" class="nav-link hidden sm:block">Library</a>
                        <a href="profile.php" class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <span class="text-sm font-medium text-black"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        </a>
                        <a href="logout.php" class="btn btn-ghost btn-sm whitespace-nowrap">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">Login</a>
                        <a href="register.php" class="btn btn-primary btn-sm whitespace-nowrap">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
