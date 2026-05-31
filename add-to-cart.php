<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = 1;
    $productID = $_POST['ProductID'];
    $quantity = $_POST['Quantity'];

    $checkQuery = "SELECT * FROM Cart WHERE UserID = $userID AND ProductID = $productID";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $updateQuery = "UPDATE Cart SET Quantity = Quantity + $quantity WHERE UserID = $userID AND ProductID = $productID";
        $conn->query($updateQuery);
    } else {
        $insertQuery = "INSERT INTO Cart (UserID, ProductID, Quantity) VALUES ($userID, $productID, $quantity)";
        $conn->query($insertQuery);
    }
    header("Location: product-details.php?id=$productID");
}
?>