<?php
session_start();
require "../database/connect.php";
require "../h&f/header2.php";
global $con;

// Function to fetch the stock value for a given product ID
function getProductStock($productId) {
    global $con;
    $productId = mysqli_real_escape_string($con, $productId); // Escape the product ID
    $query = "SELECT stock FROM products WHERE product_id = '$productId'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        return $product['stock'];
    }
    
    return 0; // Return 0 if the product ID is not found or an error occurs
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    // Calculate the total price of the cart items
    $totalPrice = 0;

    foreach ($_SESSION['cart'] as $itemId => $item) {
        $stock = getProductStock($itemId);
        $itemPrice = $item['price'] * $item['quantity'];
        $totalPrice += $itemPrice;
    }

    // Insert data into the orders table
    $userId = $_SESSION['user_info']['ID']; // Assuming user_id is stored in the session
    $date = date('Y-m-d'); // Current date

    $insertOrderQuery = "INSERT INTO orders (user_id, order_date, total) VALUES ('$userId', '$date', '$totalPrice')";
    mysqli_query($con, $insertOrderQuery);

    // Get the order_id of the newly inserted order
    $orderId = mysqli_insert_id($con);

    // Insert data into the order_items table for each item in the cart
    foreach ($_SESSION['cart'] as $itemId => $item) {
        $productId = $itemId;
        $quantity = $item['quantity'];

        $insertOrderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$orderId', '$productId', '$quantity')";
        mysqli_query($con, $insertOrderItemQuery);
    }

    // Clear the cart after submitting the order
    $_SESSION['cart'] = array();

    // Display a success message
    echo "<p>Order submitted successfully.</p>";
}

require "../h&f/footer.php";
?>
