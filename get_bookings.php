<?php
header("Content-Type: application/json");
require "db.php";

if (!isset($_POST['booking_id'])) {
    echo json_encode(["status" => "error", "message" => "Booking ID missing"]);
    exit;
}

$booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);

/**
 * SQL logic:
 * - Join bookings with add_equipment for product details
 * - Join bookings with users for customer details (optional)
 */
$sql = "SELECT b.*, e.name as equipment_name, e.price_per_day as daily_rate, u.name as user_name, u.email as user_email
        FROM bookings b 
        JOIN add_equipment e ON b.equipment_id = e.id 
        LEFT JOIN users u ON b.user_id = u.id 
        WHERE b.id = '$booking_id' 
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        "status" => "success",
        "data" => [
            "booking_id" => (int) $row['id'],
            "equipment_name" => $row['equipment_name'],
            "user_name" => $row['user_name'],
            "user_email" => $row['user_email'],
            "location" => $row['location'],
            "days" => (int) $row['days'],
            "daily_rate" => (double) $row['daily_rate'],
            "total_amount" => (double) $row['total_amount'],
            "booking_date" => $row['booking_date'],
            "status" => $row['status'],
            "image" => $row['image']
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Booking not found"]);
}
?>