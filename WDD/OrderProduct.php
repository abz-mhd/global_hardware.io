<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

include 'DBConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get product details from form
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Get current date/time
    $order_date = date('Y-m-d H:i:s');

    try {
        // Prepare SQL statement
        $sql = "INSERT INTO orders (
                    product_id, 
                    product_name, 
                    product_description,
                    product_price, 
                    quantity, 
                    total_price,
                    order_date,
                    user_id
                ) VALUES (
                    :product_id, 
                    :product_name, 
                    :product_description,
                    :product_price, 
                    :quantity, 
                    :total_price,
                    :order_date,
                    :user_id
                )";
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':product_description', $product_description);
        $stmt->bindParam(':product_price', $product_price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':order_date', $order_date);
        $stmt->bindParam(':user_id', $user_id);
        
        // Execute the query
        if ($stmt->execute()) {
            // Redirect to success page
            header("Location: ProductPage.php?order_id=" . $conn->lastInsertId());
            exit();
        } else {
            // Redirect with error
            header("Location: OrderProduct.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Log error and redirect
        error_log("Order Error: " . $e->getMessage());
        header("Location: OrderProduct.php?error=1");
        exit();
    }
}

// If not a POST request, continue to display the order form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product - Global Hardware</title>
    <link rel="stylesheet" href="Orderproduct.css">
    <style>
        /* Basic styling - you can move this to a separate CSS file */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
            margin-right: 15px;
        }
        
        .home-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .order-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .product-details {
            flex: 1;
            min-width: 300px;
        }
        
        .product-image {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .order-form {
            flex: 1;
            min-width: 300px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .submit-btn {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        
        .submit-btn:hover {
            background-color: #1a252f;
        }
        
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 2rem 0;
            margin-top: 2rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        
        .footer-section {
            flex: 1;
            min-width: 200px;
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        .footer-section h3 {
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }
        
        .copyright {
            text-align: center;
            padding: 10px;
            background-color: #1a252f;
            color: white;
        }
        
        @media (max-width: 768px) {
            .order-container {
                flex-direction: column;
            }
        }
    </style>
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

    <center>
         <!-- Order Container -->
    <div class="order-container">
        <!-- Product Details Section -->
        <div class="product-details">
            <h2>Order Details</h2>
            <img id="product-image" src="" alt="Product Image" class="product-image">
            <h3 id="product-name"></h3>
            <p id="product-description"></p>
            <p>Price: <span id="product-price"></span> LKR</p>
            <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" onchange="updateTotal()">
            </div>
            <h3>Total: <span id="total-price"></span> LKR</h3>
             <!-- Order Form Section -->
        <form method="POST" action="OrderProduct.php" class="order-form">
            <input type="hidden" id="product-id" name="product_id">
            <input type="hidden" id="product-name-input" name="product_name">
            <input type="hidden" id="product-description-input" name="product_description">
            <input type="hidden" id="product-price-input" name="product_price">
            <input type="hidden" id="total-price-input" name="total_price">
            <input type="hidden" name="quantity" id="quantity-input">
            
            <button type="submit" class="submit-btn">Place Order</button>
        </form>
    </div>
        </div>
        
       
    </center>

    <script>
        // Function to update total price when quantity changes
        function updateTotal() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const price = parseFloat(document.getElementById('product-price').textContent.replace(/,/g, ''));
            const total = quantity * price;
            document.getElementById('total-price').textContent = total.toLocaleString();
            document.getElementById('total-price-input').value = total;
            document.getElementById('quantity-input').value = quantity;
        }
        
        // When page loads, get the URL parameters and populate the form
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Get all parameters from URL
            const productId = urlParams.get('product');
            const productName = urlParams.get('name');
            const productPrice = urlParams.get('price');
            const productImage = urlParams.get('image');
            const productDesc = urlParams.get('desc');
            
            // Set the product details on the page
            if (productImage) {
                document.getElementById('product-image').src = decodeURIComponent(productImage);
            }
            if (productName) {
                document.getElementById('product-name').textContent = decodeURIComponent(productName);
                document.getElementById('product-name-input').value = decodeURIComponent(productName);
            }
            if (productDesc) {
                document.getElementById('product-description').textContent = decodeURIComponent(productDesc);
                document.getElementById('product-description-input').value = decodeURIComponent(productDesc);
            }
            if (productPrice) {
                document.getElementById('product-price').textContent = decodeURIComponent(productPrice);
                document.getElementById('product-price-input').value = decodeURIComponent(productPrice);
            }
            
            // Set hidden product ID field
            if (productId) {
                document.getElementById('product-id').value = decodeURIComponent(productId);
            }
            
            // Initialize total price
            updateTotal();
        });
    </script>
</body>
</html>