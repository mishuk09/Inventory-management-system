<?php
// delete_item.php
include('./config.php'); // Include the database connection

if (isset($_GET['asset_id'])) {
    $assetId = $_GET['asset_id'];

    // Delete the asset from the database
    $query = "DELETE FROM assets WHERE asset_id='$assetId'";
    if (mysqli_query($conn, $query)) {
        // Redirect with success message
        $_SESSION['message'] = "Asset deleted successfully!";
    } else {
        // Handle failure
        $_SESSION['message'] = "Error deleting asset.";
    }

    // Redirect to the main page
    header('Location: index.php');
}
?>