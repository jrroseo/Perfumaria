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

// Ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['marcar_lida'])) {
        $pdo->prepare("UPDATE notificacoes SET lida = 1 WHERE id = ?")->execute([intval($_POST['id'])]);
    } elseif (isset($_POST['remover'])) {
        $pdo->prepare("DELETE FROM notificacoes WHERE id = ?")->execute([intval($_POST['id'])]);
    } elseif (isset($_POST['marcar_todas_lidas'])) {
        $pdo->exec("UPDATE notificacoes SET lida = 1");
    } elseif (isset($_POST['remover_todas'])) {
        $pdo->exec("DELETE FROM notificacoes");
    }
}

$notificacoes = $pdo->query("SELECT * FROM notificacoes ORDER BY criado_em DESC")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>🔔 Notificações</h3>
        <div class="d-flex gap-2">
            <form method="post" class="mb-0">
                <button name="marcar_todas_lidas" class="btn btn-light border btn-sm">✅ Marcar todas como lidas</button>
            </form>
            <form method="post" class="mb-0" onsubmit="return confirm('Remover todas as notificações?')">
                <button name="remover_todas" class="btn btn-light border btn-sm">🗑️ Remover todas</button>
            </form>
        </div>
    </div>

    <?php if (empty($notificacoes)): ?>
        <div class="alert alert-secondary text-center">Nenhuma notificação no momento.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notificacoes as $n): ?>
                <div class="list-group-item d-flex justify-content-between align-items-start p-3 <?= $n['lida'] ? 'bg-light' : 'bg-warning-subtle' ?>">
                    <div class="flex-grow-1 pe-3">
                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($n['titulo']) ?></h6>
                        <p class="mb-1 text-break"><?= nl2br(htmlspecialchars($n['mensagem'])) ?></p>
                        <small class="text-muted"><?= date("d/m/Y H:i", strtotime($n['criado_em'])) ?></small>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <?php if (!$n['lida']): ?>
                            <form method="post" class="mb-0">
                                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                <button name="marcar_lida" class="btn btn-outline-success btn-sm" title="Marcar como lida">✔️</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" class="mb-0" onsubmit="return confirm('Remover esta notificação?')">
                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button name="remover" class="btn btn-outline-secondary btn-sm" title="Remover">🗑️</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
