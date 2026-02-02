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
        // Add new supplier
        $name = trim($_POST['name'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($name) || empty($email)) {
            header("Location: suppliers.php?error=" . urlencode("Name and Email are required fields."));
            exit();
        }

        try {
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetchColumn() > 0) {
                header("Location: suppliers.php?error=" . urlencode("Email already exists. Please use a different email."));
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact, email, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $contact, $email, $address]);
            
            header("Location: suppliers.php?success=" . urlencode("Supplier added successfully."));
            exit();
        } catch (Exception $e) {
            header("Location: suppliers.php?error=" . urlencode("Error adding supplier: " . $e->getMessage()));
            exit();
        }

    } elseif ($action === 'delete') {
        // Delete supplier
        $supplierId = intval($_POST['supplier_id'] ?? 0);

        if ($supplierId > 0) {
            try {
                // Check if supplier has products assigned
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM product_supplier WHERE supplier_id = ?");
                $checkStmt->execute([$supplierId]);
                $productCount = $checkStmt->fetchColumn();

                if ($productCount > 0) {
                    header("Location: suppliers.php?error=" . urlencode("Cannot delete supplier. This supplier has " . $productCount . " product(s) assigned. Please remove product assignments first."));
                    exit();
                }

                $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
                $stmt->execute([$supplierId]);
                header("Location: suppliers.php?success=" . urlencode("Supplier deleted successfully."));
                exit();
            } catch (Exception $e) {
                header("Location: suppliers.php?error=" . urlencode("Error deleting supplier: " . $e->getMessage()));
                exit();
            }
        }
    }
}

header("Location: suppliers.php");
exit();
?>
