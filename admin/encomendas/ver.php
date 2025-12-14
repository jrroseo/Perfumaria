<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

// Verificar se o ID da encomenda foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><p class='text-danger'>ID inválido.</p></div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$id_encomenda = (int)$_GET['id'];

// Buscar encomenda
$stmt = $pdo->prepare("SELECT e.*, u.email, u.tipo, s.estado AS nome_estado
                       FROM encomendas e
                       JOIN utilizadores u ON e.id_utilizador = u.id
                       LEFT JOIN estados_encomenda s ON e.id_estado = s.id
                       WHERE e.id = ?");
$stmt->execute([$id_encomenda]);
$encomenda = $stmt->fetch();

if (!$encomenda) {
    echo "<div class='container mt-5'><p class='text-danger'>Encomenda não encontrada.</p></div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

// Buscar produtos
$stmt = $pdo->prepare("SELECT p.nome, pe.quantidade, pe.preco_unitario
                       FROM produtos_encomenda pe
                       JOIN produtos p ON pe.id_produto = p.id
                       WHERE pe.id_encomenda = ?");
$stmt->execute([$id_encomenda]);
$produtos = $stmt->fetchAll();

// Verificar se foi cancelada
$stmt = $pdo->prepare("SELECT * FROM cancelamentos WHERE id_encomenda = ?");
$stmt->execute([$id_encomenda]);
$cancelamento = $stmt->fetch();
?>

<div class="container mt-5">
    <h2>🧾 Detalhes da Encomenda #<?= $encomenda['id'] ?></h2>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($encomenda['data'])) ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($encomenda['email']) ?> (<?= $encomenda['tipo'] ?>)</p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($encomenda['nome_estado']) ?></p>
    <p><strong>Total:</strong> <?= number_format($encomenda['total'], 2) ?></p>

    <?php if ($cancelamento): ?>
        <div class="alert alert-danger mt-4">
            <strong>❌ Cancelado por <?= htmlspecialchars($cancelamento['cancelado_por']) ?>:</strong><br>
            <?= nl2br(htmlspecialchars($cancelamento['motivo'])) ?><br>
            <small><i><?= date('d/m/Y H:i', strtotime($cancelamento['data_cancelamento'])) ?></i></small>
        </div>
    <?php endif; ?>

    <h4 class="mt-4">Produtos:</h4>
    <table class="table table-bordered">
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

    <a href="index.php" class="btn btn-secondary mt-3">← Voltar</a>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
