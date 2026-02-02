<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Login - GLOBAL HARDWARE Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #000000;
            --bg-secondary: #1a1a1a;
            --card-bg: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --text-muted: #666666;
            --orange: #f97316;
            --orange-dark: #ea580c;
            --border-color: #2a2a2a;
            --hover-bg: #252525;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-primary);
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-color);
        }

        .login-left {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(249, 115, 22, 0.1) 0%, transparent 70%);
        }

        .logo-brand {
            position: relative;
            z-index: 1;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: var(--orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 56px;
            font-weight: 700;
            color: white;
            letter-spacing: 2px;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
        }

        .logo-icon-img {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
            transition: all 0.3s;
            background: transparent;
        }

        .logo-icon-img:hover {
            transform: scale(1.05);
        }

        .brand-name {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .brand-tagline {
            font-size: 14px;
            color: var(--text-secondary);
            letter-spacing: 1px;
        }

        .login-right {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--card-bg);
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 32px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .login-button:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .error {
            color: #ef4444;
            margin-bottom: 24px;
            padding: 12px 16px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 8px;
            border-left: 3px solid #ef4444;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
        }

        .register-link p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .register-link a {
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .register-link a:hover {
            color: var(--orange-dark);
            text-decoration: underline;
        }

        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            .login-left {
                padding: 30px 20px;
            }

            .login-right {
                padding: 30px 20px;
            }

            .brand-name {
                font-size: 24px;
            }

            .login-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo-brand">
                <img src="../Images/GLOBAL.png" alt="Global Hardware Logo" class="logo-icon-img">
                <div class="brand-name">GLOBAL</div>
                <div class="brand-name">HARDWARE</div>
                <div class="brand-tagline">YOUR TRUSTED HARDWARE PARTNER</div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-header">
                <h2>Customer Login</h2>
                <p>Sign in to access your account</p>
            </div>
            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <form action="login_process.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
