<?php
session_start();
include('../database/connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Get the user ID from the session
$userID = $_SESSION['user_info']['ID'];

// Check if the cancel_order parameter is provided in the URL
if (isset($_GET['cancel_order'])) {
    $cancelOrderID = $_GET['cancel_order'];

    // Retrieve the order details
    $cancelQuery = "SELECT orders.order_id, orders.status, order_items.product_id, order_items.quantity
                  FROM orders
                  INNER JOIN order_items ON orders.order_id = order_items.order_id
                  WHERE orders.order_id = $cancelOrderID";
    $cancelResult = mysqli_query($con, $cancelQuery);

    if (mysqli_num_rows($cancelResult) > 0) {
        // Fetch the order details
        $cancelOrder = mysqli_fetch_assoc($cancelResult);
        $cancelStatus = $cancelOrder['status'];

        // Check if the order status is "under review"
        if ($cancelStatus === 'under review') {
            // Delete the order and refund the items to the stock

            // Start a transaction
            mysqli_begin_transaction($con);

            try {
                // Delete the order from the orders table
                $deleteOrderQuery = "DELETE FROM orders WHERE order_id = $cancelOrderID";
                mysqli_query($con, $deleteOrderQuery);

                // Refund the items to the stock
                $productID = $cancelOrder['product_id'];
                $quantity = $cancelOrder['quantity'];
                $refundStockQuery = "UPDATE products SET stock = stock + $quantity WHERE product_id = $productID";
                mysqli_query($con, $refundStockQuery);

                // Commit the transaction
                mysqli_commit($con);

                // Redirect to the same page without the cancel_order parameter
                header("Location: orders.php");
                exit();
            } catch (Exception $e) {
                // An error occurred, rollback the transaction
                mysqli_rollback($con);
                // Handle the error as needed
            }
        }
    }
}

// Fetch orders for the logged-in user from the database
$query = "SELECT orders.order_id, orders.order_date, orders.total, orders.status, order_items.quantity, products.name
            FROM orders
            INNER JOIN order_items ON orders.order_id = order_items.order_id
            INNER JOIN products ON order_items.product_id = products.product_id
            WHERE orders.user_id = $userID";

// Check if a status filter is provided
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    // Add the status filter to the SQL query
    if ($status === 'all') {
        $query .= " AND orders.status IN ('accepted', 'under review', 'completed')";
    } else {
        $query .= " AND orders.status = '$status'";
    }
}

$query .= " ORDER BY orders.order_id DESC";

$result = mysqli_query($con, $query);

// Check if there are any orders
if (mysqli_num_rows($result) > 0) {
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orderID = $row['order_id'];
        if (!isset($orders[$orderID])) {
            $orders[$orderID] = [
                'order_id' => $orderID,
                'order_date' => $row['order_date'],
                'total' => $row['total'],
                'status' => $row['status'],
                'items' => []
            ];
        }
        $orders[$orderID]['items'][] = [
            'name' => $row['name'],
            'quantity' => $row['quantity']
        ];
    }
} else {
    $orders = [];
}

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Orders - My Website</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .status {
            font-weight: bold;
        }

        .status-accepted {
            color: blue;
        }

        .status-under-review {
            color: lightblue;
        }

        .status-completed {
            color: green;
        }
    </style>
</head>

<body>
    <?php require "../h&f/header2.php"; ?>
    <div style="text-align: center;">
        <button onclick="goBack()">Back</button>
    </div>

    <div style="margin: auto; max-width: 600px;">
        <h2 style="text-align: center;">Orders</h2>

        <!-- Display the four buttons -->
        <div style="text-align: center;">
            <button onclick="filterOrders('all')">All</button>
            <button onclick="filterOrders('under review')">Under Review</button>
            <button onclick="filterOrders('accepted')">Accepted</button>
            <button onclick="filterOrders('completed')">Completed</button>
        </div>

        <?php if (!empty($orders)) : ?>
            <?php foreach ($orders as $order) : ?>
                <br><br><br>
                <h3>Order ID: <?php echo $order['order_id']; ?></h3>
                <p>Order Date: <?php echo date('Y-m-d', strtotime($order['order_date'])); ?></p>
                <p>Total: <?php echo $order['total'] . " $"; ?></p>
                <p>Status: <span class="status <?php echo getStatusClass($order['status']); ?>"><?php echo $order['status']; ?></span></p>
                <table style="margin: auto; width: 100%;">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>&nbsp;&nbsp;&nbsp;</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item) : ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td><?php echo $item['quantity']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($order['status'] === 'under review') : ?>
                    <p style="text-align: center;">
                        <button onclick="cancelOrder('<?php echo $order['order_id']; ?>')">Cancel Order</button>
                    </p>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align: center;">No orders found.</p>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript function to filter orders based on the status
        function filterOrders(status) {
            // Redirect to the same page with a query parameter to indicate the status
            window.location.href = 'orders.php?status=' + status;
        }

        // JavaScript function to cancel and delete an order
        function cancelOrder(orderID) {
            if (confirm('Are you sure you want to cancel and delete this order?')) {
                // Redirect to the same page with a query parameter to indicate the order cancellation
                window.location.href = 'orders.php?cancel_order=' + orderID;
            }
        }

        // JavaScript function to navigate back
        function goBack() {
            window.history.back();
        }
    </script>

    <?php require "../h&f/footer.php"; ?>

    <?php
    // Function to determine the class name for the status based on its value
    function getStatusClass($status)
    {
        switch ($status) {
            case 'accepted':
                return 'status-accepted';
            case 'under review':
                return 'status-under-review';
            case 'completed':
                return 'status-completed';
            default:
                return '';
        }
    }
    ?>
</body>

</html>
