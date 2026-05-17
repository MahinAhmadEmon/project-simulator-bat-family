<?php
/* ------------------- User Auth ------------------- */
function authUser($conn, $email, $password) {
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password_hash, role, is_verified FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return ($row && password_verify($password, $row['password_hash'])) ? $row : false;
}

function getUserById($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, role, is_verified, profile_picture FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function registerUser($conn, $name, $email, $password, $role = 'user') {
    // Check if email exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, 's', $email);
    mysqli_stmt_execute($check);
    $exists = mysqli_num_rows(mysqli_stmt_get_result($check)) > 0;
    mysqli_stmt_close($check);
    if ($exists) return false;

    // Insert user
    $hash     = password_hash($password, PASSWORD_DEFAULT);
    $verified = 0; // New users need approval
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $hash, $role, $verified);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updateUserProfile($conn, $userId, $name, $email, $password = null, $picture = null) {
    $updates = [];
    $types   = '';
    $params  = [];

    if ($name !== null) {
        $updates[] = "name = ?";
        $types    .= 's';
        $params[]  = $name;
    }
    if ($email !== null) {
        $updates[] = "email = ?";
        $types    .= 's';
        $params[]  = $email;
    }
    if ($password !== null) {
        $updates[] = "password_hash = ?";
        $types    .= 's';
        $params[]  = password_hash($password, PASSWORD_DEFAULT);
    }
    if ($picture !== null) {
        $updates[] = "profile_picture = ?";
        $types    .= 's';
        $params[]  = $picture;
    }

    if (empty($updates)) return false;

    $types   .= 'i';
    $params[] = $userId;
    $sql  = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

/* ------------------- Admin Functions ------------------- */
function getPendingUsers($conn) {
    $r = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM users WHERE is_verified = 0 ORDER BY created_at DESC");
    return mysqli_fetch_all($r, MYSQLI_ASSOC);
}

function approveUser($conn, $userId) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET is_verified = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function rejectUser($conn, $userId) {
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}


function getApprovedPosts($conn, $limit = 6, $offset = 0) {
    $stmt = mysqli_prepare($conn,
        "SELECT id, title, country, genre, cost_level, short_history, created_at
         FROM posts WHERE status = 'approved'
         ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function getPostById($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function getPendingPosts($conn) {
    $r = mysqli_query($conn, "SELECT id, title, country, scout_id, status FROM posts WHERE status = 'pending' ORDER BY created_at DESC");
    return mysqli_fetch_all($r, MYSQLI_ASSOC);
}
?>
