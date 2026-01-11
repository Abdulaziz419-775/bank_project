<?php
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'النظام البنكي' ?></title>
    
    <link rel="stylesheet" href="/bank_project/public/css/style.css">
</head>
<body>
    <div class="container">
        
        <nav class="main-nav">
            <div class="nav-logo">
                <a href="index.php">النظام البنكي</a>
            </div>
            <div class="nav-links">
                <?php // هذا الكود يتحقق من الجلسة التي تم بدأها في الملف الرئيسي ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">لوحة التحكم</a>
                    <a href="transfer.php">تحويل أموال</a>
                    <a href="logout.php">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="login.php">تسجيل الدخول</a>
                    <a href="register.php">إنشاء حساب</a>
                <?php endif; ?>
            </div>
        </nav>
        
        <main>

