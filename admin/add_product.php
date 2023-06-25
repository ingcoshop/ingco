<?php
session_start();
include("../database/connect.php");

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_info']) || $_SESSION['user_info']['role'] !== 'admin') {
    header('location: ../login.php');
    exit;
}

// Initialize variables
$name = '';
$price = '';
$description = '';
$stock = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    // Validate form inputs (you can add more validation if needed)
    if (empty($name) || empty($price) || empty($description) || empty($stock)) {
        $error = 'All fields are required.';
    } else {
        // Check if an image file was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];

            // Check if the uploaded file is an image
            $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
            $fileInfo = pathinfo($image['name']);
            $extension = strtolower($fileInfo['extension']);

            if (in_array($extension, $allowedTypes)) {
                // Generate a unique filename for the image
                $filename = uniqid() . '.' . $extension;

                // Set the destination directory for the image
                $destination = '../images/' . $filename;

                // Move the uploaded file to the desired directory
                move_uploaded_file($image['tmp_name'], $destination);

                // Insert the new product into the database with the image filename
                $imagePath = 'images/' . $filename; // Set the image path
                $query = "INSERT INTO products (name, price, description, stock, product_image) VALUES ('$name', '$price', '$description', '$stock', '$imagePath')";
                mysqli_query($con, $query);

                // Redirect to the admin dashboard
                header('location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid file type. Only JPG, JPEG, PNG, and GIF images are allowed.';
            }
        } else {
            // Insert the new product into the database without an image
            $query = "INSERT INTO products (name, price, description, stock) VALUES ('$name', '$price', '$description', '$stock')";
            mysqli_query($con, $query);

            // Redirect to the admin dashboard
            header('location: dashboard.php');
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require "../h&f/header2.php"; ?>
    <h1 style="text-align: center;">Add Product</h1>
    <br><br>

    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>">
        </div>
        <div>
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo $price; ?>">
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo $description; ?></textarea>
        </div>
        <div>
            <label for="stock">Stock:</label>
            <input type="text" id="stock" name="stock" value="<?php echo $stock; ?>">
        </div>
        <div>
            <label for="image">Photo of Product:</label>
            <input type="file" id="image" name="image">
        </div>

        <div>
            <input type="submit" value="Add">
        </div>
        <?php if (!empty($error)): ?>
        <p><?php echo $error; ?></p>
        <?php endif; ?>
    </form>

    <button><a href="dashboard.php">Go back to Dashboard</a></button>

    <!-- Add more content or functionality as required -->

    <button> <a href="../logout.php">Logout</a></button>
    <?php require "../h&f/footer.php"; ?>
</body>
</html>
