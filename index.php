<?php
// ================================================================
// FRONT CONTROLLER - router + AJAX endpoint + logout
// Faculty compact MVC format
// ================================================================
session_start();

require 'config.php';
require 'models.php';
require 'controllers.php';

$page = $_GET['page'] ?? 'browse';

/* ------------- Logout ------------- */
if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();
    setcookie('remember_email', '', time() - 3600, '/');
    header('Location: index.php?page=browse');
    exit;
}

/* ------------- AJAX endpoint ------------- */
if ($page === 'ajax') {
    ajaxCtrl($conn);
    exit;
}

/* ------------- Dispatch ------------- */
switch ($page) {
    case 'login':
        loginCtrl($conn);
        break;
    case 'browse':
        browseCtrl($conn);
        break;
    case 'detail':
        detailCtrl($conn);
        break;
    case 'featured':
        featuredCtrl($conn);
        break;
    default:
        header('Location: index.php?page=browse');
        exit;
}

mysqli_close($conn);
?>
