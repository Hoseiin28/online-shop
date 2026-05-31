<?php
include 'config.php';
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login-register.php");
    exit();
}

$orderID = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$orderQuery = "SELECT * FROM Orders WHERE OrderID = $orderID AND UserID = " . $_SESSION['UserID'];
$orderResult = $conn->query($orderQuery);

if ($orderResult->num_rows == 0) {
    die("سفارش مورد نظر یافت نشد!");
}

$order = $orderResult->fetch_assoc();

$detailsQuery = "SELECT Products.ProductName, OrderDetails.Quantity, OrderDetails.Price 
                 FROM OrderDetails 
                 JOIN Products ON OrderDetails.ProductID = Products.ProductID 
                 WHERE OrderDetails.OrderID = $orderID";
$detailsResult = $conn->query($detailsQuery);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرداخت موفق | فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="style-success.css">
</head>
<body>
    <div class="container">
        <div class="success-icon">
        </div>
        
        <h1>پرداخت با موفقیت انجام شد!</h1>
        
        <div class="order-info">
            <p>شماره سفارش: <?php echo $order['OrderID']; ?></p>
            <p>تاریخ سفارش: <?php echo date('Y/m/d H:i', strtotime($order['CreatedAt'])); ?></p>
            <p>وضعیت سفارش: تکمیل شده</p>
        </div>
        
        <h2>جزئیات سفارش</h2>
        
        <table class="order-details">
            <thead>
                <tr>
                    <th>نام محصول</th>
                    <th>تعداد</th>
                    <th>قیمت واحد</th>
                    <th>جمع جزء</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalPrice = 0;
                while ($detail = $detailsResult->fetch_assoc()): 
                    $subtotal = $detail['Price'] * $detail['Quantity'];
                    $totalPrice += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($detail['ProductName']); ?></td>
                    <td><?php echo $detail['Quantity']; ?></td>
                    <td><?php echo number_format($detail['Price'], 0); ?> تومان</td>
                    <td><?php echo number_format($subtotal, 0); ?> تومان</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="total-price">
            مبلغ قابل پرداخت: <?php echo number_format($totalPrice, 0); ?> تومان
        </div>
        
        <a href="products.php" class="back-to-shop">
            بازگشت به فروشگاه
        </a>
    </div>
</body>
</html>