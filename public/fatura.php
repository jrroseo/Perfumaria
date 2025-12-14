<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_encomenda = $_GET['id'] ?? 0;

// Buscar encomenda
$stmt = $pdo->prepare("SELECT e.*, s.estado AS nome_estado
                       FROM encomendas e
                       LEFT JOIN estados_encomenda s ON e.id_estado = s.id
                       WHERE e.id = ? AND e.id_utilizador = ?");
$stmt->execute([$id_encomenda, $_SESSION['user_id']]);
$encomenda = $stmt->fetch();

if (!$encomenda) {
    echo "<p>Encomenda não encontrada.</p>";
    exit;
}

// Buscar produtos
$stmt = $pdo->prepare("SELECT p.nome, pe.quantidade, pe.preco_unitario
                       FROM produtos_encomenda pe
                       JOIN produtos p ON pe.id_produto = p.id
                       WHERE pe.id_encomenda = ?");
$stmt->execute([$id_encomenda]);
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Fatura #<?= $encomenda['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>🧾 Fatura da Encomenda #<?= $encomenda['id'] ?></h2>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($encomenda['data'])) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($encomenda['nome_estado']) ?></p>
    <p><strong>Total:</strong> <?= number_format($encomenda['total'], 2) ?></p>

    <table class="table table-bordered mt-4">
        <thead class="table-light">
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['quantidade'] ?></td>
                    <td><?= number_format($p['preco_unitario'], 2) ?></td>
                    <td><?= number_format($p['preco_unitario'] * $p['quantidade'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="conta.php" class="btn btn-secondary mt-3">Voltar</a>
</body>
</html>
