<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$orderId = intval($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit();
}

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$orderId, $_SESSION['customer_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php?error=" . urlencode("Order not found."));
    exit();
}

// Fetch order items with product details
$itemsStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image, p.description, p.category 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?");
$itemsStmt->execute([$orderId]);
$orderItems = $itemsStmt->fetchAll();

// Get cart item count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="footer_styles.css">
    <link rel="stylesheet" href="common_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Order Tracking Section */
        .order-tracking-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
        }

        .tracking-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 32px;
        }

        .tracking-progress {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            position: relative;
            flex-wrap: wrap;
            gap: 20px;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            min-width: 120px;
        }

        .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--hover-bg);
            border: 3px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--text-muted);
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .progress-step.completed .step-icon {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }

        .progress-step.active .step-icon {
            background: var(--orange);
            border-color: var(--orange);
            color: white;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.2);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); }
            100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
        }

        .step-content {
            text-align: center;
        }

        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .step-description {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .progress-line {
            flex: 1;
            height: 4px;
            background: var(--border-color);
            position: relative;
            margin: 0 10px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .progress-line.completed {
            background: linear-gradient(90deg, var(--success-color) 0%, var(--success-color) 100%);
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.3);
        }

        .current-status {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .status-badge.status-pending {
            background: rgba(249, 115, 22, 0.2);
            color: var(--orange);
        }

        .status-badge.status-processing {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-badge.status-shipped {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
        }

        .status-badge.status-delivered {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        .status-badge.status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .status-message {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
        }

        .status-message.pending {
            background: rgba(249, 115, 22, 0.1);
            color: var(--orange);
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .status-message.processing {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-message.shipped {
            background: rgba(168, 85, 247, 0.1);
            color: #a855f7;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        .status-message.delivered {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-message.cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Order Success Specific Styles */
        .success-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .success-header {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .success-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .success-content {
            position: relative;
            z-index: 1;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .success-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .success-message {
            font-size: 16px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .order-number {
            font-size: 24px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 16px;
        }

        .order-meta {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Order Items Section */
        .order-items-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
        }

        .items-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            text-align: center;
        }

        .order-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .order-item-card {
            background: var(--hover-bg);
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
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
        }

        .item-category {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Order Summary */
        .order-summary {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
        }

        .summary-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 24px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .summary-item {
            text-align: center;
            padding: 20px;
            background: var(--hover-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .summary-label {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .summary-value.total {
            color: var(--orange);
            font-size: 24px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn.primary {
            background: var(--orange);
            color: white;
        }

        .action-btn.primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .action-btn.secondary {
            background: transparent;
            border: 2px solid var(--orange);
            color: var(--orange);
        }

        .action-btn.secondary:hover {
            background: var(--orange);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tracking-progress {
                flex-direction: column;
                gap: 20px;
            }

            .progress-line {
                width: 3px;
                height: 40px;
                margin: 10px 0;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .action-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="success-container">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-content">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h1 class="success-title">Order Placed Successfully!</h1>
                    <p class="success-message">Thank you for your order. Your order has been received and is being processed.</p>
                    <div class="order-number">Order #<?= $order['order_id'] ?></div>
                    <div class="order-meta">
                        <i class="fas fa-clock" style="margin-right: 8px;"></i>
                        Order placed on <?= date('F j, Y \a\t g:i A', strtotime($order['order_date'])) ?>
                        <br>
                        <strong>Status: <?= htmlspecialchars($order['status']) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Order Tracking -->
            <div class="order-tracking-section">
                <h2 class="tracking-title">Order Tracking</h2>
                <div class="tracking-progress">
                    <?php 
                    $currentStatus = trim($order['status']);
                    $statusOrder = ['Pending', 'Processing', 'Shipped', 'Delivered'];
                    $currentIndex = array_search($currentStatus, $statusOrder);
                    
                    // Handle case-insensitive matching
                    if ($currentIndex === false) {
                        $currentIndex = array_search(ucfirst(strtolower($currentStatus)), $statusOrder);
                    }
                    
                    // Default to -1 if status not found (before first step)
                    if ($currentIndex === false) {
                        $currentIndex = -1;
                    }
                    ?>
                    
                    <div class="progress-step <?= $currentIndex >= 0 ? 'completed' : ($currentStatus === 'Pending' ? 'active' : '') ?>">
                        <div class="step-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Order Placed</div>
                            <div class="step-description">Your order has been placed successfully</div>
                        </div>
                    </div>
                    
                    <div class="progress-line <?= $currentIndex >= 1 ? 'completed' : '' ?>"></div>
                    
                    <div class="progress-step <?= $currentIndex >= 1 ? 'completed' : ($currentStatus === 'Processing' ? 'active' : '') ?>">
                        <div class="step-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Processing</div>
                            <div class="step-description">Your order is being prepared</div>
                        </div>
                    </div>
                    
                    <div class="progress-line <?= $currentIndex >= 2 ? 'completed' : '' ?>"></div>
                    
                    <div class="progress-step <?= $currentIndex >= 2 ? 'completed' : ($currentStatus === 'Shipped' ? 'active' : '') ?>">
                        <div class="step-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Shipped</div>
                            <div class="step-description">Your order is on the way</div>
                        </div>
                    </div>
                    
                    <div class="progress-line <?= $currentIndex >= 3 ? 'completed' : '' ?>"></div>
                    
                    <div class="progress-step <?= $currentIndex >= 3 ? 'completed' : ($currentStatus === 'Delivered' ? 'active' : '') ?>">
                        <div class="step-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-title">Delivered</div>
                            <div class="step-description">Your order has been delivered</div>
                        </div>
                    </div>
                </div>
                
                <div class="current-status">
                    <div class="status-badge status-<?= strtolower($currentStatus) ?>">
                        Current Status: <?= htmlspecialchars($currentStatus) ?>
                    </div>
                    
                    <?php if (strtolower($currentStatus) === 'cancelled'): ?>
                        <div class="status-message cancelled">
                            <i class="fas fa-times-circle"></i>
                            This order has been cancelled. If you have any questions, please contact our support team.
                        </div>
                    <?php elseif (strtolower($currentStatus) === 'delivered'): ?>
                        <div class="status-message delivered">
                            <i class="fas fa-check-circle"></i>
                            Your order has been successfully delivered! Thank you for shopping with GLOBAL HARDWARE.
                        </div>
                    <?php elseif (strtolower($currentStatus) === 'shipped'): ?>
                        <div class="status-message shipped">
                            <i class="fas fa-truck"></i>
                            Your order is on the way! Expected delivery: 2-3 business days. Track your package for real-time updates.
                        </div>
                    <?php elseif (strtolower($currentStatus) === 'processing'): ?>
                        <div class="status-message processing">
                            <i class="fas fa-cogs"></i>
                            Your order is being prepared for shipment. Expected shipping: 1-2 business days.
                        </div>
                    <?php else: ?>
                        <div class="status-message pending">
                            <i class="fas fa-clock"></i>
                            Your order is pending confirmation. We'll start processing it within 24 hours.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items-section">
                <h2 class="items-title">Order Items</h2>
                <div class="order-items-grid">
                    <?php foreach($orderItems as $item): ?>
                    <div class="order-item-card">
                        <div class="item-image">
                            <?php if (!empty($item['image'])): ?>
                                <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                     onerror="this.parentElement.innerHTML='<div style=\'display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);\'><i class=\'fas fa-box\' style=\'font-size: 32px;\'></i></div>'">
                            <?php else: ?>
                                <i class="fas fa-box" style="font-size: 32px; color: var(--text-muted);"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div class="item-category"><?= htmlspecialchars($item['category']) ?></div>
                            <div class="item-pricing">
                                <span class="item-quantity">Qty: <?= $item['quantity'] ?></span>
                                <span class="item-price">LKR <?= number_format($item['quantity'] * $item['unit_price'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2 class="summary-title">Order Summary</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Items</div>
                        <div class="summary-value"><?= count($orderItems) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Order Date</div>
                        <div class="summary-value"><?= date('M j, Y', strtotime($order['order_date'])) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Amount</div>
                        <div class="summary-value total">LKR <?= number_format($order['total_amount'], 2) ?></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="orders.php" class="action-btn secondary">
                    <i class="fas fa-list"></i>
                    View All Orders
                </a>
                <a href="products.php" class="action-btn primary">
                    <i class="fas fa-shopping-bag"></i>
                    Continue Shopping
                </a>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Enhanced status tracking functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animations to progress steps
            const progressSteps = document.querySelectorAll('.progress-step');
            const progressLines = document.querySelectorAll('.progress-line');
            
            // Animate progress steps on load
            progressSteps.forEach((step, index) => {
                setTimeout(() => {
                    step.style.opacity = '0';
                    step.style.transform = 'translateY(20px)';
                    step.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        step.style.opacity = '1';
                        step.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
            
            // Animate progress lines
            progressLines.forEach((line, index) => {
                if (line.classList.contains('completed')) {
                    setTimeout(() => {
                        line.style.width = '0';
                        line.style.transition = 'width 0.8s ease';
                        
                        setTimeout(() => {
                            line.style.width = '100%';
                        }, 100);
                    }, (index + 1) * 300);
                }
            });
            
            // Add hover effects to status messages
            const statusMessage = document.querySelector('.status-message');
            if (statusMessage) {
                statusMessage.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                statusMessage.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            }
        });
    </script>

</body>
</html>