<?php
session_start();

$db_host = 'localhost';
$db_name = 'bank_system';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
   
    error_log("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
    die("عذراً، حدث خطأ فني في النظام. يرجى المحاولة لاحقاً.");
}
?>


