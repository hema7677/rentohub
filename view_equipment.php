<?php
header("Content-Type: application/json");

include('db.php');

$sql = "SELECT id, name, brand, category, price_per_day, deposit, description, image FROM add_equipment ORDER BY id DESC";

$result = $conn->query($sql);

$equipment = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Convert image path to full URL if needed
        $row['image'] = $row['image'];

        $equipment[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "count" => count($equipment),
        "data" => $equipment
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "No equipment found"
    ]);
}

$conn->close();
?>
