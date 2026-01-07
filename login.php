<?php
header("Content-Type: application/json");
require "db.php";

if (!isset($_POST['email'], $_POST['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing email or password"
    ]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password']; // normal password (not hashed)

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);

    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "data" => [
            "id" => $row['id'],
            "name" => $row['name'],
            "email" => $row['email'],
            "usertype" => $row['usertype']
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email or password"
    ]);
}
?>