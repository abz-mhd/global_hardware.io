<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Fetch all products
$productsStmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $productsStmt->fetchAll();

// Fetch all suppliers for dropdown
$suppliersStmt = $pdo->query("SELECT * FROM suppliers ORDER BY name");
$allSuppliers = $suppliersStmt->fetchAll();

// Get suppliers for each product (for display)
$productSuppliers = [];
foreach ($products as $product) {
    $psStmt = $pdo->prepare("SELECT s.supplier_id, s.name FROM product_supplier ps 
        JOIN suppliers s ON ps.supplier_id = s.supplier_id 
        WHERE ps.product_id = ?");
    $psStmt->execute([$product['product_id']]);
    $productSuppliers[$product['product_id']] = $psStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - GLOBAL HARDWARE Store</title>
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
            --danger-color: #ef4444;
            --success-color: #10b981;
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
            padding: 32px 40px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-greeting {
            margin-bottom: 24px;
        }

        .greeting-text {
            font-size: 32px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .greeting-subtitle {
            font-size: 16px;
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

        /* Container */
        .container {
            flex: 1;
            padding: 32px 40px;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
        }

        /* Typography */
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        /* Form Section */
        .form-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: all 0.2s;
            font-family: inherit;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        input[type="number"] {
            width: 100px;
        }

        input[type="file"] {
            padding: 8px;
        }

        small {
            display: block;
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* Buttons */
        button, .btn {
            padding: 10px 24px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover, .btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        button.danger, .btn.danger {
            background: var(--danger-color);
        }

        button.danger:hover, .btn.danger:hover {
            background: #dc2626;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-buttons button {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* Table - Dark Theme */
        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--card-bg);
        }

        th {
            padding: 14px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 14px 20px;
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

        .suppliers-list {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Messages */
        .error, .success {
            padding: 14px 18px;
            margin-bottom: 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            border-left: 4px solid;
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-left-color: var(--danger-color);
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            border-left-color: var(--success-color);
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
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../Images/GLOBAL.png" alt="Global Hardware Logo" class="sidebar-logo-img">
            <span class="sidebar-logo-text">GLOBAL HARDWARE</span>
        </div>
        <nav class="nav-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main Menu</div>
                <a href="dashboard.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Overview</span>
                </a>
                <a href="products.php" class="nav-item active">
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
    
    <main class="main-content">
        <header class="top-header">
            <div class="header-greeting">
                <div class="greeting-text">Product Management</div>
                <div class="greeting-subtitle">Manage your inventory and product catalog efficiently.</div>
            </div>
        </header>
        <div class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Add New Product</h2>
            <form action="product_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Product Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="category" required>
                </div>
                <div class="form-group">
                    <label>Price (LKR):</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity:</label>
                    <input type="number" name="stock" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Product Image:</label>
                    <input type="file" name="image" accept="image/*">
                    <small style="color: #666;">Select an image file (JPG, PNG, GIF - Max 5MB)</small>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="Available">Available</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign Suppliers (Hold Ctrl/Cmd to select multiple):</label>
                    <select name="suppliers[]" multiple size="5">
                        <?php foreach($allSuppliers as $supplier): ?>
                            <option value="<?= $supplier['supplier_id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Add Product</button>
            </form>
        </div>

        <h2>All Products</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Suppliers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach($products as $product): ?>
            <tr>
                <td>
                    <?php if (!empty($product['image'])): ?>
                        <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <div style="width: 60px; height: 60px; background: var(--hover-bg); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--text-muted);">No Image</div>
                    <?php endif; ?>
                </td>
                <td><?= $product['product_id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td>LKR <?= number_format($product['price'], 2) ?></td>
                <td>
                    <form action="product_process.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="update_stock">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <input type="number" name="stock" value="<?= $product['stock'] ?>" style="width:70px; padding: 6px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary);">
                        <button type="submit" style="padding: 6px 12px; font-size: 12px;">Update</button>
                    </form>
                </td>
                <td><?= $product['status'] ?></td>
                <td class="suppliers-list">
                    <?php 
                    if (!empty($productSuppliers[$product['product_id']])) {
                        foreach($productSuppliers[$product['product_id']] as $supp) {
                            echo htmlspecialchars($supp['name']) . "<br>";
                        }
                    } else {
                        echo "No suppliers assigned";
                    }
                    ?>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="product_edit.php?id=<?= $product['product_id'] ?>"><button>Edit</button></a>
                        <form action="product_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <button type="submit" class="danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>

        <!-- Copyright -->
        <div style="text-align: center; padding: 20px; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-secondary); font-size: 12px;">
            <p>&copy; <?= date('Y') ?> GLOBAL HARDWARE Store. All rights reserved. | Admin Portal v2.0</p>
        </div>
    </div>
</body>
</html>
