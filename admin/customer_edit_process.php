<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customerId = intval($_POST['customer_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'Active';
    $password = $_POST['password'] ?? '';

    if ($customerId <= 0 || empty($name) || empty($email)) {
        header("Location: customer_edit.php?id=" . $customerId . "&error=" . urlencode("Name and Email are required fields."));
        exit();
    }

    try {
        // Check if email already exists for another customer
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE email = ? AND customer_id != ?");
        $checkStmt->execute([$email, $customerId]);
        if ($checkStmt->fetchColumn() > 0) {
            header("Location: customer_edit.php?id=" . $customerId . "&error=" . urlencode("Email already exists. Please use a different email."));
            exit();
        }

        // Update customer (with optional password change)
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ?, 
                status = ?, password = ? WHERE customer_id = ?");
            $stmt->execute([$name, $email, $phone, $address, $status, $hashedPassword, $customerId]);
        } else {
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ?, 
                status = ? WHERE customer_id = ?");
            $stmt->execute([$name, $email, $phone, $address, $status, $customerId]);
        }
        
        header("Location: customers.php?success=" . urlencode("Customer updated successfully."));
        exit();
    } catch (Exception $e) {
        header("Location: customer_edit.php?id=" . $customerId . "&error=" . urlencode("Error updating customer: " . $e->getMessage()));
        exit();
    }
}

header("Location: customers.php");
exit();
?>
