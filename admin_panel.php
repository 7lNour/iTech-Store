<?php

// Author: Taghreed Mashal Alhrabi

session_start();
include 'Database/db.php';

// 
// Check if the user is logged in as an admin 
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not logged in as admin, redirect to login page
    header("Location: login.php");
    exit();
}

$search_query = "";  // Default search query is empty

// 
// Handle product search 
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);  // Get search query from GET request
    $like = "%" . $search_query . "%";  // Add '%' for LIKE query in SQL

    // Prepare the query to search products by name or category
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_name LIKE ? OR category LIKE ?");
    $stmt->bind_param("ss", $like, $like);  // Bind parameters for the query
    $stmt->execute();
    $result = $stmt->get_result();  // Execute and get the result
} else {
    // If no search query, show all products
    $result = $conn->query("SELECT * FROM product");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - iTech Store</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<header class="main-header">
    <div class="nav-container">

        <div>
            <a href="admin_panel.php" class="navbar-brand">
                iTech Admin Panel
            </a>

         <p class="admin-welcome">
    Welcome, <span class="admin-welcome-name"><?php echo htmlspecialchars($admin_name); ?></span>
</p>
        </div>

        <!-- Admin Navigation Menu -->
        <nav>
            <ul class="nav-list">
                <li><a href="admin_panel.php">Dashboard</a></li>
                <li><a href="admin_add_product.php">Add Product</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </nav>

    </div>
</header>

<main class="admin-page">

    <div class="admin-card">

        <div class="admin-top">
            <h1>Product Management</h1>

            <!-- Button to add a new product -->
            <a href="admin_add_product.php" class="admin-add-btn">
                + Add Product
            </a>
        </div>

        <!-- Search Form -->
        <form action="admin_panel.php" method="GET" class="admin-search">
            <input type="text"
                   name="search"
                   placeholder="Search products..."
                   value="<?php echo htmlspecialchars($search_query); ?>"> <!-- Show search query in the input box -->

            <button type="submit">
                Search
            </button>

        </form>

        <!-- Products Table -->
        <table class="admin-product-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($result && $result->num_rows > 0): ?>  <!-- If products found -->

                    <?php while ($row = $result->fetch_assoc()): ?>  <!-- Loop through products -->

                    <tr>

                        <td>
                            <?php echo $row['product_id']; ?>
                        </td>

                        <td>
                            <!-- Display product image -->
                            <img src="images/<?php echo htmlspecialchars($row['image_file']); ?>"
                                 class="admin-product-img">
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['product_name']); ?>
                        </td>

                        <td>
                            <?php echo number_format($row['price'], 2); ?> SAR
                        </td>

                        <td>
                            <?php
echo $row['quantity']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['category']); ?>
                        </td>

                        <td>

                            <div class="admin-actions">
                                <!-- Edit product link -->
                                <a href="admin_edit_product.php?id=<?php echo $row['product_id']; ?>"
                                   class="admin-edit-btn">
                                   Edit
                                </a>

                                <!-- Delete product link with confirmation -->
                                <a href="admin_delete_product.php?id=<?php echo $row['product_id']; ?>"
                                   class="admin-delete-btn delete-confirm">
                                   Delete
                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <!-- If no products found -->
                    <tr>
                        <td colspan="7" class="no-results">
                            No products found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

<script>
// Confirm before deleting a product
document.querySelectorAll('.delete-confirm').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this product?')) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>