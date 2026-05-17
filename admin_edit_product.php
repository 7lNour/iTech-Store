<?php


session_start();
include 'Database/db.php';

//  Check if the user is logged in as an admin 
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not logged in as admin, redirect to login page
    header("Location: login.php");
    exit();
}

$product = null;  // Initialize the product variable

//  Fetch product details if product ID is provided 
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];  // Get product ID from URL
    // Prepare and execute query to fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $id);  // Bind the product ID parameter
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();  // Fetch product details
}

//  Handle form submission for editing product 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['product_name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Validate form data
    if ($name === '' || $category === '' || $price <= 0 || $stock < 0 || strlen($description) < 10) {
        $_SESSION['edit_error'] = 'Please fill all fields correctly.';
        header("Location: admin_edit_product.php?id=$id"); exit();
    }

    //  Handle image upload 
    if (!empty($_FILES['product_image']['name'])) {
        // Get the uploaded image file name and extension
        $image_name = basename($_FILES['product_image']['name']);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Check if the uploaded file is a valid image format
        if (!in_array($ext, $allowed)) {
            $_SESSION['edit_error'] = 'Invalid image format. Use JPG, PNG, GIF or WEBP.';
            header("Location: admin_edit_product.php?id=$id"); exit();
        }

        // Move the uploaded image to the "images" directory
        move_uploaded_file($_FILES['product_image']['tmp_name'], "images/" . $image_name);

        // Update product details in the database with the new image
        $stmt = $conn->prepare("UPDATE product SET product_name=?, price=?, quantity=?, category=?, description=?, image_file=? WHERE product_id=?");
        $stmt->bind_param("sdisssi", $name, $price, $stock, $category, $description, $image_name, $id);
    } else {
        // If no new image is uploaded, update without changing the image
        $stmt = $conn->prepare("UPDATE product SET product_name=?, price=?, quantity=?, category=?, description=? WHERE product_id=?");
        $stmt->bind_param("sdissi", $name, $price, $stock, $category, $description, $id);
    }

    // Execute the update query
    if ($stmt->execute()) {
        $_SESSION['edit_success'] = 'Product updated successfully!';
        header("Location: admin_panel.php"); exit();
    } else {
        $_SESSION['edit_error'] = 'Error updating product.';
        header("Location: admin_edit_product.php?id=$id"); exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - iTech Admin</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>

<header class="main-header">
    <div class="nav-container">
        <a href="admin_panel.php" class="navbar-brand">iTech Admin Panel</a>

        <nav>
            <ul class="nav-list">
                <li><a href="admin_panel.php">Dashboard</a></li>
                <li><a href="admin_add_product.php">Add Product</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="form-container">
        <h2 class="admin-title">Edit Product</h2>

        <!-- Show server-side messages if any -->
        <?php if (isset($_SESSION['edit_error'])): ?>
            <div class="error-msg error-msg--visible">
                <?php echo htmlspecialchars($_SESSION['edit_error']); unset($_SESSION['edit_error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($product): ?>
        <form action="admin_edit_product.php" method="POST" enctype="multipart/form-data" id="editForm">

            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

            <!-- Product Name Input -->
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" id="ep_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                <span class="err" id="ep_nameErr"></span>
            </div>

            <!-- Category Input -->
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="ep_cat" value="<?php echo htmlspecialchars($product['category']); ?>">
                <span class="err" id="ep_catErr"></span>
            </div>

            <!-- Price Input -->
            <div class="form-group">
                <label>Price (SAR)</label>
                <input type="number" step="0.01" name="price" id="ep_price" value="<?php echo $product['price']; ?>">
                <span class="err" id="ep_priceErr"></span>
            </div>

            <!-- Stock Quantity Input -->
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" id="ep_stock" value="<?php echo $product['quantity']; ?>">
                <span class="err" id="ep_stockErr"></span>
            </div>

            <!-- Description Input -->
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="ep_desc"><?php echo htmlspecialchars($product['description']); ?></textarea>
                <span class="err" id="ep_descErr"></span>
            </div>

            <!-- Current Image Display and New Image Upload -->
            <div class="form-group">
                <label>Current Image</label>
                <img src="images/<?php echo htmlspecialchars($product['image_file']); ?>" class="current-img-preview" width="120">

                <label>Change Image</label>
                <input type="file" name="product_image" id="ep_img" accept="image/*">
                <small>Leave empty to keep current image.</small>
                <span class="err" id="ep_imgErr"></span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-checkout">Update Product</button>

        </form>

        <a href="admin_panel.php" class="cancel-link">Back to Dashboard</a>

        <?php else: ?>
            <!-- If product not found -->
            <p class="error-msg">Product not found.</p>
            <a href="admin_panel.php" class="btn-checkout">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</main>

<script>
//  Form Validation 
function validateEditForm() {
    let ok = true;

    function setError(id, msg) {
        document.getElementById(id).textContent = msg;
        ok = false;
    }

    // Clear previous error messages
    document.getElementById("ep_nameErr").textContent = "";
    document.getElementById("ep_catErr").textContent = "";
    document.getElementById("ep_priceErr").textContent = "";
    document.getElementById("ep_stockErr").textContent = "";
    document.getElementById("ep_descErr").textContent = "";
    document.getElementById("ep_imgErr").textContent = "";

    // Get form values
    let name     = document.getElementById("ep_name").value.trim();
    let category = document.getElementById("ep_cat").value.trim();
    let price    = parseFloat(document.getElementById("ep_price").value);
    let stock    = parseInt(document.getElementById("ep_stock").value);
    let desc     = document.getElementById("ep_desc").value.trim();

    //  Product Name: letters and spaces only, 2-100 chars 
    if (name === "") {
        setError("ep_nameErr", "Product name is required.");
    } else if (!/^[a-zA-Z]/.test(name)) {
        setError("ep_nameErr", "Product name must start with a letter.");
    } else if (!/^[a-zA-Z][a-zA-Z0-9\s]+$/.test(name)) {
        setError("ep_nameErr", "Product name must start with a letter and contain only letters, numbers, and spaces.");
    } else if (name.length < 2) {
        setError("ep_nameErr", "Product name must be at least 2 characters.");
    } else if (name.length > 100) {
        setError("ep_nameErr", "Product name must not exceed 100 characters.");
    }

    //  Category: letters and spaces only, 2-50 chars 
    if (category === "") {
        setError("ep_catErr", "Category is required.");
    } else if (!/^[a-zA-Z\s]+$/.test(category)) {
        setError("ep_catErr", "Category must contain letters only.");
    } else if (category.length < 2) {
        setError("ep_catErr", "Category must be at least 2 characters.");
    } else if (category.length > 50) {
        setError("ep_catErr", "Category must not exceed 50 characters.");
    }

    //  Price: positive number, max 2 decimals, max 999999 
    if (document.getElementById("ep_price").value === "") {
        setError("ep_priceErr", "Price is required.");
    } else if (isNaN(price) || price <= 0) {
        setError("ep_priceErr", "Price must be a positive number.");
    } else if (price > 999999) {
        setError("ep_priceErr", "Price must not exceed 999,999 SAR.");
    } else if (!/^\d+(\.\d{1,2})?$/.test(document.getElementById("ep_price").value)) {
        setError("ep_priceErr", "Price must have at most 2 decimal places.");
    }

    //  Stock: integer, 0-9999 
    if (document.getElementById("ep_stock").value === "") {
        setError("ep_stockErr", "Stock quantity is required.");
    } else if (isNaN(stock) || stock < 0) {
        setError("ep_stockErr", "Stock must be 0 or more.");
    } else if (stock > 9999) {
        setError("ep_stockErr", "Stock must not exceed 9,999.");
    }

    //  Description: 10-1000 chars 
    if (desc === "") {
        setError("ep_descErr", "Description is required.");
    } else if (desc.length < 10) {
        setError("ep_descErr", "Description must be at least 10 characters.");
    } else if (desc.length > 1000) {
        setError("ep_descErr", "Description must not exceed 1,000 characters.");
    }

    //  Image (optional in edit): validate only if a new file is selected 
    let imgInput = document.querySelector("input[name='product_image']");
    if (imgInput && imgInput.files.length > 0) {
        let file         = imgInput.files[0];
        let allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        let maxSize      = 5 * 1024 * 1024;
        if (!allowedTypes.includes(file.type)) {
            setError("ep_imgErr", "Only JPG, PNG, GIF, or WEBP images are allowed.");
        } else if (file.size > maxSize) {
            setError("ep_imgErr", "Image size must not exceed 5MB.");
        }
    }

    return ok;
}

// Attach submit event listener 
document.getElementById("editForm").addEventListener("submit", function(e) {
    if (!validateEditForm()) {
        e.preventDefault();
    }
});
</script>

</body>
</html>
