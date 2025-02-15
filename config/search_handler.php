<?php
// Include the database connection file
include './config.php'; // Replace with your actual database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search_query']);

    // Query the database to match `model`, `serial_number`, and other fields
    $query = "SELECT * FROM assets WHERE model LIKE '%$search_query%' OR serial_number LIKE '%$search_query%' OR category LIKE '%$search_query%'";

    $result = mysqli_query($conn, $query);

    // Return the search results
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='border-b py-2'>"; // Customize result layout
            echo "<strong>Model:</strong> " . htmlspecialchars($row['model']) . "<br>";
            echo "<strong>Category:</strong> " . htmlspecialchars($row['category']) . "<br>";
            echo "<strong>Regi No:</strong> " . htmlspecialchars($row['registration_number']);
            echo "</div>";
        }
    } else {
        echo "<div class='text-gray-500'>No results found</div>";
    }
    exit; // Ensure no additional output is sent back
}

?>