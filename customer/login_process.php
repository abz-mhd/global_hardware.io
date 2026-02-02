<?php
// customer/login_process.php
session_start();
require_once '../config/db.php';

// Helper: redirect with error
function redirect_with_error($msg) {
    header("Location: login.php?error=" . urlencode($msg));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        redirect_with_error("Email and password are required.");
    }

    // Lookup customer by email
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password'])) {
        // Check if account is active
        if ($customer['status'] === 'Inactive') {
            redirect_with_error("Your account is inactive. Please contact admin.");
        }

        // Success: Set session and redirect
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['name'];
        $_SESSION['customer_email'] = $customer['email'];
        header("Location: dashboard.php");
        exit();
    } else {
        redirect_with_error("Invalid email or password.");
    }
} else {
    redirect_with_error("Invalid request.");
}
?>
