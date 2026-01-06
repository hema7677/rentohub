<?php
header("Content-Type: application/json");
include('db.php');

if (!isset($_POST['id'])) {
    echo json_encode(["status" => "error", "message" => "ID required"]);
    exit;
}

$id = $_POST['id'];

/* FETCH OLD DATA */
$stmt = $conn->prepare("SELECT * FROM add_equipment WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Not Found"]);
    exit;
}

$old = $res->fetch_assoc();

/* READ VALUES (FIXED) */
$name        = isset($_POST['name'])        ? $_POST['name']        : $old['name'];
$brand       = isset($_POST['brand'])       ? $_POST['brand']       : $old['brand'];
$category    = isset($_POST['category'])    ? $_POST['category']    : $old['category'];
$daily_rate  = isset($_POST['daily_rate'])  ? $_POST['daily_rate']  : $old['price_per_day'];
$deposit     = isset($_POST['deposit'])     ? $_POST['deposit']     : $old['deposit'];
$description = isset($_POST['description']) ? $_POST['description'] : $old['description'];

/* IMAGE */
$imagePath = $old['image'];

if (!empty($_FILES['image']['name'])) {

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(["status"=>"error","message"=>"Invalid image"]);
        exit;
    }

    $dir = "uploads/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $file = time()."_".rand(1000,9999).".".$ext;
    $imagePath = $dir.$file;

    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
}

/* UPDATE */
$sql = "UPDATE add_equipment SET
        name=?,
        brand=?,
        category=?,
        price_per_day=?,
        deposit=?,
        description=?,
        image=?
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssi",
    $name,
    $brand,
    $category,
    $daily_rate,
    $deposit,
    $description,
    $imagePath,
    $id
);

if ($stmt->execute()) {
echo json_encode([
    "status" => "success",
    "message" => "Equipment updated successfully",
    "id" => (string)$id
]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
