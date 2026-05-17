<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home &mdash; Travel Guide</title>
<link rel="stylesheet" href="auth-style.css">
</head>
<body class="app-body">

<!-- Navbar -->
<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=home">
            <span class="brand-icon">✈️</span>
            <span>Travel Guide</span>
        </a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></span>
                    <span class="user-role"><?= strtoupper($user['role'] ?? 'USER') ?></span>
                </span>
            </span>
            <a href="index.php?page=profile" class="btn btn-small">Profile</a>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

<!-- Main -->
<main class="app-main">
    <div class="container">
        <?php if (!$user): ?>
            <!-- Non-authenticated -->
            <section class="hero">
                <h1>Explore the World</h1>
                <p>Discover amazing travel destinations and save your favorites</p>
                <div class="btn-group">
                    <a href="index.php?page=login" class="btn btn-primary btn-lg">Sign In</a>
                    <a href="index.php?page=register" class="btn btn-secondary btn-lg">Register</a>
                </div>
            </section>

        <?php elseif (!$user['verified']): ?>
            <!-- Unverified user -->
            <div class="alert alert-info">
                <strong>Account Pending Approval</strong>
                <p>Your account is waiting for admin approval. You'll be able to browse guides once approved.</p>
            </div>

        <?php else: ?>
            <!-- Verified user - show posts -->
            <section class="section">
                <h2>Featured Guides</h2>

                <?php if (empty($posts)): ?>
                    <p class="text-center">No guides available yet.</p>
                <?php else: ?>
                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <div class="post-card">
                                <div class="post-header">
                                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                                    <span class="badge"><?= htmlspecialchars($post['country']) ?></span>
                                </div>
                                <p><?= htmlspecialchars(substr($post['short_history'], 0, 100)) ?>...</p>
                                <div class="post-meta">
                                    <span>📍 <?= htmlspecialchars($post['genre']) ?></span>
                                    <span>💰 <?= htmlspecialchars($post['cost_level']) ?></span>
                                </div>
                                <div class="post-actions">
                                    <button class="btn btn-small wishlist-toggle" data-post-id="<?= $post['id'] ?>">
                                        💾 Save
                                    </button>
                                    <a href="#" class="btn btn-small">Read More</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?= date('Y') ?> Travel Guide. All rights reserved.</p>
</footer>
</body>
</html>
