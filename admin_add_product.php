<?php

session_start();
include 'Database/db.php';

//  Ensure the user is logged in as an admin 
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not an admin, redirect to login page
    header("Location: login.php");
    exit();
}

$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 1;  // Admin ID, default to 1

//  Handle form submission for adding a new product 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['product_name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Allowed image types and max file size (5MB)
    $allowed_types = ['image/jpeg','image/png','image/gif','image/webp'];
    $max_size = 5 * 1024 * 1024;

    //  Validate image file 
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
        $_SESSION['add_error'] = 'Please select a product image.';
        header("Location: admin_add_product.php"); exit();
    }

    //  Check if the image type is allowed 
    if (!in_array($_FILES['product_image']['type'], $allowed_types)) {
        $_SESSION['add_error'] = 'Invalid image type. Use JPG, PNG, GIF or WEBP.';
        header("Location: admin_add_product.php"); exit();
    }

    //  Check if the image size exceeds the allowed limit 
    if ($_FILES['product_image']['size'] > $max_size) {
        $_SESSION['add_error'] = 'Image size must be less than 5MB.';
        header("Location: admin_add_product.php"); exit();
    }

    //  Generate the image file name and move the uploaded file to the images directory 
    $image_name = basename($_FILES['product_image']['name']);
    $target_file = "images/" . $image_name;

    // Move the uploaded file to the target location
    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
        //  Insert the product details into the database 
        $stmt = $conn->prepare(
            "INSERT INTO product (product_name, price, quantity, category, description, image_file, admin_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdisssi", $name, $price, $stock, $category, $description, $image_name, $admin_id);

        //  Execute the insert query 
        if ($stmt->execute()) {
            $_SESSION['add_success'] = 'Product added successfully!';
            header("Location: admin_panel.php"); exit();
        } else {
            $_SESSION['add_error'] = 'Database error. Please try again.';
            header("Location: admin_add_product.php"); exit();
        }
    } else {
        $_SESSION['add_error'] = 'Failed to upload image.';
        header("Location: admin_add_product.php"); exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - iTech Admin</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<header class="main-header">
    <div class="nav-container">
        <a href="admin_panel.php" class="navbar-brand">iTech Admin Panel</a>

        <nav>
            <ul class="nav-list">
                <li><a href="admin_panel.php">Dashboard</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="form-container">
        <h2 class="admin-title">Add New Product</h2>

        <!-- Show server-side messages if any -->
        <?php if (isset($_SESSION['add_error'])): ?>
            <div class="error-msg error-msg--visible">
                <?php echo htmlspecialchars($_SESSION['add_error']); unset($_SESSION['add_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Product Add Form -->
        <form action="admin_add_product.php" method="POST" enctype="multipart/form-data" id="addForm">

            <!-- Product Name Input -->
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" id="ap_name">
                <span class="err" id="ap_nameErr"></span>
            </div>

            <!-- Category Input -->
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="ap_cat" placeholder="iphone, ipad, macbook...">
                <span class="err" id="ap_catErr"></span>
            </div>

            <!-- Price Input -->
            <div class="form-group">
                <label>Price (SAR)</label>
                <input type="number" step="0.01" name="price" id="ap_price">
                <span class="err" id="ap_priceErr"></span>
            </div>

            <!-- Stock Quantity Input -->
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" id="ap_stock">
                <span class="err" id="ap_stockErr"></span>
            </div>

            <!-- Description Input -->
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="ap_desc"></textarea>
                <span class="err" id="ap_descErr"></span>
            </div>

            <!-- Product Image Upload -->
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" id="ap_img" accept="image/*">
                <span class="err" id="ap_imgErr"></span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-checkout">Save Product</button>
        </form>

        <!-- Back to Dashboard Link -->
        <a href="admin_panel.php" class="cancel-link">Back to Dashboard</a>
    </div>
</main>

<script>
//  Form Validation 
function validateAddForm() {
    let ok = true;

    function setError(id, msg) {
        document.getElementById(id).textContent = msg;
        ok = false;
    }

    // Clear any previous error messages
    document.getElementById("ap_nameErr").textContent = "";
    document.getElementById("ap_catErr").textContent = "";
    document.getElementById("ap_priceErr").textContent = "";
    document.getElementById("ap_stockErr").textContent = "";
    document.getElementById("ap_descErr").textContent = "";
    document.getElementById("ap_imgErr").textContent = "";

    // Get form values
    let name     = document.getElementById("ap_name").value.trim();
    let category = document.getElementById("ap_cat").value.trim();
    let price    = parseFloat(document.getElementById("ap_price").value);
    let stock    = parseInt(document.getElementById("ap_stock").value);
    let desc     = document.getElementById("ap_desc").value.trim();
    let img      = document.getElementById("ap_img");

    //  Product Name: letters and spaces only, 2-100 chars 
    if (name === "") {
        setError("ap_nameErr", "Product name is required.");
    } else if (!/^[a-zA-Z]/.test(name)) {
        setError("ap_nameErr", "Product name must start with a letter.");
    } else if (!/^[a-zA-Z][a-zA-Z0-9\s]+$/.test(name)) {
        setError("ap_nameErr", "Product name must start with a letter and contain only letters, numbers, and spaces.");
    } else if (name.length < 2) {
        setError("ap_nameErr", "Product name must be at least 2 characters.");
    } else if (name.length > 100) {
        setError("ap_nameErr", "Product name must not exceed 100 characters.");
    }

    //  Category: letters and spaces only, 2-50 chars 
    if (category === "") {
        setError("ap_catErr", "Category is required.");
    } else if (!/^[a-zA-Z\s]+$/.test(category)) {
        setError("ap_catErr", "Category must contain letters only.");
    } else if (category.length < 2) {
        setError("ap_catErr", "Category must be at least 2 characters.");
    } else if (category.length > 50) {
        setError("ap_catErr", "Category must not exceed 50 characters.");
    }

    //  Price: positive number, max 2 decimals, max 999999 
    if (document.getElementById("ap_price").value === "") {
        setError("ap_priceErr", "Price is required.");
    } else if (isNaN(price) || price <= 0) {
        setError("ap_priceErr", "Price must be a positive number.");
    } else if (price > 999999) {
        setError("ap_priceErr", "Price must not exceed 999,999 SAR.");
    } else if (!/^\d+(\.\d{1,2})?$/.test(document.getElementById("ap_price").value)) {
        setError("ap_priceErr", "Price must have at most 2 decimal places.");
    }

    //  Stock: integer, 0-9999 
    if (document.getElementById("ap_stock").value === "") {
        setError("ap_stockErr", "Stock quantity is required.");
    } else if (isNaN(stock) || stock < 0) {
        setError("ap_stockErr", "Stock must be 0 or more.");
    } else if (stock > 9999) {
        setError("ap_stockErr", "Stock must not exceed 9,999.");
    }

    //  Description: 10-1000 chars 
    if (desc === "") {
        setError("ap_descErr", "Description is required.");
    } else if (desc.length < 10) {
        setError("ap_descErr", "Description must be at least 10 characters.");
    } else if (desc.length > 1000) {
        setError("ap_descErr", "Description must not exceed 1,000 characters.");
    }

    //  Image: required, allowed types, max 5MB 
    if (img.files.length === 0) {
        setError("ap_imgErr", "Product image is required.");
    } else {
        let file         = img.files[0];
        let allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        let maxSize      = 5 * 1024 * 1024;
        if (!allowedTypes.includes(file.type)) {
            setError("ap_imgErr", "Only JPG, PNG, GIF, or WEBP images are allowed.");
        } else if (file.size > maxSize) {
            setError("ap_imgErr", "Image size must not exceed 5MB.");
        }
    }

    return ok;
}

// Attach submit event listener 
document.getElementById("addForm").addEventListener("submit", function(e) {
    if (!validateAddForm()) {
        e.preventDefault();
    }
});
</script>

</body>
</html>
