<?php
session_start();
include('../database/connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the order_id parameter is set
if (!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit();
}

// Get the order ID from the URL parameter
$orderID = $_GET['order_id'];

// Fetch the order details and customer information from the database
$query = "SELECT orders.order_id, orders.order_date, orders.total, orders.status, order_items.quantity, products.name, users.ID AS user_id, users.name AS customer_name
FROM orders
INNER JOIN order_items ON orders.order_id = order_items.order_id
INNER JOIN products ON order_items.product_id = products.product_id
INNER JOIN users ON orders.user_id = users.ID
WHERE orders.order_id = $orderID";
$result = mysqli_query($con, $query);

// Check if the order exists
if (mysqli_num_rows($result) == 0) {
    header("Location: orders.php");
    exit();
}

// Get the order details
$order = mysqli_fetch_assoc($result);

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted status value
    $status = $_POST['status'];

    // Update the order status in the database
    $updateQuery = "UPDATE orders SET status = '$status' WHERE order_id = $orderID";
    $updateResult = mysqli_query($con, $updateQuery);

    // Check if the update was successful
    if ($updateResult) {
        // Redirect the user or display a success message
        header("Location: orders.php");
        exit();
    } else {
        // Display an error message or handle the error accordingly
        echo "Error updating the order status.";
    }
}

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Order - My Website</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require "../h&f/header2.php"; ?>
    <button onclick="goBack()">Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    <div style="margin: auto; max-width: 600px;">
        <h2 style="text-align: center;">Edit Order</h2>

        <h3>Order ID: <?php echo $order['order_id']; ?></h3>
        <p>Order Date: <?php echo date('Y-m-d', strtotime($order['order_date'])); ?></p>
        <p>Total: <?php echo $order['total'] . " $"; ?></p>

        <h4>Customer Information:</h4>
        <p>Customer ID: <?php echo $order['user_id']; ?></p>
        <p>Customer Name: <?php echo $order['customer_name']; ?></p>

        <h4>Products Ordered:</h4>
        <table style="margin: auto; width: 100%;">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the products and quantities for the order
                // Fetch and display the products and quantities
                mysqli_data_seek($result, 0); // Reset the result pointer
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <form method="POST">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="under review" <?php if ($order['status'] === 'under review') echo 'selected'; ?>>Under Review</option>
                <option value="accepted" <?php if ($order['status'] === 'accepted') echo 'selected'; ?>>Accepted</option>
                <option value="completed" <?php if ($order['status'] === 'completed') echo 'selected'; ?>>Completed</option>
            </select>

            <br><br>

            <button type="submit">Update Status</button>
        </form>
    </div>

    <?php require "../h&f/footer.php"; ?>
</body>

</html>