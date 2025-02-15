<?php
session_start();
include('./config/config.php'); // Ensure correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        // Return by Item ID
        $id = intval($_POST['id']);
        $query = "DELETE FROM issued_items WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            echo "success";
        } else {
            echo "error";
        }
    } elseif (isset($_POST['registration_number'])) {
        // Return by Registration Number
        $regNo = mysqli_real_escape_string($conn, $_POST['registration_number']);
        $query = "SELECT id FROM issued_items WHERE registration_number = '$regNo'";
        $result = mysqli_query($conn, $query);

        $itemIds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $itemIds[] = $row['id'];
        }

        if (!empty($itemIds)) {
            $idsString = implode(',', $itemIds);
            $deleteQuery = "DELETE FROM issued_items WHERE registration_number = '$regNo'";
            if (mysqli_query($conn, $deleteQuery)) {
                echo json_encode(["status" => "success", "itemIds" => $itemIds]);
            } else {
                echo json_encode(["status" => "error"]);
            }
        } else {
            echo json_encode(["status" => "error"]);
        }
    }
}
?>