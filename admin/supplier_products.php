<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$supplierId = intval($_GET['id'] ?? 0);

if ($supplierId <= 0) {
    header("Location: suppliers.php");
    exit();
}

// Fetch supplier details
$supplierStmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$supplierStmt->execute([$supplierId]);
$supplier = $supplierStmt->fetch();

if (!$supplier) {
    header("Location: suppliers.php?error=" . urlencode("Supplier not found."));
    exit();
}

// Fetch products supplied by this supplier
$productsStmt = $pdo->prepare("SELECT p.* FROM products p
    JOIN product_supplier ps ON p.product_id = ps.product_id
    WHERE ps.supplier_id = ?
    ORDER BY p.name");
$productsStmt->execute([$supplierId]);
$products = $productsStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products Supplied by <?= htmlspecialchars($supplier['name']) ?> - GLOBAL HARDWARE Store</title>
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
        nav a {margin-right: 20px; text-decoration: none; color: white; font-weight: 500; padding: 8px 15px; border-radius: 6px; transition: all 0.3s;}
        .header nav a:hover {background: rgba(255,255,255,0.2); transform: translateY(-2px);}
        table {width:100%; border-collapse: separate; border-spacing: 0; margin-top: 25px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        th {background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 15px; text-align: left; font-weight: 600; letter-spacing: 0.5px;}
        td {padding: 15px; border-bottom: 1px solid #e0e0e0; background: white;}
        tr:last-child td {border-bottom: none;}
        tr:hover td {background: #f8f9fa; transition: background 0.2s;}
        .back-link {display: inline-block; margin-bottom: 25px; color: #ff6b35; text-decoration: none; font-weight: 600; padding: 10px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;}
        .back-link:hover {transform: translateX(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
        p {color: #333; line-height: 1.8; margin-bottom: 15px;}
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
        <h1>Products Supplied by <?= htmlspecialchars($supplier['name']) ?></h1>

        <a href="suppliers.php" class="back-link">← Back to Suppliers</a>

        <p><strong>Supplier:</strong> <?= htmlspecialchars($supplier['name']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?><br>
        <strong>Contact:</strong> <?= htmlspecialchars($supplier['contact'] ?? 'N/A') ?><br>
        <strong>Total Products:</strong> <?= count($products) ?></p>

        <?php if (empty($products)): ?>
            <p>This supplier has no products assigned.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
                <?php foreach($products as $product): ?>
                <tr>
                    <td><?= $product['product_id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td>LKR <?= number_format($product['price'], 2) ?></td>
                    <td><?= $product['stock'] ?></td>
                    <td><?= $product['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
