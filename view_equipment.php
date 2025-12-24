<?php
header("Content-Type: application/json");

include('db.php');

$base_url = "https://qjvq60kp-80.inc1.devtunnels.ms/";

$sql = "SELECT id, name, brand, category, price_per_day, deposit, description, image 
        FROM add_equipment 
        ORDER BY id DESC";

$result = $conn->query($sql);

$equipment = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // ✅ Convert image path to full URL
        if (!empty($row['image'])) {
            $row['image'] = $base_url . $row['image'];
        }

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
