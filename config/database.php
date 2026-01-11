<?php
// بدء الجلسة في كل الصفحات
session_start();

// إعدادات الاتصال
$db_host = 'localhost';
$db_name = 'bank_system';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';

// Data Source Name (DSN)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// خيارات PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// محاولة الاتصال
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // في حال فشل الاتصال، لا تكشف التفاصيل للمستخدم
    // سجل الخطأ في ملف logs
    error_log("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
    // اعرض رسالة عامة
    die("عذراً، حدث خطأ فني في النظام. يرجى المحاولة لاحقاً.");
}
?>
