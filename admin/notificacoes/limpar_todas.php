<?php
require_once __DIR__ . '/../includes/db.php';
$pdo->query("UPDATE notificacoes SET lida = 1");
header("Location: index.php");
exit;
