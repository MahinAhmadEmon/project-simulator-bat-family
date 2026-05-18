<?php
// ================================================================
// Travel Guide Task 4 - Database Connection (procedural mysqli)
// Student ID: 23-54508-3
// ================================================================
$conn = mysqli_connect('localhost', 'root', '', 'travel_guide');
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// Helper for safe output to prevent XSS
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isVerifiedGeneralUser() {
    return isset($_SESSION['user'])
        && $_SESSION['user']['role'] === 'user'
        && intval($_SESSION['user']['is_verified']) === 1;
}
?>
