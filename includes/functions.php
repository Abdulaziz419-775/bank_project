<?php

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}


function get_authenticated_user(): ?array {
    global $pdo; 

    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $user_data = $stmt->fetch();
    
    return $user_data === false ? null : $user_data;
}
?>

