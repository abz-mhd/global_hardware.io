<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: Login.php");
    exit();
}
include 'DBConnection.php';

// Get product details if product_id is passed
$product = null;
if (isset($_GET['product_id'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $_GET['product_id']);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['user_id'];
    $product_id = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    $total_amount = $_POST["total_amount"];
    $order_date = date('Y-m-d');

    $sql = "INSERT INTO orders (customer_id, product_id, quantity, total_amount, order_date) 
            VALUES (:customer_id, :product_id, :quantity, :total_amount, :order_date)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':order_date', $order_date);

    if ($stmt->execute()) {
        // Update product quantity
        $updateSql = "UPDATE products SET quantity = quantity - :quantity WHERE id = :product_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':quantity', $quantity);
        $updateStmt->bindParam(':product_id', $product_id);
        $updateStmt->execute();

        header("Location: HomePage.php?order_success=1");
        exit();
    } else {
        header("Location: OrderProduct.php?error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product</title>
    <link rel="stylesheet" href="Orderproduct.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="Images/Hammer Tools Construction Logo.png" alt="Logo">
        </div>
        <h1>GLOBAL HARDWARE</h1>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> | 
            <a href="Logout.php">Logout</a>
        </div>
    </header>

    <!-- Order Product Form Section -->
    <section class="form-section">
        <div class="form-container">
            <h2>Order Product</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">Error placing order. Please try again.</div>
            <?php endif; ?>
            <form action="OrderProduct.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id'] ?? ''; ?>">
                <div class="form-group">
                    <label for="product-name">Product Name:</label>
                    <input type="text" id="product-name" value="<?php echo $product['name'] ?? ''; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="price">Price (LKR):</label>
                    <input type="number" id="price" value="<?php echo $product['price'] ?? ''; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" 
                           max="<?php echo $product['quantity'] ?? ''; ?>" required
                           onchange="calculateTotal()">
                </div>
                <div class="form-group">
                    <label for="total-amount">Total Amount (LKR):</label>
                    <input type="number" id="total-amount" name="total_amount" readonly>
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-order-btn">Place Order</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function calculateTotal() {
            const price = parseFloat(document.getElementById('price').value);
            const quantity = parseInt(document.getElementById('quantity').value);
            const total = price * quantity;
            document.getElementById('total-amount').value = total.toFixed(2);
        }
        
        // Calculate total on page load if product is set
        <?php if ($product): ?>
        window.onload = calculateTotal;
        <?php endif; ?>
    </script>

    <!-- Footer Section -->
    <footer>
        <!-- Your existing footer content -->
    </footer>
</body>
</html>