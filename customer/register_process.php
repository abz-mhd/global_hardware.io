<?php
// customer/register_process.php
session_start();
require_once '../config/db.php';

// Helper: redirect with error
function redirect_with_error($msg) {
    header("Location: register.php?error=" . urlencode($msg));
    exit();
}

function redirect_with_success($msg) {
    header("Location: register.php?success=" . urlencode($msg));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        redirect_with_error("Name, Email, and Password are required fields.");
    }

    if (strlen($password) < 6) {
        redirect_with_error("Password must be at least 6 characters long.");
    }

    if ($password !== $confirmPassword) {
        redirect_with_error("Passwords do not match.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_error("Invalid email format.");
    }

    try {
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetchColumn() > 0) {
            redirect_with_error("Email already registered. Please use a different email or login.");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert customer
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, phone, address, status) 
            VALUES (?, ?, ?, ?, ?, 'Active')");
        $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);

        redirect_with_success("Registration successful! You can now login.");
    } catch (Exception $e) {
        redirect_with_error("Registration failed: " . $e->getMessage());
    }
} else {
    redirect_with_error("Invalid request.");
}
?>
