<?php
// Include the database connection file
include('../config/config.php'); // Ensure this is the correct path

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the form data and sanitize it
    $asset_id = mysqli_real_escape_string($conn, $_POST['asset_id']);
    $serial_number = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $registration_number = mysqli_real_escape_string($conn, $_POST['registration_number']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $sub_category = mysqli_real_escape_string($conn, $_POST['sub_category']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $artificial = mysqli_real_escape_string($conn, $_POST['artificial']);
    $no_engine = mysqli_real_escape_string($conn, $_POST['no_engine']);

    // Prepare the SQL query to insert data into the database
    $query = "INSERT INTO assets (asset_id, serial_number, registration_number, category, sub_category, type, model, artificial, no_engine) 
              VALUES ('$asset_id', '$serial_number', '$registration_number', '$category', '$sub_category', '$type', '$model', '$artificial', '$no_engine')";

    // Execute the query and check if data is inserted
    if (mysqli_query($conn, $query)) {
        // If insertion is successful, redirect to the home page or show a success message
        header("Location: index.php");  // Redirect to the home page (index.php)
        exit(); // Always call exit after header redirection
    } else {
        // If there is an error, display it
        echo "Error: " . mysqli_error($conn);
    }
}
?>