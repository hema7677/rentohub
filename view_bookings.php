<?php
header("Content-Type: application/json");
require "db.php";

if (!isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User ID missing"]);
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

// Updated table name to add_equipment
$sql = "SELECT b.*, e.name as equipment_name, e.image as equipment_image 
        FROM bookings b 
        JOIN add_equipment e ON b.equipment_id = e.id 
        WHERE b.user_id = '$user_id' 
        ORDER BY b.id DESC";

$result = mysqli_query($conn, $sql);
$bookings = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = [
            "booking_id" => $row['id'],
            "equipment_name" => $row['equipment_name'],
            "booking_date" => $row['booking_date'],
            "total_amount" => $row['total_amount'],
            "status" => $row['status'],
            "image" => $row['equipment_image'],
            "location" => $row['location']
        ];
    }
    echo json_encode(["status" => "success", "data" => $bookings]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>