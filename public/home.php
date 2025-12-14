<?php
// home.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';

// Define CSS e JS específicos para esta página (como array)
$page_css = ['style.css', 'utility.css', 'vendor.css'];
$page_js = ['app.js'];

// Inclui o header UMA VEZ
require_once __DIR__ . '/../includes/header.php';

try {
    // Buscar produtos em promoção
    $stmt_promo = $pdo->prepare("SELECT * FROM produtos WHERE em_promocao = 1 AND eliminado = 0 ORDER BY criado_em DESC LIMIT 3");
    $stmt_promo->execute();
    $promocao = $stmt_promo->fetchAll();

    // Buscar novidades
    $stmt_novidades = $pdo->prepare("SELECT * FROM produtos WHERE eliminado = 0 ORDER BY criado_em DESC LIMIT 3 OFFSET 3");
    $stmt_novidades->execute();
    $novidades = $stmt_novidades->fetchAll();

    // Buscar mais vendidos
    $stmt_vendidos = $pdo->prepare("SELECT * FROM produtos WHERE eliminado = 0 ORDER BY RAND() LIMIT 3");
    $stmt_vendidos->execute();
    $mais_vendidos = $stmt_vendidos->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erro ao buscar produtos: " . $e->getMessage());
    $promocao = $novidades = $mais_vendidos = [];
}

// Script inline para o carrossel
$inline_script = "
$(document).ready(function(){
    $('#hero-slider').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });
});
";
?>

<!--====== App Content ======-->
<div class="app-content">

    <!--====== Primary Slider ======-->
    <div class="s-skeleton s-skeleton--h-600 s-skeleton--bg-grey">
        <div class="owl-carousel primary-style-1" id="hero-slider">
            <div class="hero-slide hero-slide--1">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="slider-content slider-content--animation">
                                <span class="content-span-1 u-c-secondary">Últimas Novidades em Estoque</span>
                                <span class="content-span-2 u-c-secondary">30% de Desconto</span>
                                <span class="content-span-3 u-c-secondary">Encontre os melhores preços. Descubra também os produtos mais vendidos.</span>
                                <span class="content-span-4 u-c-secondary">A partir de
                                    <span class="u-c-brand">R$150.00</span>
                                </span>
                                <a class="shop-now-link btn--e-brand" href="shop-side-version-2.php">COMPRE AGORA</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-slide hero-slide--2">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="slider-content slider-content--animation">
                                <span class="content-span-1 u-c-secondary">Últimas Novidades em Estoque</span>
                                <span class="content-span-2 u-c-secondary">30% de Desconto</span>
                                <span class="content-span-3 u-c-secondary">Encontre os melhores preços. Descubra também os produtos mais vendidos.</span>
                                <span class="content-span-4 u-c-secondary">A partir de
                                    <span class="u-c-brand">R$150.00</span>
                                </span>
                                <a class="shop-now-link btn--e-brand" href="shop-side-version-2.php">COMPRE AGORA</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-slide hero-slide--3">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="slider-content slider-content--animation">
                                <span class="content-span-1 u-c-white">Perfumaria Avon</span>
                                <span class="content-span-2 u-c-white">10% de Desconto</span>
                                <span class="content-span-3 u-c-white">Perfumes, Colônias, Desodorantes e Mais</span>
                                <span class="content-span-4 u-c-white">Começando em
                                    <span class="u-c-brand">R$380.00</span>
                                </span>
                                <a class="shop-now-link btn--e-brand" href="shop-side-version-2.php">COMPRE AGORA</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====== End - Primary Slider ======-->

    <!--====== Section 2 ======-->
    <div class="u-s-p-b-60">
        <!--====== Section Intro ======-->
        <div class="section__intro u-s-m-b-16">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section__text-wrap">
                            <h1 class="section__heading u-c-secondary u-s-m-b-12">Qual Perfume Você Precisa?</h1>
                            <span class="section__span u-c-silver">Perfumaria Sara Carrea convida a um encontro de sensações, para momentos especiais, para se sentir bem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Section Intro ======-->

        <!--====== Seção Promoções ======-->
        <div class="section__content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="section__subheading u-c-secondary u-s-m-b-24">Promoções Especiais</h2>
                        <div class="filter__grid-wrapper u-s-m-t-30">
                            <div class="row">
                                <?php if (empty($promocao)): ?>
                                    <div class="col-12">
                                        <p class="u-c-silver u-s-m-t-20">Nenhum produto em promoção no momento.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($promocao as $p): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 u-s-m-b-30 filter__item masculino">
                                            <div class="product-o product-o--hover-on product-o--radius">
                                                <div class="product-o__wrap">
                                                    <a href="product-detail.php?id=<?= $p['id'] ?>" class="aspect aspect--bg-grey aspect--square u-d-block">
                                                        <img class="aspect__img" 
                                                             src="<?= BASE_URL ?>/images/product/Perfumaria/<?= htmlspecialchars($p['imagem']) ?>" 
                                                             alt="<?= htmlspecialchars($p['nome']) ?> - Perfumaria Sara Carrea">
                                                    </a>
                                                </div>
                                                <span class="product-o__category">
                                                    <a href="shop-side-version-2.php">Perfumaria</a>
                                                </span>
                                                <span class="product-o__name">
                                                    <?= htmlspecialchars($p['nome']) ?>
                                                </span>
                                                <div class="product-o__rating gl-rating-style">
                                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                                    <span class="product-o__review">(23)</span>
                                                </div>
                                                <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                                    <del>R$ <?= number_format($p['preco'], 2, ',', '.') ?></del><br>
                                                    <span class="product-o__price u-c-brand">
                                                        R$ <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2, ',', '.') ?>
                                                    </span>
                                                    <span class="product-o__discount">-<?= $p['desconto'] ?>%</span>
                                                <?php else: ?>
                                                    <span class="product-o__price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                                <?php endif; ?>
                                                <a href="product-detail.php?id=<?= $p['id'] ?>" class="gl-tag btn--e-transparent-hover-brand-b-2">Ver produto</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Seção Promoções ======-->

        <!--====== Seção Novidades ======-->
        <div class="section__content u-s-p-t-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="section__subheading u-c-secondary u-s-m-b-24">Novidades</h2>
                        <div class="filter__grid-wrapper u-s-m-t-30">
                            <div class="row">
                                <?php if (empty($novidades)): ?>
                                    <div class="col-12">
                                        <p class="u-c-silver u-s-m-t-20">Nenhuma novidade no momento.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($novidades as $produto): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 u-s-m-b-30 filter__item masculino">
                                            <div class="product-o product-o--hover-on product-o--radius">
                                                <div class="product-o__wrap">
                                                    <a href="product-detail.php?id=<?= $produto['id'] ?>" class="aspect aspect--bg-grey aspect--square u-d-block">
                                                        <img class="aspect__img" 
                                                             src="<?= BASE_URL ?>/images/product/Perfumaria/<?= htmlspecialchars($produto['imagem']) ?>" 
                                                             alt="<?= htmlspecialchars($produto['nome']) ?> - Perfumaria Sara Carrea">
                                                    </a>
                                                </div>
                                                <span class="product-o__category">
                                                    <a href="shop-side-version-2.php">Perfumaria</a>
                                                </span>
                                                <span class="product-o__name">
                                                    <?= htmlspecialchars($produto['nome']) ?>
                                                </span>
                                                <div class="product-o__rating gl-rating-style">
                                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                                    <span class="product-o__review">(18)</span>
                                                </div>
                                                <span class="product-o__price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                                <a href="product-detail.php?id=<?= $produto['id'] ?>" class="gl-tag btn--e-transparent-hover-brand-b-2">Ver produto</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Seção Novidades ======-->

        <!--====== Seção Mais Vendidos ======-->
        <div class="section__content u-s-p-t-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="section__subheading u-c-secondary u-s-m-b-24">Mais Vendidos</h2>
                        <div class="filter__grid-wrapper u-s-m-t-30">
                            <div class="row">
                                <?php if (empty($mais_vendidos)): ?>
                                    <div class="col-12">
                                        <p class="u-c-silver u-s-m-t-20">Nenhum produto para exibir.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($mais_vendidos as $produto): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 u-s-m-b-30 filter__item masculino">
                                            <div class="product-o product-o--hover-on product-o--radius">
                                                <div class="product-o__wrap">
                                                    <a href="product-detail.php?id=<?= $produto['id'] ?>" class="aspect aspect--bg-grey aspect--square u-d-block">
                                                        <img class="aspect__img" 
                                                             src="<?= BASE_URL ?>/images/product/Perfumaria/<?= htmlspecialchars($produto['imagem']) ?>" 
                                                             alt="<?= htmlspecialchars($produto['nome']) ?> - Perfumaria Sara Carrea">
                                                    </a>
                                                </div>
                                                <span class="product-o__category">
                                                    <a href="shop-side-version-2.php">Perfumaria</a>
                                                </span>
                                                <span class="product-o__name">
                                                    <?= htmlspecialchars($produto['nome']) ?>
                                                </span>
                                                <div class="product-o__rating gl-rating-style">
                                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                                    <span class="product-o__review">(35)</span>
                                                </div>
                                                <span class="product-o__price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                                <a href="product-detail.php?id=<?= $produto['id'] ?>" class="gl-tag btn--e-transparent-hover-brand-b-2">Ver produto</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Seção Mais Vendidos ======-->
    </div>
    <!--====== End - Section 2 ======-->
</div>
<!--====== End - App Content ======-->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</div>
<!--====== End - Main App ======-->