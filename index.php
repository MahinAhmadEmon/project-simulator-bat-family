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

/* ------------- AJAX Endpoints ------------- */
if ($page === 'ajax') {
    $type = $_GET['type'] ?? '';
    if ($type === 'wishlist-add') {
        ajaxWishlistAdd($conn);
    } elseif ($type === 'wishlist-remove') {
        ajaxWishlistRemove($conn);
    } elseif ($type === 'wishlist-check') {
        ajaxWishlistCheck($conn);
    }elseif ($type === 'change-request') {
        ajaxChangeRequest($conn);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
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
elseif ($page === 'wishlist') wishlistCtrl($conn);
elseif ($page === 'scout') scoutCtrl($conn);
elseif ($page === 'scoutrequests') scoutRequestsCtrl($conn);
elseif ($page === 'scoutapprovedposts') approvedPostsCtrl($conn);
else {
    header('Location: index.php?page=home');
    exit;
}

mysqli_close($conn);
?>
