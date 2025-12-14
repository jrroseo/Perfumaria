<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/header_admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<div class='container mt-5'><p class='text-danger'>Acesso negado. Área exclusiva para administradores.</p></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$erro = '';
$sucesso = '';
$pesquisa = $_GET['q'] ?? '';

// Buscar admins
$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE tipo IN ('admin','super_admin') AND (nome LIKE ? OR email LIKE ?) ORDER BY criado_em DESC");
$stmt->execute(["%$pesquisa%", "%$pesquisa%"]);
$admins = $stmt->fetchAll();

// Remover admin
if (isset($_GET['remover'])) {
    $alvoId = (int)$_GET['remover'];

    if ($alvoId === $_SESSION['user_id']) {
        $erro = "❌ Não podes remover a tua própria conta.";
    } else {
        $stmt = $pdo->prepare("SELECT tipo FROM utilizadores WHERE id = ?");
        $stmt->execute([$alvoId]);
        $tipoAlvo = $stmt->fetchColumn();

        if ($tipoAlvo === 'super_admin') {
            $erro = "❌ Não podes remover um super administrador.";
        } else {
            $pdo->prepare("DELETE FROM utilizadores WHERE id = ?")->execute([$alvoId]);
            $pdo->prepare("INSERT INTO log_admins (acao, admin_id, alvo_id) VALUES ('removeu', ?, ?)")->execute([$_SESSION['user_id'], $alvoId]);
            $sucesso = "✅ Administrador Removido Com Sucesso.";
        }
    }
}

// Criar admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $nivel = $_POST['nivel'] ?? 'admin';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif (strlen($senha) < 9 || !preg_match('/[A-Z]/', $senha) || !preg_match('/\d/', $senha)) {
        $erro = "A senha deve ter no mínimo 9 caracteres, uma letra maiúscula e um número.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = "Este email já está registado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, palavra_passe, tipo, criado_por) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $hash, $nivel, $_SESSION['user_id']]);
            $idCriado = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO log_admins (acao, admin_id, alvo_id) VALUES ('criou', ?, ?)")->execute([$_SESSION['user_id'], $idCriado]);
            $sucesso = "✅ Administrador criado com sucesso!";
        }
    }
}

// Logs de admins
$logs = $pdo->query("SELECT l.*, a.nome AS admin_nome, u.nome AS alvo_nome FROM log_admins l JOIN utilizadores a ON l.admin_id = a.id JOIN utilizadores u ON l.alvo_id = u.id ORDER BY l.data DESC LIMIT 20")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">🔐 Gestão de Administradores</h2>

    <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>

    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="🔎 Procurar por nome ou email" value="<?= htmlspecialchars($pesquisa) ?>">
            <button class="btn btn-secondary">Pesquisar</button>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">➕ Criar Novo Admin</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="senha" id="senha" class="form-control" placeholder="Senha segura" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="gerarSenha()">Gerar</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="nivel" class="form-select">
                        <option value="admin">Admin normal</option>
                        <option value="super_admin">Super admin</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-success">Criar Administrador</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">📄 Lista de Administradores</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Nome</th><th>Email</th><th>Nível</th><th>Data</th><th>Ação</th></tr></thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nome']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= $a['tipo'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($a['criado_em'])) ?></td>
                        <td>
                            <?php if ($a['id'] !== $_SESSION['user_id'] && $a['tipo'] !== 'super_admin'): ?>
                                <a href="?remover=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remover este admin?')">🗑</a>
                            <?php elseif ($a['id'] === $_SESSION['user_id']): ?>
                                <span class="text-muted">Sessão atual</span>
                            <?php else: ?>
                                <span class="text-muted">Protegido</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($admins)): ?>
                        <tr><td colspan="5" class="text-center">Nenhum admin encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">📅 Histórico de Ações</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Admin</th><th>Ação</th><th>Alvo</th><th>Data</th></tr></thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['admin_nome']) ?></td>
                        <td><?= ucfirst($log['acao']) ?></td>
                        <td><?= htmlspecialchars($log['alvo_nome']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($log['data'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="4" class="text-center">Nenhum log registado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function gerarSenha() {
    const letras = 'abcdefghijklmnopqrstuvwxyz';
    const maiusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numeros = '0123456789';
    let senha = '';
    senha += maiusculas[Math.floor(Math.random() * maiusculas.length)];
    senha += numeros[Math.floor(Math.random() * numeros.length)];

    while (senha.length < 9) {
        const all = letras + maiusculas + numeros;
        senha += all[Math.floor(Math.random() * all.length)];
    }

    document.getElementById('senha').value = senha;
}
</script>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
