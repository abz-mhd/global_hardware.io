<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="Images/Hammer Tools Construction Logo.png" alt="Logo">
        </div>
        <h1>GLOBAL HARDWARE</h1>
        <button class="home-btn" onclick="location.href='homepage.php';">Home</button>
    </header>

    <!-- Login Form Section -->
    <section class="form-section">
        <div class="form-container">
            <h2>Login</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    if ($_GET['error'] == 'invalidcredentials') {
                        echo "Invalid username or password";
                    } elseif ($_GET['error'] == 'invalidrole') {
                        echo "Invalid user role";
                    }
                    ?>
                </div>
            <?php endif; ?>
            <form action="LoginC.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="button-container">
                    <button type="submit" class="login-btn">Login</button>
                </div>
            </form>
        </div>
    </section>
    <?php include 'DBConnection.php';?>

    <footer>
        <div class="footer-container">
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p>Phone: 123-456-789</p>
                <p>Email: info@globalhardware.com</p>
                <p>Address: 123 Hardware Street, City</p>
            </div>

            <div class="footer-section follow">
                <h3>Follow Us</h3>
                <p>Facebook</p>
                <p>Twitter</p>
                <p>Instagram</p>
            </div>

            <div class="footer-section legal">
                <h3>Legal</h3>
                <p>Privacy Policy</p>
                <p>Terms of Service</p>
                <p>Returns & Refunds</p>
            </div>
        </div>

        <!-- Copyright Section -->
        <p class="copyright">© 2025 Global Hardware Store. All Rights Reserved.</p>
    </footer>

</body>
</html>