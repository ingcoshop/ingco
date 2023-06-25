<?php
session_start();
require "../database/connect.php";
require "../h&f/header2.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    // Retrieve the ordered items from the session
    $orderedItems = $_SESSION['cart'];

    // Update the stock for each ordered item in the database
    foreach ($orderedItems as $itemId => $item) {
        $stock = getProductStock($itemId); // Get the current stock for the item
        $newStock = $stock + $item['quantity']; // Calculate the new stock after canceling the order

        // Update the stock in the database
        $query = "UPDATE products SET stock = '$newStock' WHERE product_id = '$itemId'";
        mysqli_query($con, $query);
    }

    // Remove the ordered items from the session
    unset($_SESSION['cart']);

    echo "<p>Your order has been canceled.</p>";
    echo "<a href='../homeshop.php'><button>Go back to Home Shop</button></a>";
} else {
    // Redirect to the cart page if accessed directly without canceling the order
    header("Location: cart.php");
    exit();
}

// Function to fetch the stock value for a given product ID
function getProductStock($productId)
{
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

require "../h&f/footer.php";
?>
