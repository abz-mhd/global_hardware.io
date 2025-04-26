<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supplier') {
    header("Location: Login.php");
    exit();
}
include 'DBConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $supplier_id = $_SESSION['user_id'];

    $sql = "INSERT INTO products (name, description, quantity, price, supplier_id) 
            VALUES (:name, :description, :quantity, :price, :supplier_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':supplier_id', $supplier_id);

    if ($stmt->execute()) {
        header("Location: AddProduct.php?success=1");
        exit();
    } else {
        header("Location: AddProduct.php?error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="Addproduct.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="Images/Hammer Tools Construction Logo.png" alt="Logo">
        </div>
        <h1>GLOBAL HARDWARE</h1>
        <button class="home-btn" onclick="location.href='homepage.php';">Home</button>
    </header>

    <!-- Add Product Form Section -->
    <section class="form-section">
        <div class="form-container">
            <h2>Add Product</h2>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Product added successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">Error adding product. Please try again.</div>
            <?php endif; ?>
            <form action="AddProduct.php" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (LKR):</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div class="button-container">
                    <button type="submit" class="add-product-btn">Add Product</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <!-- Your existing footer content -->
    </footer>
</body>
</html>