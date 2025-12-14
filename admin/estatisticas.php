<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
$titulo = "Página de Estatísticas";
require_once __DIR__ . '/includes/header_admin.php';

// Total de encomendas
$total_encomendas = $pdo->query("SELECT COUNT(*) FROM encomendas")->fetchColumn();

// Total faturado (encomendas pagas)
$total_faturado = $pdo->query("
    SELECT SUM(total) FROM encomendas WHERE id_estado IN (2, 3, 4, 5)
")->fetchColumn();
$total_faturado = $total_faturado ?? 0;

// Top 5 produtos vendidos
$top_produtos = $pdo->query("
    SELECT p.nome, SUM(pe.quantidade) AS total_vendido
    FROM produtos_encomenda pe
    JOIN produtos p ON pe.id_produto = p.id
    GROUP BY pe.id_produto
    ORDER BY total_vendido DESC
    LIMIT 5
")->fetchAll();
?>

<div class="d-flex justify-content-between mb-3">
        <a href="dashboard.php" class="btn btn-outline-dark">← Voltar</a>
    </div>

<div class="container mt-5">
    <h2 class="mb-4">📊 Estatísticas da home</h2>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Total de Encomendas</h5>
                    <p class="display-6"><?= $total_encomendas ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Total Faturado</h5>
                    <p class="display-6 text-success"><?= number_format($total_faturado, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4>🏆 Top 5 Produtos Mais Vendidos</h4>
    <?php if ($top_produtos): ?>
        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Produto</th>
                    <th>Unidades Vendidas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_produtos as $i => $produto): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= $produto['total_vendido'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">Ainda não há vendas registadas.</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
