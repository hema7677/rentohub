<?php
header("Content-Type: application/json");
include('db.php');

$sql = "SELECT 
            id, 
            name, 
            brand, 
            category, 
            price_per_day, 
            deposit, 
            description, 
            image,
            status
        FROM add_equipment 
        ORDER BY id DESC";

$result = $conn->query($sql);

$equipment = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        // Image will be returned as stored in DB (no base URL)
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
        "message" => "No equipment found",
        "count" => 0,
        "data" => []
    ]);
}

$conn->close();
?>
