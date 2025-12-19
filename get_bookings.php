<?php
header("Content-Type: application/json");
include "db.php";

if (!isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "user_id is required"]);
    exit;
}

$user_id = $_POST['user_id'];

$sql = "SELECT 
            b.id AS booking_id,
            b.days,
            b.daily_rate,
            b.total_amount,
            b.status,
            b.created_at,
            i.name AS equipment_name,
            i.image AS equipment_image
        FROM bookings b
        JOIN add_equipment i ON b.equipment_id = i.id
        WHERE b.user_id = ?
        ORDER BY b.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($data),
    "bookings" => $data
]);

$stmt->close();
$conn->close();
?>
