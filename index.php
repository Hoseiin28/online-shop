<?php
include 'config.php';
session_start();

$query = "SELECT ProductID, ProductName, Price, ImageURL, Description FROM Products ORDER BY CreatedAt DESC LIMIT 8";
$result = $conn->query($query);

$isAdmin = isset($_SESSION['Role']) && $_SESSION['Role'] === 'Admin';
$cartCount = 0;
if (isset($_SESSION['UserID'])) {
    $cartQuery = "SELECT SUM(Quantity) as total FROM Cart WHERE UserID = ?";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $_SESSION['UserID']);
    $stmt->execute();
    $cartResult = $stmt->get_result();
    if ($cartResult->num_rows > 0) {
        $cartData = $cartResult->fetch_assoc();
        $cartCount = $cartData['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="style-index.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    فروشگاه اینترنتی
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                </button>
                
                <ul class="nav-links" id="navLinks">
                    <li><a href="index.php">صفحه اصلی</a></li>
                    <li><a href="products.php"> محصولات</a></li>
                    <li><a href="cart.php"> سبد خرید
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <?php if ($isAdmin): ?>
                        <li><a href="admin-panel.php"> پنل مدیریت</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['UserID'])): ?>
                        <li><a href="logout.php"> خروج</a></li>
                    <?php else: ?>
                        <li><a href="login-register.php">ورود</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>به فروشگاه اینترنتی ما خوش آمدید</h1>
            <p>محصولات شگفت انگیز را با قیمت های استثنایی کشف کنید. همین حالا خرید کنید و از تحویل سریع و خدمات عالی بهره مند شوید.</p>
            <div>
                <a href="products.php" class="btn btn-primary">همین حالا خرید کنید</a>
                <?php if (!isset($_SESSION['UserID'])): ?>
                    <a href="login-register.php" class="btn btn-secondary">عضو شوید</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="section-title">
            <h2>محصولات ویژه</h2>
        </div>
        
        <div class="products-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-badge">جدید</div>
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($row['ImageURL']); ?>" alt="<?php echo htmlspecialchars($row['ProductName']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['ProductName']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($row['Description'], 0, 100)); ?>...</p>
                        <div class="product-price"><?php echo number_format($row['Price'], 0); ?> تومان</div>
                        <form action="add-to-cart.php" method="POST" class="product-actions">
                            <input type="hidden" name="ProductID" value="<?php echo $row['ProductID']; ?>">
                            <input type="number" name="Quantity" value="1" min="1" class="quantity-input">
                            <button type="submit" class="add-to-cart-btn">
                                افزودن
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div style="text-align: center; margin-bottom: 4rem;">
            <a href="products.php" class="btn btn-primary">مشاهده همه محصولات</a>
        </div>
    </section>

    <section class="container">
        <div class="section-title">
            <h2>چرا ما را انتخاب کنید؟</h2>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <h3 class="feature-title">تحویل سریع</h3>
                <p class="feature-text">محصولات شما در عرض 2-3 روز کاری به درب منزل تحویل داده می‌شود.</p>
            </div>
            
            <div class="feature-card">
                <h3 class="feature-title">پرداخت امن</h3>
                <p class="feature-text">روش‌های پرداخت 100% امن با رمزگذاری SSL برای ایمنی شما.</p>
            </div>
            
            <div class="feature-card">
                <h3 class="feature-title">پشتیبانی 24/7</h3>
                <p class="feature-text">تیم پشتیبانی ما به صورت شبانه روزی آماده خدمت رسانی به شماست.</p>
            </div>
            
            <div class="feature-card">
                <h3 class="feature-title">مرجوعی آسان</h3>
                <p class="feature-text">راضی نبودید؟ محصولات را تا 30 روز پس از خرید می‌توانید مرجوع کنید.</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>فروشگاه اینترنتی</h3>
                    <p>فروشگاه یک‌پارچه برای تمام نیازهای شما. ما محصولات باکیفیت را با قیمت مناسب و خدمات عالی ارائه می‌دهیم.</p>
                </div>
                
                <div class="footer-column">
                    <h3>لینک‌های سریع</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">صفحه اصلی</a></li>
                        <li><a href="products.php">محصولات</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>اطلاعات تماس</h3>
                    <ul class="footer-links">
                        <li>تهران، خیابان نمونه، پلاک 123</li>
                        <li> 021-12345678</li>
                        <li> info@example.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> فروشگاه اینترنتی. تمام حقوق محفوظ است.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });
    </script>
</body>
</html>