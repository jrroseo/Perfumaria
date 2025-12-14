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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID inválido.</div>";
    exit;
}

$id = (int)$_GET['id'];

// Obter encomenda
$stmt = $pdo->prepare("
    SELECT e.id, e.id_estado, u.nome AS cliente
    FROM encomendas e
    JOIN utilizadores u ON e.id_utilizador = u.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$encomenda = $stmt->fetch();

if (!$encomenda) {
    echo "<div class='alert alert-warning'>Encomenda não encontrada.</div>";
    exit;
}

// Obter lista de estados possíveis
$estados = $pdo->query("SELECT id, estado FROM estados_encomenda")->fetchAll();

// Submeter nova escolha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoEstado = (int)$_POST['id_estado'];

    $stmt = $pdo->prepare("UPDATE encomendas SET id_estado = ? WHERE id = ?");
    $stmt->execute([$novoEstado, $id]);

    header("Location: index.php?estado=alterado");
    exit;
}
?>

<div class="container mt-5">
    <h4><i class="bi bi-arrow-repeat text-warning"></i> Alterar Estado da Encomenda #<?= $encomenda['id'] ?></h4>
    <p>Cliente: <strong><?= htmlspecialchars($encomenda['cliente']) ?></strong></p>

    <form method="post" class="mt-3 bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Novo Estado</label>
            <select name="id_estado" class="form-select" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado['id'] ?>" <?= $estado['id'] == $encomenda['id_estado'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($estado['estado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Estado</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
