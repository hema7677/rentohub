<?php
header("Content-Type: application/json");
include("db.php");

// Check if ID is sent
if (!isset($_POST['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Item ID is required"
    ]);
    exit;
}

$id = $_POST['id'];

// ---- FETCH ITEM TO DELETE ---- //
$query = $conn->query("SELECT image FROM add_equipment WHERE id = $id LIMIT 1");

if ($query->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Item not found"
    ]);
    exit;
}

$row = $query->fetch_assoc();
$imagePath = $row['image'];

// ---- DELETE FROM DATABASE ---- //
$delete = $conn->query("DELETE FROM add_equipment WHERE id = $id");

if ($delete) {

    // Remove file if exists
    if (!empty($imagePath) && file_exists($imagePath)) {
        unlink($imagePath);
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
