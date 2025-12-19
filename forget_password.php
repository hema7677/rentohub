<?php
header("Content-Type: application/json");
require "db.php"; // database connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

if (empty($email) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Email and new password are required"]);
    exit;
}

// Hash the password (recommended)
// $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password using email
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $new_password, $email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password"]);
}

?>
