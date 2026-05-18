<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Guide</title>
    <link rel="stylesheet" href="auth-style.css">
</head>

<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-side">
            <div class="logo-big">🌍</div>
            <h1>Travel Guide</h1>
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
                <p class="auth-foot">Do not have an account?
                    <a href="index.php?page=register">Sign Up</a>
                </p>
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
