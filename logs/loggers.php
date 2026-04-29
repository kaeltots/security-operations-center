<?php
function logAction($conn, $user_id, $action, $status) {
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $ip, $status);
    $stmt->execute();
}
?>