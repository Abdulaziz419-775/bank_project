<?php
require_once '../config/database.php';
require_once '../includes/functions.php';


require_login();


$user = get_authenticated_user();
$error = '';
$success = '';


if ($user === null) {
    header('Location: logout.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toUsername = $_POST['to_username'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);

    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE username = ?");
    $stmt->execute([$toUsername]);
    $receiver = $stmt->fetch();

    if (!$receiver) {
        $error = 'المستخدم المستلم غير موجود.';
    } elseif ($receiver['id'] == $user['id']) {
        $error = 'لا يمكنك التحويل لنفسك.';
    } elseif ($amount <= 0) {
        $error = 'مبلغ التحويل يجب أن يكون أكبر من صفر.';
    } else {

        try {
            $pdo->beginTransaction();

            
            $stmt = $pdo->prepare("SELECT balance FROM accounts WHERE id = ? FOR UPDATE");
            $stmt->execute([$user['id']]);
            $senderBalance = $stmt->fetchColumn();

            if ($senderBalance < $amount) {
                throw new Exception("رصيدك الحالي غير كافٍ لإتمام هذه العملية.");
            }

            $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $user['id']]);


            throw new Exception("محاكاة فشل النظام بعد الخصم!");

            $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $receiver['id']]);

            
            $stmt = $pdo->prepare("INSERT INTO transfers (from_account_id, to_account_id, amount) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $receiver['id'], $amount]);

            
            $pdo->commit();
            $success = "تم تحويل مبلغ " . number_format($amount, 2) . " ريال بنجاح إلى المستخدم " . htmlspecialchars($toUsername) . ".";
            
            
            $user = get_authenticated_user();

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "فشل التحويل: " . $e->getMessage();
            error_log("Transfer Failed: " . $e->getMessage());
        }
    }
}


$page_title = 'إجراء تحويل';
require_once '../includes/header.php';
?>

<div class="header">
    <h2>إجراء تحويل مالي</h2>
    <a href="dashboard.php">العودة للوحة التحكم</a>
</div>

<div class="balance-box">
    <h3>رصيدك الحالي</h3>
    <p><?= number_format($user['balance'], 2) ?> ريال</p>
</div>

<?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

<form action="transfer.php" method="post">
    <label for="to_username">اسم مستخدم المستلم:</label>
    <input type="text" id="to_username" name="to_username" required>
    
    <label for="amount">المبلغ:</label>
    <input type="number" step="0.01" id="amount" name="amount" required>
    
    <button type="submit">تأكيد التحويل</button>
</form>

<?php

require_once '../includes/footer.php';
?>

