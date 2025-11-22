<?php

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_role'])) {
    header('Location: ../login.php?error=' . urlencode('Please log in as an admin to access this page.'));
    exit();
}

function requireSuperAdmin() {
    if ($_SESSION['admin_role'] !== 'super_admin') {
        header('Location: ../admin/dashboard.php?error=' . urlencode('Access denied. Super admin privileges required.'));
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']);
}

function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}

