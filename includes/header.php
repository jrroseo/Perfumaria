<?php
// includes/header.php

// 1. Verificar sessão e carregar configurações PRIMEIRO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Carregar configuração (DEVE ser o primeiro require)
require_once __DIR__ . '/../config/config_remote.php';

// 3. Carregar banco de dados
require_once __DIR__ . '/db.php';

// 4. Obter nome do usuário se estiver logado
$nomeUtilizador = null;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT nome FROM utilizadores WHERE id = ? AND eliminado = 0");
        $stmt->execute([$_SESSION['user_id']]);
        $res = $stmt->fetch();
        $nomeUtilizador = $res['nome'] ?? null;
    } catch (PDOException $e) {
        error_log("Erro ao buscar nome do usuário: " . $e->getMessage());
    }
}

// 5. Definir título da página
$pageTitle = $titulo ?? 'Perfumaria Sara Corrêa';

// 6. Contar itens do carrinho
$cartCount = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    $cartCount = array_sum($_SESSION['carrinho']);
}
?>

<!DOCTYPE html>
<html class="no-js" lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Loja online de perfumes e cosméticos - Perfumaria Sara Corrêa">
    <meta name="author" content="Perfumaria Sara Corrêa">
    <meta name="keywords" content="perfumes, cosméticos, loja online, comprar perfume, beleza">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/images/favicon.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/images/favicon.png">
    
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.4/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Principal -->
    <?php if (isset($page_css) && is_array($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= $css_file ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        /* Estilos para o preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }
        
        .preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .preloader__wrap {
            text-align: center;
        }
        
        .preloader__img {
            width: 80px;
            height: 80px;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Melhorias para responsividade */
        @media (max-width: 768px) {
            .ah-lg-mode {
                max-width: 90%;
                margin: 0 auto;
            }
            
            .toggle-button {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
        
        /* Estilos para o contador do carrinho */
        .total-item-round {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .toggle-button-shop {
            position: relative;
        }
    </style>
</head>

<body class="config">
    <!-- Script para gerenciar tema -->
    <script>
        // Gerenciar tema (claro/escuro)
        function toggleTheme() {
            const body = document.body;
            const current = localStorage.getItem('theme');
            const nextTheme = current === 'theme-light' ? 'theme-dark' : 'theme-light';
            
            body.classList.remove('theme-dark', 'theme-light');
            body.classList.add(nextTheme);
            localStorage.setItem('theme', nextTheme);
            
            // Disparar evento personalizado
            document.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme: nextTheme }
            }));
        }
        
        // Aplicar tema salvo ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'theme-dark';
            document.body.classList.add(savedTheme);
            
            // Esconder preloader após carregamento
            setTimeout(() => {
                const preloader = document.querySelector('.preloader');
                if (preloader) {
                    preloader.classList.add('hidden');
                }
            }, 500);
        });
        
        // Atualizar contador do carrinho via JavaScript
        function updateCartCount(count) {
            const cartElements = document.querySelectorAll('#cart-count, .cart-count');
            cartElements.forEach(element => {
                element.textContent = count;
                
                // Animação
                element.classList.add('updated');
                setTimeout(() => {
                    element.classList.remove('updated');
                }, 300);
            });
        }
        
        // Inicializar contador do carrinho
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount(<?php echo $cartCount; ?>);
        });
    </script>
    
    <!-- Preloader -->
    <div class="preloader is-active">
        <div class="preloader__wrap">
            <img class="preloader__img" src="<?php echo BASE_URL; ?>/images/preloader.png" alt="Carregando...">
        </div>
    </div>

    <!--====== Main App ======-->
    <div id="app">

        <!--====== Main Header ======-->
        <header class="header--style-1">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!--====== Nav 1 - Usuário Logado ======-->
                <nav class="primary-nav primary-nav-wrapper--border">
                    <div class="container">
                        <div class="primary-nav">
                            <!-- Logo -->
                            <a class="main-logo" href="<?php echo BASE_URL; ?>/">
                                <img src="<?php echo BASE_URL; ?>/images/logo/logo-a.png" alt="Perfumaria Sara Corrêa">
                            </a>
                            
                            <!-- Menu de Usuário -->
                            <div class="menu-init" id="navigation">
                                <button class="btn btn--icon toggle-button toggle-button--secondary far fa-user-circle" type="button">
                                    <span class="d-none d-md-inline">Perfil</span>
                                </button>
                                
                                <!-- Menu Dropdown -->
                                <div class="ah-lg-mode">
                                    <span class="ah-close">✕ Fechar</span>
                                    <ul class="ah-list ah-list--design1 ah-list--link-color-secondary">
                                        <li>
                                            <i class="fas fa-user u-s-m-r-6"></i>
                                            <span>Bem-vindo(a), <?php echo htmlspecialchars($nomeUtilizador ?? 'Utilizador'); ?></span>
                                        </li>
                                        
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/dash-profile.php">
                                                <i class="fas fa-user-cog u-s-m-r-6"></i>
                                                <span>Meu Perfil</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/minhas-compras.php">
                                                <i class="fas fa-shopping-bag u-s-m-r-6"></i>
                                                <span>Minhas Compras</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/logout.php">
                                                <i class="fas fa-sign-out-alt u-s-m-r-6"></i>
                                                <span>Sair</span>
                                            </a>
                                        </li>
                                        
                                        <!-- Redes Sociais -->
                                        <li class="social-links">
                                            <a class="s-whatsapp--color-hover" href="https://wa.me/SEUNUMERO" target="_blank" title="WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a class="s-insta--color-hover" href="#" target="_blank" title="Instagram">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                            <a class="s-fb--color-hover" href="#" target="_blank" title="Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                            <a class="s-youtube--color-hover" href="#" target="_blank" title="YouTube">
                                                <i class="fab fa-youtube"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                <!--====== End - Nav 1 ======-->
            <?php else: ?>
                <!--====== Nav 1 - Visitante ======-->
                <nav class="primary-nav primary-nav-wrapper--border">
                    <div class="container">
                        <div class="primary-nav">
                            <!-- Logo -->
                            <a class="main-logo" href="<?php echo BASE_URL; ?>/">
                                <img src="<?php echo BASE_URL; ?>/images/logo/logo-a.png" alt="Perfumaria Sara Corrêa">
                            </a>
                            
                            <!-- Menu de Visitante -->
                            <div class="menu-init" id="navigation">
                                <button class="btn btn--icon toggle-button toggle-button--secondary far fa-user-circle" type="button">
                                    <span class="d-none d-md-inline">Entrar</span>
                                </button>
                                
                                <!-- Menu Dropdown -->
                                <div class="ah-lg-mode">
                                    <span class="ah-close">✕ Fechar</span>
                                    <ul class="ah-list ah-list--design1 ah-list--link-color-secondary">
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/login.php">
                                                <i class="fas fa-lock u-s-m-r-6"></i>
                                                <span>Entrar na Conta</span>
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/register.php">
                                                <i class="fas fa-user-plus u-s-m-r-6"></i>
                                                <span>Criar Conta</span>
                                            </a>
                                        </li>
                                        
                                        <!-- Redes Sociais -->
                                        <li class="social-links">
                                            <a class="s-whatsapp--color-hover" href="https://wa.me/SEUNUMERO" target="_blank" title="WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a class="s-insta--color-hover" href="#" target="_blank" title="Instagram">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                            <a class="s-fb--color-hover" href="#" target="_blank" title="Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                            <a class="s-youtube--color-hover" href="#" target="_blank" title="YouTube">
                                                <i class="fab fa-youtube"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                <!--====== End - Nav 1 ======-->
            <?php endif; ?>
            
            <!--====== Nav 2 - Menu Principal ======-->
            <nav class="secondary-nav-wrapper">
                <div class="container">
                    <div class="secondary-nav">
                        <!-- Menu Principal -->
                        <div class="menu-init" id="navigation2">
                            <button class="btn btn--icon toggle-button toggle-button--secondary fa fa-bars" type="button">
                                <span class="d-none d-md-inline">MENU</span>
                            </button>
                            
                            <div class="ah-lg-mode">
                                <span class="ah-close">✕ Fechar</span>
                                <ul class="ah-list ah-list--design2 ah-list--link-color-secondary">
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/">
                                            <i class="fas fa-home u-s-m-r-6"></i>
                                            <span>Início</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/shop.php">
                                            <i class="fas fa-store u-s-m-r-6"></i>
                                            <span>Produtos</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/categorias.php">
                                            <i class="fas fa-tags u-s-m-r-6"></i>
                                            <span>Categorias</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/promocoes.php">
                                            <i class="fas fa-percentage u-s-m-r-6"></i>
                                            <span>Promoções</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/faq.php">
                                            <i class="fas fa-question-circle u-s-m-r-6"></i>
                                            <span>FAQ</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/sobre.php">
                                            <i class="fas fa-info-circle u-s-m-r-6"></i>
                                            <span>Sobre Nós</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/contato.php">
                                            <i class="fas fa-envelope u-s-m-r-6"></i>
                                            <span>Contato</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Carrinho de Compras -->
                        <div class="menu-init" id="navigation3">
                            <a href="<?php echo BASE_URL; ?>/carrinho.php" class="cart-link">
                                <button class="btn btn--icon toggle-button toggle-button--secondary fa fa-shopping-cart toggle-button-shop" type="button">
                                    <span class="d-none d-md-inline">Carrinho</span>
                                </button>
                                <span class="total-item-round" id="cart-count"><?php echo $cartCount; ?></span>
                            </a>
                            
                            <div class="ah-lg-mode">
                                <span class="ah-close">✕ Fechar</span>
                                <ul class="ah-list ah-list--design1 ah-list--link-color-secondary">
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/">
                                            <i class="fas fa-home u-c-brand"></i>
                                            <span>Início</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/wishlist.php">
                                            <i class="far fa-heart"></i>
                                            <span>Favoritos</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/carrinho.php" class="mini-cart-shop-link">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span>Carrinho</span>
                                            <span class="total-item-round"><?php echo $cartCount; ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <!--====== End - Nav 2 ======-->
        </header>
        <!--====== End - Main Header ======-->

        <!--====== Main Content ======-->
        <main class="main-content">
            <!-- O conteúdo das páginas será inserido aqui -->