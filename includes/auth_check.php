<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /secure_web_app/auth/login.php");
    exit();
}
?>