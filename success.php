<?php

session_start();
include 'Database/db.php';

//  Check if cart is empty 
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$order_items = [];
$order_total = 0;
$errors      = [];

//  Check stock before proceeding 
foreach ($_SESSION['cart'] as $product_id => $item) {
    $pid        = (int)$product_id;
    $qty_bought = (int)$item['quantity'];

    $stmt = $conn->prepare("SELECT quantity FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product || $product['quantity'] < $qty_bought) {
        $errors[] = $item['name'];
    }
}

if (!empty($errors)) {
    $_SESSION['cart_msg'] = 'Some products are out of stock: ' . implode(', ', $errors);
    header("Location: cart.php");
    exit();
}

//  Load existing past purchases from cookie 
// $existing = all OLD purchases (before this order)
$existing = [];
if (isset($_COOKIE['past_purchases'])) {
    $decoded = json_decode($_COOKIE['past_purchases'], true);
    if (is_array($decoded)) $existing = $decoded;
}

// Save a snapshot of previous orders BEFORE adding the current one
// This is used in the "Previous Orders" section on the page
$previous_orders = $existing;

//  Update stock, build current order, append to history 
foreach ($_SESSION['cart'] as $product_id => $item) {
    $pid        = (int)$product_id;
    $qty_bought = (int)$item['quantity'];
    $line_total = $item['price'] * $qty_bought;

    // Build current order summary
    $order_items[] = [
        'name'  => $item['name'],
        'qty'   => $qty_bought,
        'price' => $item['price'],
        'total' => $line_total
    ];

    $order_total += $line_total;

    // Append current items to history
    $existing[] = [
        'name'  => $item['name'],
        'qty'   => $qty_bought,
        'total' => $line_total,
        'date'  => date('Y-m-d H:i')
    ];

    // Update product stock in the database
    $stmt = $conn->prepare("UPDATE product SET quantity = quantity - ? WHERE product_id = ?");
    $stmt->bind_param("ii", $qty_bought, $pid);
    $stmt->execute();
}

// Keep only the last 50 purchases
if (count($existing) > 50) $existing = array_slice($existing, -50);

// Save updated history to cookie (30 days)
setcookie("past_purchases", json_encode($existing), time() + (30 * 24 * 3600), "/");

// Clear the cart
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - iTech Store</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<?php include 'Includes/header.php'; ?>

<div class="success-page">

    <!-- Thank You Card -->
    <div class="success-hero">
        <img src="images/logo.png" alt="iTech Store" class="success-logo">
        <div class="success-icon">&#10003;</div>
        <h1>Thank You!</h1>
        <p><strong>Your order has been confirmed.</strong></p>
        <p>Your purchase was completed successfully.</p>
        <span class="order-id">&#128197; <?php echo date('Y-m-d H:i'); ?></span>
    </div>

    <!-- Order Summary (current order) -->
    <div class="order-card">
        <div class="order-card-header">
            <h2>Order Summary</h2>
        </div>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                    <td class="td-price"><?php echo number_format($item['price'], 2); ?> SAR</td>
                    <td>&#215;<?php echo (int)$item['qty']; ?></td>
                    <td class="td-total"><?php echo number_format($item['total'], 2); ?> SAR</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!--  Order Total  -->
    <div class="order-total-bar">
        <span class="label">Order Total</span>
        <span class="amount"><?php echo number_format($order_total, 2); ?> SAR</span>
    </div>

    <!--  Back to Store Button  -->
    <div class="actions-row">
        <a href="index.php" class="btn-back-store">&#127968; Back to Store</a>
    </div>

    <!--  Previous Orders (from cookie — excludes current order)  -->
    <?php if (!empty($previous_orders)): ?>
    <div class="order-card order-card--previous" style="margin-top: 30px;">
        <div class="order-card-header">
            <h2>Previous Orders</h2>
        </div>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse($previous_orders) as $p): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                    <td>&#215;<?php echo (int)$p['qty']; ?></td>
                    <td class="td-total"><?php echo number_format($p['total'], 2); ?> SAR</td>
                    <td style="color:#888; font-size:13px;"><?php echo htmlspecialchars($p['date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

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
            <p>&#128205; Dammam, Saudi Arabia</p>
            <p>&#128231; support@itechstore.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 iTech Store | All Rights Reserved</p>
    </div>
</footer>

</body>
</html>
