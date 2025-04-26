<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: Login.php");
    exit();
}
include 'DBConnection.php';

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $supplier_id = $_POST["supplier_id"];

    $sql = "INSERT INTO products (name, description, quantity, price, supplier_id) 
            VALUES (:name, :description, :quantity, :price, :supplier_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();
}

// Handle Delete Actions
if (isset($_GET['delete'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    
    if ($type == 'user') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    } elseif ($type == 'product') {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    } elseif ($type == 'order') {
        $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = :id");
    }
    
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header("Location: AdminDashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* CSS Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 60px;
        }
        h1 {
            margin: 0;
            font-size: 1.8rem;
            text-align: center;
            flex-grow: 1;
        }
        .user-info {
            font-size: 0.9rem;
            color: white;
        }
        .user-info a {
            color: #ecf0f1;
            text-decoration: none;
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #e74c3c;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .user-info a:hover {
            background-color: #c0392b;
        }
        .admin-nav {
            background-color: #34495e;
            padding: 1rem 0;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .admin-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        .admin-nav li {
            margin: 0 15px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-weight: bold;
        }
        .admin-nav a:hover {
            background-color: #2c3e50;
        }
        .dashboard-section {
            background-color: white;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 1200px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .add-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .add-btn:hover {
            background-color: #219653;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e6f7ff;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }
            .logo {
                margin-bottom: 10px;
            }
            .user-info {
                margin-top: 10px;
            }
            .admin-nav ul {
                flex-direction: column;
            }
            .admin-nav li {
                margin-bottom: 5px;
            }
            .modal-content {
                width: 90%;
            }
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="Images/Hammer Tools Construction Logo.png" alt="Logo">
        </div>
        <h1>GLOBAL HARDWARE - ADMIN DASHBOARD</h1>
        <button class="home-btn" onclick="location.href='homepage.php';">Home</button>
    </header>

    <nav class="admin-nav">
        <div class="nav-container">
            <ul>
                <li><a href="#customers">Customers</a></li>
                <li><a href="#suppliers">Suppliers</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#orders">Orders</a></li>
            </ul>
        </div>
    </nav>

    <section id="customers" class="dashboard-section">
        <div class="section-header">
            <h2>Customer Management</h2>
            <button class="add-btn" onclick="document.getElementById('addUserModal').style.display='block'">Add Customer</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM users WHERE role = 'customer'");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <button onclick=\"editUser({$row['id']})\">Edit</button>
                            <a href='AdminDashboard.php?delete=1&type=user&id={$row['id']}'><button>Delete</button></a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <section id="suppliers" class="dashboard-section">
        <div class="section-header">
            <h2>Supplier Management</h2>
            <button class="add-btn" onclick="showAddSupplierModal()">Add Supplier</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM users WHERE role = 'supplier'");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <button onclick=\"editUser({$row['id']})\">Edit</button>
                            <a href='AdminDashboard.php?delete=1&type=user&id={$row['id']}'><button>Delete</button></a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <section id="products" class="dashboard-section">
        <div class="section-header">
            <h2>Product Management</h2>
            <button class="add-btn" onclick="document.getElementById('addProductModal').style.display='block'">Add Product</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT p.*, u.username as supplier_name FROM products p LEFT JOIN users u ON p.supplier_id = u.id");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['supplier_name']}</td>
                        <td>
                            <button onclick=\"editProduct({$row['id']})\">Edit</button>
                            <a href='AdminDashboard.php?delete=1&type=product&id={$row['id']}'><button>Delete</button></a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <section id="orders" class="dashboard-section">
    <div class="section-header">
        <h2>Order Management</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->query("SELECT o.*, u.username as customer_name 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.id 
                                ORDER BY o.order_date DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['total_price']}</td>
                    <td>{$row['order_date']}</td>
                    <td>
                        <button onclick=\"editOrder({$row['order_id']})\">Edit</button>
                        <a href='AdminDashboard.php?delete=1&type=order&id={$row['order_id']}'><button>Delete</button></a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</section>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addUserModal').style.display='none'">&times;</span>
            <h2>Add New User</h2>
            <form method="POST" action="AdminDashboard.php">
                <input type="hidden" name="role" value="customer">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="add_user" class="submit-btn">Add User</button>
            </form>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addSupplierModal').style.display='none'">&times;</span>
            <h2>Add New Supplier</h2>
            <form method="POST" action="AdminDashboard.php">
                <input type="hidden" name="role" value="supplier">
                <div class="form-group">
                    <label for="supplierUsername">Username:</label>
                    <input type="text" id="supplierUsername" name="username" required>
                </div>
                <div class="form-group">
                    <label for="supplierPassword">Password:</label>
                    <input type="password" id="supplierPassword" name="password" required>
                </div>
                <button type="submit" name="add_user" class="submit-btn">Add Supplier</button>
            </form>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addProductModal').style.display='none'">&times;</span>
            <h2>Add New Product</h2>
            <form method="POST" action="AdminDashboard.php">
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
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="supplier_id">Supplier:</label>
                    <select id="supplier_id" name="supplier_id" required>
                        <?php
                        $suppliers = $conn->query("SELECT * FROM users WHERE role = 'supplier'");
                        while ($supplier = $suppliers->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$supplier['id']}'>{$supplier['username']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="add_product" class="submit-btn">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        // Function to show add supplier modal
        function showAddSupplierModal() {
            document.getElementById('addSupplierModal').style.display = 'block';
        }

        // Function to edit user (placeholder - implement as needed)
        function editUser(id) {
            alert("Edit user with ID: " + id);
            // Implement actual edit functionality here
        }

        // Function to edit product (placeholder - implement as needed)
        function editProduct(id) {
            alert("Edit product with ID: " + id);
            // Implement actual edit functionality here
        }

        // Function to edit order (placeholder - implement as needed)
        function editOrder(id) {
            alert("Edit order with ID: " + id);
            // Implement actual edit functionality here
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>