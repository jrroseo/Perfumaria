<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/header_admin.php';

// ⚠️ Remoção
if (isset($_GET['remover']) && is_numeric($_GET['remover'])) {
    $stmt = $pdo->prepare("DELETE FROM newsletter WHERE id = ?");
    $stmt->execute([$_GET['remover']]);
    header("Location: newsletter.php?msg=removido");
    exit;
}

// 🔁 Paginação
$por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $por_pagina;

// Total
$total = $pdo->query("SELECT COUNT(*) FROM newsletter")->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Buscar página atual
$stmt = $pdo->prepare("SELECT * FROM newsletter ORDER BY inscrito_em DESC LIMIT $por_pagina OFFSET $offset");
$stmt->execute();
$inscritos = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">📧 Subscritores da Newsletter</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'removido'): ?>
        <div class="alert alert-success">Subscrição removida com sucesso.</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="dashboard.php" class="btn btn-outline-dark">← Voltar</a>
        <a href="newsletter.php?export=csv" class="btn btn-outline-success">⬇️ Exportar CSV</a>
    </div>

    <?php if (empty($inscritos)): ?>
        <p class="text-muted">Nenhum subscritor encontrado.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Data de Inscrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $i): ?>
                        <tr>
                            <td><?= $i['id'] ?></td>
                            <td><?= htmlspecialchars($i['email']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($i['inscrito_em'])) ?></td>
                            <td>
                                <a href="?remover=<?= $i['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remover este email?')">🗑 Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $pagina_atual === $i ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
