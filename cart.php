<?php

// Author: Noor Ribhi Almahuzi

session_start();
include 'Database/db.php';

//  Add item to cart 
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);  // Get the product ID from the URL parameter
    // Get the requested quantity (from details.php or default to 1)
    $qty_req = isset($_GET['qty_req']) ? max(1, intval($_GET['qty_req'])) : 1;

    // Query to fetch the product details based on product ID
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        // If cart does not exist, create it
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;  // Get the current quantity in cart
        $total_want  = $current_qty + $qty_req;  // Calculate total quantity to add

        // Check if the requested total quantity does not exceed stock
        if ($total_want <= $product['quantity']) {
            // If product is already in cart, increase the quantity
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $qty_req;
            } else {
                // If product is not in cart, add it with details
                $_SESSION['cart'][$product_id] = [
                    'name'     => $product['product_name'],
                    'price'    => $product['price'],
                    'image'    => $product['image_file'],
                    'quantity' => $qty_req,
                    'stock'    => $product['quantity']
                ];
            }
            $_SESSION['cart_success'] = " Item added to cart successfully!";  // Success message
            header("Location: cart.php");  // Redirect to cart page
        } else {
            // If requested quantity exceeds available stock, show appropriate message
            $available = $product['quantity'] - $current_qty;
            if ($available <= 0) {
                $_SESSION['cart_msg'] = "Maximum stock reached for \"{$product['product_name']}\".";
            } else {
                $_SESSION['cart_msg'] = "Only {$available} more item(s) can be added for \"{$product['product_name']}\".";
            }
            header("Location: cart.php");  // Redirect to cart page
        }
    } else {
        header("Location: cart.php");  // If the product is not found, go back to cart
    }
    exit();
}

//  Other actions (remove, update, empty cart) 
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'remove' && isset($_GET['id'])) {
        unset($_SESSION['cart'][intval($_GET['id'])]);  // Remove item from cart
        header("Location: cart.php"); exit();
    }
    if ($action == 'update' && isset($_GET['id'], $_GET['qty'])) {
        $pid = intval($_GET['id']); $qty = intval($_GET['qty']);
        if (isset($_SESSION['cart'][$pid])) {
            $stock = $_SESSION['cart'][$pid]['stock'];
            if ($qty <= 0) {
                unset($_SESSION['cart'][$pid]);  // Remove item if quantity is zero or less
            } elseif ($qty <= $stock) {
                $_SESSION['cart'][$pid]['quantity'] = $qty;  // Update quantity
            } else {
                $_SESSION['cart_msg'] = "Only {$stock} item(s) available.";  // Show message if exceeding stock
            }
        }
        header("Location: cart.php"); exit();
    }
    if ($action == 'empty') {
        unset($_SESSION['cart']);  // Empty the cart
        header("Location: cart.php"); exit();
    }
}

//  Calculate total price and handle cart messages 
$total_price = 0;
$cart_msg = '';
$cart_success = '';
if (isset($_SESSION['cart_msg'])) { $cart_msg = $_SESSION['cart_msg']; unset($_SESSION['cart_msg']); }
if (isset($_SESSION['cart_success'])) { $cart_success = $_SESSION['cart_success']; unset($_SESSION['cart_success']); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - iTech Store</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<?php include 'Includes/header.php'; ?>

<main class="cart-page-container">
    <a href="index.php" class="btn-back">&larr; Back to Store</a>
    <h2 class="cart-header">Shopping Cart</h2>

    <!-- Display success message if an item was successfully added -->
    <?php if ($cart_success): ?>
        <div class="cart-success-msg">
            <?php echo htmlspecialchars($cart_success); ?>
        </div>
    <?php endif; ?>

    <!-- Display stock message if any item has stock-related issues -->
    <?php if ($cart_msg): ?>
        <div class="stock-msg"> <?php echo htmlspecialchars($cart_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Loop through cart items and calculate subtotal for each item
                foreach ($_SESSION['cart'] as $id => $item):
                    $sub = $item['price'] * $item['quantity'];
                    $total_price += $sub;  // Calculate total price of all items
                ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>"
                                 class="cart-item-img" alt="Product">
                            <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                        </div>
                    </td>
                    <td class="cart-price"><?php echo number_format($item['price'], 2); ?> SAR</td>
                    <td class="quantity-column">
                        <!-- Quantity input form to update the quantity -->
                        <form class="qty-form" action="cart.php" method="GET">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="number" name="qty"
                                   value="<?php echo $item['quantity']; ?>"
                                   min="1" max="<?php echo $item['stock']; ?>">
                            <button type="submit" class="btn-update">Update</button>
                        </form>
                    </td>
                    <td class="cart-price subtotal-column"><?php echo number_format($sub, 2); ?> SAR</td>
                    <td>
                        <!-- Remove item from cart with confirmation -->
                        <a href="cart.php?action=remove&id=<?php echo $id; ?>"
                           class="btn-remove remove-confirm">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Cart Total and Checkout -->
        <div class="cart-total-section">
            <span class="cart-total-label">Grand Total:</span>
            <span class="cart-total-amount"><?php echo number_format($total_price, 2); ?> SAR</span>
            <div class="checkout-wrapper">
                <!-- Empty cart button -->
                <a href="cart.php?action=empty" class="btn-empty" id="emptyCartBtn">
                   Empty Cart
                </a>
                <!-- Proceed to checkout button -->
                <a href="success.php" class="btn-checkout">Proceed to Checkout</a>
            </div>
        </div>

    <?php else: ?>
        <!-- Message if cart is empty -->
        <div class="empty-cart-msg">
            <p>Your shopping cart is currently empty.</p>
            <a href="index.php" class="link-continue-shopping"> Continue Shopping</a>
        </div>
    <?php endif; ?>
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

<script>
// Confirm before removing a single item
document.querySelectorAll('.remove-confirm').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('Remove this item from your cart?')) {
            e.preventDefault();
        }
    });
});

// Confirm before emptying the cart
var emptyBtn = document.getElementById('emptyCartBtn');
if (emptyBtn) {
    emptyBtn.addEventListener('click', function(e) {
        if (!confirm('Empty your entire cart?')) {
            e.preventDefault();
        }
    });
}
</script>

</body>
</html>