<?php
header("Content-Type: application/json");
require "db.php";

// Check required fields
if (!isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['usertype'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

$name     = trim($_POST['name']);
$email    = trim($_POST['email']);
$password = trim($_POST['password']);
$usertype = trim($_POST['usertype']);

// -----------------------------
// EMAIL VALIDATION
// -----------------------------
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email format"
    ]);
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email already registered"
    ]);
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO users (name, email, password, usertype) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $usertype);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully",
        "data" => [
            "name" => $name,
            "email" => $email,
            "usertype" => $usertype
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed"
    ]);
}

?>
