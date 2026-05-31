<?php
session_start();
include 'config.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login-register.php");
    exit();
}

if ($_SESSION['Role'] !== 'Admin') {
    die('دسترسی غیرمجاز - شما مجوز دسترسی به این بخش را ندارید');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function uploadImage($file) {
    if ($file['error'] != 0) return null;
    
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($fileExt, $allowedTypes)) return null;
    
    $fileName = uniqid() . '.' . $fileExt;
    $targetFilePath = $targetDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        return $targetFilePath;
    }
    
    return null;
}

if (isset($_POST['addProduct'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }

    $productName = sanitizeInput($_POST['ProductName']);
    $description = sanitizeInput($_POST['Description']);
    $price = (float)$_POST['Price'];
    $stock = (int)$_POST['Stock'];
    $imageURL = isset($_FILES['ImageFile']) ? uploadImage($_FILES['ImageFile']) : null;

    $stmt = $conn->prepare("INSERT INTO Products (ProductName, Description, Price, Stock, ImageURL) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $productName, $description, $price, $stock, $imageURL);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "محصول با موفقیت افزوده شد";
    header("Location: admin-panel.php");
    exit();
}

if (isset($_POST['editProduct'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }

    $productID = (int)$_POST['ProductID'];
    $productName = sanitizeInput($_POST['ProductName']);
    $description = sanitizeInput($_POST['Description']);
    $description = str_replace(["\\r", "\\n"], ["\r", "\n"], $description);
    $price = (float)$_POST['Price'];
    $stock = (int)$_POST['Stock'];

    if (isset($_FILES['ImageFile']) && $_FILES['ImageFile']['error'] == 0) {
        $imageURL = uploadImage($_FILES['ImageFile']);

        $result = $conn->query("SELECT ImageURL FROM Products WHERE ProductID = $productID");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['ImageURL']) && file_exists($row['ImageURL'])) {
                unlink($row['ImageURL']);
            }
        }
        
        $stmt = $conn->prepare("UPDATE Products SET ProductName=?, Description=?, Price=?, Stock=?, ImageURL=? WHERE ProductID=?");
        $stmt->bind_param("ssdisi", $productName, $description, $price, $stock, $imageURL, $productID);
    } else {
        $stmt = $conn->prepare("UPDATE Products SET ProductName=?, Description=?, Price=?, Stock=? WHERE ProductID=?");
        $stmt->bind_param("ssdii", $productName, $description, $price, $stock, $productID);
    }
    
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "محصول با موفقیت ویرایش شد";
    header("Location: admin-panel.php");
    exit();
}

if (isset($_POST['deleteProduct'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }

    $productID = (int)$_POST['ProductID'];

    $conn->query("DELETE FROM OrderDetails WHERE ProductID = $productID");

    $result = $conn->query("SELECT ImageURL FROM Products WHERE ProductID = $productID");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['ImageURL']) && file_exists($row['ImageURL'])) {
            unlink($row['ImageURL']);
        }
    }

    $conn->query("DELETE FROM Products WHERE ProductID = $productID");

    $_SESSION['message'] = "محصول با موفقیت حذف شد";
    header("Location: admin-panel.php");
    exit();
}

$productsQuery = "SELECT * FROM Products ORDER BY ProductID DESC";
$productsResult = $conn->query($productsQuery);

$ordersQuery = "SELECT o.OrderID, u.FullName, o.TotalPrice, o.Status, o.CreatedAt 
               FROM Orders o
               JOIN Users u ON o.UserID = u.UserID
               ORDER BY o.OrderID DESC";
$ordersResult = $conn->query($ordersQuery);

$usersQuery = "SELECT UserID, FullName, Email, Role, CreatedAt FROM Users ORDER BY UserID DESC";
$usersResult = $conn->query($usersQuery);

if (isset($_POST['deleteUser'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }

    $userID = (int)$_POST['UserID'];

    if ($userID == $_SESSION['UserID']) {
        $_SESSION['error'] = "شما نمی‌توانید حساب خود را حذف کنید";
        header("Location: admin-panel.php");
        exit();
    }

    $conn->query("DELETE FROM Cart WHERE UserID = $userID");
    $conn->query("DELETE od FROM OrderDetails od JOIN Orders o ON od.OrderID = o.OrderID WHERE o.UserID = $userID");
    $conn->query("DELETE FROM Orders WHERE UserID = $userID");

    $conn->query("DELETE FROM Users WHERE UserID = $userID");

    $_SESSION['message'] = "کاربر با موفقیت حذف شد";
    header("Location: admin-panel.php");
    exit();
}

if (isset($_POST['toggleRole'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }

    $userID = (int)$_POST['UserID'];
    $currentRole = sanitizeInput($_POST['CurrentRole']);
    $newRole = ($currentRole === 'Admin') ? 'User' : 'Admin';

    if ($userID == $_SESSION['UserID']) {
        $_SESSION['error'] = "شما نمی‌توانید نقش خود را تغییر دهید";
        header("Location: admin-panel.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE Users SET Role = ? WHERE UserID = ?");
    $stmt->bind_param("si", $newRole, $userID);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "نقش کاربر با موفقیت تغییر کرد";
    header("Location: admin-panel.php");
    exit();
}

if (isset($_POST['OrderID'], $_POST['Status'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('درخواست نامعتبر');
    }
    $orderID = (int)$_POST['OrderID'];
    $status = sanitizeInput($_POST['Status']);

    $stmt = $conn->prepare("UPDATE Orders SET Status = ? WHERE OrderID = ?");
    $stmt->bind_param("si", $status, $orderID);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "وضعیت سفارش با موفقیت به‌روزرسانی شد";
    header("Location: admin-panel.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت</title>
    <link rel="stylesheet" href="style-panel.css">
</head>
<body>
    <div class="container">
        <h1>پنل مدیریت</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
        
        <div class="menu">
            <a href="#" onclick="showSection('manageProducts')" class="active">
               مدیریت محصولات
            </a>
            <a href="#" onclick="showSection('manageOrders')">
               مدیریت سفارشات
            </a>
            <a href="#" onclick="showSection('manageUsers')">
                 مدیریت کاربران
            </a>
            <a href="index.php">
               صفحه اصلی
            </a>
            <a href="products.php">
               محصولات
            </a>
            <a href="logout.php">
                خروج
            </a>
        </div>
        
        <div id="manageProducts" class="content active">
            <div class="form-container">
                <h2>افزودن محصول جدید</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="ProductName">نام محصول:</label>
                        <input type="text" id="ProductName" name="ProductName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="Description">توضیحات:</label>
                        <textarea id="Description" name="Description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="Price">قیمت (تومان):</label>
                        <input type="number" id="Price" name="Price" step="1000" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="Stock">موجودی:</label>
                        <input type="number" id="Stock" name="Stock" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ImageFile">تصویر محصول:</label>
                        <input type="file" id="ImageFile" name="ImageFile" accept="image/*">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="addProduct" class="btn btn-primary">
                        افزودن محصول
                        </button>
                    </div>
                </form>
            </div>
            <br>
            <h2>لیست محصولات</h2>
            <table>
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام محصول</th>
                        <th>قیمت</th>
                        <th>موجودی</th>
                        <th>تصویر</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productsResult->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($product = $productsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($product['ProductName']) ?></td>
                                <td><?= number_format($product['Price']) ?> تومان</td>
                                <td><?= $product['Stock'] ?></td>
                                <td>
                                    <?php if (!empty($product['ImageURL'])): ?>
                                        <img src="<?= htmlspecialchars($product['ImageURL']) ?>" alt="تصویر محصول" class="img-thumbnail">
                                    <?php else: ?>
                                        بدون تصویر
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="openEditModal(<?= $product['ProductID'] ?>)" class="btn btn-primary">
                                            ویرایش
                                        </button>
                                        <form method="POST" onsubmit="return confirm('آیا از حذف این محصول مطمئن هستید؟');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="ProductID" value="<?= $product['ProductID'] ?>">
                                            <button type="submit" name="deleteProduct" class="btn btn-danger">
                                                 حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">هیچ محصولی یافت نشد</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="manageOrders" class="content">
            <h2>مدیریت سفارشات</h2>
            <table>
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام مشتری</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ordersResult->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($order = $ordersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($order['FullName']) ?></td>
                                <td><?= number_format($order['TotalPrice']) ?> تومان</td>
                                <td><?= htmlspecialchars($order['Status']) ?></td>
                                <td><?= $order['CreatedAt'] ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="OrderID" value="<?= $order['OrderID'] ?>">
                                        <select name="Status" onchange="this.form.submit()">
                                            <option value="Pending" <?= $order['Status'] == 'Pending' ? 'selected' : '' ?>>در انتظار</option>
                                            <option value="Processing" <?= $order['Status'] == 'Processing' ? 'selected' : '' ?>>در حال پردازش</option>
                                            <option value="Completed" <?= $order['Status'] == 'Completed' ? 'selected' : '' ?>>تکمیل شده</option>
                                            <option value="Cancelled" <?= $order['Status'] == 'Cancelled' ? 'selected' : '' ?>>لغو شده</option>
                                        </select>
                                        <input type="hidden" name="updateOrderStatus" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">هیچ سفارشی یافت نشد</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div id="manageUsers" class="content">
            <h2>مدیریت کاربران</h2>
            <table>
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام کامل</th>
                        <th>ایمیل</th>
                        <th>نقش</th>
                        <th>تاریخ ثبت‌نام</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($usersResult->num_rows > 0): ?>
                        <?php $counter = 1; ?>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($user['FullName']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td><?= htmlspecialchars($user['Role']) ?></td>
                                <td><?= $user['CreatedAt'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                                            <input type="hidden" name="CurrentRole" value="<?= $user['Role'] ?>">
                                            <button type="submit" name="toggleRole" class="btn btn-primary">
                                                <?= $user['Role'] === 'Admin' ? 'تبدیل به کاربر' : 'تبدیل به ادمین' ?>
                                            </button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                                            <button type="submit" name="deleteUser" class="btn btn-danger">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">هیچ کاربری یافت نشد</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">ویرایش محصول</h3>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editProductForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="ProductID" id="editProductID">
                <input type="hidden" name="editProduct" value="1">
                
                <div class="form-group">
                    <label for="editProductName">نام محصول:</label>
                    <input type="text" id="editProductName" name="ProductName" required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription">توضیحات:</label>
                    <textarea id="editDescription" name="Description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editPrice">قیمت (تومان):</label>
                    <input type="number" id="editPrice" name="Price" step="1000" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editStock">موجودی:</label>
                    <input type="number" id="editStock" name="Stock" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editImageFile">تصویر جدید (اختیاری):</label>
                    <input type="file" id="editImageFile" name="ImageFile" accept="image/*">
                    <div id="currentImageContainer" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        ذخیره تغییرات
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeEditModal()">
                         انصراف
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content').forEach(section => {
                section.classList.remove('active');
            });
            
            document.getElementById(sectionId).classList.add('active');

            document.querySelectorAll('.menu a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('onclick')?.includes(sectionId)) {
                    link.classList.add('active');
                }
            });
        }
        
        function openEditModal(productID) {
            fetch('get_product.php?id=' + productID)
                .then(response => response.json())
                .then(product => {
                    document.getElementById('editProductID').value = product.ProductID;
                    document.getElementById('editProductName').value = product.ProductName;
                    document.getElementById('editDescription').value = product.Description;
                    document.getElementById('editPrice').value = product.Price;
                    document.getElementById('editStock').value = product.Stock;
                    
                    const imageContainer = document.getElementById('currentImageContainer');
                    imageContainer.innerHTML = '';
                    
                    if (product.ImageURL) {
                        imageContainer.innerHTML = `
                            <p>تصویر فعلی:</p>
                            <img src="${product.ImageURL}" class="img-thumbnail" style="max-width: 150px;">
                        `;
                    } else {
                        imageContainer.innerHTML = '<p>تصویری برای این محصول وجود ندارد</p>';
                    }
                    
                    document.getElementById('editProductModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در دریافت اطلاعات محصول');
                });
        }
        
        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editProductModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }


        window.addEventListener('DOMContentLoaded', () => {
        const successAlert = document.querySelector('.alert-success');
        const errorAlert = document.querySelector('.alert-error');
        if (successAlert || errorAlert) {
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    });
    </script>
</body>
</html>