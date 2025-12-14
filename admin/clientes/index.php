<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

// Verifica se estamos a ver clientes removidos
$verRemovidos = isset($_GET['removidos']) && $_GET['removidos'] == 1;

// Buscar clientes com base no filtro
$stmt = $pdo->prepare("
    SELECT u.id, u.nome, u.email, u.bloqueado, u.eliminado, u.criado_em,
           c.telefone, c.morada, c.nif
    FROM utilizadores u
    LEFT JOIN clientes_dados c ON u.id = c.id_utilizador
    WHERE u.tipo = 'cliente' AND u.eliminado = ?
    ORDER BY u.criado_em DESC
");
$stmt->execute([$verRemovidos ? 1 : 0]);
$clientes = $stmt->fetchAll();
?>

<div class="container mt-4">

    <!-- 🔁 Botões para alternar entre ativos e removidos -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people-fill <?= $verRemovidos ? 'text-danger' : 'text-success' ?>"></i> <?= $verRemovidos ? 'Contas Removidas' : 'Clientes Ativos' ?></h3>
        <div>
            <?php if ($verRemovidos): ?>
                <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Ver Ativos</a>
            <?php else: ?>
                <a href="index.php?removidos=1" class="btn btn-outline-danger"><i class="bi bi-trash3"></i> Ver Removidos</a>
            <?php endif; ?>
            <a href="../dashboard.php" class="btn btn-secondary ms-2"><i class="bi bi-house-door"></i> Voltar ao Dashboard</a>
        </div>
    </div>

    <!-- ✅ Mensagens de feedback -->
    <?php if (isset($_GET['removido']) && $_GET['removido'] === 'ok'): ?>
        <div class="alert alert-success">Cliente Removido Com Sucesso.</div>
    <?php elseif (isset($_GET['restaurado']) && $_GET['restaurado'] === 'ok'): ?>
        <div class="alert alert-success">Cliente restaurado com sucesso.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Morada</th>
                    <th>NIF</th>
                    <th>Estado</th>
                    <th>Registado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['nome']) ?></td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                        <td><?= nl2br(htmlspecialchars($cliente['morada'])) ?></td>
                        <td><?= htmlspecialchars($cliente['nif']) ?></td>
                        <td>
                            <?php if ($cliente['bloqueado']): ?>
                                <span class="badge bg-danger">Bloqueado</span>
                            <?php else: ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($cliente['criado_em'])) ?></td>
                        <td>
                            <?php if (!$verRemovidos): ?>
                                <a href="editar.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                <a href="bloquear.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-outline-<?= $cliente['bloqueado'] ? 'success' : 'danger' ?>" title="<?= $cliente['bloqueado'] ? 'Desbloquear' : 'Bloquear' ?>">
                                    <i class="bi bi-<?= $cliente['bloqueado'] ? 'unlock' : 'lock' ?>"></i>
                                </a>
                                <a href="encomendas.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-info" title="Ver Encomendas"><i class="bi bi-bag-check"></i></a>
                                <a href="remover.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Remover" onclick="return confirm('Tem a certeza que deseja remover este cliente?');"><i class="bi bi-trash"></i></a>
                            <?php else: ?>
                                <a href="restaurar.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-success" title="Reativar Cliente"><i class="bi bi-arrow-clockwise"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
