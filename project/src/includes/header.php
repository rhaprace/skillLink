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

        /* Mobile menu styles */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            background: white;
            border-bottom: 1px solid #E0E0E0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 999;
            max-height: calc(100vh - 64px);
            overflow-y: auto;
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu-link {
            display: block;
            padding: 1rem 1.5rem;
            color: #000;
            font-weight: 500;
            transition: background-color 0.2s;
            border-bottom: 1px solid #F0F0F0;
        }

        .mobile-menu-link:hover {
            background-color: #FAFAFA;
        }

        .mobile-menu-link.active {
            background-color: #F5F5F5;
            color: #000;
            font-weight: 600;
        }
    </style>
  </head>
  <body class="bg-white min-h-screen antialiased">
    <?php
    require_once __DIR__ . '/components/notification-drawer.php';

    // Determine current page for active state
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="nav">
        <div class="container-custom">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <span class="text-xl font-bold text-black">SkillLink</span>
                    </a>
                </div>
                <div class="flex items-center gap-2 md:gap-4 lg:gap-6">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="books.php" class="nav-link <?php echo $currentPage === 'books.php' ? 'nav-link-active' : ''; ?>">Books</a>
                        <a href="admin/dashboard.php" class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">Admin Panel</span>
                        </a>
                        <a href="admin/dashboard.php" class="lg:hidden btn btn-ghost btn-sm whitespace-nowrap">
                            Admin
                        </a>
                        <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-black"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                            <span class="px-2 py-0.5 bg-black text-white text-xs font-medium rounded-full">
                                <?php echo ucfirst(str_replace('_', ' ', $_SESSION['admin_role'] ?? 'admin')); ?>
                            </span>
                        </div>
                        <a href="admin/logout.php" class="btn btn-ghost btn-sm whitespace-nowrap">
                            Logout
                        </a>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <a href="index.php" class="nav-link hidden sm:block <?php echo $currentPage === 'index.php' ? 'nav-link-active' : ''; ?>">Dashboard</a>
                        <a href="books.php" class="nav-link <?php echo $currentPage === 'books.php' ? 'nav-link-active' : ''; ?>">Books</a>
                        <a href="my-library.php" class="nav-link hidden md:block <?php echo $currentPage === 'my-library.php' ? 'nav-link-active' : ''; ?>">Library</a>
                        <a href="bookmarks.php" class="nav-link hidden lg:block <?php echo $currentPage === 'bookmarks.php' ? 'nav-link-active' : ''; ?>">Bookmarks</a>
                        <a href="archive.php" class="nav-link hidden lg:block <?php echo $currentPage === 'archive.php' ? 'nav-link-active' : ''; ?>">Archive</a>

                        <!-- Mobile Menu Toggle Button -->
                        <button id="mobileMenuToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Toggle mobile menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

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

    <!-- Mobile Menu for logged-in users -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div id="mobileMenu" class="mobile-menu">
        <a href="index.php" class="mobile-menu-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
            Dashboard
        </a>
        <a href="books.php" class="mobile-menu-link <?php echo $currentPage === 'books.php' ? 'active' : ''; ?>">
            Books
        </a>
        <a href="my-library.php" class="mobile-menu-link <?php echo $currentPage === 'my-library.php' ? 'active' : ''; ?>">
            My Library
        </a>
        <a href="bookmarks.php" class="mobile-menu-link <?php echo $currentPage === 'bookmarks.php' ? 'active' : ''; ?>">
            Bookmarks
        </a>
        <a href="archive.php" class="mobile-menu-link <?php echo $currentPage === 'archive.php' ? 'active' : ''; ?>">
            Reading Archive
        </a>
        <a href="profile.php" class="mobile-menu-link <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
            Profile
        </a>
    </div>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenu = document.getElementById('mobileMenu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('active');

                    // Update icon
                    const icon = this.querySelector('svg path');
                    if (mobileMenu.classList.contains('active')) {
                        this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    } else {
                        this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.remove('active');
                        mobileMenuToggle.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
                    }
                });

                // Close menu when clicking a link
                const menuLinks = mobileMenu.querySelectorAll('.mobile-menu-link');
                menuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.remove('active');
                        mobileMenuToggle.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
                    });
                });
            }
        });
    </script>
    <?php endif; ?>

    <main>
