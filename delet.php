<?php
header("Content-Type: application/json");
include("db.php");

if (!isset($_POST['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Item ID is required"
    ]);
    exit;
}

$id = intval($_POST['id']);

// Fetch image
$query = $conn->query("SELECT image FROM add_equipment WHERE id = $id LIMIT 1");

if ($query->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Item not found"
    ]);
    exit;
}

$row = $query->fetch_assoc();
$imagePath = $row['image']; // uploads/xxx.jpg

// Delete DB row
$delete = $conn->query("DELETE FROM add_equipment WHERE id = $id");

if ($delete) {

    // ✅ Correct full server path
    $fullPath = __DIR__ . "/" . $imagePath;

    if (!empty($imagePath) && file_exists($fullPath)) {
        unlink($fullPath);
    }

    echo json_encode([
        "status" => "success",
        "message" => "Item deleted successfully",
        "deleted_id" => $id
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete item"
    ]);
}

$conn->close();
?>
