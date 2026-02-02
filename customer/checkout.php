<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php?error=" . urlencode("Your cart is empty."));
    exit();
}

$customerId = $_SESSION['customer_id'];

try {
    $pdo->beginTransaction();

    // Verify all cart items are still available and calculate total
    $totalAmount = 0;
    $orderItems = [];
    
    foreach ($_SESSION['cart'] as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND status = 'Available'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception("Product '" . htmlspecialchars($item['name']) . "' is no longer available.");
        }
        
        if ($quantity > $product['stock']) {
            throw new Exception("Not enough stock for '" . htmlspecialchars($item['name']) . "'. Only " . $product['stock'] . " units available.");
        }
        
        $unitPrice = $product['price'];
        $subtotal = $quantity * $unitPrice;
        $totalAmount += $subtotal;
        
        $orderItems[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice
        ];
    }

    // Create order
    $orderStmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'Pending')");
    $orderStmt->execute([$customerId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Insert order items and update stock
    foreach ($orderItems as $item) {
        // Insert order item
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['unit_price']]);
        
        // Update product stock (decrease)
        $updateStockStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
        $updateStockStmt->execute([$item['quantity'], $item['product_id']]);
        
        // Update product status if stock reaches 0
        $statusStmt = $pdo->prepare("UPDATE products SET status = CASE WHEN stock <= 0 THEN 'Out of Stock' ELSE 'Available' END WHERE product_id = ?");
        $statusStmt->execute([$item['product_id']]);
    }

    $pdo->commit();

    // Clear cart
    $_SESSION['cart'] = [];

    header("Location: order_success.php?order_id=" . $orderId);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: cart.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
