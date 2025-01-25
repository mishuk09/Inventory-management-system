<?php
// update_item.php
include('../config/config.php'); // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assetId = $_POST['asset_id'];
    $serialNumber = $_POST['serial_number'];
    $registrationNumber = $_POST['registration_number'];

    // You can add other fields if necessary

    // Update the asset in the database
    $query = "UPDATE assets SET serial_number='$serialNumber', registration_number='$registrationNumber' WHERE asset_id='$assetId'";
    if (mysqli_query($conn, $query)) {
        // Redirect to the main page with a success message
        $_SESSION['message'] = "Asset updated successfully!";
        header('Location: index.php');
    } else {
        // Handle failure
        $_SESSION['message'] = "Error updating asset.";
        header('Location: index.php');
    }
}
?>
