<?php
session_start();
include("../database/connect.php");

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_info']) || $_SESSION['user_info']['role'] !== 'admin') {
    header('location: ../login.php');
    exit;
}

// Retrieve the product ID from the URL parameter
if (!isset($_GET['id'])) {
    header('location: dashboard.php');
    exit;
}

$productId = $_GET['id'];

// Fetch the product from the database
$query = "SELECT * FROM products WHERE product_id = $productId";
$result = mysqli_query($con, $query);
$product = mysqli_fetch_assoc($result);


// Function to save the uploaded image to the target directory
function saveImage($file)
{
    $targetDir = "../product_images/";
    $fileName = basename($file["name"]);
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        // Remove the first "../" from the file path
        $targetPath = substr($targetPath, 3);
        return $targetPath;
    }

    return "";
}


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
        // Update the product in the database
        $query = "UPDATE products SET name='$name', price='$price', description='$description', stock='$stock' WHERE product_id=$productId";
        mysqli_query($con, $query);

        // Handle product photo upload if a file was selected
        if ($_FILES['photo']['name']) {
            $targetFilePath = saveImage($_FILES['photo']);

            if ($targetFilePath !== "") {
                // Update the product's photo in the database
                $query = "UPDATE products SET product_image='$targetFilePath' WHERE product_id=$productId";
                mysqli_query($con, $query);

                // Remove the old photo if it exists
                if (!empty($product['product_image'])) {
                    $oldFilePath = $product['product_image'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            } else {
                $error = "Error occurred while uploading the file.";
            }
        }

        // Redirect to the admin dashboard
        header('location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require "../h&f/header2.php"; ?>

    <h1>Edit Product</h1>

    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>">
        </div>
        <div>
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo $product['price']; ?>">
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo $product['description']; ?></textarea>
        </div>
        <div>
            <label for="stock">Stock:</label>
            <input type="text" id="stock" name="stock" value="<?php echo $product['stock']; ?>">
        </div>
        <div>
            <label for="photo">Product Photo:</label>
            <input type="file" id="photo" name="photo">
        </div>
        <div>
            <input type="submit" value="Update">
        </div>
        <?php if (!empty($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
    <button onclick="goBack()">Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <button><a href="dashboard.php">Go back to Dashboard</a></button>

    <!-- Add more content or functionality as required -->

    <button><a href="../logout.php">Logout</a></button>
    <?php require "../h&f/footer.php"; ?>
</body>

</html>