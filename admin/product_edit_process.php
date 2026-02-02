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
    $productId = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $currentImage = trim($_POST['current_image'] ?? '');
    $status = $_POST['status'] ?? 'Available';
    $suppliers = $_POST['suppliers'] ?? [];

    if ($productId <= 0 || empty($name) || empty($category) || $price <= 0) {
        header("Location: product_edit.php?id=" . $productId . "&error=" . urlencode("Please fill all required fields correctly."));
        exit();
    }

    try {
        // Handle image upload - if new image uploaded, use it; otherwise keep current
        $image = $currentImage; // Default to current image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if (!empty($currentImage) && file_exists('../assets/images/' . $currentImage)) {
                @unlink('../assets/images/' . $currentImage);
            }
            // Upload new image
            $image = uploadImage($_FILES['image']);
        }

        $pdo->beginTransaction();

        // Update product
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, 
            description = ?, image = ?, status = ? WHERE product_id = ?");
        $stmt->execute([$name, $category, $price, $stock, $description, $image, $status, $productId]);

        // Remove existing supplier assignments
        $deleteStmt = $pdo->prepare("DELETE FROM product_supplier WHERE product_id = ?");
        $deleteStmt->execute([$productId]);

        // Assign new suppliers
        if (!empty($suppliers) && is_array($suppliers)) {
            $supplierStmt = $pdo->prepare("INSERT INTO product_supplier (product_id, supplier_id) VALUES (?, ?)");
            foreach ($suppliers as $supplierId) {
                $supplierStmt->execute([$productId, intval($supplierId)]);
            }
        }

        $pdo->commit();
        header("Location: products.php?success=" . urlencode("Product updated successfully."));
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: product_edit.php?id=" . $productId . "&error=" . urlencode("Error updating product: " . $e->getMessage()));
        exit();
    }
}

header("Location: products.php");
exit();
?>
