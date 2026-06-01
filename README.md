# 🛍️ فروشگاه آنلاین

> فروشگاه آنلاین مناسب پروژه های دانشجویی و دانشگاهی

[![PHP](https://img.shields.io/badge/PHP-70.8%25-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![CSS](https://img.shields.io/badge/CSS-29.2%25-1572B6?style=flat-square&logo=css3)](https://www.w3.org/Style/CSS/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 فهرست مطالب

- [درباره پروژه](#-درباره-پروژه)
- [ویژگی‌ها](#-ویژگی‌ها)
- [پیش‌نیاز‌ها](#-پیش‌نیاز‌ها)
- [نصب و راه‌اندازی](#-نصب-و-راه‌اندازی)
- [ساختار پروژه](#-ساختار-پروژه)
- [استفاده](#-استفاده)
- [تنظیمات](#-تنظیمات)
- [سازندگان](#-سازندگان)

---

## 📖 درباره پروژه

**فروشگاه آنلاین** یک پروژه دانشجویی کامل است که شامل:
- 🛒 سیستم فروشگاهی کامل
- 💳 مدیریت محصولات
- 👥 مدیریت کاربران
- 📊 داشبورد مدیریتی

---

## ✨ ویژگی‌ها

- ✅ رابط کاربری مدرن و واکنش‌پذیر
- ✅ سیستم احراز هویت کاربر
- ✅ مدیریت سبد خرید
- ✅ پایگاه داده MySQL
- ✅ پانل مدیریتی
- ✅ جستجو و فیلترکردن محصولات

---

## 🔧 پیش‌نیاز‌ها

قبل از نصب، این‌ها را نصب کنید:

| نرم‌افزار | نسخه | توضیح |
|---------|------|--------|
| **PHP** | 7.4+ | زبان برنامه‌نویسی |
| **MySQL** | 5.7+ | پایگاه داده |
| **Apache/Nginx** | آخرین | سرور وب |
| **Composer** | اختیاری | مدیریت بسته‌ها |

### ✔️ چک کردن نصب:

```bash
# بررسی PHP
php -v

# بررسی MySQL
mysql --version
```

---

## 🚀 نصب و راه‌اندازی

### 1️⃣ Clone کردن Repository

```bash
# با HTTPS
git clone https://github.com/Hoseiin28/online-shop.git

# یا با SSH
git clone git@github.com:Hoseiin28/online-shop.git

# رفتن به فولدر پروژه
cd online-shop
```

### 2️⃣ تنظیم Database

#### الف) ایجاد دیتابیس:

```bash
# وارد MySQL شوید
mysql -u root -p

# یا اگر رمز ندارید
mysql -u root
```

#### ب) اجرای SQL Script:

```sql
-- ایجاد دیتابیس
CREATE DATABASE online_shop;

-- استفاده از دیتابیس
USE online_shop;

-- ایجاد جداول (فایل database.sql را اجرا کنید)
SOURCE database.sql;
```

**یا:** اگر فایل `database.sql` موجود است:

```bash
mysql -u root -p online_shop < database.sql
```

### 3️⃣ تنظیم Configuration

فایل `config.php` یا `.env` را ویرایش کنید:

```php
<?php
// config.php

// تنظیمات Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');  // رمز خود را وارد کنید
define('DB_NAME', 'online_shop');

// تنظیمات سایت
define('SITE_URL', 'http://localhost/online-shop');
define('SITE_NAME', 'فروشگاه آنلاین');
?>
```

### 4️⃣ اجرا کردن پروژه

#### روش 1: با Apache (XAMPP/WAMP)

```bash
# فایل‌ها را در htdocs قرار دهید
# مثلاً: C:\xampp\htdocs\online-shop

# سپس:
http://localhost/online-shop
```

#### روش 2: با PHP Built-in Server

```bash
php -S localhost:8000
```

سپس در مرورگر باز کنید:
```
http://localhost:8000
```

---

## 📁 ساختار پروژه

```
online-shop/
│
├── index.php              # صفحه‌ی اصلی
├── config.php             # تنظیمات پایگاه داده
├── database.sql           # فایل SQL (دیتابیس)
│
├── css/                   # فایل‌های CSS
│   ├── style.css
│   ├── responsive.css
│   └── ...
│
├── js/                    # فایل‌های JavaScript
│   ├── script.js
│   ├── main.js
│   └── ...
│
├── includes/              # فایل‌های شامل‌شده
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── ...
│
├── pages/                 # صفحات اصلی
│   ├── shop.php
│   ├── product.php
│   ├── cart.php
│   ├── checkout.php
│   ├── login.php
│   └── ...
│
├── admin/                 # پانل مدیریتی
│   ├── dashboard.php
│   ├── products.php
│   ├── users.php
│   └── ...
│
├── images/               # تصاویر
│   ├── products/
│   ├── banners/
│   └── ...
│
├── assets/               # منابع دیگر
│   ├── icons/
│   ├── fonts/
│   └── ...
│
└── README.md             # این فایل
```

---

## 💻 استفاده

### 🏠 صفحه اصلی
```
http://localhost/online-shop
```

### 🛒 صفحه فروشگاه
```
http://localhost/online-shop/pages/shop.php
```

### 👤 صفحه ورود
```
http://localhost/online-shop/pages/login.php
```

### 🔐 پانل مدیریتی
```
http://localhost/online-shop/admin/dashboard.php
```

**نام کاربری (مثال)**: admin  
**رمز عبور (مثال)**: admin123

> ⚠️ **نکته مهم**: این رمز را در production تغییر دهید!

---

## ⚙️ تنظیمات

### تنظیم Database

فایل `config.php` را باز کنید و تنظیمات زیر را مطابق سیستم خود تغییر دهید:

```php
// تنظیمات Connection
define('DB_HOST', 'localhost');      // آدرس سرور MySQL
define('DB_USER', 'root');           // نام کاربری MySQL
define('DB_PASS', '');               // رمز عبور MySQL
define('DB_NAME', 'online_shop');    // نام دیتابیس

// تنظیمات سایت
define('SITE_URL', 'http://localhost/online-shop');
define('SITE_NAME', 'فروشگاه آنلاین');
define('ADMIN_EMAIL', 'admin@example.com');
```

### تنظیم اجازات فولدر (Permissions)

برخی فولدرها نیاز به اجازه نوشتاری دارند:

```bash
# در Linux/Mac
chmod 755 uploads/
chmod 755 temp/
chmod 755 logs/

# یا
chmod -R 775 uploads/ temp/ logs/
```

---

## 🔐 نکات امنیتی

⚠️ **قبل از Deploy به سرور:**

- [ ] رمز MySQL را تغییر دهید
- [ ] رمز Admin را تغییر دهید
- [ ] از HTTPS استفاده کنید
- [ ] Input Validation را فعال کنید
- [ ] SQL Injection از بین برود
- [ ] XSS Protection فعال شود
- [ ] Session Timeout تنظیم شود

---

## 🐛 حل مشکلات (Troubleshooting)

### ❌ خطا: "Cannot connect to database"

```bash
# بررسی کنید:
1. آیا MySQL در حال کار است؟
2. نام کاربری و رمز صحیح است؟
3. دیتابیس ایجاد شده است؟

# حل:
mysql -u root -p -e "CREATE DATABASE online_shop;"
```

### ❌ خطا: "Permission denied"

```bash
# حل:
chmod 755 config.php
chmod 775 uploads/
```

### ❌ صفحه خالی یا 404

```bash
# بررسی:
1. فایل‌ها در صحیح جای هستند؟
2. Apache Rewrite Module فعال است؟
3. .htaccess صحیح است؟
```

### ❌ تصاویر نمایش داده نمی‌شود

```bash
# بررسی مسیر تصاویر:
1. فولدر images/ موجود است؟
2. فایل‌های تصویری در جای صحیح هستند؟
3. اجازه خواندن فایل‌ها موجود است؟
```

---

## 📚 منابع مفید

- [مستندات PHP](https://www.php.net/manual/)
- [مستندات MySQL](https://dev.mysql.com/doc/)
- [CSS-Tricks](https://css-tricks.com/)
- [MDN Web Docs](https://developer.mozilla.org/)

---

## 👨‍💻 سازندگان

**توسعه‌دهنده اصلی:**
- [@Hoseiin28](https://github.com/Hoseiin28)

---

## 📄 لایسنس

این پروژه تحت لایسنس **MIT** منتشر شده است.  
برای اطلاعات بیشتر به فایل [LICENSE](LICENSE) مراجعه کنید.

---

## 💬 ارتباط

اگر سوال یا پیشنهادی دارید:
- 🐛 [ایجاد Issue](https://github.com/Hoseiin28/online-shop/issues)
- 💌 ایمیل: [تماس بگیرید](mailto:your-email@example.com)

---

## 🎯 نقشه راه (Roadmap)

- [ ] اضافه کردن سیستم نظرات و امتیاز‌دهی
- [ ] بهبود سیستم جستجو
- [ ] اضافه کردن Payment Gateway
- [ ] سیستم ایمیل خودکار
- [ ] موبایل App

---

<div align="center">

⭐ اگر این پروژه برای شما مفید بود، لطفاً **Star** دهید!

</div>

---

**آخرین بروزرسانی**: 1 ژوئن 2026
