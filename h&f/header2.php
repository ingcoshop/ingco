<!-- <?php print_r($_SESSION['user_info']);?> -->
<!DOCTYPE html>
<html>

<head>
    <title>My Website</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <div class="logo-container"><a href="../index.php"><img src="../images/logo/INGCO.png"  alt="Logo"></a></div>
        
        
        <?php if (isset($_SESSION['user_info']) && $_SESSION['user_info']['role'] == 'customer') : ?>
            <li style="list-style: none;padding: 16px;"><a href="../profile.php">Profile</a></li>
            <li style="list-style: none;padding: 16px;"><a href="../homeshop.php">Shop</a></li>
            <li style="list-style: none;padding: 16px;"><a href="../customer/cart.php">View Cart</a></li>
            <li style="list-style: none;padding: 16px;"><a href="orders.php">View Orders</a></li>
            <?php elseif (isset($_SESSION['user_info']) && $_SESSION['user_info']['role']=='admin') : ?>
            <li style="list-style: none;padding: 16px;"><a href="../profile.php">Profile</a></li>
            <li style="list-style: none;padding: 16px;"><a href="dashboard.php">Dashboard</a></li>
            <li style="list-style: none;padding: 16px;"><a href="orders.php">View Orders</a></li>

        <?php endif; ?>
        <?php if (!isset($_SESSION['user_info'])) : ?>
            <div style="padding: 16px;"><a href="../index.php">Home</a></div>
        <div style="padding: 16px;"><a href="login.php">Login</a></div>
        <div style="padding: 16px;"><a href="signup.php">Signup</a></div>
        <?php endif; ?>


        <?php if (isset($_SESSION['user_info'])) : ?>
            <li style="list-style: none;padding: 16px;"><a href="../logout.php">Logout</a></li> 
        <?php endif; ?>

    </header>
</body>

</html>
