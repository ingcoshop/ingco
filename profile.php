<?php
session_start();
include('database/connect.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_GET['action']) && $_GET['action'] == 'edit') {

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $_SESSION['user_info']['name'] = $username;
        $_SESSION['user_info']['email'] = $email;
        $_SESSION['user_info']['password'] = $password;

        $image = $_SESSION['user_info']['image']; 
        $userId = $_SESSION['user_info']['ID'];

        if ($_FILES['image']['error'] === 0) {
            $image = saveImage($_FILES['image']);
            $_SESSION['user_info']['image'] = $image;
        }

        if ($con) {
            // Escape the user inputs to prevent SQL injection
            $username = mysqli_real_escape_string($con, $username);
            $email = mysqli_real_escape_string($con, $email);
            $password = mysqli_real_escape_string($con, $password);
            $image = mysqli_real_escape_string($con, $image);

            // Prepare the update query
            $updateQuery = "UPDATE users SET name='$username', email='$email', password='$password', image='$image' WHERE ID='$userId'";

            // Execute the update query
            mysqli_query($con, $updateQuery);

            // Close the database connection
            mysqli_close($con);
        }
    }elseif (!empty($_GET['action']) && $_GET['action'] == 'delete') {
        // Delete profile logic...
        $userId = $_SESSION['user_info']['ID'];

        // Delete user's posts
        deletePosts($userId, $con);

        // Prepare the delete query for the user
        $deleteUserQuery = "DELETE FROM users WHERE ID='$userId'";

        // Execute the delete query for the user
        mysqli_query($con, $deleteUserQuery);

        // Destroy the session and redirect to another page (e.g., homepage)
        session_destroy();
        header("Location: index.php");
        exit();
    }

}
function deletePosts($userId, $con) {
    // Prepare the delete query for posts
    $deletePostsQuery = "DELETE FROM posts WHERE user_id='$userId'";

    // Execute the delete query for posts
    mysqli_query($con, $deletePostsQuery);
}

function saveImage($file)
{
    $targetDir = "images/";
    $fileName = basename($file["name"]);
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return $targetPath;
    }

    return "";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile - my website</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php require "h&f/header.php"; ?>

    <div style="margin: auto;max-width: 600px">
        <?php if (!empty($_GET['action']) && $_GET['action'] == 'edit') : ?>
            <h2 style="text-align: center;">Edit profile</h2>

            <form method="post" enctype="multipart/form-data" style="margin: auto;padding:10px;" action="">
                <img src="<?php echo $_SESSION['user_info']['image'] ?>" style="width: 100px;height: 100px;object-fit: cover;margin: auto;display: block;">
                image: <input type="file" name="image"><br>
                <input value="<?php echo $_SESSION['user_info']['name'] ?>" type="text" name="username" placeholder="Username" required><br>
                <input value="<?php echo $_SESSION['user_info']['email'] ?>" type="email" name="email" placeholder="Email" required><br>
                <input value="<?php echo $_SESSION['user_info']['password'] ?>" type="text" name="password" placeholder="Password" required><br>

                <button type="submit">Save</button>
                <a href="profile.php">

                    <button type="button">Cancel</button>
                </a>

            </form>

        <?php elseif (!empty($_GET['action']) && $_GET['action'] == 'delete') : ?>
            <h2 style="text-align: center;">Are you sure you want to delete your profile??</h2>

            <div style="margin: auto;max-width: 600px;text-align: center;">
                <form method="post" style="margin: auto;padding:10px;">

                    <img src="<?php echo $_SESSION['user_info']['image'] ?>" style="width: 100px;height: 100px;object-fit: cover;margin: auto;display: block;">
                    <div><?php echo $_SESSION['user_info']['name'] ?></div>
                    <div><?php echo $_SESSION['user_info']['email'] ?></div>
                    <input type="hidden" name="action" value="delete">
                    <button>Delete</button>
                    <a href="profile.php">
                        <button type="button">Cancel</button>
                    </a>
                </form>
            </div>

        <?php else : ?>
            <h2 style="text-align: center;">User Profile</h2>
            <br>
            <div style="margin: auto;max-width: 600px;text-align: center;">
                <div>
                    <td><img src="<?php echo $_SESSION['user_info']['image'] ?>" style="width: 150px;height: 150px;object-fit: cover;"></td>
                </div>
                <br><br>
                <div>
                    <td><?php echo $_SESSION['user_info']['name'] ?></td>
                </div>
                <br>
                <br>

                <div>
                    <td><?php echo $_SESSION['user_info']['email'] ?></td>
                </div>

                <a href="profile.php?action=edit">
                    <button>Edit profile</button>
                </a>

                <a href="profile.php?action=delete">
                    <button>Delete profile</button>
                </a>
                <?php if (isset($_SESSION['user_info']) && $_SESSION['user_info']['role'] == 'customer') : ?>
                    <a href="customer/orders.php"><button>View Orders</button></a>
                <?php endif; ?>

            </div>
            <br>
        <?php endif; ?>
    </div>


    <?php require "h&f/footer.php"; ?>
</body>

</html>