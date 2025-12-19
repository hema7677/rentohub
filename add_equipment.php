<?php
header("Content-Type: application/json");

include('db.php');

// ---- VALIDATION ---- //
$errors = [];

if (empty($_POST['name']))          $errors[] = "Name is required";
if (empty($_POST['brand']))         $errors[] = "Brand is required";
if (empty($_POST['category']))      $errors[] = "Category is required";
if (empty($_POST['daily_rate']))    $errors[] = "Daily rate is required";
if (empty($_POST['deposit']))       $errors[] = "Deposit amount is required";
if (empty($_POST['description']))   $errors[] = "Description is required";

// Numeric validations
if (!empty($_POST['daily_rate']) && !is_numeric($_POST['daily_rate']))
    $errors[] = "Daily rate must be numeric";

if (!empty($_POST['deposit']) && !is_numeric($_POST['deposit']))
    $errors[] = "Deposit must be numeric";

// Image validation
if (!isset($_FILES['image'])) {
    $errors[] = "Image is required";
} else {
    $allowed = ["jpg", "jpeg", "png"];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed))
        $errors[] = "Only JPG, JPEG, PNG images allowed";

    if ($_FILES['image']['size'] > 2 * 1024 * 1024)
        $errors[] = "Image size must be less than 2MB";
}

// Show validation errors
if (!empty($errors)) {
    echo json_encode(["status" => "error", "errors" => $errors]);
    exit;
}

// ---- IMAGE UPLOAD ---- //
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir);

$imageName = time() . "_" . rand(1000, 9999) . "." . $file_ext;
$imagePath = $uploadDir . $imageName;

move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

// ---- INSERT INTO DATABASE ---- //
$name        = $_POST['name'];
$brand       = $_POST['brand'];
$category    = $_POST['category'];  // TEXT VALUE
$daily_rate  = $_POST['daily_rate'];
$deposit     = $_POST['deposit'];
$description = $_POST['description'];

$sql = "INSERT INTO add_equipment (name, brand, category, price_per_day, deposit, description, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssidss", $name, $brand, $category, $daily_rate, $deposit, $description, $imagePath);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Equipment added successfully",
        "equipment_id" => $stmt->insert_id,
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
    echo json_encode(["status" => "error", "message" => "Failed to add equipment"]);
}

$stmt->close();
$conn->close();
?>
