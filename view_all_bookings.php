<?php
header("Content-Type: application/json");
include "db.php";

$sql = "SELECT 
            id AS booking_id,
            user_id,
            equipment_id,
            days,
            daily_rate,
            total_amount,
            status,
            created_at,
            image
        FROM bookings
        ORDER BY id DESC";

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "count" => count($data),
        "bookings" => $data
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No bookings found"
    ]);
}

$conn->close();
?>
