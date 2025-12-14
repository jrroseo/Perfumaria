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
    echo "<div class='alert alert-danger'>ID de produto inválido.</div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$id = (int)$_GET['id'];

// Buscar nome do produto
$stmt = $pdo->prepare("SELECT nome FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "<div class='alert alert-warning'>Produto não encontrado.</div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}
?>

<div class="container mt-5">
    <h4 class="mb-3"><i class="bi bi-trash3-fill text-danger"></i> Remover Produto</h4>
    <div class="alert alert-warning shadow-sm">
        Está prestes a remover o produto <strong><?= htmlspecialchars($produto['nome']) ?></strong>.<br>
        Esta ação irá ocultá-lo da home (remoção lógica).
    </div>

    <!-- Botão para abrir o modal -->
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal">Remover Produto</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</div>

<!-- Modal de confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form method="post" action="remover.php?id=<?= $id ?>">
    <div class="modal-header">
      <h5 class="modal-title" id="confirmModalLabel">Confirmar Remoção</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
    </div>
    <div class="modal-body">
      Tem a certeza que deseja eliminar o produto <strong><?= htmlspecialchars($produto['nome']) ?></strong>?
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" name="confirmar" class="btn btn-danger">Sim, remover</button>
    </div>
      </form>
    </div>
  </div>
</div>

<?php
// ✅ Processar remoção ANTES do footer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $stmt = $pdo->prepare("UPDATE produtos SET eliminado = 1 WHERE id = ?");
    $stmt->execute([$id]);

    // ✅ Redirecionar após remoção
    header("Location: index.php?removido=ok");
    exit;
}

require_once __DIR__ . '//../includes/footer_admin.php';
?>
