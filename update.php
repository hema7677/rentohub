<?php
header("Content-Type: application/json");

include('db.php');

// Check required ID
if (!isset($_POST['id'])) {
    echo json_encode(["status" => "error", "message" => "Equipment ID is required"]);
    exit;
}

$id = $_POST['id'];

// ---- FETCH OLD DATA ---- //
$queryOld = $conn->query("SELECT * FROM add_equipment WHERE id = $id");

if ($queryOld->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Equipment Not Found"]);
    exit;
}

$old = $queryOld->fetch_assoc();

// ---- READ NEW OR EXISTING VALUES ---- //
$name        = !empty($_POST['name'])        ? $_POST['name']        : $old['name'];
$brand       = !empty($_POST['brand'])       ? $_POST['brand']       : $old['brand'];
$category    = !empty($_POST['category'])    ? $_POST['category']    : $old['category'];
$daily_rate  = !empty($_POST['daily_rate'])  ? $_POST['daily_rate']  : $old['price_per_day'];
$deposit     = !empty($_POST['deposit'])     ? $_POST['deposit']     : $old['deposit'];
$description = !empty($_POST['description']) ? $_POST['description'] : $old['description'];

// ---- IMAGE HANDLING ---- //
$imagePath = $old['image'];  // default old image

if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {

    $allowed = ["jpg", "jpeg", "png"];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed)) {
        echo json_encode(["status" => "error", "message" => "Only JPG, JPEG, PNG allowed"]);
        exit;
    }

    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        echo json_encode(["status" => "error", "message" => "Image must be less than 2MB"]);
        exit;
    }

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir);

    $imageName = time() . "_" . rand(1000, 9999) . "." . $file_ext;
    $imagePath = $uploadDir . $imageName;

    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
}

// ---- UPDATE DATABASE ---- //
$sql = "UPDATE add_equipment SET
        name = ?, 
        brand = ?, 
        category = ?, 
        price_per_day = ?, 
        deposit = ?, 
        description = ?, 
        image = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssiddsi", $name, $brand, $category, $daily_rate, $deposit, $description, $imagePath, $id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Equipment updated successfully",
        "id" => $id,
        "data" => [
            "name" => $name,
            "brand" => $brand,
            "category" => $category,
            "daily_rate" => $daily_rate,
            "deposit" => $deposit,
            "description" => $description,
            "image" => $imagePath
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update equipment"]);
}

$stmt->close();
$conn->close();
?>
