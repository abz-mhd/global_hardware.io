<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Function to handle image upload
function uploadImage($file, $uploadDir = '../assets/images/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ''; // No file uploaded or error occurred
    }

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.");
    }

    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $maxSize) {
        throw new Exception("File size exceeds 5MB limit.");
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Failed to upload image file.");
    }

    return $filename; // Return just the filename
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Add new product
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'Available';
        $suppliers = $_POST['suppliers'] ?? [];

        if (empty($name) || empty($category) || $price <= 0) {
            header("Location: products.php?error=" . urlencode("Please fill all required fields correctly."));
            exit();
        }

        try {
            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = uploadImage($_FILES['image']);
            }

            $pdo->beginTransaction();

            // Insert product
            $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, description, image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $price, $stock, $description, $image, $status]);
            $productId = $pdo->lastInsertId();

            // Assign suppliers
            if (!empty($suppliers) && is_array($suppliers)) {
                $supplierStmt = $pdo->prepare("INSERT INTO product_supplier (product_id, supplier_id) VALUES (?, ?)");
                foreach ($suppliers as $supplierId) {
                    $supplierStmt->execute([$productId, intval($supplierId)]);
                }
            }

            $pdo->commit();
            header("Location: products.php?success=" . urlencode("Product added successfully."));
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: products.php?error=" . urlencode("Error adding product: " . $e->getMessage()));
            exit();
        }

    } elseif ($action === 'delete') {
        // Delete product
        $productId = intval($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->execute([$productId]);
                header("Location: products.php?success=" . urlencode("Product deleted successfully."));
                exit();
            } catch (Exception $e) {
                header("Location: products.php?error=" . urlencode("Error deleting product: " . $e->getMessage()));
                exit();
            }
        }

    } elseif ($action === 'update_stock') {
        // Update stock quantity
        $productId = intval($_POST['product_id'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);

        if ($productId > 0 && $stock >= 0) {
            try {
                $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
                $stmt->execute([$stock, $productId]);
                
                // Update status based on stock
                $statusStmt = $pdo->prepare("UPDATE products SET status = CASE 
                    WHEN stock = 0 THEN 'Out of Stock' 
                    ELSE 'Available' 
                    END WHERE product_id = ?");
                $statusStmt->execute([$productId]);
                
                header("Location: products.php?success=" . urlencode("Stock updated successfully."));
                exit();
            } catch (Exception $e) {
                header("Location: products.php?error=" . urlencode("Error updating stock: " . $e->getMessage()));
                exit();
            }
        }
    }
}

header("Location: products.php");
exit();
?>
