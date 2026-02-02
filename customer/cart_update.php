<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cartKey = intval($_POST['cart_key'] ?? -1);
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($cartKey < 0 || !isset($_SESSION['cart'][$cartKey])) {
        header("Location: cart.php?error=" . urlencode("Invalid cart item."));
        exit();
    }

    if ($quantity <= 0) {
        header("Location: cart.php?error=" . urlencode("Quantity must be at least 1."));
        exit();
    }

    // Check stock availability
    $productId = $_SESSION['cart'][$cartKey]['product_id'];
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ? AND status = 'Available'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: cart.php?error=" . urlencode("Product is no longer available."));
        exit();
    }

    if ($quantity > $product['stock']) {
        header("Location: cart.php?error=" . urlencode("Not enough stock. Only " . $product['stock'] . " units available."));
        exit();
    }

    // Update quantity
    $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
    header("Location: cart.php?success=" . urlencode("Cart updated successfully."));
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>
