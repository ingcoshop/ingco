<?php
include('connect.php');

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$date = date('Y-m-d H:i:s');

// Check if the image file is uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Define the new destination directory for the copied file
    $newDestination = '../images/';

    // Create the new directory if it doesn't exist
    if (!file_exists($newDestination)) {
        mkdir($newDestination, 0755, true);
    }

    // Generate a unique filename for the copied file
    $newFileName = uniqid() . '_' . $_FILES['image']['name'];

    // Copy the file to the new destination
    $newFilePath = $newDestination . $newFileName;
    move_uploaded_file($_FILES['image']['tmp_name'], $newFilePath);

    // Trim the "../" from the beginning of the file path
    $trimmedFilePath = ltrim($newFilePath, '../');

    // Update the database with the new file path
    $query = "INSERT INTO users (name, email, password, image, date) VALUES ('$username', '$email', '$password', '$trimmedFilePath', '$date')";
} else {
    // No image file uploaded, insert default value in the database
    $query = "INSERT INTO users (name, email, password, date) VALUES ('$username', '$email', '$password', '$date')";
}

mysqli_query($con, $query);

header('location: ../login.php');
?>
