<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total = 0;

// Calculate total and verify stock availability, get product details
$cartWithDetails = [];
foreach ($cart as $key => $item) {
    $productId = $item['product_id'];
    $stmt = $pdo->prepare("SELECT stock, status, image, category, description FROM products WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product || $product['status'] !== 'Available') {
        unset($cart[$key]);
        continue;
    }
    
    if ($item['quantity'] > $product['stock']) {
        $cart[$key]['quantity'] = $product['stock'];
    }
    
    $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $cart[$key]['price'];
    $cart[$key]['image'] = $product['image'];
    $cart[$key]['category'] = $product['category'];
    $cart[$key]['description'] = $product['description'];
    $total += $cart[$key]['subtotal'];
    
    $cartWithDetails[$key] = $cart[$key];
}

$_SESSION['cart'] = $cart;

// Get cart item count
$cartCount = 0;
foreach ($cart as $item) {
    $cartCount += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="footer_styles.css">
    <link rel="stylesheet" href="common_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --sidebar-bg: #1a1a1a;
            --main-bg: #000000;
            --card-bg: #1a1a1a;
            --orange: #f97316;
            --orange-dark: #ea580c;
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --border-color: #27272a;
            --hover-bg: #27272a;
            --danger-color: #ef4444;
            --success-color: #10b981;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--main-bg);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            min-height: 100vh;
        }

        /* Top Navigation Bar */
        .top-nav {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .nav-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
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

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-icon-btn {
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
            position: relative;
        }

        .nav-icon-btn:hover {
            background: var(--hover-bg);
            border-color: var(--orange);
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--orange);
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            text-align: center;
        }

        .logout-btn {
            width: 44px;
            height: 44px;
            background: var(--orange);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(249, 115, 22, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(249, 115, 22, 0.3);
        }

        .logout-btn i {
            font-size: 18px;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            gap: 4px;
            padding: 12px 0;
            border-top: 1px solid var(--border-color);
        }

        .nav-link {
            padding: 10px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            border-radius: 6px;
            position: relative;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--hover-bg);
        }

        .nav-link.active {
            color: var(--orange);
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--orange);
            border-radius: 2px 2px 0 0;
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 42px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .page-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        /* Cart Section */
        .cart-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
        }

        /* Cart Items Grid */
        .cart-items-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 32px;
        }

        .cart-item-card {
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            display: grid;
            grid-template-columns: 100px 1fr auto auto auto;
            gap: 20px;
            align-items: center;
            transition: all 0.2s;
        }

        .cart-item-card:hover {
            border-color: var(--orange);
            transform: translateY(-2px);
        }

        .item-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-image-placeholder {
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .item-category {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .item-description {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .item-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--orange);
            text-align: center;
            min-width: 80px;
        }

        .item-quantity {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            min-width: 120px;
        }

        .quantity-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .quantity-input:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .update-btn {
            background: var(--orange);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .update-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .item-subtotal {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            min-width: 100px;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            min-width: 80px;
        }

        .remove-btn {
            background: var(--danger-color);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .remove-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Responsive Cart */
        @media (max-width: 1024px) {
            .cart-item-card {
                grid-template-columns: 80px 1fr;
                gap: 16px;
            }

            .item-image {
                width: 80px;
                height: 80px;
            }

            .cart-item-details {
                display: grid;
                grid-template-columns: 1fr auto auto auto;
                gap: 16px;
                align-items: center;
            }
        }

        @media (max-width: 768px) {
            .cart-item-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .cart-item-details {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .item-image {
                width: 100px;
                height: 100px;
                margin: 0 auto;
            }
        }

        /* Buttons */
        button, .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .update-btn {
            background: var(--orange);
            color: white;
            margin-left: 8px;
        }

        .update-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .remove-btn {
            background: var(--danger-color);
            color: white;
        }

        .remove-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Total Section */
        .total-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
        }

        .total-label {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .total-amount {
            font-size: 36px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 24px;
        }

        .checkout-btn {
            padding: 14px 40px;
            background: var(--orange);
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 16px;
            display: inline-block;
        }

        .checkout-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .continue-shopping {
            display: inline-block;
            padding: 12px 24px;
            background: var(--text-secondary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .continue-shopping:hover {
            background: #475569;
            transform: translateY(-1px);
        }

        .total-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 80px 40px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .empty-cart p {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        /* Messages */
        .message {
            padding: 14px 18px;
            margin-bottom: 24px;
            border-radius: 8px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-left-color: var(--danger-color);
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            border-left-color: var(--success-color);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .nav-links {
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Shopping Cart</h1>
            <p class="page-subtitle">Review your items and proceed to checkout</p>
        </div>

        <!-- Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="message success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Cart Section -->
        <div class="cart-section">
            <h2 class="section-title">Your Cart Items</h2>

            <?php if (empty($cart)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="dashboard.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items-grid">
                    <?php foreach($cartWithDetails as $key => $item): ?>
                    <div class="cart-item-card">
                        <!-- Product Image -->
                        <div class="item-image">
                            <?php if (!empty($item['image'])): ?>
                                <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>">
                            <?php else: ?>
                                <div class="item-image-placeholder">
                                    <i class="fas fa-image" style="font-size: 24px; color: var(--text-muted);"></i>
                                    <div>No Image</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Product Details -->
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <?php if (!empty($item['category'])): ?>
                                <div class="item-category"><?= htmlspecialchars($item['category']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item['description'])): ?>
                                <div class="item-description"><?= htmlspecialchars($item['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Mobile/Desktop responsive layout -->
                        <div class="cart-item-details">
                            <!-- Price -->
                            <div class="item-price">
                                LKR <?= number_format($item['price'], 2) ?>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="item-quantity">
                                <form action="cart_update.php" method="POST" class="quantity-controls">
                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="quantity-input" required>
                                    <button type="submit" class="update-btn">
                                        <i class="fas fa-sync-alt"></i> Update
                                    </button>
                                </form>
                            </div>

                            <!-- Subtotal -->
                            <div class="item-subtotal">
                                LKR <?= number_format($item['subtotal'], 2) ?>
                            </div>

                            <!-- Actions -->
                            <div class="item-actions">
                                <form action="cart_remove.php" method="POST">
                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                    <button type="submit" class="remove-btn" onclick="return confirm('Remove this item from cart?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="total-section">
                    <div class="total-label">Total Amount</div>
                    <div class="total-amount">LKR <?= number_format($total, 2) ?></div>
                    <div class="total-buttons">
                        <form action="checkout.php" method="POST">
                            <button type="submit" class="checkout-btn">Proceed to Checkout</button>
                        </form>
                        <a href="products.php" class="continue-shopping">Continue Shopping</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include 'footer.php'; ?>
