<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = intval($_POST['order_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');

    // Valid status values
    $allowedStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

    if ($orderId <= 0 || !in_array($newStatus, $allowedStatuses)) {
        header("Location: orders.php?error=" . urlencode("Invalid request. Please check order ID and status."));
        exit();
    }

    try {
        // Check if order exists first
        $checkStmt = $pdo->prepare("SELECT order_id, status FROM orders WHERE order_id = ?");
        $checkStmt->execute([$orderId]);
        $existingOrder = $checkStmt->fetch();
        
        if (!$existingOrder) {
            header("Location: orders.php?error=" . urlencode("Order #$orderId not found."));
            exit();
        }
        
        $currentStatus = trim($existingOrder['status']);
        
        // Don't update if status is the same
        if ($currentStatus === $newStatus) {
            header("Location: orders.php?info=" . urlencode("Order #$orderId is already set to $newStatus status."));
            exit();
        }

        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $result = $stmt->execute([$newStatus, $orderId]);
        
        if ($result && $stmt->rowCount() > 0) {
            $successMessage = "Order #$orderId status updated from '$currentStatus' to '$newStatus' successfully.";
            $redirectUrl = isset($_GET['from']) && $_GET['from'] === 'view' 
                ? "order_view.php?id=" . $orderId . "&success=" . urlencode($successMessage)
                : "orders.php?success=" . urlencode($successMessage);
        } else {
            $redirectUrl = "orders.php?error=" . urlencode("Failed to update order #$orderId status. Please try again.");
        }
        
        header("Location: " . $redirectUrl);
        exit();
    } catch (Exception $e) {
        header("Location: orders.php?error=" . urlencode("Error updating order status: Database error occurred."));
        exit();
    }
}

header("Location: orders.php");
exit();
?>
