<?php
header("Content-Type: application/json");
include "db.php";

// ✅ Validate required fields
if (!isset($_POST['user_id'], $_POST['equipment_id'], $_POST['days'], $_POST['location'])) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id, equipment_id, days, and location are required"
    ]);
    exit;
}

$user_id = $_POST['user_id'];
$equipment_id = $_POST['equipment_id'];
$days = $_POST['days'];
$location = trim($_POST['location']);
$status = "Completed"; // ✅ Set default status after successful payment

// ✅ Fetch equipment details
$fetch = $conn->query("SELECT name, price_per_day, image FROM add_equipment WHERE id='$equipment_id' LIMIT 1");
if ($fetch->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Equipment not found"]);
    exit;
}

$item = $fetch->fetch_assoc();
$daily_rate = $item['price_per_day'];
$image = $item['image'];

$total_amount = $daily_rate * $days;

// ✅ Insert booking with status "Completed"
$sql = "INSERT INTO bookings 
        (user_id, equipment_id, location, days, daily_rate, total_amount, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iisiddss", // 's' added for the status string
    $user_id,
    $equipment_id,
    $location,
    $days,
    $daily_rate,
    $total_amount,
    $image,
    $status
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Booking successful",
        "booking_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to place booking"]);
}

$stmt->close();
$conn->close();
?>