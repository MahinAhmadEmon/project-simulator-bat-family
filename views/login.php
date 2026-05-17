<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login &mdash; Travel Guide</title>
<link rel="stylesheet" href="auth-style.css">
</head>
<body class="auth-body">

<div class="auth-shell">
    <div class="auth-side">
        <div class="logo-big">✈️</div>
        <h1>Travel Guide</h1>
        <p>Discover amazing travel destinations and save your favorites.</p>
        <ul class="feature-list">
            <li>✓ Browse travel guides</li>
            <li>✓ Save favorites</li>
            <li>✓ Manage your profile</li>
            <li>✓ Secure login</li>
        </ul>
    </div>

    <div class="auth-form-wrap">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p class="muted">Sign in to your account</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" class="form" novalidate>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($prefill ?? '') ?>"
                           placeholder="Enter email" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Enter password" required>
                </div>
                <label class="checkbox">
                    <input type="checkbox" name="remember" <?= !empty($prefill) ? 'checked' : '' ?>>
                    <span>Remember me (30 days)</span>
                </label>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <p class="auth-foot">Don't have an account?
                <a href="index.php?page=register">Register here</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
