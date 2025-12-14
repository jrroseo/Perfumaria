<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$notificacoes = $pdo->query("
    SELECT id, titulo, mensagem, lida, criado_em 
    FROM notificacoes 
    ORDER BY criado_em DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notificacoes);
