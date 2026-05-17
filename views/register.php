<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register &mdash; Travel Guide</title>
<link rel="stylesheet" href="auth-style.css">
</head>
<body class="auth-body">

<div class="auth-shell">
    <div class="auth-side">
        <div class="logo-big">🌍</div>
        <h1>Join Travel Guide</h1>
        <p>Create an account to start exploring travel destinations.</p>
        <ul class="feature-list">
            <li>✓ Easy registration</li>
            <li>✓ Admin approval</li>
            <li>✓ Secure password</li>
            <li>✓ Choose your role</li>
        </ul>
    </div>

    <div class="auth-form-wrap">
        <div class="auth-card">
            <h2>Create Account</h2>
            <p class="muted">Fill in your details to get started</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=register" class="form" novalidate>
                <div class="field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                           placeholder="John Doe" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="john@example.com" required>
                </div>
                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user" <?= ($old['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>User (Tourist)</option>
                        <option value="scout" <?= ($old['role'] ?? '') === 'scout' ? 'selected' : '' ?>>Scout (Writer)</option>
                    </select>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="At least 8 characters" required>
                </div>
                <div class="field">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Repeat password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <p class="auth-foot">Already have an account?
                <a href="index.php?page=login">Sign in</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
