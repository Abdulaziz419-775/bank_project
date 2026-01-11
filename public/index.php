
<?php
// تضمين ملف الاتصال لبدء الجلسة والتحقق منها
require_once '../config/database.php';

// 1. التحقق مما إذا كان المستخدم مسجلاً دخوله بالفعل (عبر وجود user_id في الجلسة).
if (isset($_SESSION['user_id'])) {
    // 2. إذا كان مسجلاً، وجهه مباشرة إلى لوحة التحكم.
    header('Location: dashboard.php');
} else {
    // 3. إذا لم يكن مسجلاً، وجهه إلى صفحة تسجيل الدخول.
    header('Location: login.php');
}
// 4. إيقاف التنفيذ بعد التوجيه.
exit();
?>
