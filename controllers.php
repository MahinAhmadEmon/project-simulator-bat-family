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
                if ($user['role'] === 'scout') {
                    header('Location: index.php?page=scout');
                } else {
                    header('Location: index.php?page=home');
                }
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

//Done by Ramim
//Ramim change-1
function scoutCtrl($conn) {
    
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $user = $_SESSION['user'];
    
    // Only scouts and admins can access scout panel
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
    
    // Get scout's posts
    $scoutId = $user['id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE scout_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $scoutId);
    mysqli_stmt_execute($stmt);
    $posts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    // Get pending requests
    $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE scout_id = ? ORDER BY requested_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $scoutId);
    mysqli_stmt_execute($stmt);
    $requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post_request'])) {
    $title = trim($_POST['title'] ?? '');
    $short_history = trim($_POST['short_history'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $genre = $_POST['genre'] ?? '';
    $cost_level = $_POST['cost_level'] ?? '';
    $travel_medium_info = trim($_POST['travel_medium_info'] ?? '');
    
    $errors = [];
    
    if (empty($title)) $errors[] = "Title is required";
    if (empty($short_history)) $errors[] = "Short history is required";
    if (empty($country)) $errors[] = "Country is required";
    if (empty($genre)) $errors[] = "Genre is required";
    if (empty($cost_level)) $errors[] = "Cost level is required";
    if (empty($travel_medium_info)) $errors[] = "Travel medium info is required";
    
    if (empty($errors)) {
        $post_data = json_encode([
            'title' => $title,
            'short_history' => $short_history,
            'country' => $country,
            'genre' => $genre,
            'cost_level' => $cost_level,
            'travel_medium_info' => $travel_medium_info
        ]);
        
        $stmt = mysqli_prepare($conn, "INSERT INTO post_requests (scout_id, post_data, status) VALUES (?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'is', $scoutId, $post_data);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Post request submitted successfully! Waiting for admin approval.";
        } else {
            $error = "Failed to submit request. Please try again.";
        }
        mysqli_stmt_close($stmt);
        } else {
        $error = implode(", ", $errors);
        }
}
    
    require 'views/scout.php';
}

//Ramim change-2
function scoutRequestsCtrl($conn) {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $user = $_SESSION['user'];
    
    // Only scouts and admins can access
    if ($user['role'] !== 'scout' && $user['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
    
    $scoutId = $user['id'];
    $message = '';
    $error = '';
    $editRequest = null;
    
    // Handle Edit - GET request to fetch data for editing
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $requestId = $_GET['edit'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'ii', $requestId, $scoutId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $editRequest = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$editRequest) {
            $error = "Request not found or cannot be edited.";
        }
    }
    
    // Handle Update (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request'])) {
        $requestId = $_POST['request_id'];
        $title = trim($_POST['title'] ?? '');
        $short_history = trim($_POST['short_history'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $genre = $_POST['genre'] ?? '';
        $cost_level = $_POST['cost_level'] ?? '';
        $travel_medium_info = trim($_POST['travel_medium_info'] ?? '');
        
        $post_data = json_encode([
            'title' => $title,
            'short_history' => $short_history,
            'country' => $country,
            'genre' => $genre,
            'cost_level' => $cost_level,
            'travel_medium_info' => $travel_medium_info
        ]);
        
        $stmt = mysqli_prepare($conn, "UPDATE post_requests SET post_data = ? WHERE id = ? AND scout_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'sii', $post_data, $requestId, $scoutId);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Request updated successfully!";
            $editRequest = null;
        } else {
            $error = "Failed to update request.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Handle Delete (POST for delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
        $requestId = $_POST['request_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, 'ii', $requestId, $scoutId);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Request deleted successfully!";
        } else {
            $error = "Failed to delete request.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Get all requests for this scout
    $stmt = mysqli_prepare($conn, "SELECT * FROM post_requests WHERE scout_id = ? and status = 'pending' ORDER BY requested_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $scoutId);
    mysqli_stmt_execute($stmt);
    $requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    require 'views/scoutrequests.php';
}

//Ramim till here