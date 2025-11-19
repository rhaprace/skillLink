<?php
session_start();

unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_role']);

session_destroy();

header('Location: ../login.php?success=' . urlencode('You have been logged out successfully'));
exit();

