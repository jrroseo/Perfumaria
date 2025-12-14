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

// 🔽 Receber filtros do GET
$filtroEstado = isset($_GET['estado']) ? (int)$_GET['estado'] : 0;
$filtroPagamento = isset($_GET['pagamento']) ? (int)$_GET['pagamento'] : 0;

// 🔍 Construir SQL dinamicamente
$sql = "
    SELECT e.id, e.data, e.total,
           u.nome AS nome_cliente, u.email,
           p.metodo AS pagamento,
           s.estado AS estado_encomenda
    FROM encomendas e
    JOIN utilizadores u ON e.id_utilizador = u.id
    LEFT JOIN pagamentos p ON e.id_pagamento = p.id
    LEFT JOIN estados_encomenda s ON e.id_estado = s.id
    WHERE 1
";

$params = [];

if ($filtroEstado > 0) {
    $sql .= " AND e.id_estado = ?";
    $params[] = $filtroEstado;
}

if ($filtroPagamento > 0) {
    $sql .= " AND e.id_pagamento = ?";
    $params[] = $filtroPagamento;
}

$sql .= " ORDER BY e.data DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$encomendas = $stmt->fetchAll();

// 🔄 Buscar listas para os filtros
$estados = $pdo->query("SELECT * FROM estados_encomenda")->fetchAll();
$pagamentos = $pdo->query("SELECT * FROM pagamentos")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-bag-fill text-info"></i> Encomendas</h3>
        <a href="../dashboard.php" class="btn btn-secondary"><i class="bi bi-house-door"></i> Voltar</a>
    </div>

    <!-- 🔽 Filtros -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Filtrar por Estado</label>
            <select name="estado" class="form-select" onchange="this.form.submit()">
                <option value="0">-- Todos os Estados --</option>
                <?php foreach ($estados as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $filtroEstado == $e['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['estado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrar por Pagamento</label>
            <select name="pagamento" class="form-select" onchange="this.form.submit()">
                <option value="0">-- Todos os Métodos --</option>
                <?php foreach ($pagamentos as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filtroPagamento == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['metodo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- 📋 Tabela de encomendas -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Total (R$)</th>
                    <th>Pagamento</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($encomendas as $enc): ?>
                    <tr>
                        <td><?= $enc['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($enc['data'])) ?></td>
                        <td><?= htmlspecialchars($enc['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($enc['email']) ?></td>
                        <td><?= number_format($enc['total'], 2) ?></td>
                        <td><?= htmlspecialchars($enc['pagamento'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($enc['estado_encomenda']) ?></span>
                        </td>
                        <td>
                            <a href="alterar_estado.php?id=<?= $enc['id'] ?>" class="btn btn-sm btn-warning" title="Alterar Estado">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                            <a href="ver.php?id=<?= $enc['id'] ?>" class="btn btn-sm btn-info" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($encomendas)): ?>
                    <tr><td colspan="8" class="text-center">Nenhuma encomenda encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
