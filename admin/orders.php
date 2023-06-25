<?php
session_start();
include('../database/connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_info'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch orders for all customers from the database
$query = "SELECT orders.order_id, orders.order_date, orders.total, orders.status
            FROM orders
            ORDER BY
                CASE
                    WHEN orders.status = 'under review' THEN 1
                    WHEN orders.status = 'accepted' THEN 2
                    WHEN orders.status = 'completed' THEN 3
                END,
                orders.order_id DESC";
$result = mysqli_query($con, $query);

// Check if there are any orders
if (mysqli_num_rows($result) > 0) {
    $underReviewOrders = [];
    $acceptedOrders = [];
    $completedOrders = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $order = [
            'order_id' => $row['order_id'],
            'order_date' => $row['order_date'],
            'total' => $row['total'],
            'status' => $row['status']
        ];

        switch ($row['status']) {
            case 'under review':
                $underReviewOrders[] = $order;
                break;
            case 'accepted':
                $acceptedOrders[] = $order;
                break;
            case 'completed':
                $completedOrders[] = $order;
                break;
        }
    }
} else {
    $underReviewOrders = [];
    $acceptedOrders = [];
    $completedOrders = [];
}

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Orders - My Website</title>
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
        <h2 style="text-align: center;">All Orders</h2>

        <?php if (!empty($underReviewOrders)) : ?>
            <h3>Under Review Orders</h3>
            <table style="margin: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($underReviewOrders as $order) : ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['total']; ?></td>
                            <td><a href="edit_order.php?order_id=<?php echo $order['order_id']; ?>">Show Order</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($acceptedOrders)) : ?>
            <h3>Accepted Orders</h3>
            <table style="margin: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acceptedOrders as $order) : ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['total']; ?></td>
                            <td><a href="edit_order.php?order_id=<?php echo $order['order_id']; ?>">Show Order</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($completedOrders)) : ?>
            <h3>Completed Orders</h3>
            <table style="margin: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedOrders as $order) : ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['total']; ?></td>
                            <td><a href="edit_order.php?order_id=<?php echo $order['order_id']; ?>">Show Order</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (empty($underReviewOrders) && empty($acceptedOrders) && empty($completedOrders)) : ?>
            <p style="text-align: center;">No orders found.</p>
        <?php endif; ?>
    </div>

    <button onclick="goBack()">Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    <?php require "../h&f/footer.php"; ?>
</body>

</html>
