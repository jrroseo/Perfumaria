<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome'])) {
    $nome = trim($_POST['nome']);

    $stmt = $pdo->prepare("INSERT INTO marcas (nome) VALUES (?)");
    $stmt->execute([$nome]);

    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId(),
        'nome' => $nome
    ]);
} else {
    echo json_encode(['success' => false]);
}
