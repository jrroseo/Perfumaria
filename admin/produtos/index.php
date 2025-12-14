<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

// Verifica se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<div class='container mt-5'><p class='text-danger'>Acesso negado.</p></div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

// Buscar todos os produtos com dados associados
$stmt = $pdo->query("
    SELECT p.*, 
           c.nome AS categoria, 
           m.nome AS marca, 
           ca.ml AS capacidade, 
           co.tipo AS concentracao
    FROM produtos p
    LEFT JOIN categorias c ON p.id_categoria = c.id
    LEFT JOIN marcas m ON p.id_marca = m.id
    LEFT JOIN capacidades ca ON p.id_capacidade = ca.id
    LEFT JOIN concentracoes co ON p.id_concentracao = co.id
    WHERE p.eliminado = 0
    ORDER BY p.criado_em DESC
");

$produtos = $stmt->fetchAll();
?>

<?php if (isset($_GET['removido']) && $_GET['removido'] === 'ok'): ?>
    <div class="alert alert-success">Produto Removido Com Sucesso.</div>
<?php endif; ?>


<div class="container mt-5 text-dark">
<?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="../dashboard.php" class="btn btn-outline-dark">🔧 Voltar para Administração</a>
    </div>
<?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🧴 Gestão de Produtos</h2>
        <a href="criar.php" class="btn btn-success">➕ Adicionar Produto</a>
        <a href="removidos.php" class="btn btn-outline-danger">Ver Removidos</a>
    </div>

    <?php if (empty($produtos)): ?>
        <p class="text-muted">Nenhum produto encontrado.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-light">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Marca</th>
                        <th>Capacidade</th>
                        <th>Concentração</th>
                        <th>Stock</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><img src="../../assets/images/<?= htmlspecialchars($p['imagem']) ?>" width="60"></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td>R$ <?= number_format($p['preco'], 2) ?></td>
                            <td><?= $p['marca'] ?? '---' ?></td>
                            <td><?= $p['capacidade'] ?? '---' ?></td>
                            <td><?= $p['concentracao'] ?? '---' ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td>
                                <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                                <a href="remover.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmar remoção?')">🗑️ Apagar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php';; ?>