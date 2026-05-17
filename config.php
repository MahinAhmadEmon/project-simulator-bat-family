<?php
$conn = mysqli_connect('localhost', 'root', '', 'travel_guide_db');
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// we are creating demo admin if there is none (default email:admin@example.com, password: admin123)
$check = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
if ($check && mysqli_num_rows($check) === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, ?)");
    $name     = 'Administrator';
    $email    = 'admin@example.com';
    $role     = 'admin';
    $verified = 1;
    mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $hash, $role, $verified);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
