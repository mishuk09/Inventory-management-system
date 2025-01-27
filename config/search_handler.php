<?php
// Include the database connection file
include './config.php'; // Replace with your actual database connection file

// Check if the search query is set
if (isset($_GET['search_query'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search_query']);

    // Query the database for matches
    $query = "SELECT * FROM assets WHERE serial_number LIKE '%$search_query%' OR category LIKE '%$search_query%'";
    $result = mysqli_query($conn, $query);

    // Return results
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='border-b py-2'>"; // Customize result HTML
            echo "<strong>Serial:</strong> " . htmlspecialchars($row['serial_number']) . "<br>";
            echo "<strong>Category:</strong> " . htmlspecialchars($row['category']);
            echo "</div>";
        }
    } else {
        echo "<div class='text-gray-500'>No results found</div>";
    }
}
?>