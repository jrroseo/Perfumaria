<?php
require_once __DIR__ . '/../includes/db.php';

// Parâmetros de alerta
$limite = 5;

// Verifica produtos com stock baixo e sem notificação recente
$stmt = $pdo->query("
    SELECT id, nome, stock 
    FROM produtos 
    WHERE stock <= $limite AND eliminado = 0
");

$produtos = $stmt->fetchAll();

foreach ($produtos as $p) {
    // Verifica se já existe uma notificação aberta para este produto
    $check = $pdo->prepare("SELECT COUNT(*) FROM notificacoes WHERE titulo = ? AND lida = 0");
    $check->execute(["Stock baixo: {$p['nome']}"]);

    if ($check->fetchColumn() == 0) {
        $msg = "O produto {$p['nome']} está com apenas {$p['stock']} unidades em stock.";
        $stmtInsert = $pdo->prepare("INSERT INTO notificacoes (titulo, mensagem) VALUES (?, ?)");
        $stmtInsert->execute(["Stock baixo: {$p['nome']}", $msg]);
    }
}
