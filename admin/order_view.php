<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$orderId = intval($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit();
}

// Fetch order details with customer information
$orderStmt = $pdo->prepare("SELECT o.*, c.name as customer_name, c.email as customer_email, 
    c.phone as customer_phone, c.address as customer_address
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch();

if (!$order) {
    header("Location: orders.php?error=" . urlencode("Order not found."));
    exit();
}

// Fetch order items with product details
$itemsStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.product_id 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.order_item_id");
$itemsStmt->execute([$orderId]);
$orderItems = $itemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Details #<?= $orderId ?> - GLOBAL HARDWARE Store</title>
    <meta charset="UTF-8">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin:0; min-height: 100vh;}
        .header {background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 20px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;}
        .header-content {max-width: 1200px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center;}
        .logo {display: flex; align-items: center; gap: 15px;}
        .logo img {max-width: 140px; height: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));}
        .logo-text {font-size: 22px; font-weight: 600; letter-spacing: 0.5px;}
        .container {max-width: 1200px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        h1 {text-align: center; color: #ff6b35; font-size: 32px; margin-bottom: 30px; font-weight: 600;}
        h3 {color: #ff6b35; font-size: 22px; margin-bottom: 20px; font-weight: 600;}
        nav a {margin-right: 20px; text-decoration: none; color: white; font-weight: 500; padding: 8px 15px; border-radius: 6px; transition: all 0.3s;}
        .header nav a:hover {background: rgba(255,255,255,0.2); transform: translateY(-2px);}
        .back-link {display: inline-block; margin-bottom: 25px; color: #ff6b35; text-decoration: none; font-weight: 600; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;}
        .back-link:hover {transform: translateX(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
        .info-section {background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 25px; margin-bottom: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e9ecef;}
        .info-section h3 {margin-top: 0; margin-bottom: 20px;}
        .info-row {margin-bottom: 15px; font-size: 15px; color: #333; line-height: 1.8;}
        .info-label {font-weight: 600; color: #666; display: inline-block; width: 150px;}
        table {width:100%; border-collapse: separate; border-spacing: 0; margin-top: 25px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        th {background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 15px; text-align: left; font-weight: 600; letter-spacing: 0.5px;}
        td {padding: 15px; border-bottom: 1px solid #e0e0e0; background: white;}
        tr:last-child td {border-bottom: none;}
        tr:hover td {background: #f8f9fa; transition: background 0.2s;}
        .status-pending {color: #f57c00; font-weight: 600; padding: 6px 14px; background: #fff3cd; border-radius: 6px; display: inline-block;}
        .status-processing {color: #1976d2; font-weight: 600; padding: 6px 14px; background: #cfe2ff; border-radius: 6px; display: inline-block;}
        .status-completed {color: #ff6b35; font-weight: 600; padding: 6px 14px; background: #ffe0b2; border-radius: 6px; display: inline-block;}
        .status-cancelled {color: #d32f2f; font-weight: 600; padding: 6px 14px; background: #f8d7da; border-radius: 6px; display: inline-block;}
        .total-row {background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-weight: 700; font-size: 18px; color: #ff6b35;}
        select {padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 8px; margin-right: 15px; font-size: 14px; transition: all 0.3s; cursor: pointer;}
        select:focus {outline: none; border-color: #ff6b35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1);}
        button {padding: 12px 25px; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 15px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255,107,53,0.4);}
        button:hover {transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,107,53,0.5);}
        .status-update-form {background: linear-gradient(135deg, #fff3cd 0%, #ffe082 100%); padding: 25px; border-radius: 16px; margin-top: 25px; box-shadow: 0 4px 15px rgba(255,193,7,0.3); border: 2px solid #ffc107;}
        .status-update-form h3 {color: #856404; margin-bottom: 15px;}
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="../Images/GLOBAL.png" alt="Global Hardware Logo">
                <span class="logo-text">GLOBAL HARDWARE</span>
            </div>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="suppliers.php">Suppliers</a>
                <a href="customers.php">Customers</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </div>
    <div class="container">
        <h1>Order Details #<?= $orderId ?></h1>

        <a href="orders.php" class="back-link">← Back to Orders</a>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #ffebee; color: #c62828; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ef5350;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #fff3e0; color: #ff6b35; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ff6b35;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <div class="info-section">
            <h3>Order Information</h3>
            <div class="info-row">
                <span class="info-label">Order ID:</span>
                <span>#<?= $order['order_id'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span><?= date('Y-m-d H:i:s', strtotime($order['order_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status-<?= strtolower($order['status']) ?>"><?= $order['status'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span style="font-size: 1.2em; font-weight: bold;">LKR <?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>

        <div class="info-section">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span><?= htmlspecialchars($order['customer_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span><?= htmlspecialchars($order['customer_email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span><?= htmlspecialchars($order['customer_phone'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span><?= htmlspecialchars($order['customer_address'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="status-update-form">
            <h3>Update Order Status</h3>
            <form action="order_process.php?from=view" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <select name="status">
                    <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        </div>

        <h3>Ordered Items</h3>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
            <?php 
            $grandTotal = 0;
            foreach($orderItems as $item): 
                $subtotal = $item['quantity'] * $item['unit_price'];
                $grandTotal += $subtotal;
            ?>
            <tr>
                <td><?= $item['product_id'] ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>LKR <?= number_format($item['unit_price'], 2) ?></td>
                <td>LKR <?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Amount:</td>
                <td>LKR <?= number_format($order['total_amount'], 2) ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
