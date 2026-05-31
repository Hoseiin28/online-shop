<link rel="stylesheet" href="style-product-details.css">
<?php
include 'config.php';
session_start();

$successMessage = '';
if (isset($_SESSION['cart_success'])) {
    $successMessage = $_SESSION['cart_success'];
    unset($_SESSION['cart_success']);
}

$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM Products WHERE ProductID = $productID";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("محصول مورد نظر یافت نشد!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['UserID'])) {
        header("Location: login-register.php?redirect=product-details.php?id=" . $productID);
        exit();
    }

    $quantity = intval($_POST['Quantity']);
    
    if ($quantity > 0 && $quantity <= $product['Stock']) {
        $userID = $_SESSION['UserID'];
        
        $checkQuery = $conn->prepare("SELECT * FROM Cart WHERE UserID = ? AND ProductID = ?");
        $checkQuery->bind_param("ii", $userID, $productID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            $updateQuery = $conn->prepare("UPDATE Cart SET Quantity = Quantity + ? WHERE UserID = ? AND ProductID = ?");
            $updateQuery->bind_param("iii", $quantity, $userID, $productID);
            $updateQuery->execute();
        } else {
            $insertQuery = $conn->prepare("INSERT INTO Cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
            $insertQuery->bind_param("iii", $userID, $productID, $quantity);
            $insertQuery->execute();
        }
        
        $updateStock = $conn->prepare("UPDATE Products SET Stock = Stock - ? WHERE ProductID = ?");
        $updateStock->bind_param("ii", $quantity, $productID);
        $updateStock->execute();
        
        $_SESSION['cart_success'] = "محصول با موفقیت به سبد خرید اضافه شد!";
        header("Location: product-details.php?id=" . $productID);
        exit();
    } else {
        $error = "تعداد نامعتبر یا موجودی ناکافی!";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جزئیات محصول | فروشگاه اینترنتی</title>
</head>
<body>
    <div class="container">
        <a href="products.php" class="back-link">
          <--- بازگشت به محصولات
        </a>
        
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success" id="successAlert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="product-details">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['ImageURL']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['ProductName']); ?></h1>
                
                <div class="product-meta">
                    <div class="price"><?php echo number_format($product['Price'], 0); ?> تومان</div>
                    
                    <span class="stock-status <?php 
                        echo $product['Stock'] > 10 ? 'in-stock' : 
                             ($product['Stock'] > 0 ? 'low-stock' : 'out-of-stock'); 
                    ?>">
                        <?php 
                        echo $product['Stock'] > 10 ? 'موجود در انبار' : 
                             ($product['Stock'] > 0 ? 'موجودی محدود (' . $product['Stock'] . ' عدد باقی مانده)' : 'ناموجود'); 
                        ?>
                    </span>
                </div>
                
                <p class="description"><?php echo nl2br(htmlspecialchars($product['Description'])); ?></p>
                
                <form method="POST" class="add-to-cart-form">
                    <input type="hidden" name="ProductID" value="<?php echo $product['ProductID']; ?>">
                    
                    <div class="form-group">
                        <label for="quantity">تعداد:</label>
                        <input type="number" id="quantity" name="Quantity" value="1" min="1" 
                               max="<?php echo $product['Stock']; ?>" 
                               <?php echo $product['Stock'] <= 0 ? 'disabled' : ''; ?>>
                    </div>
                    
                    <button type="submit" name="add_to_cart" <?php echo $product['Stock'] <= 0 ? 'disabled' : ''; ?>>
                        افزودن به سبد خرید
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            var alert = document.getElementById('successAlert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>