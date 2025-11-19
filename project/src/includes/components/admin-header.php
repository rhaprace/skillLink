<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard - SkillLink'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/notifications.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-gray-50 antialiased">
    <?php require_once __DIR__ . '/notification-drawer.php'; ?>

    <div class="admin-layout">
        <?php require_once __DIR__ . '/admin-sidebar.php'; ?>

        <div class="admin-main">
            <header class="admin-header">
                <div class="flex items-center justify-between">
                    <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-4 ml-auto">
                        <span class="text-sm text-gray-600">
                            Welcome, <span class="font-semibold text-black"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        </span>
                        <span class="px-2 py-1 bg-black text-white text-xs font-medium rounded-full">
                            <?php echo ucfirst(str_replace('_', ' ', $_SESSION['admin_role'])); ?>
                        </span>
                    </div>
                </div>
            </header>

            <main class="admin-content">

