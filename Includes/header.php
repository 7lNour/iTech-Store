<?php

// Author: Noor Abdulkhaleq Alkhames

//  Check if the session has started, and start it if not 
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start session if not already started
}

$cart_count = 0;  // Initialize cart item count

//  Check if the cart session exists and contains items 
if (!empty($_SESSION['cart'])) {
    // Loop through each item in the cart and sum the quantities
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];  // Add item quantity to cart count
    }
}
?>

<header class="main-header">
    <div class="nav-container">

        <!-- Logo section: Link to the homepage -->
        <a href="index.php" class="logo-wrap">
            <img src="images/logo.png" alt="iTech Store Logo" class="site-logo">
        </a>

        <!-- Navigation menu -->
        <nav>
            <ul class="nav-list">
                <!-- Home link -->
                <li><a href="index.php">Home</a></li>

                <!-- Cart link with dynamic item count -->
                <li>
                    <a href="cart.php">
                         🛒 Cart
                        <span class="cart-count">
                            <?php echo $cart_count; ?>  <!-- Display cart item count -->
                        </span>
                    </a>
                </li>

                <!-- Contact Us link -->
                <li><a href="contact_us.php">Contact Us</a></li>

                <!-- Admin Panel link if logged in as admin -->
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>

                    <!-- Admin panel link -->
                    <li><a href="admin_panel.php">Admin Panel</a></li>

                    <!-- Logout link for admin -->
                    <li>
                        <a href="logout.php" class="btn-logout">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <!-- Admin Login link if not logged in as admin -->
                    <li>
                        <a href="login.php">
                            Admin Login
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </nav>

    </div>
</header>