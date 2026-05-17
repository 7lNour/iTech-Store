<?php 

// Author: Noor Ribhi Almahuzi

session_start(); 
include 'Database/db.php'; 

//  Get product ID from the URL parameter 
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

//  Prepare and execute the query to fetch product details 
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);  // Bind product ID parameter
$stmt->execute();
$result = $stmt->get_result();

//  If the product exists in the database 
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();  // Fetch the product details
} else {
    echo "Product not found!";  // Display error message if product not found
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['product_name']); ?> - iTech Store</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<header class="main-header">
    <div class="nav-container">
        <a href="index.php" class="navbar-brand">iTech Store</a>
        <nav>
            <ul class="nav-list">
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="main-container">

    <!-- Product Details Section -->
    <div class="product-details-wrapper">
        <!-- Product Image -->
        <div class="product-image-box">
            <img src="images/<?php echo htmlspecialchars($row['image_file']); ?>" 
                 alt="<?php echo htmlspecialchars($row['product_name']); ?>" class="details-img">
        </div>

        <!-- Product Info Section -->
        <div class="product-info-box">
            <h1><?php echo htmlspecialchars($row['product_name']); ?></h1>
            <h2 class="details-price"><?php echo number_format($row['price'], 2); ?> SAR</h2>

            <?php
            $stock = (int)$row['quantity'];  // Get the stock quantity
            if ($stock == 0)      echo '<span class="stock-badge stock-out">Out of Stock</span>';
            elseif ($stock <= 5)  echo '<span class="stock-badge stock-low">Only ' . $stock . ' left in stock!</span>';
            else                  echo '<span class="stock-badge stock-in">In Stock (' . $stock . ' available)</span>';
            ?>

            <!-- Product Description Section -->
            <div class="description-section">
                <h3>Description:</h3>
                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            </div>

            <!-- Add to Cart Form (If in stock) -->
            <?php if ($stock > 0): ?>
            <form action="cart.php" method="GET" id="addToCartForm">
                <input type="hidden" name="add" value="<?php echo $row['product_id']; ?>">
                <div class="qty-wrapper">
                    <label for="qty">Quantity:</label>
                    <input type="number" id="qty" name="qty_req" value="1"
                           min="1" max="<?php echo $stock; ?>" class="qty-input" required>
                    <button type="button" class="btn-help" id="helpBtn" title="Help">?</button>
                </div>
                <button type="submit" class="btn-add-large">Add to Cart</button>
            </form>
            <?php else: ?>
                <p class="unavailable-text">Currently unavailable</p>
            <?php endif; ?>

            <!-- Link to go back to Home -->
            <div class="back-link">
                <a href="index.php">← Back to Home</a>
            </div>
        </div>
    </div>
</main>

<!-- Help Modal -->
<div class="help-overlay" id="helpOverlay" role="dialog" aria-modal="true" aria-label="Shopping Help">
    <div class="help-modal">
        <button class="close-help" id="closeHelpBtn" aria-label="Close"></button>
        <h3> How to Shop</h3>
        <ul>
            <li>Choose the quantity you want (max = available stock).</li>
            <li>Click <strong>Add to Cart</strong> to add the item.</li>
            <li>Go to <strong>My Cart</strong> to review your order.</li>
            <li>Click <strong>Proceed to Checkout</strong> to complete your purchase.</li>
            <li>You must be logged in to place an order.</li>
        </ul>
        <p class="help-contact-note">
            Need help? Contact us at support@itechstore.com
        </p>
    </div>
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
            <p>Dammam, Saudi Arabia</p>
            <p>support@itechstore.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 iTech Store | All Rights Reserved</p>
    </div>
</footer>

<script>
// Validate quantity input on form submission
var maxStock = <?php echo $stock; ?>;

function validateQty() {
    var qty = parseInt(document.getElementById('qty').value);
    if (isNaN(qty) || qty < 1) {
        alert('Please enter a valid quantity (minimum 1).');
        return false;
    }
    if (qty > maxStock) {
        alert('Sorry, only ' + maxStock + ' item(s) are available in stock.');
        return false;
    }
    return true;
}

// Open and close the help overlay modal
function openHelp()  { document.getElementById('helpOverlay').classList.add('active'); }
function closeHelp() { document.getElementById('helpOverlay').classList.remove('active'); }

// Attach event listeners instead of inline handlers
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    if (!validateQty()) {
        e.preventDefault();
    }
});

document.getElementById('helpBtn').addEventListener('click', openHelp);
document.getElementById('closeHelpBtn').addEventListener('click', closeHelp);

// Close modal if user clicks outside of it
document.getElementById('helpOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeHelp();
});
</script>

</body>
</html>