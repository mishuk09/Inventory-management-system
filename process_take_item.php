<?php
session_start();
include('./config/config.php'); // Ensure correct path

// Enable JSON response
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "assets_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

$user_email = $_SESSION['user'];
$registration_number = $_POST['registration_number'] ?? '';

if (empty($registration_number)) {
    echo json_encode(["success" => false, "message" => "Registration number is required."]);
    exit;
}

// Check if the same user already issued the same asset
$checkQuery = "SELECT * FROM issued_items WHERE user_email = ? AND registration_number = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ss", $user_email, $registration_number);
$stmt->execute();
$checkResult = $stmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "You have already issued this asset."]);
    exit;
}

// Prepared statement to check asset existence
$query = "SELECT * FROM assets WHERE registration_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $registration_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $asset = $result->fetch_assoc();

    // Calculate due date (15 days from today)
    $due_date = date('Y-m-d', strtotime('+15 days'));

    // Insert issued item (allowing duplicate but checking per user)
    $insertQuery = "INSERT INTO issued_items (user_email, asset_id, serial_number, registration_number, category, sub_category, type, model, artificial, no_engine, due_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param(
        "sisssssssss",
        $user_email,
        $asset['asset_id'],
        $asset['serial_number'],
        $asset['registration_number'],
        $asset['category'],
        $asset['sub_category'],
        $asset['type'],
        $asset['model'],
        $asset['artificial'],
        $asset['no_engine'],
        $due_date
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item successfully taken!", "due_date" => $due_date]);
    } else {
        echo json_encode(["success" => false, "message" => "Error issuing item: " . $stmt->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Asset not found!"]);
}

$stmt->close();
$conn->close();
?>