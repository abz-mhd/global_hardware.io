<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$customerId = intval($_GET['id'] ?? 0);

if ($customerId <= 0) {
    header("Location: customers.php");
    exit();
}

// Fetch customer details
$customerStmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$customerStmt->execute([$customerId]);
$customer = $customerStmt->fetch();

if (!$customer) {
    header("Location: customers.php?error=" . urlencode("Customer not found."));
    exit();
}

// Fetch all orders for this customer
$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$ordersStmt->execute([$customerId]);
$orders = $ordersStmt->fetchAll();

// Get order items for each order
$orderItems = [];
foreach ($orders as $order) {
    $itemsStmt = $pdo->prepare("SELECT oi.*, p.name as product_name 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?");
    $itemsStmt->execute([$order['order_id']]);
    $orderItems[$order['order_id']] = $itemsStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order History for <?= htmlspecialchars($customer['name']) ?> - GLOBAL HARDWARE Store</title>
    <meta charset="UTF-8">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin:0; min-height: 100vh;}
        .header {background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 20px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px;}
        .header-content {max-width: 1400px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center;}
        .logo {display: flex; align-items: center; gap: 15px;}
        .logo img {max-width: 140px; height: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));}
        .logo-text {font-size: 22px; font-weight: 600; letter-spacing: 0.5px;}
        .container {max-width: 1400px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        h1 {text-align: center; color: #ff6b35; font-size: 32px; margin-bottom: 30px; font-weight: 600;}
        h3 {color: #ff6b35; font-size: 22px; margin-bottom: 20px; font-weight: 600;}
        nav a {margin-right: 20px; text-decoration: none; color: white; font-weight: 500; padding: 8px 15px; border-radius: 6px; transition: all 0.3s;}
        .header nav a:hover {background: rgba(255,255,255,0.2); transform: translateY(-2px);}
        table {width:100%; border-collapse: separate; border-spacing: 0; margin-top: 25px; margin-bottom: 35px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        th {background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 15px; text-align: left; font-weight: 600; letter-spacing: 0.5px;}
        td {padding: 15px; border-bottom: 1px solid #e0e0e0; background: white;}
        tr:last-child td {border-bottom: none;}
        tr:hover td {background: #f8f9fa; transition: background 0.2s;}
        .back-link {display: inline-block; margin-bottom: 25px; color: #ff6b35; text-decoration: none; font-weight: 600; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;}
        .back-link:hover {transform: translateX(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
        .order-details {margin-bottom: 30px; padding: 25px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e9ecef;}
        .status-pending {color: #f57c00; font-weight: 600; padding: 6px 14px; background: #fff3cd; border-radius: 6px; display: inline-block;}
        .status-processing {color: #1976d2; font-weight: 600; padding: 6px 14px; background: #cfe2ff; border-radius: 6px; display: inline-block;}
        .status-completed {color: #ff6b35; font-weight: 600; padding: 6px 14px; background: #ffe0b2; border-radius: 6px; display: inline-block;}
        .status-cancelled {color: #d32f2f; font-weight: 600; padding: 6px 14px; background: #f8d7da; border-radius: 6px; display: inline-block;}
        .items-table {margin-top: 15px; font-size: 0.95em;}
        .items-table th {background: linear-gradient(135deg, #757575 0%, #616161 100%);}
        p {color: #333; line-height: 1.8; margin-bottom: 10px;}
        strong {color: #ff6b35;}
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
        <h1>Order History for <?= htmlspecialchars($customer['name']) ?></h1>

        <a href="customers.php" class="back-link">← Back to Customers</a>

        <p><strong>Customer:</strong> <?= htmlspecialchars($customer['name']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($customer['phone'] ?? 'N/A') ?><br>
        <strong>Total Orders:</strong> <?= count($orders) ?></p>

        <?php if (empty($orders)): ?>
            <p>This customer has no orders yet.</p>
        <?php else: ?>
            <?php foreach($orders as $order): ?>
                <div class="order-details">
                    <h3>Order #<?= $order['order_id'] ?></h3>
                    <p><strong>Order Date:</strong> <?= $order['order_date'] ?><br>
                    <strong>Total Amount:</strong> LKR <?= number_format($order['total_amount'], 2) ?><br>
                    <strong>Status:</strong> <span class="status-<?= strtolower($order['status']) ?>"><?= $order['status'] ?></span></p>
                    
                    <h4>Ordered Items:</h4>
                    <table class="items-table">
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                        <?php 
                        $items = $orderItems[$order['order_id']] ?? [];
                        foreach($items as $item): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>LKR <?= number_format($item['unit_price'], 2) ?></td>
                            <td>LKR <?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
