<?php
header("Content-Type: application/json");
require "db.php";

// We expect name and email from the Google Sign-In process
if (!isset($_POST['email'], $_POST['name'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing name or email from Google"
    ]);
    exit;
}

$email = mysqli_real_escape_string($conn, $_POST['email']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$defaultPassword = "123"; // Your requested default password
$usertype = "user";

// 1. Check if the user already exists in the database
$checkSql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$checkResult = mysqli_query($conn, $checkSql);

if (mysqli_num_rows($checkResult) > 0) {
    // User exists, fetch their details
    $row = mysqli_fetch_assoc($checkResult);
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
    // 2. User doesn't exist, create a new record with default password '123'
    $insertSql = "INSERT INTO users (name, email, password, usertype) 
                  VALUES ('$name', '$email', '$defaultPassword', '$usertype')";

    if (mysqli_query($conn, $insertSql)) {
        $newUserId = mysqli_insert_id($conn);
        echo json_encode([
            "status" => "success",
            "message" => "Account created and login successful",
            "data" => [
                "id" => (string) $newUserId,
                "name" => $name,
                "email" => $email,
                "usertype" => $usertype
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to create user record: " . mysqli_error($conn)
        ]);
    }
}
?>