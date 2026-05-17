<?php
session_start();

require 'config.php';
require 'models.php';
require 'controllers.php';

$page = $_GET['page'] ?? 'login';

/* ------------- Logout ------------- */
if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header('Location: index.php?page=login');
    exit;
}

/* ------------- Auth gates ------------- */
$publicPages = ['login', 'register'];

// Already logged in -> skip login/register
if (in_array($page, $publicPages) && isset($_SESSION['user'])) {
    header('Location: index.php?page=home');
    exit;
}

// Public pages
if ($page === 'login')    { loginCtrl($conn);    exit; }
if ($page === 'register') { registerCtrl($conn); exit; }

// Protected pages require login
if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}
    
if ($page === 'home')     homeCtrl($conn);
elseif ($page === 'profile')  profileCtrl($conn);
else {
    header('Location: index.php?page=home');
    exit;
}

mysqli_close($conn);
?>
