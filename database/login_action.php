<?php
    session_start();
    include("connect.php");

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' ";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_info'] = $row;

        if($row['role'] == 'admin') {
            header('location:../admin/dashboard.php');
        } else {
            header('location:../homeshop.php');
        }
    } else {
        header("location:../login.php?flag=1");
    }
?>
