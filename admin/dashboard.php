<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/header_admin.php';
require_once __DIR__ . '/notificacoes/gerar_stock_baixo.php';


if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<div class='container mt-5'><p class='text-danger'>Acesso negado. Área exclusiva para administradores.</p></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// 🔢 Estatísticas
$totalProdutos = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalClientes = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo = 'cliente' AND eliminado = 0")->fetchColumn();
$totalEncomendas = $pdo->query("SELECT COUNT(*) FROM encomendas")->fetchColumn();
$totalFaturado = $pdo->query("SELECT SUM(total) FROM encomendas")->fetchColumn();
$totalFaturado = $totalFaturado ? number_format($totalFaturado, 2) : "0.00";
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📊 Painel de Administração</h2>
        <a href="/index.php" class="btn btn-secondary">🔙 Voltar à home</a>
    </div>

    <div class="alert alert-info">
        👋 Bem-vindo, administrador! Use os blocos abaixo para gerir o conteúdo da home.
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Produtos</h5>
                    <p class="fs-3"><?= $totalProdutos ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Clientes</h5>
                    <p class="fs-3"><?= $totalClientes ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Encomendas</h5>
                    <p class="fs-3"><?= $totalEncomendas ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">Faturado (R$)</h5>
                    <p class="fs-3"><?= $totalFaturado ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações rápidas -->
    <div class="row">
        <!-- Produtos -->
        <div class="col-md-4 mb-4">
            <a href="produtos/index.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">📦 Produtos</h4>
                        <p class="card-text">Adicionar, editar ou remover perfumes.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Clientes -->
        <div class="col-md-4 mb-4">
            <a href="clientes/index.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">👤 Clientes</h4>
                        <p class="card-text">Gerir contas de utilizadores.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Encomendas -->
        <div class="col-md-4 mb-4">
            <a href="encomendas/index.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">🧾 Encomendas</h4>
                        <p class="card-text">Ver e atualizar o estado das encomendas.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Newsletter -->
        <div class="col-md-4 mb-4">
            <a href="newsletter.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">📧 Newsletter</h4>
                        <p class="card-text">Gerir subscrições dos clientes.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Estatísticas -->
        <div class="col-md-4 mb-4">
            <a href="estatisticas.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">📈 Estatísticas</h4>
                        <p class="card-text">Resumo de vendas e produtos mais vendidos.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Admins -->
        <div class="col-md-4 mb-4">
            <a href="admins.php" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <h4 class="card-title">🔐 Administradores</h4>
                    <p class="card-text">Gerir contas de administradores.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer_admin.php'; ?>
