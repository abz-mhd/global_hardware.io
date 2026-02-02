    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-top">
                <div class="logo-left">
                    <img src="../Images/GLOBAL.png" alt="Global Hardware Logo" class="brand-logo-img">
                </div>
                <div class="brand-section">
                    <div class="brand-name">GLOBAL HARDWARE</div>
                    <div class="brand-tagline">YOUR TRUSTED HARDWARE PARTNER</div>
                </div>
                <div class="nav-actions">
                    <a href="profile.php" class="nav-icon-btn">👤</a>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
            <div class="nav-links">
                <div class="nav-links-center">
                    <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Home</a>
                    <a href="products.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Products</a>
                    <a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">My Orders</a>
                    <a href="cart.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>" id="cart-link">
                        Cart
                        <?php
                        $cartCount = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cartCount += $item['quantity'];
                            }
                        }
                        if ($cartCount > 0):
                        ?>
                        <span class="cart-count" id="cart-count">(<?= $cartCount ?>)</span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>