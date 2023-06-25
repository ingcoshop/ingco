<?php
session_start();
require "../database/connect.php";
require "../h&f/header2.php";
$totalPrice = 0;

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

// Check if the cart exists in the session
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    echo "<p>Your cart is empty.</p>";
    echo "<a href='../homeshop.php'><button>Go back to Home Shop</button></a>";
} else {
    echo "<h2>Cart Items:</h2>";
    echo "<ul>";
    foreach ($_SESSION['cart'] as $itemId => $item) {
        $stock = getProductStock($itemId);
        $itemPrice = $item['price'] * $item['quantity']; // Calculate the price for this item
        $totalPrice += $itemPrice; // Add the item price to the total

        echo "<li>";
        echo "Product Name: " . $item['name'] . "<br>";
        echo "<form method='POST' action='cart.php'>"; // Update the form's action attribute
        echo "<input type='hidden' name='item_id' value='$itemId'>";

        // Check if the quantity is greater than 0
        if ($item['quantity'] > 0) {
            echo "Quantity: <input type='number' name='quantity[$itemId]' value='" . $item['quantity'] . "' min='1' max='" . min(10, $stock) . "'><br>";
            echo "Price: $" . $item['price'] . "<br>";

            // Add a "Delete Item" button
            echo "<button type='submit' name='update_quantity'>Save</button>";
            echo "<button type='submit' name='delete_item'>Delete Item</button>";
        } else {
            // Remove the item from the cart if the quantity is 0
            unset($_SESSION['cart'][$itemId]);
            echo "Item deleted from cart.<br>";
        }
        echo "</form>";

        echo "</li>";
    }
    echo "Total Price: $" . $totalPrice . "<br>";

    echo "</ul>";

    // Cancel Order Form
    echo "<form method='POST' action='cancel_order.php'>";
    echo "<button type='submit' name='cancel_order'>Cancel Order</button>";
    echo "</form>";

    echo "<a href='../homeshop.php'><button>Go back to Home Shop To add more Items</button></a>";

    // Submit Order Form
    echo "<form method='POST' action='submit_order.php'>";
    echo "<button type='submit' name='submit_order'>Submit Order</button>";
    echo "</form>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    foreach ($_POST['quantity'] as $itemId => $newQuantity) {
        $itemStock = getProductStock($itemId);

        if ($newQuantity > $itemStock) {
            echo "<p>Error: Cannot add more than the available stock for item ID $itemId.</p>";
        } else {
            if ($newQuantity > 0) {
                $_SESSION['cart'][$itemId]['quantity'] = $newQuantity;
            } else {
                // Remove the item from the cart if the quantity is 0
                unset($_SESSION['cart'][$itemId]);
                echo "Item deleted from cart.<br>";
            }
        }
    }
    header("Location: cart.php"); // Redirect to the same page to refresh
    exit();
}

// Handle deleting an item from the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $itemId = $_POST['item_id'];
    unset($_SESSION['cart'][$itemId]);
    echo "<p>Item deleted from cart.</p>";
    header("Location: cart.php"); // Redirect to the same page to refresh
    exit();
}

require "../h&f/footer.php";
?>
