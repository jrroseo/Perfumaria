<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<div class='alert alert-danger'>Acesso negado.</div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

// Inserir reposição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['repor'])) {
    $id = (int)$_POST['id_produto'];
    $qtd = (int)$_POST['quantidade'];
    $fornecedor = trim($_POST['fornecedor']);
    $nota = trim($_POST['nota']);

    if ($qtd > 0) {
        // Atualizar stock
        $pdo->prepare("UPDATE produtos SET stock = stock + ? WHERE id = ?")->execute([$qtd, $id]);

        // Inserir no histórico
        $pdo->prepare("INSERT INTO historico_stock (id_produto, quantidade, tipo, fornecedor, nota, data) 
                       VALUES (?, ?, 'entrada', ?, ?, NOW())")
            ->execute([$id, $qtd, $fornecedor, $nota]);

        // Remover notificação específica
        $pdo->prepare("DELETE FROM notificacoes WHERE tipo = 'stock_baixo' AND id_produto = ?")->execute([$id]);

        echo "<div class='alert alert-success container mt-3'>✅ Reposição registada.</div>";
    }
}

$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📦 Gestão de Stock</h3>
        <a href="historico.php" class="btn btn-outline-primary">📜 Ver Histórico</a>
    </div>

    <table class="table table-bordered mt-2 bg-light">

        <thead class="table-dark">
            <tr>
                <th>Produto</th>
                <th>Stock Atual</th>
                <th>Status</th>
                <th>Repor Stock</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $p): ?>
            <tr class="<?= $p['stock'] < 5 ? 'table-warning' : '' ?>">
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= $p['stock'] ?></td>
                <td>
                    <?= $p['stock'] < 5 ? '<span class="text-danger">⚠️ Baixo</span>' : '<span class="text-muted">OK</span>' ?>
                </td>
                <td>
                    <form method="post" class="row g-1 align-items-center">
                        <input type="hidden" name="id_produto" value="<?= $p['id'] ?>">
                        <div class="col">
                            <input type="number" name="quantidade" class="form-control form-control-sm" placeholder="Qtd" required>
                        </div>
                        <div class="col">
                            <input type="text" name="fornecedor" class="form-control form-control-sm" placeholder="Fornecedor">
                        </div>
                        <div class="col">
                            <input type="text" name="nota" class="form-control form-control-sm" placeholder="Nota (opcional)">
                        </div>
                        <div class="col-auto">
                            <button name="repor" class="btn btn-sm btn-success">➕ Repor</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
