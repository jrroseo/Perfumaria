<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php);
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

// Buscar histórico de stock
$stmt = $pdo->query("
    SELECT hs.*, p.nome AS produto_nome
    FROM historico_stock hs
    JOIN produtos p ON hs.id_produto = p.id
    ORDER BY hs.data DESC
");
$registos = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📦 Histórico de Movimentos de Stock</h3>
        <a href="index.php" class="btn btn-outline-secondary">← Voltar à Gestão de Stock</a>
    </div>

    <?php if (empty($registos)): ?>
        <div class="alert alert-info">Nenhum registo encontrado.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-light">
                <thead class="table-dark">
                    <tr>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Fornecedor</th>
                        <th>Nota</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registos as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['produto_nome']) ?></td>
                            <td>
                                <span class="badge <?= $r['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($r['tipo']) ?>
                                </span>
                            </td>
                            <td><?= $r['quantidade'] ?></td>
                            <td><?= htmlspecialchars($r['fornecedor']) ?: '-' ?></td>
                            <td><?= htmlspecialchars($r['nota']) ?: '-' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r['data'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
