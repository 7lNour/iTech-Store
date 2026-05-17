<?php

session_start();
include 'Database/db.php';

//  Ensure the user is logged in as admin 
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // If not an admin, redirect to login page
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];  // Get the product ID from the URL

    //  Prepare the query to fetch the product's image file 
    $stmt = $conn->prepare("SELECT image_file FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);  // Bind the product ID to the query
    $stmt->execute();
    $result = $stmt->get_result();  // Execute the query and get the result

    //  If the product exists 
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();  // Fetch the product details

        //  Construct the path to the product image 
        $imagePath = "images/" . $row['image_file'];

        //  If the image exists and is not empty, delete it from the server 
        if (!empty($row['image_file']) && file_exists($imagePath)) {
            unlink($imagePath);  // Delete the image file
        }

        //  Prepare and execute the query to delete the product from the database 
        $delete = $conn->prepare("DELETE FROM product WHERE product_id = ?");
        $delete->bind_param("i", $product_id);  // Bind the product ID for deletion
        $delete->execute();  // Execute the delete query
    }
}

//  Redirect back to the admin panel after deletion 
header("Location: admin_panel.php");
exit();
?>
