<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$customerId = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: profile.php?pwd_error=" . urlencode("All password fields are required."));
        exit();
    }

    if (strlen($newPassword) < 6) {
        header("Location: profile.php?pwd_error=" . urlencode("New password must be at least 6 characters long."));
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header("Location: profile.php?pwd_error=" . urlencode("New passwords do not match."));
        exit();
    }

    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM customers WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch();

        if (!$customer || !password_verify($currentPassword, $customer['password'])) {
            header("Location: profile.php?pwd_error=" . urlencode("Current password is incorrect."));
            exit();
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $updateStmt = $pdo->prepare("UPDATE customers SET password = ? WHERE customer_id = ?");
        $updateStmt->execute([$hashedPassword, $customerId]);

        header("Location: profile.php?pwd_success=" . urlencode("Password changed successfully."));
        exit();
    } catch (Exception $e) {
        header("Location: profile.php?pwd_error=" . urlencode("Error changing password: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>
