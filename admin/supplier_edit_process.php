<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $supplierId = intval($_POST['supplier_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($supplierId <= 0 || empty($name) || empty($email)) {
        header("Location: supplier_edit.php?id=" . $supplierId . "&error=" . urlencode("Name and Email are required fields."));
        exit();
    }

    try {
        // Check if email already exists for another supplier
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE email = ? AND supplier_id != ?");
        $checkStmt->execute([$email, $supplierId]);
        if ($checkStmt->fetchColumn() > 0) {
            header("Location: supplier_edit.php?id=" . $supplierId . "&error=" . urlencode("Email already exists. Please use a different email."));
            exit();
        }

        $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact = ?, email = ?, address = ? WHERE supplier_id = ?");
        $stmt->execute([$name, $contact, $email, $address, $supplierId]);
        
        header("Location: suppliers.php?success=" . urlencode("Supplier updated successfully."));
        exit();
    } catch (Exception $e) {
        header("Location: supplier_edit.php?id=" . $supplierId . "&error=" . urlencode("Error updating supplier: " . $e->getMessage()));
        exit();
    }
}

header("Location: suppliers.php");
exit();
?>
