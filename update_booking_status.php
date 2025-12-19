<?php
header("Content-Type: application/json");
include "db.php";

if (!isset($_POST['booking_id'], $_POST['status'])) {
    echo json_encode(["status" => "error", "message" => "booking_id and status are required"]);
    exit;
}

$booking_id = $_POST['booking_id'];
$status = $_POST['status'];

if (!in_array($status, ['accepted', 'rejected'])) {
    echo json_encode(["status" => "error", "message" => "Invalid status"]);
    exit;
}

$sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Booking status updated",
        "booking_id" => $booking_id,
        "status_updated_to" => $status
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update status"]);
}

$stmt->close();
$conn->close();
?>
