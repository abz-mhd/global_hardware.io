<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$customerId = $_SESSION['customer_id'];

// Get cart item count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

// Fetch all orders for this customer
$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$ordersStmt->execute([$customerId]);
$orders = $ordersStmt->fetchAll();

// Get order items for each order with product details
$orderItems = [];
foreach ($orders as $order) {
    $itemsStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image, p.description, p.category, p.price as current_price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?");
    $itemsStmt->execute([$order['order_id']]);
    $orderItems[$order['order_id']] = $itemsStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - GLOBAL HARDWARE Store</title>
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

        /* Order Cards */
        .orders-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .order-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 28px;
            transition: all 0.3s;
        }

        .order-card:hover {
            transform: translateY(-2px);
            border-color: var(--orange);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .order-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .order-date {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .order-status-section {
            text-align: right;
        }

        .order-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .status-pending {
            background: rgba(249, 115, 22, 0.2);
            color: var(--orange);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-shipped {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-delivered {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-total {
            font-size: 24px;
            font-weight: 700;
            color: var(--orange);
        }

        .expand-btn {
            padding: 12px 24px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .expand-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .expand-btn i {
            font-size: 12px;
        }

        .order-details {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .order-details.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Enhanced Order Details */
        .order-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .order-item-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 16px;
            transition: all 0.2s;
        }

        .order-item-card:hover {
            border-color: var(--orange);
            transform: translateY(-2px);
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--hover-bg);
            display: flex;
            align-items: center;
            justify-content: center;
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
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item-name {
            font-size: 16px;
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
            margin: 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .item-pricing {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }

        .item-quantity {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .item-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--orange);
        }

        .item-subtotal {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Order Summary */
        .order-summary {
            background: var(--hover-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .summary-row.total {
            border-top: 1px solid var(--border-color);
            margin-top: 12px;
            padding-top: 16px;
            font-size: 18px;
            font-weight: 700;
            color: var(--orange);
        }

        /* No Orders */
        .no-orders {
            text-align: center;
            padding: 80px 40px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .no-orders p {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .no-orders a {
            display: inline-block;
            padding: 12px 24px;
            background: var(--orange);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .no-orders a:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .nav-links {
                overflow-x: auto;
            }

            .order-header {
                flex-direction: column;
                gap: 16px;
            }

            .order-status-section {
                text-align: left;
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
            <h1 class="page-title">My Order History</h1>
            <p class="page-subtitle">View all your past and current orders</p>
        </div>

        <!-- Orders Section -->
        <div class="orders-section">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <p>You haven't placed any orders yet.</p>
                    <a href="dashboard.php">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>Order #<?= $order['order_id'] ?></h3>
                                <div class="order-date">Date: <?= date('M d, Y H:i', strtotime($order['order_date'])) ?></div>
                            </div>
                            <div class="order-status-section">
                                <span class="order-status status-<?= strtolower(trim($order['status'])) ?>"><?= htmlspecialchars(trim($order['status'])) ?></span>
                                <div class="order-total">LKR <?= number_format($order['total_amount'], 2) ?></div>
                            </div>
                        </div>

                        <button class="expand-btn" onclick="toggleDetails(<?= $order['order_id'] ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        
                        <a href="order_success.php?order_id=<?= $order['order_id'] ?>" class="expand-btn" style="text-decoration: none; margin-left: 12px;">
                            <i class="fas fa-truck"></i> Track Order
                        </a>
                        
                        <div id="details-<?= $order['order_id'] ?>" class="order-details">
                            <div class="order-items-grid">
                                <?php 
                                $items = $orderItems[$order['order_id']] ?? [];
                                foreach($items as $item): 
                                ?>
                                <div class="order-item-card">
                                    <div class="item-image">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                 onerror="this.parentElement.innerHTML='<div style=\'display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);\'><i class=\'fas fa-box\' style=\'font-size: 24px;\'></i></div>'">
                                        <?php else: ?>
                                            <div class="item-image-placeholder">
                                                <i class="fas fa-image" style="font-size: 24px; color: var(--text-muted);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <?php if (!empty($item['category'])): ?>
                                            <div class="item-category"><?= htmlspecialchars($item['category']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($item['description'])): ?>
                                            <div class="item-description"><?= htmlspecialchars($item['description']) ?></div>
                                        <?php endif; ?>
                                        <div class="item-pricing">
                                            <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                                            <div class="item-price">LKR <?= number_format($item['unit_price'], 2) ?> each</div>
                                            <div class="item-subtotal">LKR <?= number_format($item['quantity'] * $item['unit_price'], 2) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Items Total:</span>
                                    <span>LKR <?= number_format(array_sum(array_map(function($item) { return $item['quantity'] * $item['unit_price']; }, $items)), 2) ?></span>
                                </div>
                                <div class="summary-row total">
                                    <span>Order Total:</span>
                                    <span>LKR <?= number_format($order['total_amount'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

<?php include 'footer.php'; ?>

    <script>
        function toggleDetails(orderId) {
            const details = document.getElementById('details-' + orderId);
            const button = event.target;
            
            if (details.classList.contains('show')) {
                details.classList.remove('show');
                button.textContent = 'View Details';
                button.innerHTML = '<i class="fas fa-eye"></i> View Details';
            } else {
                details.classList.add('show');
                button.textContent = 'Hide Details';
                button.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Details';
            }
        }
    </script>
