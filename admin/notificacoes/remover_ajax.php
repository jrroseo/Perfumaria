<?php
require_once __DIR__ . '/../includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM notificacoes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo "ok";
}
