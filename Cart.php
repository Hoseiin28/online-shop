<?php
include 'config.php';
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login-register.php?redirect=cart.php");
    exit();
}

$userID = $_SESSION['UserID'];

$query = "SELECT Cart.CartID, Products.ProductID, Products.ProductName, Products.Price, Cart.Quantity, Products.ImageURL, Products.Stock
          FROM Cart 
          JOIN Products ON Cart.ProductID = Products.ProductID 
          WHERE Cart.UserID = $userID";
$result = $conn->query($query);

if (isset($_POST['delete'])) {
    $cartID = intval($_POST['CartID']);
    $deleteQuery = "DELETE FROM Cart WHERE CartID = $cartID AND UserID = $userID";
    $conn->query($deleteQuery);
    $_SESSION['cart_message'] = "محصول با موفقیت از سبد خرید حذف شد";
    header("Location: cart.php");
    exit();
}

if (isset($_POST['update'])) {
    $cartID = intval($_POST['CartID']);
    $quantity = intval($_POST['Quantity']);
    $productID = intval($_POST['ProductID']);
    
    $stockCheck = $conn->query("SELECT Stock FROM Products WHERE ProductID = $productID");
    $stock = $stockCheck->fetch_assoc()['Stock'];
    
    if ($quantity <= $stock && $quantity > 0) {
        $updateQuery = "UPDATE Cart SET Quantity = $quantity WHERE CartID = $cartID AND UserID = $userID";
        $conn->query($updateQuery);
        $_SESSION['cart_message'] = "سبد خرید با موفقیت به‌روزرسانی شد";
    } else {
        $_SESSION['cart_error'] = "تعداد وارد شده بیشتر از موجودی است";
    }
    
    header("Location: cart.php");
    exit();
}

$successMessage = '';
$errorMessage = '';
if (isset($_SESSION['cart_message'])) {
    $successMessage = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']);
}
if (isset($_SESSION['cart_error'])) {
    $errorMessage = $_SESSION['cart_error'];
    unset($_SESSION['cart_error']);
}

$totalPrice = 0;
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['Price'] * $row['Quantity'];
    $totalPrice += $subtotal;
    $cartItems[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبد خرید | فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="style-cart.css"> 
</head>
<body>
    <div class="container">
        <h1>سبد خرید شما</h1>
        
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-error">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($cartItems) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>تصویر</th>
                        <th>نام محصول</th>
                        <th>قیمت واحد</th>
                        <th>تعداد</th>
                        <th>جمع جزء</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php $subtotal = $item['Price'] * $item['Quantity']; ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['ImageURL']); ?>" alt="<?php echo htmlspecialchars($item['ProductName']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                            <td><?php echo number_format($item['Price'], 0); ?> تومان</td>
                            <td>
                                <form method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="CartID" value="<?php echo $item['CartID']; ?>">
                                    <input type="hidden" name="ProductID" value="<?php echo $item['ProductID']; ?>">
                                    <input type="number" name="Quantity" value="<?php echo $item['Quantity']; ?>" min="1" max="<?php echo $item['Stock']; ?>" class="quantity-input">
                                    <button type="submit" name="update" class="btn btn-primary">
                                       به‌روزرسانی
                                    </button>
                                </form>
                            </td>
                            <td><?php echo number_format($subtotal, 0); ?> تومان</td>
                            <td class="action-buttons">
                                <form method="POST">
                                    <input type="hidden" name="CartID" value="<?php echo $item['CartID']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-price">
                جمع کل: <?php echo number_format($totalPrice, 0); ?> تومان
            </div>
            
            <a href="checkout.php" class="checkout-btn">
                پرداخت و تکمیل سفارش 
            </a>
        <?php else: ?>
            <div class="empty-cart">
                
                <p>سبد خرید شما خالی است</p>
                <a href="products.php">مشاهده محصولات</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>