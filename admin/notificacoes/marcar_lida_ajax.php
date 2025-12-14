<?php
require_once __DIR__ . '/../includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE notificacoes SET lida = 1 WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo "ok";
}
