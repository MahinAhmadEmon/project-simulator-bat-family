<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wishlist &mdash; Travel Guide</title>
<link rel="stylesheet" href="auth-style.css">
</head>
<body class="app-body">
<?php $user = $_SESSION['user'] ?? null; ?>

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
            <a href="index.php?page=home" class="btn btn-small">Home</a>
            <a href="index.php?page=profile" class="btn btn-small">Profile</a>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

<!-- Main -->
<main class="app-main">
    <div class="container">
        <section class="section">
            <h2>Your Wishlist</h2>

            <?php if (empty($items)): ?>
                <div class="alert alert-info">
                    <p>Your wishlist is empty. Save guides from the home page to see them here.</p>
                </div>
            <?php else: ?>
                <div class="wishlist-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="wishlist-card">
                            <div class="post-header">
                                <h3><?= htmlspecialchars($item['title']) ?></h3>
                                <span class="badge"><?= htmlspecialchars($item['country']) ?></span>
                            </div>
                            <div class="post-meta">
                                <span>📍 <?= htmlspecialchars($item['genre']) ?></span>
                                <span>💰 <?= htmlspecialchars($item['cost_level']) ?></span>
                            </div>
                            <div class="post-actions">
                                <button class="btn btn-small wishlist-remove" data-post-id="<?= $item['post_id'] ?>">
                                    Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?= date('Y') ?> Travel Guide. All rights reserved.</p>
</footer>

<script>
(function() {
    const endpoint = 'index.php?page=ajax';
    const buttons = document.querySelectorAll('.wishlist-remove');
    if (!buttons.length) return;

    buttons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const postId = btn.dataset.postId;
            if (!postId) return;

            btn.disabled = true;
            try {
                const res = await fetch(endpoint + '&type=wishlist-remove', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({post_id: Number(postId)})
                });
                const data = await res.json();
                if (data && data.success) {
                    const card = btn.closest('.wishlist-card');
                    if (card) card.remove();
                    if (!document.querySelector('.wishlist-card')) {
                        const container = document.querySelector('.container');
                        if (container) {
                            container.innerHTML = '<div class="alert alert-info"><p>Your wishlist is empty. Save guides from the home page to see them here.</p></div>';
                        }
                    }
                } else {
                    alert(data.error || 'Unable to remove from wishlist.');
                }
            } catch (err) {
                alert('Unable to remove from wishlist.');
            } finally {
                btn.disabled = false;
            }
        });
    });
})();
</script>
</body>
</html>
