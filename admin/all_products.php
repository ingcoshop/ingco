<?php
session_start();
include("../database/connect.php");

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_info']) || $_SESSION['user_info']['role'] !== 'admin') {
    header('location: ../login.php');
    exit;
}

// Fetch all products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($con, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Products</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require "../h&f/header2.php"; ?>
    <h1 style="text-align: center;">All Products</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><a href="edit_product.php?id=<?php echo $product['product_id']; ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add more content or functionality as required -->
    <button onclick="goBack()">Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    <button><a href="../logout.php">Logout</a></button>
    <?php require "../h&f/footer.php"; ?>
</body>

</html>