<?php
// Admin Dashboard
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?page=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="auth-style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .admin-title {
            font-size: 24px;
            font-weight: 700;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #ff5252;
        }

        .nav-tabs {
            display: flex;
            background-color: white;
            border-bottom: 2px solid #e0e0e0;
            padding: 0 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .nav-tab {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            font-size: 15px;
        }

        .nav-tab:hover {
            color: #667eea;
        }

        .nav-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .stat-label {
            font-size: 13px;
            color: #999;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 19px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 12px;
        }

        .subsection {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 25px;
            border-left: 3px solid #667eea;
        }

        .subsection-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background-color: #f8f9fa;
        }

        th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #666;
            border-bottom: 2px solid #e0e0e0;
            font-size: 13px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5568d3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #333;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-info {
            background-color: #e7f3ff;
            color: #0066cc;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="admin-title">Admin Dashboard</div>
        <div class="admin-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
            <form method="GET" action="index.php" style="display: inline;">
                <input type="hidden" name="page" value="logout">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <a href="index.php?page=admin" class="nav-tab <?php echo (!isset($_GET['module']) || $_GET['module'] === 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
        <a href="index.php?page=admin&module=users" class="nav-tab <?php echo ($_GET['module'] ?? '') === 'users' ? 'active' : ''; ?>">User Management</a>
        <a href="index.php?page=admin&module=posts" class="nav-tab <?php echo ($_GET['module'] ?? '') === 'posts' ? 'active' : ''; ?>">Post Moderation</a>
        <a href="index.php?page=admin&module=comments" class="nav-tab <?php echo ($_GET['module'] ?? '') === 'comments' ? 'active' : ''; ?>">Comment Moderation</a>
    </div>

    <div class="container">
        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>✓</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>✕</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php
        $module = $_GET['module'] ?? 'dashboard';
        ?>

        <!-- DASHBOARD MODULE -->
        <?php if ($module === 'dashboard'): ?>
            <div class="section">
                <h2 class="section-title">Dashboard Summary</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value"><?php echo htmlspecialchars($adminStats['user_count_total'] ?? 0); ?></div>
                        <div style="font-size: 12px; color: #999; margin-top: 10px;">
                            Admin: <?php echo htmlspecialchars($adminStats['user_count_admin'] ?? 0); ?> | 
                            Scout: <?php echo htmlspecialchars($adminStats['user_count_scout'] ?? 0); ?> | 
                            User: <?php echo htmlspecialchars($adminStats['user_count_user'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Pending Requests</div>
                        <div class="stat-value" style="color: #ff9800;"><?php echo htmlspecialchars($adminStats['pending_posts_count'] ?? 0); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Posts</div>
                        <div class="stat-value" style="color: #4caf50;"><?php echo htmlspecialchars($adminStats['total_posts_count'] ?? 0); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Comments</div>
                        <div class="stat-value" style="color: #2196f3;"><?php echo htmlspecialchars($adminStats['total_comments_count'] ?? 0); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- USER MANAGEMENT MODULE -->
        <?php if ($module === 'users'): ?>
            <div class="section">
                <h2 class="section-title">User Management</h2>

                <!-- Add New User Form -->
                <div class="subsection">
                    <div class="subsection-title">Add New User</div>
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Name *</label>
                                <input type="text" name="user_name" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="user_email" required>
                            </div>
                            <div class="form-group">
                                <label>Password *</label>
                                <input type="password" name="user_password" required>
                            </div>
                            <div class="form-group">
                                <label>Role *</label>
                                <select name="user_role" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="user">User</option>
                                    <option value="scout">Scout</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </form>
                </div>

                <!-- Users Table -->
                <?php if (!empty($allUsers)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($user['is_verified'] ?? 0) ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo ($user['is_verified'] ?? 0) ? 'Verified' : 'Unverified'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id'] ?? 0); ?>">
                                                <button type="submit" name="verify_user" class="btn btn-sm <?php echo ($user['is_verified'] ?? 0) ? 'btn-warning' : 'btn-success'; ?>" onclick="return confirm('Toggle verification status?');">
                                                    <?php echo ($user['is_verified'] ?? 0) ? 'Unverify' : 'Verify'; ?>
                                                </button>
                                            </form>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id'] ?? 0); ?>">
                                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user and all their data? This cannot be undone.');">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">No users found</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- POST MODERATION MODULE -->
        <?php if ($module === 'posts'): ?>
            <div class="section">
                <h2 class="section-title">Post Moderation</h2>

                <!-- Pending Requests -->
                <div class="subsection">
                    <div class="subsection-title">Pending Post Requests</div>
                    <?php if (!empty($pendingRequests)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Scout Name</th>
                                    <th>Request Data</th>
                                    <th>Requested</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRequests as $req): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($req['scout_name'] ?? ''); ?></td>
                                        <td>
                                            <?php 
                                            $data = json_decode($req['post_data'], true);
                                            echo htmlspecialchars($data['title'] ?? 'N/A');
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($req['requested_at'] ?? ''); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($req['id'] ?? 0); ?>">
                                                    <button type="submit" name="approve_post" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($req['id'] ?? 0); ?>">
                                                    <button type="submit" name="reject_post" class="btn btn-sm btn-danger" onclick="return confirm('Reject this post request?');">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">No pending requests</div>
                    <?php endif; ?>
                </div>

                <!-- Approved Posts -->
                <div class="subsection">
                    <div class="subsection-title">Approved Posts</div>
                    <?php if (!empty($approvedPosts)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Scout Name</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvedPosts as $post): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($post['scout_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($post['title'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge badge-success">Approved</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id'] ?? 0); ?>">
                                                    <button type="submit" name="delete_post" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post and all associated comments? This cannot be undone.');">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">No approved posts</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- COMMENT MODERATION MODULE -->
        <?php if ($module === 'comments'): ?>
            <div class="section">
                <h2 class="section-title">Comment Moderation</h2>
                <?php if (!empty($allComments)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Post Title</th>
                                <th>Commenter</th>
                                <th>Comment Content</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allComments as $comment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($comment['post_title'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($comment['commenter_name'] ?? ''); ?></td>
                                    <td>
                                        <span title="<?php echo htmlspecialchars($comment['content'] ?? ''); ?>">
                                            <?php 
                                            $content = $comment['content'] ?? '';
                                            echo htmlspecialchars(strlen($content) > 80 ? substr($content, 0, 80) . '...' : $content);
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($comment['created_at'] ?? ''); ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id'] ?? 0); ?>">
                                            <button type="submit" name="delete_comment" class="btn btn-sm btn-danger" onclick="return confirm('Delete this comment?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">No comments found</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
