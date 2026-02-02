<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cartKey = intval($_POST['cart_key'] ?? -1);

    if ($cartKey >= 0 && isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        header("Location: cart.php?success=" . urlencode("Item removed from cart."));
        exit();
    } else {
        header("Location: cart.php?error=" . urlencode("Invalid cart item."));
        exit();
    }
} else {
    header("Location: cart.php");
    exit();
}
?>
