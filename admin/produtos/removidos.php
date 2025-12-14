<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<div class='container mt-5'><p class='text-danger'>Acesso negado.</p></div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$stmt = $pdo->query("
    SELECT p.*, 
           m.nome AS marca, 
           c.nome AS categoria
    FROM produtos p
    LEFT JOIN marcas m ON p.id_marca = m.id
    LEFT JOIN categorias c ON p.id_categoria = c.id
    WHERE p.eliminado = 1
    ORDER BY p.criado_em DESC
");
$produtos = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🗑 Produtos Removidos</h2>
        <a href="index.php" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if (empty($produtos)): ?>
        <p class="text-muted">Nenhum produto removido.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered bg-light">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Marca</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Restaurar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><img src="/assets/images/<?= htmlspecialchars($p['imagem']) ?>" width="60"></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars($p['marca'] ?? '---') ?></td>
                            <td><?= htmlspecialchars($p['categoria'] ?? '---') ?></td>
                            <td>R$ <?= number_format($p['preco'], 2) ?></td>
                            <td>
                                <a href="restaurar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-success"
                                   onclick="return confirm('Confirmar reposição deste produto?')">↩️ Restaurar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
