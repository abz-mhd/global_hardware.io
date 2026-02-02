<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

header('Content-Type: application/json');
echo json_encode(['count' => $cartCount]);
?>