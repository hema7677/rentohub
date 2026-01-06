<?php
header("Content-Type: application/json");
include('db.php');

$base_url = "https://qjvq60kp-80.inc1.devtunnels.ms/rentohub/";

// Check if 'id' is provided in GET request
if (!isset($_GET['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID parameter is missing"
    ]);
    exit;
}

$id = intval($_GET['id']); // sanitize input

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
        WHERE id = $id
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // ✅ Full image URL
    if (!empty($row['image'])) {
        $row['image'] = $base_url . $row['image'];
    }

    echo json_encode([
        "status" => "success",
        "data" => $row
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Equipment not found"
    ]);
}

$conn->close();
?>
