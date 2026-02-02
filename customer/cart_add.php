<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $productName = $_POST['product_name'] ?? '';
    $productPrice = floatval($_POST['product_price'] ?? 0);

    if ($productId <= 0 || $quantity <= 0) {
        header("Location: $redirectPage?error=" . urlencode("Invalid product or quantity."));
        exit();
    }

    // Check product availability and stock
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND status = 'Available'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    // Get the referring page to redirect back
    $referer = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    $redirectPage = (strpos($referer, 'products.php') !== false) ? 'products.php' : 'dashboard.php';

    if (!$product) {
        header("Location: $redirectPage?error=" . urlencode("Product not available."));
        exit();
    }

    if ($quantity > $product['stock']) {
        header("Location: $redirectPage?error=" . urlencode("Not enough stock available. Only " . $product['stock'] . " units in stock."));
        exit();
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product already in cart
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $productId) {
            // Update quantity (but check total doesn't exceed stock)
            $newQuantity = $item['quantity'] + $quantity;
            if ($newQuantity > $product['stock']) {
                header("Location: $redirectPage?error=" . urlencode("Cannot add more. Only " . $product['stock'] . " units in stock. You already have " . $item['quantity'] . " in cart."));
                exit();
            }
            $_SESSION['cart'][$key]['quantity'] = $newQuantity;
            $found = true;
            break;
        }
    }

    // If not found, add new item
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => $quantity
        ];
    }

    // Get the referring page to redirect back
    $referer = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    $redirectPage = (strpos($referer, 'products.php') !== false) ? 'products.php' : 'dashboard.php';

    header("Location: $redirectPage?success=" . urlencode("Product added to cart successfully."));
    exit();
} else {
    $referer = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    $redirectPage = (strpos($referer, 'products.php') !== false) ? 'products.php' : 'dashboard.php';
    header("Location: $redirectPage");
    exit();
}
?>
