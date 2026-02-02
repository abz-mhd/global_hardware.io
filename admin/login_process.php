<?php
// admin/login_process.php
session_start();
require_once '../config/db.php';

// Helper: redirect with error
function redirect_with_error($msg) {
    header("Location: login.php?error=" . urlencode($msg));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        redirect_with_error("All fields are required.");
    }

    // Lookup admin by username
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Success: Set session and redirect
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        redirect_with_error("Invalid username or password.");
    }
} else {
    redirect_with_error("Invalid request.");
}
?>
