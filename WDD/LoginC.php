<?php
session_start();
include 'DBConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare SQL to prevent SQL injection
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: AdminDashboard.php");
                break;
            case 'supplier':
                header("Location: AddProduct.php");
                break;
            case 'customer':
                header("Location: Productpage.php");
                break;
            default:
                header("Location: Login.php?error=invalidrole");
                break;
        }
        exit();
    } else {
        header("Location: Login.php?error=invalidcredentials");
        exit();
    }
}
?>