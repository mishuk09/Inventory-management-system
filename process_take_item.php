<?php
session_start();
include('./config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_number'])) {
    $user_email = $_SESSION['user'];
    $reg_number = mysqli_real_escape_string($conn, $_POST['registration_number']);

    // Fetch asset details
    $query = "SELECT * FROM assets WHERE registration_number = '$reg_number' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $asset = mysqli_fetch_assoc($result);

        // Insert issued item
        $insertQuery = "INSERT INTO issued_items (user_email, asset_id, serial_number, registration_number, category, model, issued_at, due_date)
                        VALUES ('$user_email', '{$asset['asset_id']}', '{$asset['serial_number']}', '$reg_number', '{$asset['category']}', '{$asset['model']}', NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY))";
        if (mysqli_query($conn, $insertQuery)) {
            // Return JSON response
            echo json_encode([
                "success" => true,
                "counter" => mysqli_insert_id($conn),
                "asset_id" => $asset['asset_id'],
                "serial_number" => $asset['serial_number'],
                "registration_number" => $asset['registration_number'],
                "category" => $asset['category'],
                "model" => $asset['model'],
                "issued_at" => date('Y-m-d H:i:s'),
                "due_date" => date('Y-m-d', strtotime('+15 days'))
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to issue item."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Item not found."]);
    }
}
?>