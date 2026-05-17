<?php 


session_start(); 
include 'Database/db.php';
include 'Includes/header.php';

//  Add directly to cart from homepage 
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);  // Get the product ID to add
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");  // Prepare the query to fetch product details
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();  // product details

    if ($product) {
        //  Prevent adding to cart if stock is 0 
        if ($product['quantity'] <= 0) {
            $_SESSION['cart_msg'] = "Sorry, \"{$product['product_name']}\" is out of stock.";  // message for out of stock product
            header("Location: index.php");  // Redirect to homepage
            exit();
        }

        //  Initialize the cart if it doesn't exist 
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;  // Get the current quantity in cart

        //  Check if the product is available in the quantity 
        if ($current_qty < $product['quantity']) {
            // If the product is already in the cart, increase the quantity
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                // If it's not in the cart, add it
                $_SESSION['cart'][$product_id] = [
                    'name'     => $product['product_name'],
                    'price'    => $product['price'],
                    'image'    => $product['image_file'],
                    'quantity' => 1,
                    'stock'    => $product['quantity']
                ];
            }
            $_SESSION['cart_success'] = " \"{$product['product_name']}\" added to cart!";  // Success message
            header("Location: index.php"); 
        } else {
            // If maximum stock is reached, show message
            $_SESSION['cart_msg'] = "Sorry, maximum stock reached for \"{$product['product_name']}\".";
            header("Location: index.php");  
        }
    } else {
        header("Location: index.php");  // If the product doesn't exist, redirect to homepage
    }
    exit();
}

//  Display cart message if set 
$cart_msg = '';
$cart_success = '';
if (isset($_SESSION['cart_msg'])) {
    $cart_msg = $_SESSION['cart_msg'];
    unset($_SESSION['cart_msg']);
}
if (isset($_SESSION['cart_success'])) {
    $cart_success = $_SESSION['cart_success'];
    unset($_SESSION['cart_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iTech Store - Home</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<section class="hero-section">
    <h1>Experience the Future of iTech</h1>
    <p>Discover the latest Apple products at iTech Store</p>
</section>

<main class="main-container">

    <!-- Display success message -->
    <?php if ($cart_success): ?>
        <div class="cart-success-msg">
            <?php echo htmlspecialchars($cart_success); ?>
        </div>
    <?php endif; ?>

    <!-- Display cart message -->
    <?php if ($cart_msg): ?>
        <div class="stock-alert"> <?php echo htmlspecialchars($cart_msg); ?></div>
    <?php endif; ?>

    <h2 class="section-title">Featured Products</h2>

    <div class="products-grid">
        <?php
        //  Fetch and display products from the database 
        $result = $conn->query("SELECT * FROM product");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Check if the product is out of stock
                $is_out = ($row['quantity'] <= 0);
            ?>
                <div class="product-card <?php echo $is_out ? 'out-of-stock' : ''; ?>">
<!-- Display 'Out of Stock' badge if product is out of stock -->
                    <?php if ($is_out): ?>
                        <span class="out-of-stock-badge">Out of Stock</span>
                    <?php endif; ?>

                    <!-- Product Image -->
                    <img src="images/<?php echo htmlspecialchars($row['image_file']); ?>"
                         alt="<?php echo htmlspecialchars($row['product_name']); ?>">

                    <!-- Product Name and Price -->
                    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                    <p class="price-tag"><?php echo number_format($row['price'], 2); ?> SAR</p>

                    <div class="card-actions">
                        <!-- View Product Details Button -->
                        <a href="details.php?id=<?php echo $row['product_id']; ?>" class="btn-details">
                            View Details
                        </a>

                        <!-- Add to Cart Button (disabled if product is out of stock) -->
                        <?php if ($is_out): ?>
                            <span class="btn-cart-disabled">Unavailable</span>
                        <?php else: ?>
                            <a href="index.php?add=<?php echo $row['product_id']; ?>" class="btn-cart">
                                 Add to Cart
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
        <?php }
        } else {
            // Display message if no products are found
            echo "<p class='no-products-msg'>No products found.</p>";
        } ?>
    </div>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>About iTech Store</h3>
            <p>Your premium destination for the latest technology and genuine Apple products in Saudi Arabia.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="contact_us.php">Contact Us</a>
        </div>
        <div class="footer-section">
            <h3>Contact Info</h3>
            <p> Dammam, Saudi Arabia</p>
            <p> support@itechstore.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 iTech Store | All Rights Reserved</p>
    </div>
</footer>

</body>
</html>
