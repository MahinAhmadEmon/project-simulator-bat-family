<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile &mdash; Travel Guide</title>
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
            <a href="index.php?page=home" class="btn btn-small">Home</a>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

<!-- Main -->
<main class="app-main">
    <div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="profile-wrapper">
            <!-- Sidebar -->
            <aside class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php if ($user['profile_picture']): ?>
                            <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile">
                        <?php else: ?>  
                            <div class="avatar-placeholder"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge role-badge"><?= strtoupper($user['role']) ?></span>
                </div>
            </aside>

            <!-- Content -->
            <div class="profile-content">
                <form method="POST" action="index.php?page=profile" enctype="multipart/form-data" class="form">
                    <fieldset>
                        <legend>Basic Information</legend>
                        <div class="field">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Profile Picture</legend>
                        <div class="field">
                            <label for="picture">Upload Picture (JPG, PNG, GIF)</label>
                            <input type="file" id="picture" name="picture" accept="image/*">
                            <small>Max 2MB. Accepted: JPG, PNG, GIF</small>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Change Password</legend>
                        <p class="hint">Leave blank to keep current password</p>
                        <div class="field">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                   placeholder="At least 8 characters">
                        </div>
                        <div class="field">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   placeholder="Repeat password">
                        </div>
                    </fieldset>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="index.php?page=home" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?= date('Y') ?> Travel Guide. All rights reserved.</p>
</footer>

</body>
</html>
