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
    echo "<div class='alert alert-danger'>ID de cliente inválido.</div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$id = (int)$_GET['id'];

// Buscar dados combinados
$stmt = $pdo->prepare("
    SELECT u.id, u.nome, u.email, c.morada, c.telefone, c.nif
    FROM utilizadores u
    LEFT JOIN clientes_dados c ON u.id = c.id_utilizador
    WHERE u.id = ? AND u.tipo = 'cliente'
");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    echo "<div class='alert alert-warning'>Cliente não encontrado.</div>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);
    $morada = trim($_POST['morada']);
    $telefone = trim($_POST['telefone']);
    $nif = trim($_POST['nif']);

    // Verifica se o email está em uso por outro cliente
    $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ? AND id != ?");
    $stmt->execute([$novo_email, $id]);

    if ($stmt->rowCount() > 0) {
        $erro = "Já existe um utilizador com este email.";
    } else {
        // Atualiza utilizadores
        $stmt = $pdo->prepare("UPDATE utilizadores SET nome = ?, email = ? WHERE id = ?");
        $stmt->execute([$novo_nome, $novo_email, $id]);

        // Verifica se já existem dados em clientes_dados
        $check = $pdo->prepare("SELECT id FROM clientes_dados WHERE id_utilizador = ?");
        $check->execute([$id]);

        if ($check->rowCount() > 0) {
            // Atualiza dados
            $stmt = $pdo->prepare("UPDATE clientes_dados SET morada = ?, telefone = ?, nif = ? WHERE id_utilizador = ?");
            $stmt->execute([$morada, $telefone, $nif, $id]);
        } else {
            // Insere dados se ainda não existirem
            $stmt = $pdo->prepare("INSERT INTO clientes_dados (id_utilizador, morada, telefone, nif) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $morada, $telefone, $nif]);
        }

        $sucesso = "Dados do cliente atualizados com sucesso.";

        // Atualizar dados no formulário
        $cliente['nome'] = $novo_nome;
        $cliente['email'] = $novo_email;
        $cliente['morada'] = $morada;
        $cliente['telefone'] = $telefone;
        $cliente['nif'] = $nif;
    }
}
?>

<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-pencil-fill text-warning"></i> Editar Cliente</h3>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="post" class="bg-light p-4 rounded shadow-sm">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Morada</label>
            <textarea name="morada" class="form-control"><?= htmlspecialchars($cliente['morada']) ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($cliente['telefone']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">NIF</label>
                <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($cliente['nif']) ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
