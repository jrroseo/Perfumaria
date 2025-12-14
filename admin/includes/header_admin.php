<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';

// Base path para navegação correta
$adminBase = dirname($_SERVER['PHP_SELF']);
$adminBase = str_replace(['/produtos', '/clientes', '/encomendas', '/notificacoes','/stock'], '', $adminBase);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Admin - Perfumes Verdes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $adminBase ?>/../assets/css/style_admin.css">
    <link rel="icon" href="/saracorrea/assets/favicon.ico" type="image/x-icon">
</head>
<body>

<script>
function toggleTheme() {
    const body = document.body;
    const current = localStorage.getItem('theme');
    const next = current === 'theme-light' ? 'theme-dark' : 'theme-light';
    body.classList.remove('theme-dark', 'theme-light');
    body.classList.add(next);
    localStorage.setItem('theme', next);
}
window.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('theme') || 'theme-dark';
    document.body.classList.add(saved);
});
</script>

<!-- Alertas -->
<div class="position-fixed top-0 start-50 translate-middle-x mt-3 z-3" style="max-width: 400px;">
    <div id="alert-message" class="alert d-none text-center py-2 px-3" role="alert" style="font-size: 0.9rem;"></div>
</div>

<!-- NAV ADMIN -->
<header class="bg-dark py-2 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo fs-4 fw-bold text-success">
            <a href="<?= $adminBase ?>/dashboard.php" class="text-decoration-none text-success">🌿 Perfumes Verdes - Admin</a>
        </div>

        <nav class="d-flex align-items-center gap-3 position-relative">
            <a href="<?= $adminBase ?>/dashboard.php" class="text-light text-decoration-none">Dashboard</a>
            <a href="<?= $adminBase ?>/produtos/index.php" class="text-light text-decoration-none">Produtos</a>
            <a href="<?= $adminBase ?>/clientes/index.php" class="text-light text-decoration-none">Clientes</a>
            <a href="<?= $adminBase ?>/encomendas/index.php" class="text-light text-decoration-none">Encomendas</a>
            <a href="<?= $adminBase ?>/stock/index.php" class="text-light text-decoration-none">Stock</a>
            <a href="/../index.php" class="text-light text-decoration-none">Ver home</a>
            <a href="<?= $adminBase ?>/../public/logout.php" class="text-light text-decoration-none">Sair</a>
            <button onclick="toggleTheme()" class="btn btn-sm btn-outline-success">🌗 Tema</button>

            <!-- Dropdown AJAX -->
            <div class="dropdown position-relative">
                <button class="btn btn-sm btn-outline-light position-relative" id="notificacoesBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill"></i>
                    <span id="badge-notif" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"></span>
            </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" id="notificacoes-lista" style="width: 320px;">
                <li class="dropdown-item text-muted">Carregando...</li>
                </ul>
            </div>

        </nav>
    </div>
</header>

<!-- Javascript notificacoes -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('notificacoesBtn');
    const lista = document.getElementById('notificacoes-lista');
    const badge = document.getElementById('badge-notif');

    dropdown.addEventListener('mouseenter', carregarNotificacoes);

    function carregarNotificacoes() {
        fetch("<?= $adminBase ?>/notificacoes/notificacoes_ajax.php")
            .then(res => res.json())
            .then(data => {
                lista.innerHTML = '';
                if (data.length === 0) {
                    lista.innerHTML = '<li class="dropdown-item text-muted">Sem notificações</li>';
                    badge.classList.add('d-none');
                } else {
                    badge.textContent = data.filter(n => n.lida == 0).length;
                    badge.classList.remove('d-none');
                    data.forEach(n => {
                        const li = document.createElement('li');
                        li.className = 'dropdown-item small d-flex justify-content-between align-items-start';
                        li.innerHTML = `
                            <div class="flex-grow-1 pe-2">
                                <strong>R${n.titulo}</strong><br>
                                <small>R${n.mensagem.substring(0, 60)}...</small>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                ${n.lida == 0 ? `<button class="btn btn-sm btn-success mb-1" onclick="marcarComoLida(${n.id})">✔️</button>` : ''}
                                <button class="btn btn-sm btn-danger" onclick="removerNotificacao(${n.id})">🗑️</button>
                            </div>
                        `;
                        lista.appendChild(li);
                    });
                    lista.innerHTML += `<li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center text-primary" href="<?= $adminBase ?>/notificacoes/index.php">📋 Ver todas</a></li>`;
                }
            });
    }

    window.removerNotificacao = function (id) {
        fetch("<?= $adminBase ?>/notificacoes/remover_ajax.php?id=" + id).then(() => carregarNotificacoes());
    };

    window.marcarComoLida = function (id) {
        fetch("<?= $adminBase ?>/notificacoes/marcar_lida_ajax.php?id=" + id).then(() => carregarNotificacoes());
    };
});
</script>


<main class="container mt-4">
