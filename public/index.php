<?php
/**
 * Front Controller - public/index.php
 * 
 * Ponto de entrada principal da aplicação
 */

// ==============================================
// 1. CARREGAR CONFIGURAÇÕES PRIMEIRO
// ==============================================

// Verificar se já carregou
if (!defined('PUBLIC_INDEX')) {
    define('PUBLIC_INDEX', true);
}

// Carregar configurações (DEVE ser o primeiro)
require_once __DIR__ . '/../config/config_remote.php';

// ==============================================
// 2. INICIAR SESSÃO
// ==============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==============================================
// 3. REMOVER /PUBLIC/ DA URL PARA ROTEAMENTO
// ==============================================

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remover /public_html/public se existir
$cleanUri = str_replace('/public_html/public', '', $requestUri);

// Extrair caminho
$parsed = parse_url($cleanUri);
$path = $parsed['path'] ?? '/';

// ==============================================
// 4. ROTEAMENTO BÁSICO
// ==============================================

$page = 'home.php'; // Página padrão

// Mapeamento de rotas
if ($path === '/' || $path === '' || $path === '/index.php') {
    $page = 'home.php';
} elseif (strpos($path, '/login') === 0) {
    $page = 'login.php';
} elseif (strpos($path, '/register') === 0) {
    $page = 'register.php';
} elseif (strpos($path, '/shop') === 0) {
    $page = 'shop.php';
} elseif (strpos($path, '/product') === 0) {
    $page = 'product-detail.php';
} elseif (strpos($path, '/cart') === 0 || strpos($path, '/carrinho') === 0) {
    $page = 'cart.php';
} elseif (strpos($path, '/admin') === 0) {
    $page = 'admin/dashboard.php';
}

// ==============================================
// 5. CARREGAR DEPENDÊNCIAS
// ==============================================

// Banco de dados
require_once __DIR__ . '/../includes/db.php';

// ==============================================
// 6. CARREGAR E EXIBIR A PÁGINA
// ==============================================

$pagePath = __DIR__ . '/' . $page;

if (file_exists($pagePath)) {
    // Header
    require_once __DIR__ . '/../includes/header.php';
    
    // Conteúdo da página
    require_once $pagePath;
    
    // Footer
    require_once __DIR__ . '/../includes/footer.php';
} else {
    // 404
    http_response_code(404);
    
    if (file_exists(__DIR__ . '/../includes/header.php')) {
        require_once __DIR__ . '/../includes/header.php';
    }
    
    echo '<div class="container text-center py-5">
        <h1 class="display-1">404</h1>
        <h2>Página Não Encontrada</h2>
        <p>A página que você está procurando não existe.</p>
        <a href="' . BASE_URL . '" class="btn btn-primary">Voltar à Página Inicial</a>
    </div>';
    
    if (file_exists(__DIR__ . '/../includes/footer.php')) {
        require_once __DIR__ . '/../includes/footer.php';
    }
}