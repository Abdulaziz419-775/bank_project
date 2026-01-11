<?php
require_once '../config/database.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $owner_name = $_POST['owner_name'] ?? '';

    if (empty($username) || empty($password) || empty($owner_name)) {
        $error = 'الرجاء ملء جميع الحقول.';
    } else {
        // التحقق من أن اسم المستخدم غير موجود مسبقاً
        $stmt = $pdo->prepare("SELECT id FROM accounts WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'اسم المستخدم هذا مسجل بالفعل. الرجاء اختيار اسم آخر.';
        } else {
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // إدراج المستخدم الجديد
            $stmt = $pdo->prepare("INSERT INTO accounts (username, password, owner_name) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $owner_name])) {
                $success = 'تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول.';
                header('Location: login.php?status=registered');
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب. الرجاء المحاولة مرة أخرى.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب جديد</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>إنشاء حساب بنكي جديد</h1>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
        <form action="register.php" method="post">
            <label for="owner_name">الاسم الكامل:</label>
            <input type="text" id="owner_name" name="owner_name" required>
            
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">إنشاء الحساب</button>
        </form>
        <p>لديك حساب بالفعل؟ <a href="login.php">سجل الدخول من هنا</a></p>
    </div>
</body>
</html>
