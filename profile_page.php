<?php
header("Content-Type: application/json");
require "db.php";

// Check required input (email OR id)
if (!isset($_POST['email']) && !isset($_POST['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Email or ID is required"
    ]);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$id    = isset($_POST['id']) ? trim($_POST['id']) : null;

// Prepare query
if ($email) {
    $stmt = $conn->prepare("SELECT id, name, usertype, password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
} else {
    $stmt = $conn->prepare("SELECT id, name, usertype, password FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// User not found
if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
    exit;
}

// Fetch data
$user = $result->fetch_assoc();

// Response
echo json_encode([
    "status" => "success",
    "message" => "Profile loaded successfully",
    "data" => $user
]);

?>
