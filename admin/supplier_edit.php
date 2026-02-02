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
$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$stmt->execute([$supplierId]);
$supplier = $stmt->fetch();

if (!$supplier) {
    header("Location: suppliers.php?error=" . urlencode("Supplier not found."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier - GLOBAL HARDWARE Store</title>
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
            --success-color: #10b981;
            --danger-color: #ef4444;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--main-bg);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar - Same as other admin pages */
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
            border-radius: 8px;
            margin: 2px 8px;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .sidebar-logout {
            padding: 0;
            border-top: 1px solid var(--border-color);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--main-bg);
        }

        .top-header {
            background: var(--main-bg);
            padding: 24px 40px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .header-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .content-area {
            flex: 1;
            padding: 40px;
        }

        /* Form Styling */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group.half {
            display: inline-block;
            width: 48%;
            margin-right: 4%;
        }

        .form-group.half:nth-child(even) {
            margin-right: 0;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: var(--hover-bg);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            transform: translateY(-1px);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 32px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--orange);
            color: white;
        }

        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        /* Messages */
        .message {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
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
        @media (max-width: 768px) {
            .form-group.half {
                width: 100%;
                margin-right: 0;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../Images/GLOBAL.png" alt="Global Hardware Logo" class="sidebar-logo-img">
            <div class="sidebar-logo-text">GLOBAL HARDWARE</div>
        </div>
        <nav class="nav-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main Menu</div>
                <a href="dashboard.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Overview</span>
                </a>
                <a href="products.php" class="nav-item">
                    <span class="nav-icon"><i class="fas fa-boxes"></i></span>
                    <span>Products</span>
                </a>
                <a href="suppliers.php" class="nav-item active">
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
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-header">
            <h1 class="header-title">Edit Supplier</h1>
            <p class="header-subtitle">Update supplier information and contact details</p>
        </div>

        <div class="content-area">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Supplier Details</h2>
                    <p class="form-subtitle">Modify the supplier information below</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>

                <form action="supplier_edit_process.php" method="POST">
                    <input type="hidden" name="supplier_id" value="<?= $supplier['supplier_id'] ?>">
                    
                    <div class="form-group half">
                        <label>Supplier Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>
                    </div>
                    
                    <div class="form-group half">
                        <label>Contact Number</label>
                        <input type="tel" name="contact" value="<?= htmlspecialchars($supplier['contact'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($supplier['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="4" placeholder="Enter supplier address"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Supplier
                        </button>
                        <a href="suppliers.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Copyright -->
        <div style="text-align: center; padding: 20px; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-secondary); font-size: 12px;">
            <p>&copy; <?= date('Y') ?> GLOBAL HARDWARE Store. All rights reserved. | Admin Portal v2.0</p>
        </div>
    </div>
</body>
</html>
