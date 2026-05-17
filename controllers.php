<?php
function loginCtrl($conn) {
    $error  = '';
    $prefill = $_COOKIE['remember_user'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if ($email === '' || $password === '') {
            $error = 'Please fill in both fields.';
        } else {
            $user = authUser($conn, $email, $password);
            if ($user) {
                $_SESSION['user'] = [
                    'id'       => $user['id'],
                    'name'     => $user['name'],
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                    'verified' => $user['is_verified']
                ];
                if ($remember) setcookie('remember_user', $email, time() + 86400 * 30, '/');
                else           setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=home');
                exit;
            }
            $error = 'Invalid email or password.';
        }
    }

    require 'views/login.php';
}

function registerCtrl($conn) {
    $error = $success = '';
    $old   = ['name' => '', 'email' => '', 'role' => 'user'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $role     = $_POST['role'] ?? 'user';
        $old      = compact('name', 'email', 'role');

        if ($name === '' || $email === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            if (registerUser($conn, $name, $email, $password, $role)) {
                $success = 'Account created! Please wait for admin approval.';
                $old     = ['name' => '', 'email' => '', 'role' => 'user'];
            } else {
                $error = 'Email already exists or registration failed.';
            }
        }
    }

    require 'views/register.php';
}

function homeCtrl($conn) {
    $user  = $_SESSION['user'] ?? null;
    $posts = [];

    if ($user && $user['verified']) {
        $posts = getApprovedPosts($conn, 6, 0);
    }

    require 'views/home.php';
}

/* ============== Wishlist ============== */
function wishlistCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    $user   = $_SESSION['user'];
    $userId = $user['id'];
    $items  = getUserWishlist($conn, $userId);

    require 'views/wishlist.php';
}

/* ============== AJAX: Wishlist Add ============== */
function ajaxWishlistAdd($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    if (addToWishlist($conn, $userId, $postId)) {
        echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Already in wishlist or failed']);
    }
    exit;
}

/* ============== AJAX: Wishlist Remove ============== */
function ajaxWishlistRemove($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    if (removeFromWishlist($conn, $userId, $postId)) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to remove']);
    }
    exit;
}

/* ============== AJAX: Wishlist Check ============== */
function ajaxWishlistCheck($conn) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid post ID']);
        exit;
    }

    echo json_encode(['in_wishlist' => isInWishlist($conn, $userId, $postId)]);
    exit;
}

function profileCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $user   = getUserById($conn, $userId);
    $error  = $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name            = trim($_POST['name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $picture         = null;

        // File upload
        if (!empty($_FILES['picture']['name'])) {
            $file    = $_FILES['picture'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed)) {
                $error = 'Only JPG, PNG, GIF allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'File size must be under 2MB.';
            } else {
                $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
                $picture = 'uploads/' . uniqid() . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], $picture)) {
                    $error   = 'File upload failed.';
                    $picture = null;
                }
            }
        }

        // Validate inputs
        if ($name === '')                              $error = 'Name is required.';
        if ($email === '')                             $error = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Invalid email format.';

        // Password change
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $error = 'New password must be 8+ characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match.';
            }
        }

        if ($error === '') {
            updateUserProfile($conn, $userId, $name, $email,
                $newPassword !== '' ? $newPassword : null,
                $picture);
            $success = 'Profile updated successfully!';
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            $user = getUserById($conn, $userId);
        }
    }

    require 'views/profile.php';
}