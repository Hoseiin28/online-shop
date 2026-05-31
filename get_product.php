<?php
include 'config.php';
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'Admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM Products WHERE ProductID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($product);
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'محصول یافت نشد']);
}

$stmt->close();
?>