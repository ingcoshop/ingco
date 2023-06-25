<?php
    session_start();
    include("../database/connect.php");
    
    // Check if the user is logged in and has the 'admin' role
    if(!isset($_SESSION['user_info']) || $_SESSION['user_info']['role'] !== 'admin') {
        header('location: ../login.php');
        exit;
    }
    
    // Fetch latest 5 products from the database
    $query = "SELECT * FROM products ORDER BY product_id DESC LIMIT 5";
    $result = mysqli_query($con, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Fetch customers from the database
    $queryCustomers = "SELECT * FROM users WHERE role = 'customer'";
    $resultCustomers = mysqli_query($con, $queryCustomers);
    $customers = mysqli_fetch_all($resultCustomers, MYSQLI_ASSOC);
    
    // Fetch admins from the database
    $queryAdmins = "SELECT * FROM users WHERE role = 'admin'";
    $resultAdmins = mysqli_query($con, $queryAdmins);
    $admins = mysqli_fetch_all($resultAdmins, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        td{
            text-align: center;
        }
    </style>
</head>
<body>
    <?php require "../h&f/header2.php"; ?>
    <h1 style="text-align: center;">Welcome to the Admin Dashboard</h1>
    
    <h2>Latest 5 Products</h2>
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
            <?php foreach($products as $product): ?>
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
    <button><a href="add_product.php">Add Product</a></button>
    <button><a href="all_products.php">Show All Products</a></button>

    <h2>Admins</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($admins as $admin): ?>
            <tr>
                <td><?php echo $admin['ID']; ?></td>
                <td><?php echo $admin['name']; ?></td>
                <td><?php echo $admin['email']; ?></td>
                <td><?php echo $admin['role']; ?></td>
                <td><a href="edit_user.php?id=<?php echo $admin['ID']; ?>">Edit</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h2>Customers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $customer): ?>
            <tr>
                <td><?php echo $customer['ID']; ?></td>
                <td><?php echo $customer['name']; ?></td>
                <td><?php echo $customer['email']; ?></td>
                <td><?php echo $customer['role']; ?></td>
                <td><a href="edit_user.php?id=<?php echo $customer['ID']; ?>">Edit</a></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    
    
        
    <button>
    <a href="../logout.php">Logout</a>
    </button>
    <!-- <?php require "../h&f/footer.php"; ?> -->
</body>
</html>
