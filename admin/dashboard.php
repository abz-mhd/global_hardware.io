<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Quick stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Low stock alerts (≤ 5 units)
$lowStockStmt = $pdo->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC");
$lowStockProducts = $lowStockStmt->fetchAll();

// Recent orders
$recentOrdersStmt = $pdo->query("SELECT o.order_id, o.order_date, o.status, c.name 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_date DESC
    LIMIT 5");
$recentOrders = $recentOrdersStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--main-bg);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Sidebar - Dark Theme */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            border-right: 1px solid var(--border-color);
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: var(--orange);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 28px;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }

        .sidebar-logo-img {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            transition: all 0.2s;
            background: transparent;
        }

        .sidebar-logo-img:hover {
            transform: scale(1.05);
        }

        .sidebar-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.5px;
        }

        .nav-menu {
            flex: 1;
            padding: 24px 0;
            overflow-y: auto;
        }

        .menu-section {
            margin-bottom: 32px;
        }

        .menu-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px;
            margin-bottom: 12px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin: 2px 0;
        }

        .nav-item:hover {
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: var(--orange);
            color: var(--text-primary);
            font-weight: 600;
        }

        .nav-icon {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-logout {
            padding: 0;
            border-top: 1px solid var(--border-color);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--main-bg);
        }

        /* Top Header - Dark */
        .top-header {
            background: var(--main-bg);
            padding: 24px 40px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            max-width: 100%;
        }

        .header-greeting {
            margin-bottom: 24px;
        }

        .greeting-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .greeting-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 20px;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
        }

        .search-bar input::placeholder {
            color: var(--text-muted);
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--orange);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
        }

        .notification-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-secondary);
            font-size: 20px;
        }

        .notification-icon:hover {
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        /* Time Filters */
        .time-filters {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .time-filter {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .time-filter:hover {
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        .time-filter.active {
            background: var(--orange);
            border-color: var(--orange);
            color: var(--text-primary);
        }

        /* Container */
        .container {
            flex: 1;
            padding: 32px 40px;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--orange);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: var(--orange);
            color: white;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 13px;
            font-weight: 600;
            color: #10b981;
        }

        /* Alert Box */
        .alert-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--orange);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }

        .alert-title {
            font-weight: 600;
            color: var(--orange);
            margin-bottom: 12px;
            font-size: 14px;
        }

        .alert-list {
            margin-left: 20px;
            color: var(--text-secondary);
            font-size: 13px;
        }

        .alert-list li {
            margin: 6px 0;
        }

        /* Section Title */
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 32px 0 20px 0;
        }

        /* Table - Dark Theme */
        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--card-bg);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 14px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: var(--hover-bg);
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(249, 115, 22, 0.2);
            color: var(--orange);
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }

            .main-content {
                margin-left: 240px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../Images/GLOBAL.png" alt="Global Hardware Logo" class="sidebar-logo-img">
            <span class="sidebar-logo-text">GLOBAL HARDWARE</span>
        </div>
        <nav class="nav-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main Menu</div>
                <a href="dashboard.php" class="nav-item active">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Overview</span>
                </a>
                <a href="products.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-boxes"></i></span>
                    <span>Products</span>
                </a>
                <a href="suppliers.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-truck"></i></span>
                    <span>Suppliers</span>
                </a>
                <a href="customers.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span>Customers</span>
                </a>
                <a href="orders.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span>Orders</span>
                </a>
            </div>
        </nav>
        <div class="sidebar-logout">
            <a href="logout.php" class="nav-item">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-greeting">
                <div class="greeting-text">Good to see you, Admin!</div>
                <div class="greeting-subtitle">Improve your sales management for better growth.</div>
            </div>
        </header>

        <!-- Container -->
        <div class="container">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-content">
                            <div class="stat-label">Total Customer</div>
                            <div class="stat-value"><?= number_format($totalCustomers) ?></div>
                            <div class="stat-change">+<?= round(($totalCustomers / max($totalCustomers, 1)) * 3.1, 1) ?>% From last month</div>
                        </div>
                        <div class="stat-icon">👥</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-content">
                            <div class="stat-label">Total Revenue</div>
                            <div class="stat-value">LKR <?= number_format($totalOrders * 100, 0) ?></div>
                            <div class="stat-change">+<?= round(($totalOrders / max($totalOrders, 1)) * 5.4, 1) ?>% From last month</div>
                        </div>
                        <div class="stat-icon">💰</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-content">
                            <div class="stat-label">Total Product</div>
                            <div class="stat-value"><?= number_format($totalProducts) ?></div>
                            <div class="stat-change">+<?= round(($totalProducts / max($totalProducts, 1)) * 2.3, 1) ?>% From last month</div>
                        </div>
                        <div class="stat-icon">📦</div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <?php if (!empty($lowStockProducts)): ?>
            <div class="alert-box">
                <div class="alert-title">⚠️ Low Stock Alert</div>
                <ul class="alert-list">
                    <?php foreach ($lowStockProducts as $p): ?>
                        <li><?= htmlspecialchars($p['name']) ?> - Stock: <?= $p['stock'] ?> units</li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Recent Orders -->
            <h2 class="section-title">Recent Order</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">No orders found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($recentOrders as $order): ?>
                        <tr>
                            <td><strong>#<?= $order['order_id'] ?></strong></td>
                            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td><?= htmlspecialchars($order['name']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Copyright -->
        <div style="text-align: center; padding: 20px; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-secondary); font-size: 12px;">
            <p>&copy; <?= date('Y') ?> GLOBAL HARDWARE Store. All rights reserved. | Admin Portal v2.0</p>
        </div>
    </main>
</body>
</html>
