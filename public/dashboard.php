<?php
// الخطوة 1: تضمين الملفات الأساسية
require_once '../config/database.php';
require_once '../includes/functions.php';

// الخطوة 2: التأكد من أن المستخدم مسجل دخوله
require_login();


$user = get_authenticated_user();


// الخطوة 3.5: طبقة الحماية - الآن ستعمل بشكل صحيح
if ($user === null) {
    header('Location: logout.php'); // أرسله لصفحة الخروج لتنظيف الجلسة
    exit();
}

// الخطوة 4: جلب الإشعارات والمعاملات
$stmt = $pdo->prepare("SELECT t.*, a.owner_name as sender_name FROM transfers t JOIN accounts a ON t.from_account_id = a.id WHERE t.to_account_id = ? AND t.is_read = 0 ORDER BY t.transfer_date DESC");
$stmt->execute([$user['id']]);
$notifications = $stmt->fetchAll();

// جلب آخر 5 معاملات (صادرة أو واردة)
$stmt = $pdo->prepare("
    SELECT t.*, sender.owner_name as sender_name, receiver.owner_name as receiver_name 
    FROM transfers t
    JOIN accounts sender ON t.from_account_id = sender.id
    JOIN accounts receiver ON t.to_account_id = receiver.id
    WHERE t.from_account_id = ? OR t.to_account_id = ?
    ORDER BY t.transfer_date DESC LIMIT 5
");
$stmt->execute([$user['id'], $user['id']]);
$transactions = $stmt->fetchAll();

// تحديث الإشعارات لجعلها مقروءة بعد عرضها
if (!empty($notifications)) {
    $stmt = $pdo->prepare("UPDATE transfers SET is_read = 1 WHERE to_account_id = ? AND is_read = 0");
    $stmt->execute([$user['id']]);
}

// الخطوة 5: تضمين الهيدر لبدء عرض الصفحة
$page_title = 'لوحة التحكم'; // تعريف عنوان الصفحة
require_once '../includes/header.php';
?>

<!-- هنا يبدأ محتوى HTML الخاص بصفحة لوحة التحكم فقط -->
<div class="header">
    <h2>أهلاً بك، <?= htmlspecialchars($user['owner_name']) ?></h2>
</div>

<div class="balance-box">
    <h3>رصيدك الحالي</h3>
    <p><?= number_format($user['balance'], 2) ?> ريال</p>
</div>

<a href="transfer.php" class="btn-primary">إجراء تحويل جديد</a>

<?php if (!empty($notifications)): ?>
<div class="notifications">
    <h3>إشعارات جديدة</h3>
    <?php foreach ($notifications as $notification): ?>
        <p class="success">تم إيداع مبلغ <?= number_format($notification['amount'], 2) ?> ريال في حسابك من طرف "<?= htmlspecialchars($notification['sender_name']) ?>".</p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="transactions">
    <h3>آخر الحركات المالية</h3>
    <!-- ... باقي كود HTML كما هو ... -->
    <table>
        <thead>
            <tr>
                <th>نوع العملية</th>
                <th>الطرف الآخر</th>
                <th>المبلغ</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="4">لا توجد حركات مالية لعرضها.</td></tr>
            <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <?php if ($tx['from_account_id'] == $user['id']): ?>
                            <td class="sent">تحويل صادر</td>
                            <td>إلى: <?= htmlspecialchars($tx['receiver_name']) ?></td>
                        <?php else: ?>
                            <td class="received">تحويل وارد</td>
                            <td>من: <?= htmlspecialchars($tx['sender_name']) ?></td>
                        <?php endif; ?>
                        <td><?= number_format($tx['amount'], 2) ?> ريال</td>
                        <td><?= date('Y-m-d H:i', strtotime($tx['transfer_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// الخطوة 6: تضمين الفوتر لإغلاق الصفحة
require_once '../includes/footer.php';
?>
