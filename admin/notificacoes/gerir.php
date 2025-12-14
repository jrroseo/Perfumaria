<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

$notificacoes = $pdo->query("SELECT * FROM notificacoes ORDER BY data DESC")->fetchAll();
?>

<div class="container mt-5">
  <h3>🔔 Notificações</h3>
  <form method="post" action="limpar_todas.php" class="mb-3">
      <button class="btn btn-danger">🗑️ Apagar todas</button>
  </form>

  <ul class="list-group">
    <?php foreach ($notificacoes as $n): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= $n['mensagem'] ?>
        <form method="post" action="remover.php">
            <input type="hidden" name="id" value="<?= $n['id'] ?>">
            <button class="btn btn-sm btn-outline-danger">x</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
