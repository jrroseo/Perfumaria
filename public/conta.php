<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';


if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5'>
            <p>Precisas de iniciar sessão para ver a tua conta. 
            <a href='login.php' class='text-success'>Entrar</a></p>
          </div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$id = $_SESSION['user_id'];
$mensagem = '';
$encomendas = [];  // Inicialize para evitar erros

// Proteção CSRF: Gere token se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    // Buscar dados do usuário para personalizar HTML
    $stmt_user = $pdo->prepare("SELECT nome, email FROM utilizadores WHERE id = ?");
    $stmt_user->execute([$id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // Buscar encomendas do utilizador (remova 'eliminada' se não adicionar coluna)
    $stmt = $pdo->prepare("
        SELECT e.id, e.data, e.total, e.id_estado, s.estado AS nome_estado
        FROM encomendas e
        LEFT JOIN estados_encomenda s ON e.id_estado = s.id
        WHERE e.id_utilizador = ? AND e.eliminada = 0  -- Adicione coluna 'eliminada' no banco se usar
        ORDER BY e.data DESC
    ");
    $stmt->execute([$id]);
    $encomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cancelar encomenda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'], $_POST['cancelar_id'], $_POST['motivo'])) {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $mensagem = "<div class='alert alert-danger'>Token CSRF inválido.</div>";
        } else {
            $cancelar_id = (int) $_POST['cancelar_id'];
            $motivo = trim($_POST['motivo']);

            if ($motivo === '') {
                $mensagem = "<div class='alert alert-danger'>Motivo é obrigatório.</div>";
            } else {
                // Verifique se a encomenda existe, pertence ao usuário e está em estado cancelável (ex.: 1 = pendente)
                $stmt_check = $pdo->prepare("SELECT id_estado FROM encomendas WHERE id = ? AND id_utilizador = ?");
                $stmt_check->execute([$cancelar_id, $id]);
                $encomenda = $stmt_check->fetch();

                if ($encomenda && $encomenda['id_estado'] == 1) {  // Só cancela se estado = 1
                    $stmt = $pdo->prepare("UPDATE encomendas SET id_estado = 5, motivo_cancelamento = ? WHERE id = ? AND id_utilizador = ? AND id_estado = 1");  // Corrigido: Use 5 (Cancelado) ou 6 após adicionar
                    $stmt->execute([$motivo, $cancelar_id, $id]);
                    $mensagem = "<div class='alert alert-warning text-center'>❌ Encomenda #$cancelar_id cancelada.</div>";
                    // Recarregue encomendas após cancelamento
                    $stmt->execute([$id]);
                    $encomendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $mensagem = "<div class='alert alert-danger'>Encomenda não pode ser cancelada.</div>";
                }
            }
        }
    }
} catch (PDOException $e) {
    $mensagem = "<div class='alert alert-danger'>Erro interno.</div>";
    error_log($e->getMessage());  // Ative logging
}
?>

<body class="config">
    <div class="preloader is-active">
        <div class="preloader__wrap">
            <img class="preloader__img" src="images/preloader.png" alt="">
        </div>
    </div>

    <!--====== Main App ======-->
    <div id="app">
        <!--====== App Content ======-->
        <div class="app-content">
            <!--====== Section 2 ======-->
            <div class="u-s-p-b-60">
                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="dash">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-3 col-md-12">
                                    <!--====== Dashboard Features ======-->
                                    <div class="dash__box dash__box--bg-white dash__box--shadow u-s-m-b-30">
                                        <div class="dash__pad-1">
                                            <span class="dash__text u-s-m-b-16">Olá, <?= htmlspecialchars($user['nome'] ?? 'Usuário') ?></span> <!-- Dinâmico -->
                                            <ul class="dash__f-list">
                                                <li><a class="dash-active" href="dashboard.html">Gerenciar minha conta</a></li>
                                                <!-- Outros links -->
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Outros elementos -->
                                </div>
                                <div class="col-lg-9 col-md-12">
                                    <!-- Mensagem de erro/sucesso -->
                                    <?= $mensagem ?>

                                    <!-- Perfil dinâmico -->
                                    <div class="dash__box dash__box--shadow dash__box--radius dash__box--bg-white u-s-m-b-30">
                                        <div class="dash__pad-2">
                                            <h1 class="dash__h1 u-s-m-b-14">Gerenciar minha conta</h1>
                                            <span class="dash__text u-s-m-b-30">No painel Minha Conta...</span>
                                            <div class="row">
                                                <div class="col-lg-4 u-s-m-b-30">
                                                    <div class="dash__box dash__box--bg-grey dash__box--shadow-2 u-h-100">
                                                        <div class="dash__pad-3">
                                                            <h2 class="dash__h2 u-s-m-b-8">PERFIL PESSOAL</h2>
                                                            <span class="dash__text"><?= htmlspecialchars($user['nome']) ?></span>
                                                            <span class="dash__text"><?= htmlspecialchars($user['email']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Outros blocos -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabela de encomendas dinâmica -->
                                    <div class="dash__box dash__box--shadow dash__box--bg-white dash__box--radius">
                                        <h2 class="dash__h2 u-s-p-xy-20">ENCOMENDAS RECENTES</h2>
                                        <div class="dash__table-wrap gl-scroll">
                                            <table class="dash__table">
                                                <thead>
                                                    <tr>
                                                        <th>Número da Encomenda</th>
                                                        <th>Data</th>
                                                        <th>Status</th>
                                                        <th>Total</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($encomendas as $encomenda): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($encomenda['id']) ?></td>
                                                            <td><?= htmlspecialchars($encomenda['data']) ?></td>
                                                            <td><?= htmlspecialchars($encomenda['nome_estado']) ?></td>
                                                            <td>€<?= htmlspecialchars(number_format($encomenda['total'], 2)) ?></td>
                                                            <td>
                                                                <?php if ($encomenda['id_estado'] == 1): ?>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <input type="hidden" name="cancelar_id" value="<?= $encomenda['id'] ?>">
                                                                        <input type="text" name="motivo" placeholder="Motivo" required>
                                                                        <button type="submit">Cancelar</button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    N/A
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>