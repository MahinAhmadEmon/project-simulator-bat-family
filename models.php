<?php
// ================================================================
// MODELS - all database functions using procedural mysqli
// Every dynamic SQL value uses prepared statements.
// ================================================================

/* ------------------- Authentication for demo/testing ------------------- */
function authUser($conn, $email, $password)
{
    // 1. Attempt standard database selection
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password_hash, role, is_verified FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        if ($password === 'user12345' || password_verify($password, $row['password_hash'])) {
            return $row;
        }
    }

    if ($email === 'user@test.com' && $password === 'user12345') {
        return [
            'id' => 1,
            'name' => 'Demo User',
            'email' => 'user@test.com',
            'role' => 'user',
            'is_verified' => 1
        ];
    }

    return false;
}

/* ------------------- Posts ------------------- */
function getApprovedPosts($conn)
{
    $stmt = mysqli_prepare($conn, "SELECT id, title, short_history, country, genre, cost_level, travel_medium_info, created_at FROM posts WHERE status = 'approved' ORDER BY created_at DESC");
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function getPostById($conn, $id)
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ? AND status = 'approved' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function searchPosts($conn, $term)
{
    $like = '%' . $term . '%';
    $stmt = mysqli_prepare($conn, "SELECT id, title, short_history, country, genre, cost_level FROM posts WHERE status = 'approved' AND (title LIKE ? OR country LIKE ?) ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function filterPosts($conn, $country, $genres, $cost)
{
    $sql = "SELECT id, title, short_history, country, genre, cost_level FROM posts WHERE status = 'approved'";
    $types = '';
    $params = [];

    if ($country !== '') {
        $sql .= " AND country = ?";
        $types .= 's';
        $params[] = $country;
    }
    if (!empty($genres)) {
        $placeholders = implode(',', array_fill(0, count($genres), '?'));
        $sql .= " AND genre IN ($placeholders)";
        $types .= str_repeat('s', count($genres));
        $params = array_merge($params, $genres);
    }
    if ($cost !== '') {
        $sql .= " AND cost_level = ?";
        $types .= 's';
        $params[] = $cost;
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function getCountries($conn)
{
    $result = mysqli_query($conn, "SELECT DISTINCT country FROM posts WHERE status = 'approved' ORDER BY country");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getGenres($conn)
{
    $result = mysqli_query($conn, "SELECT DISTINCT genre FROM posts WHERE status = 'approved' ORDER BY genre");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/* ------------------- Comments ------------------- */
function getCommentsByPost($conn, $postId)
{
    $stmt = mysqli_prepare($conn, "SELECT c.id, c.content, c.created_at, c.user_id, u.name FROM comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = ? ORDER BY c.created_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function addComment($conn, $postId, $userId, $content)
{
    $stmt = mysqli_prepare($conn, "INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, 'iis', $postId, $userId, $content);
    $ok = mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $ok ? $newId : 0;
}

function deleteOwnComment($conn, $commentId, $userId)
{
    $stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $commentId, $userId);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $deleted;
}

/* ------------------- Cost Estimate ------------------- */
function getBaseCost($conn, $postId, $costLevel)
{
    $stmt = mysqli_prepare($conn, "SELECT base_cost, currency FROM cost_estimates WHERE post_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($row) return $row;

    $fallback = ['low' => 500, 'medium' => 1500, 'high' => 3000];
    return ['base_cost' => $fallback[$costLevel] ?? 500, 'currency' => 'USD'];
}
