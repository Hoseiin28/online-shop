<?php
include 'config.php';
session_start();

$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : null;
$isAdmin = isset($_SESSION['Role']) && $_SESSION['Role'] === 'Admin';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!$userID) {
        header("Location: login-register.php?redirect=products.php");
        exit();
    }

    $productID = intval($_POST['ProductID']);
    $quantity = intval($_POST['Quantity']);

    if ($productID <= 0 || $quantity <= 0) {
        $error = "محصول یا تعداد نامعتبر!";
    } else {
        $stockCheck = $conn->prepare("SELECT Stock FROM Products WHERE ProductID = ?");
        $stockCheck->bind_param("i", $productID);
        $stockCheck->execute();
        $stockResult = $stockCheck->get_result();

        if ($stockResult->num_rows > 0) {
            $product = $stockResult->fetch_assoc();
            if ($product['Stock'] >= $quantity) {
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

                $_SESSION['success'] = "محصول با موفقیت به سبد خرید اضافه شد!";
            } else {
                $error = "موجودی کافی نیست!";
            }
        } else {
            $error = "محصول یافت نشد!";
        }
    }
}

$search = isset($_GET['search']) && $_GET['search'] !== '' ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : 1000000000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$productsPerPage = 12;
$offset = ($currentPage - 1) * $productsPerPage;

$whereClause = " WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $whereClause .= " AND ProductName LIKE ?";
    $types .= "s";
    $params[] = "%$search%";
}

if ($minPrice > 0) {
    $whereClause .= " AND Price >= ?";
    $types .= "d";
    $params[] = $minPrice;
}

if ($maxPrice > 0 && $maxPrice >= $minPrice) {
    $whereClause .= " AND Price <= ?";
    $types .= "d";
    $params[] = $maxPrice;
}

$countQuery = "SELECT COUNT(*) FROM Products" . $whereClause;
$stmtCount = $conn->prepare($countQuery);
if ($types !== '') {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$stmtCount->bind_result($totalProducts);
$stmtCount->fetch();
$stmtCount->close();

$query = "SELECT ProductID, ProductName, Description, Price, ImageURL, Stock, CreatedAt FROM Products" . $whereClause;

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY Price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY Price DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY CreatedAt ASC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY CreatedAt DESC";
}

$query .= " LIMIT ? OFFSET ?";

$typesWithLimit = $types . "ii";
$paramsWithLimit = array_merge($params, [$productsPerPage, $offset]);

$stmt = $conn->prepare($query);
$stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
$stmt->execute();
$result = $stmt->get_result();

$totalPages = ceil($totalProducts / $productsPerPage);

$cartCount = 0;
if ($userID) {
    $cartQuery = "SELECT SUM(Quantity) as total FROM Cart WHERE UserID = ?";
    $stmtCart = $conn->prepare($cartQuery);
    $stmtCart->bind_param("i", $userID);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();
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
    <title>محصولات | فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="style-products.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    فروشگاه اینترنتی
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                </button>
                
                <ul class="nav-links" id="navLinks">
                    <li><a href="index.php"> صفحه اصلی</a></li>
                    <li><a href="products.php"> محصولات</a></li>
                    <li><a href="cart.php"> سبد خرید
                    <?php if ($cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                    </a></li>
                    <?php if ($isAdmin): ?>
                        <li><a href="admin-panel.php"> پنل مدیریت</a></li>
                    <?php endif; ?>
                    <?php if ($userID): ?>
                        <li><a href="logout.php"> خروج</a></li>
                    <?php else: ?>
                        <li><a href="login-register.php">ورود</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1 class="page-title">محصولات ما</h1>
            <div class="total-products"><?php echo $totalProducts; ?> محصول یافت شد</div>
        </div>

        <section class="filter-section">
            <div class="filter-header">
                <h2 class="filter-title">فیلتر محصولات</h2>
                <a href="products.php" class="btn btn-outline">بازنشانی فیلترها</a>
            </div>
            
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="search">جستجو</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="نام محصول..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="form-group">
                    <label for="min_price">حداقل قیمت</label>
                    <input type="number" id="min_price" name="min_price" class="form-control" 
                           min="0" step="1000" value="<?php echo $minPrice; ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_price">حداکثر قیمت</label>
                    <input type="number" id="max_price" name="max_price" class="form-control" 
                           min="0" step="1000" value="<?php echo $maxPrice; ?>">
                </div>
                
                <div class="form-group">
                    <label for="sort">مرتب سازی بر اساس</label>
                    <select id="sort" name="sort" class="form-control">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>جدیدترین</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>قدیمی ترین</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>ارزان ترین</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>گران ترین</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">اعمال فیلترها</button>
                </div>
            </form>
        </section>

        <div class="products-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <?php if (time() - strtotime($row['CreatedAt']) < 60*60*24*7): ?>
                        <div class="product-badge">جدید</div>
                    <?php endif; ?>
                    
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($row['ImageURL']); ?>" alt="<?php echo htmlspecialchars($row['ProductName']); ?>">
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['ProductName']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($row['Description'], 0, 100)); ?>...</p>
                        
                        <div class="product-price"><?php echo number_format($row['Price'], 0); ?> تومان</div>
                        
                        <div class="product-stock <?php 
                            echo $row['Stock'] > 10 ? 'in-stock' : 
                                 ($row['Stock'] > 0 ? 'low-stock' : 'out-of-stock'); 
                        ?>">
                            <?php 
                            echo $row['Stock'] > 10 ? 'موجود در انبار' : 
                                 ($row['Stock'] > 0 ? 'موجودی محدود ('.$row['Stock'].' عدد باقی مانده)' : 'ناموجود'); 
                            ?>
                        </div>
                        
                        <form method="POST" class="product-actions">
                            <input type="hidden" name="ProductID" value="<?php echo $row['ProductID']; ?>">
                            <input type="number" name="Quantity" value="1" min="1" max="<?php echo $row['Stock']; ?>" 
                                   class="quantity-input" <?php echo $row['Stock'] <= 0 ? 'disabled' : ''; ?>>
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn" 
                                <?php echo $row['Stock'] <= 0 ? 'disabled' : ''; ?>>
                               افزودن به سبد
                            </button>
                        </form>
                        
                        <a href="product-details.php?id=<?php echo $row['ProductID']; ?>" class="view-details-btn">
                            مشاهده جزئیات
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                <ul class="pagination-list">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="pagination-item">
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&sort=<?php echo $sort; ?>" 
                               class="pagination-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
    <br>
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