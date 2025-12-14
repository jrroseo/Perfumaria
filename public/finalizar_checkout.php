<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
if (!isset($_SESSION['user_id']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit;
}
?>