<?php
session_start();
require_once 'config/db.php';

// Get all categories
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' AND status = 'Available' ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// Get 4 products from each category
$productsByCategory = [];
foreach ($categories as $category) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND status = 'Available' ORDER BY created_at DESC LIMIT 4");
    $stmt->execute([$category]);
    $productsByCategory[$category] = $stmt->fetchAll();
}

// Check if customer is logged in
$isLoggedIn = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - GLOBAL HARDWARE Store</title>
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

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 40px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .brand-section {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .brand-logo {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: var(--orange);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .brand-logo-img {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            object-fit: cover;
            transition: all 0.2s;
            background: transparent;
        }

        .brand-logo-img:hover {
            transform: scale(1.05);
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .brand-tagline {
            font-size: 11px;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
        }

        .logo-left {
            display: flex;
            align-items: center;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .profile-icon-btn {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: transparent;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: white;
            font-size: 20px;
            text-decoration: none;
        }

        .profile-icon-btn:hover {
            background: var(--hover-bg);
            border-color: var(--orange);
        }

        .header-btn {
            padding: 10px 20px;
            background: var(--orange);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .header-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .header-btn.secondary {
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
        }

        .header-btn.secondary:hover {
            background: var(--border-color);
        }

        /* Navigation */
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--orange);
            background: rgba(249, 115, 22, 0.1);
        }

        /* Main Content */
        .main-content {
            max-width: 1600px;
            margin: 0 auto;
            padding: 40px;
        }

        /* About Section */
        .about-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 40px;
        }

        .about-section h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .about-section p {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 800px;
        }

        /* Category Section */
        .category-section {
            margin-bottom: 50px;
        }

        .category-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--orange);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: var(--orange);
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: var(--bg-primary);
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 14px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-category {
            font-size: 11px;
            font-weight: 600;
            color: var(--orange);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .product-stock {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .product-actions {
            margin-top: 16px;
            display: flex;
            gap: 8px;
        }

        .btn-add-cart {
            flex: 1;
            padding: 10px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-add-cart:hover {
            background: var(--orange-dark);
        }

        .btn-view {
            padding: 10px 16px;
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 20px 40px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1600px;
            margin: 0 auto;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .header-top {
                flex-direction: column;
                gap: 20px;
            }

            .brand-center {
                order: -1;
            }

            .nav-links {
                flex-wrap: wrap;
                gap: 15px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 20px;
            }

            .about-section {
                padding: 24px;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
        <header class="header">
        <div class="header-top">
            <div class="logo-left">
                <img src="Images/GLOBAL.png" alt="Global Hardware Logo" class="brand-logo-img">
            </div>
            <div class="brand-section">
                <div class="brand-name">GLOBAL HARDWARE</div>
                <div class="brand-tagline">YOUR TRUSTED HARDWARE PARTNER</div>
            </div>
            <div class="header-actions">
                <?php if ($isLoggedIn): ?>
                    <a href="customer/profile.php" class="profile-icon-btn">👤</a>
                <?php endif; ?>
                <?php if ($isLoggedIn): ?>
                    <a href="customer/logout.php" class="header-btn">Logout</a>
                <?php else: ?>
                    <a href="customer/login.php" class="header-btn">Login</a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-link active">Home</a>
            <a href="customer/dashboard.php" class="nav-link">Products</a>
            <a href="customer/cart.php" class="nav-link">Cart</a>
            <a href="customer/orders.php" class="nav-link">My Orders</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- About Section -->
        <section class="about-section">
            <h2>About Our Shop</h2>
            <p>
                Welcome to GLOBAL HARDWARE Store, your trusted partner for all your hardware needs. 
                We have been serving customers with quality products and excellent service for over a decade. 
                Our extensive inventory includes everything from power tools and hand tools to building materials and hardware supplies. 
                We are committed to providing professional-grade equipment and exceptional customer service to help you complete your projects successfully. 
                Whether you're a professional contractor or a DIY enthusiast, we have the right tools and expertise to support your work.
            </p>
        </section>

        <!-- Products by Category -->
        <?php foreach ($categories as $category): ?>
            <?php if (!empty($productsByCategory[$category])): ?>
                <section class="category-section">
                    <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>
                    <div class="products-grid">
                        <?php foreach ($productsByCategory[$category] as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="../<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </div>
                                <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="product-price">LKR <?= number_format($product['price'], 2) ?></div>
                                <div class="product-stock">Stock: <?= $product['stock'] ?> units</div>
                                <div class="product-actions">
                                    <?php if ($isLoggedIn): ?>
                                        <form action="customer/cart_add.php" method="POST" style="flex: 1;">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-add-cart">Add to Cart</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="customer/login.php" class="btn-add-cart" style="display: block; text-align: center; text-decoration: none;">Login to Buy</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($categories)): ?>
            <section class="category-section">
                <p style="text-align: center; color: var(--text-secondary); font-size: 16px; padding: 40px;">
                    No products available at the moment. Please check back later.
                </p>
            </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> GLOBAL HARDWARE Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
