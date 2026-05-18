<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Guide</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-side">
            <div class="logo-big">🌍</div>
            <h1>Travel Guide</h1>
            <!--<p>Task 4: General USER browse, AJAX filter, comments, and cost estimate.</p>
        <ul class="feature-list">
            <li>✓ Browse approved posts</li>
            <li>✓ AJAX search and filter</li>
            <li>✓ User comments</li>
            <li>✓ Cost calculator</li>
        </ul>-->
        </section>

        <section class="auth-form-wrap">
            <div class="auth-card">
                <h2>Login</h2>
                <p class="muted">Use General User Credentials.</p>

                <?php $error = $error ?? '';
                if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

                <form method="POST" class="form" onsubmit="return validateLogin()">
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" id="email" value="<?php echo e($oldEmail ?? ''); ?>" placeholder="">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder="">
                    </div>
                    <label class="checkbox"><input type="checkbox" name="remember"> Remember email</label>
                    <button class="btn btn-primary btn-block" type="submit">Login</button>
                </form>
                <!-- <p class="hint">Demo: user@test.com / user12345</p> -->
                <p class="auth-foot"><a href="index.php?page=browse">Continue to browse</a></p>
            </div>
        </section>
    </div>
    <script>
        function validateLogin() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            if (email === '' || password === '') {
                alert('Email and password are required.');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
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
