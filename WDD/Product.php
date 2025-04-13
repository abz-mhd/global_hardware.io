<?php
    include 'Addproduct.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $description = $_POST["description"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
        
        // Prepare the statement
        $sql = "INSERT INTO Product (name, description, quantity, price) VALUES ('$name', '$description', '$quantity', '$price')";

        try {
            $conn->exec($sql);
        } catch (PDOException $ex) {
            echo $sql . $ex->getMessage();
        }
        $conn = null;
        exit();
    }
?>