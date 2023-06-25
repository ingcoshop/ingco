<?php
session_start();
include("database/connect.php");

// Fetch all products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($con, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Initialize the cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Maximum allowed quantity
$maxQuantity = 10;

// Handle add to cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Check if the product exists
        $query = "SELECT * FROM products WHERE product_id = $productId";
        $result = mysqli_query($con, $query);
        $product = mysqli_fetch_assoc($result);

        // Add the product to the cart
        if ($product) {
            $stock = $product['stock'];

            if ($quantity <= 0) {
                $error = "Quantity must be a positive number.";
            } elseif ($quantity > $stock) {
                $error = "Cannot add more than the available stock.";
            } else {
                $cartQuantity = calculateCartQuantity($productId);
                $maxAllowedQuantity = min($stock, $maxQuantity) - $cartQuantity;

                if ($quantity > $maxAllowedQuantity) {
                    $error = "Cannot add more than $maxAllowedQuantity items to the cart.";
                } else {
                    if (array_key_exists($productId, $_SESSION['cart'])) {
                        // If the product already exists in the cart, update the quantity
                        $_SESSION['cart'][$productId]['quantity'] += $quantity;
                    } else {
                        // If the product is new, add it to the cart
                        $cartItem = array(
                            'id' => $product['product_id'],
                            'name' => $product['name'],
                            'quantity' => $quantity,
                            'price' => $product['price']
                            // Add any other necessary product details
                        );
                        $_SESSION['cart'][$productId] = $cartItem;
                    }

                    // Update the stock in the database
                    $updatedStock = $stock - $quantity;
                    $updateQuery = "UPDATE products SET stock = $updatedStock WHERE product_id = $productId";
                    mysqli_query($con, $updateQuery);

                    $message = "Product added to the cart successfully.";
                    header("Location: homeshop.php#product-$productId");
                    exit();
                }
            }
        } else {
            $error = "Product not found.";
        }
    }
}

// Function to calculate the total quantity of a product in the cart
function calculateCartQuantity($productId) {
    $totalQuantity = 0;
    if (isset($_SESSION['cart'][$productId])) {
        $totalQuantity = $_SESSION['cart'][$productId]['quantity'];
    }
    return $totalQuantity;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Shop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-row {
            display: flex;
            justify-content: space-between;
        }
        .product {
            width: 23%;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .product h3 {
            margin-top: 0;
        }

        /* Media queries */
        @media only screen and (max-width: 768px) {
            .product-list {
                display: block;
            }
            .product-row {
                flex-wrap: wrap;
            }
            .product {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php require "h&f/header.php"; ?>
    <h1 style="text-align: center;">Welcome to Home Shop</h1>

    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <?php $counter = 0; ?>
    <div class="product-list">
        <div class="product-row">
            <?php foreach ($products as $product): ?>
                <div class="product" id="product-<?php echo $product['product_id']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <div class="product-image">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="<?php echo $product['product_image']; ?>" alt="<?php echo $product['name']; ?>" width="300" height="300" style="margin-left: 50px;">
                    <?php else: ?>
                        <p>No image available</p>
                    <?php endif; ?>
                    </div>
                    <p><?php echo $product['description']; ?></p>
                    <p>Price: $<?php echo $product['price']; ?></p>
                    <?php if ($product['stock'] == 0): ?>
                        <p>Out of stock</p>
                    <?php else: ?>
                        <?php
                        $availableStock = $product['stock'];
                        $cartQuantity = calculateCartQuantity($product['product_id']);
                        $maxAllowedQuantity = min($availableStock, $maxQuantity) - $cartQuantity;
                        ?>
                        <form method="POST" action="homeshop.php#product-<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="number" name="quantity" value="1" min="0" max="<?php echo $maxAllowedQuantity; ?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php
                $counter++;
                if ($counter % 4 === 0) {
                    echo '</div><div class="product-row">';
                }
                ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require "h&f/footer.php"; ?>

    <script>
        // Scroll to the added product after page refresh
        window.onload = function() {
            var productId = <?php echo isset($productId) ? $productId : -1; ?>;
            if (productId !== -1) {
                var element = document.getElementById("product-" + productId);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }
        };
    </script>
</body>
</html>

