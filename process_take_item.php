<?php
session_start();
include('./config/config.php');

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_number'])) {
    if (!isset($_SESSION['user'])) {
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit;
    }

    $user_email = $_SESSION['user'];
    $reg_number = $_POST['registration_number'];

    // Check if this user already has this item
    $checkQuery = "SELECT id FROM issued_items WHERE user_email = ? AND registration_number = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $user_email, $reg_number);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "You have already taken this item."]);
        exit;
    }

    // Fetch asset details securely
    $query = "SELECT * FROM assets WHERE registration_number = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $reg_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $asset = $result->fetch_assoc();

        // Insert issued item
        $insertQuery = "INSERT INTO issued_items (user_email, asset_id, serial_number, registration_number, category, sub_category, type, model, issued_at, due_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY))";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param(
            "ssssssss",
            $user_email,
            $asset['asset_id'],
            $asset['serial_number'],
            $reg_number,
            $asset['category'],
            $asset['sub_category'],
            $asset['type'],
            $asset['model']
        );

        try {
            if ($insertStmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "counter" => $conn->insert_id,
                    "asset_id" => $asset['asset_id'],
                    "serial_number" => $asset['serial_number'],
                    "registration_number" => $asset['registration_number'],
                    "category" => $asset['category'],
                    "sub_category" => $asset['sub_category'],
                    "type" => $asset['type'],
                    "model" => $asset['model'],
                    "issued_at" => date('Y-m-d H:i:s'),
                    "due_date" => date('Y-m-d', strtotime('+15 days'))
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to issue item."]);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error
                echo json_encode(["success" => false, "message" => "You have already taken this item."]);
            } else {
                echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Item not found."]);
    }
}
?>