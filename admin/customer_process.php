<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Add new customer manually
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = $_POST['password'] ?? '';
        $status = $_POST['status'] ?? 'Active';

        if (empty($name) || empty($email) || empty($password)) {
            header("Location: customers.php?error=" . urlencode("Name, Email, and Password are required fields."));
            exit();
        }

        try {
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetchColumn() > 0) {
                header("Location: customers.php?error=" . urlencode("Email already exists. Please use a different email."));
                exit();
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, phone, address, status) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address, $status]);
            
            header("Location: customers.php?success=" . urlencode("Customer added successfully."));
            exit();
        } catch (Exception $e) {
            header("Location: customers.php?error=" . urlencode("Error adding customer: " . $e->getMessage()));
            exit();
        }

    } elseif ($action === 'toggle_status') {
        // Activate/Deactivate customer
        $customerId = intval($_POST['customer_id'] ?? 0);
        $currentStatus = $_POST['current_status'] ?? '';
        $newStatus = ($currentStatus === 'Active') ? 'Inactive' : 'Active';

        if ($customerId > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE customers SET status = ? WHERE customer_id = ?");
                $stmt->execute([$newStatus, $customerId]);
                header("Location: customers.php?success=" . urlencode("Customer status updated successfully."));
                exit();
            } catch (Exception $e) {
                header("Location: customers.php?error=" . urlencode("Error updating customer status: " . $e->getMessage()));
                exit();
            }
        }

    } elseif ($action === 'delete') {
        // Delete customer (orders will be deleted via CASCADE)
        $customerId = intval($_POST['customer_id'] ?? 0);

        if ($customerId > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
                $stmt->execute([$customerId]);
                header("Location: customers.php?success=" . urlencode("Customer deleted successfully."));
                exit();
            } catch (Exception $e) {
                header("Location: customers.php?error=" . urlencode("Error deleting customer: " . $e->getMessage()));
                exit();
            }
        }
    }
}

header("Location: customers.php");
exit();
?>
