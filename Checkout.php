<?php
include 'config.php';
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login-register.php?redirect=checkout.php");
    exit();
}

$userID = $_SESSION['UserID'];

$query = "SELECT Cart.ProductID, Products.ProductName, Products.Price, Cart.Quantity, Products.ImageURL 
          FROM Cart 
          JOIN Products ON Cart.ProductID = Products.ProductID 
          WHERE Cart.UserID = $userID";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $totalPrice = floatval($_POST['TotalPrice']);
    $customerName = $conn->real_escape_string($_POST['CustomerName']);
    $customerAddress = $conn->real_escape_string($_POST['CustomerAddress']);
    $customerPhone = $conn->real_escape_string($_POST['CustomerPhone']);

    $insertOrder = $conn->prepare("INSERT INTO Orders (UserID, TotalPrice, Status) VALUES (?, ?, 'Completed')");
    $insertOrder->bind_param("id", $userID, $totalPrice);
    $insertOrder->execute();
    $orderID = $conn->insert_id;

    $products = json_decode($_POST['products'], true);
    foreach ($products as $product) {
        $productID = intval($product['ProductID']);
        $quantity = intval($product['Quantity']);
        $price = floatval($product['Price']);
        
        $insertDetails = $conn->prepare("INSERT INTO OrderDetails (OrderID, ProductID, Quantity, Price) 
                                        VALUES (?, ?, ?, ?)");
        $insertDetails->bind_param("iiid", $orderID, $productID, $quantity, $price);
        $insertDetails->execute();
        $updateStock = $conn->prepare("UPDATE Products SET Stock = Stock - ? WHERE ProductID = ?");
        $updateStock->bind_param("ii", $quantity, $productID);
        $updateStock->execute();
    }

    $deleteCart = $conn->prepare("DELETE FROM Cart WHERE UserID = ?");
    $deleteCart->bind_param("i", $userID);
    $deleteCart->execute();

    header("Location: success.php?order_id=" . $orderID);
    exit();
}

$totalPrice = 0;
$products = [];
while ($row = $result->fetch_assoc()) {
    $total = $row['Price'] * $row['Quantity'];
    $totalPrice += $total;
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تکمیل سفارش | فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="style-checkout.css">
</head>
<body>
    <div class="container">
        <h1>تکمیل سفارش</h1>
        <h2>خلاصه سفارش</h2>
        
        <table class="checkout-table">
            <thead>
                <tr>
                    <th>تصویر</th>
                    <th>نام محصول</th>
                    <th>قیمت واحد</th>
                    <th>تعداد</th>
                    <th>جمع جزء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php $subtotal = $product['Price'] * $product['Quantity']; ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($product['ImageURL']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                        <td><?php echo number_format($product['Price'], 0); ?> تومان</td>
                        <td><?php echo $product['Quantity']; ?></td>
                        <td><?php echo number_format($subtotal, 0); ?> تومان</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total-price">
            جمع کل سفارش: <?php echo number_format($totalPrice, 0); ?> تومان
        </div>
        
        <div class="form-section">
            <h2>اطلاعات مشتری</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="CustomerName">نام کامل:</label>
                    <input type="text" id="CustomerName" name="CustomerName" required>
                </div>
                
                <div class="form-group">
                    <label for="CustomerAddress">آدرس دقیق:</label>
                    <textarea id="CustomerAddress" name="CustomerAddress" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="CustomerPhone">شماره تماس:</label>
                    <input type="tel" id="CustomerPhone" name="CustomerPhone" required>
                </div>
                
                <input type="hidden" name="TotalPrice" value="<?php echo $totalPrice; ?>">
                <input type="hidden" name="products" value='<?php echo json_encode($products); ?>'>
                
                <button type="submit" class="checkout-btn">
                    پرداخت و ثبت نهایی سفارش <i class="fas fa-check"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>