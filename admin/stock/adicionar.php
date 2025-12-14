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

// Buscar todos os produtos
$produtos = $pdo->query("SELECT id, nome FROM produtos ORDER BY nome")->fetchAll();

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produto = $_POST['id_produto'] ?? null;
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $fornecedor = trim($_POST['fornecedor'] ?? '');
    $nota = trim($_POST['nota'] ?? '');

    if ($id_produto && $quantidade > 0) {
        // Atualiza o stock do produto
        $stmt = $pdo->prepare("UPDATE produtos SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$quantidade, $id_produto]);

        // Regista no histórico
        $stmt = $pdo->prepare("INSERT INTO historico_stock (id_produto, tipo, quantidade, fornecedor, nota, data)
                               VALUES (?, 'entrada', ?, ?, ?, NOW())");
        $stmt->execute([$id_produto, $quantidade, $fornecedor, $nota]);

        echo "<div class='alert alert-success'>Stock adicionado com sucesso.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro: selecione um produto e uma quantidade válida.</div>";
    }
}
?>

<div class="container mt-5">
    <h3 class="mb-4">➕ Adicionar Stock</h3>

    <form method="post" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label class="form-label">Produto</label>
            <select name="id_produto" class="form-select" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" name="quantidade" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fornecedor</label>
            <input type="text" name="fornecedor" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Nota (opcional)</label>
            <textarea name="nota" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Adicionar Stock</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
