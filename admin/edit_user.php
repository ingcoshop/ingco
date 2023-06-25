<?php
    session_start();
    include("../database/connect.php");
    
    // Check if the user is logged in and has the 'admin' role
    if(!isset($_SESSION['user_info']) || $_SESSION['user_info']['role'] !== 'admin') {
        header('location: ../login.php');
        exit;
    }
    
    // Check if the user ID is provided in the query parameter
    if(!isset($_GET['id'])) {
        header('location: dashboard.php');
        exit;
    }
    
    $userID = $_GET['id'];
    
    // Fetch the user from the database based on the provided ID
    $query = "SELECT * FROM users WHERE id = $userID";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Check if the user exists
    if(!$user) {
        header('location: dashboard.php');
        exit;
    }
    
    // Handle form submission
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the updated user information from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        
        // Update the user in the database
        $updateQuery = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = $userID";
        mysqli_query($con, $updateQuery);
        
        // Redirect back to the dashboard page
        header('location: dashboard.php');
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require "../h&f/header2.php"; ?>
    <h1 style="text-align: center;">Edit User</h1>
    
    <form method="POST">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>">
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>">
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="customer" <?php if($user['role'] === 'customer') echo 'selected'; ?>>Customer</option>
                <option value="admin" <?php if($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>
        <button type="submit">Update</button>
    </form>
    
    <button onclick="goBack()">Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    
    <?php require "../h&f/footer.php"; ?>
</body>
</html>
