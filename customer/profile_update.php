<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$customerId = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name) || empty($email)) {
        header("Location: profile.php?error=" . urlencode("Name and Email are required fields."));
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: profile.php?error=" . urlencode("Invalid email format."));
        exit();
    }

    try {
        // Check if email already exists for another customer
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE email = ? AND customer_id != ?");
        $checkStmt->execute([$email, $customerId]);
        
        if ($checkStmt->fetchColumn() > 0) {
            header("Location: profile.php?error=" . urlencode("Email already exists. Please use a different email."));
            exit();
        }

        // Update customer profile
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?");
        $stmt->execute([$name, $email, $phone, $address, $customerId]);

        // Update session
        $_SESSION['customer_name'] = $name;
        $_SESSION['customer_email'] = $email;

        header("Location: profile.php?success=" . urlencode("Profile updated successfully."));
        exit();
    } catch (Exception $e) {
        header("Location: profile.php?error=" . urlencode("Error updating profile: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>
