<?php
session_start();
include 'config.php';

$message = '';

if (isset($_POST['register'])) {
    $fullname = trim($_POST['FullName']);
    $email = trim($_POST['Email']);
    $password = $_POST['Password'];
    $confirmPassword = $_POST['ConfirmPassword'];

    if ($password !== $confirmPassword) {
        $message = "رمز عبور و تکرار آن مطابقت ندارند.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "ایمیل وارد شده معتبر نیست.";
    } else {
        $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "ایمیل قبلا ثبت شده است.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Users (FullName, Email, Password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashedPassword);
            if ($stmt->execute()) {
                $message = "ثبت نام با موفقیت انجام شد. اکنون وارد شوید.";
            } else {
                $message = "خطا در ثبت نام. لطفا دوباره تلاش کنید.";
            }
        }
        $stmt->close();
    }
}

if (isset($_POST['login'])) {
    $email = trim($_POST['LoginEmail']);
    $password = $_POST['LoginPassword'];

    $stmt = $conn->prepare("SELECT UserID, FullName, Password, Role FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['FullName'] = $user['FullName'];
            $_SESSION['Role'] = $user['Role'];
            header("Location: index.php");
            exit;
        } else {
            $message = "رمز عبور اشتباه است.";
        }
    } else {
        $message = "کاربری با این ایمیل یافت نشد.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>ورود / ثبت نام</title>
    <link rel="stylesheet" href="style-login.css">
</head>
<body>

<div class="container">
    <div class="tabs">
        <div class="tab active" id="loginTab">ورود</div>
        <div class="tab" id="registerTab">ثبت نام</div>
    </div>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm" class="active" autocomplete="off">
        <label for="LoginEmail">ایمیل:</label>
        <input type="email" id="LoginEmail" name="LoginEmail" required autocomplete="off" />

        <label for="LoginPassword">رمز عبور:</label>
        <input type="password" id="LoginPassword" name="LoginPassword" required autocomplete="new-password" />

        <button type="submit" name="login">ورود</button>
    </form>

    <form method="POST" id="registerForm" autocomplete="off">
        <label for="FullName">نام کامل:</label>
        <input type="text" id="FullName" name="FullName" required autocomplete="off" />

        <label for="Email">ایمیل:</label>
        <input type="email" id="Email" name="Email" required autocomplete="off" />

        <label for="Password">رمز عبور:</label>
        <input type="password" id="Password" name="Password" required autocomplete="new-password" />

        <label for="ConfirmPassword">تکرار رمز عبور:</label>
        <input type="password" id="ConfirmPassword" name="ConfirmPassword" required autocomplete="new-password" />

        <button type="submit" name="register">ثبت نام</button>
    </form>
</div>

<script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    loginTab.addEventListener('click', () => {
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
    });

    registerTab.addEventListener('click', () => {
        registerTab.classList.add('active');
        loginTab.classList.remove('active');
        registerForm.classList.add('active');
        loginForm.classList.remove('active');
    });
</script>

</body>
</html>