<?php
header("Content-Type: application/json");
include "db.php";

// Validate required fields
if (!isset($_POST['user_id'], $_POST['equipment_id'], $_POST['days'])) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id, equipment_id, and days are required"
    ]);
    exit;
}

$user_id      = $_POST['user_id'];
$equipment_id = $_POST['equipment_id'];
$days         = $_POST['days'];

// Validate days
if (!is_numeric($days) || $days <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Days must be a positive number"
    ]);
    exit;
}

// Fetch equipment details
$fetch = $conn->query("SELECT name, price_per_day, image FROM add_equipment WHERE id='$equipment_id' LIMIT 1");

if ($fetch->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Equipment not found"
    ]);
    exit;
}

$item = $fetch->fetch_assoc();
$daily_rate   = $item['price_per_day'];
$image        = $item['image'];
$equipment    = $item['name'];

// Calculate total amount
$total_amount = $daily_rate * $days;

// Insert booking record
$sql = "INSERT INTO bookings (user_id, equipment_id, days, daily_rate, total_amount, image)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiidds", $user_id, $equipment_id, $days, $daily_rate, $total_amount, $image);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Booking successful",
        "booking_id" => $stmt->insert_id,
        "data" => [
            "equipment_id" => $equipment_id,
            "equipment_name" => $equipment,
            "image" => $image,
            "daily_rate" => $daily_rate,
            "days" => $days,
            "total_amount" => $total_amount
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to place booking"
    ]);
}

$stmt->close();
$conn->close();
?>
