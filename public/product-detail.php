<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';


// Validação de entrada mais rigorosa
$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    echo "<p class='text-danger'>Produto não encontrado ou ID inválido.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Buscar o produto (adicione 'eliminado = 0' para produtos ativos)
    $stmt = $pdo->prepare("
        SELECT p.*, 
               m.nome AS marca_nome, 
               ca.ml AS capacidade_ml, 
               co.tipo AS concentracao_tipo,
               c.nome AS categoria_nome
        FROM produtos p
        LEFT JOIN marcas m ON p.id_marca = m.id
        LEFT JOIN capacidades ca ON p.id_capacidade = ca.id
        LEFT JOIN concentracoes co ON p.id_concentracao = co.id
        LEFT JOIN categorias c ON p.id_categoria = c.id
        WHERE p.id = ? AND p.eliminado = 0
    ");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        echo "<p class='text-danger'>Produto não encontrado.</p>";
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Buscar produtos relacionados (otimize: use subquery para evitar RAND() em grandes tabelas)
    $rel_stmt = $pdo->prepare("
        SELECT id, nome, imagem, preco 
        FROM produtos 
        WHERE id_categoria = ? AND id != ? AND eliminado = 0
        ORDER BY id DESC  -- Substitua RAND() por algo determinístico para performance
        LIMIT 3
    ");
    $rel_stmt->execute([$p['id_categoria'], $p['id']]);
    $relacionados = $rel_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro interno ao carregar produto.</p>";
    error_log($e->getMessage());  // Log erro
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Função auxiliar para formatar preço com locale (R$ brasileiro)
setlocale(LC_MONETARY, 'pt_BR');
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}
?>

<!--====== App Content ======-->
<div class="app-content">
    <!--====== Section 1 ======-->
    <div class="u-s-p-t-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <!--====== Product Detail Zoom ======-->
                    <div class="pd u-s-m-b-30">
                        <div class="slider-fouc pd-wrap">
                            <div id="pd-o-initiate">
                                <div class="pd-o-img-wrap" data-src="images/product/Perfumaria/<?= htmlspecialchars(basename($p['imagem'])) ?>">
                                    <img class="u-img-fluid" 
             src="<?= PRODUCT_IMAGES_URL ?>/<?= htmlspecialchars($p['imagem']) ?>" 
             alt="<?= htmlspecialchars($p['nome']) ?>" data-zoom-image="images/product/Perfumaria/<?= htmlspecialchars(basename($p['imagem'])) ?>">
                                </div>
                            </div>
                            <span class="pd-text">Clique para ampliar</span>
                        </div>
                        <div class="u-s-m-t-15">
                            <div class="slider-fouc">
                                <div id="pd-o-thumbnail">
                                    <div class="u-s-m-b-15">
                                        <ul class="pd-social-list">
                                            <li><a class="s-fb--color-hover" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a class="s-insta--color-hover" href="#"><i class="fab fa-instagram"></i></a></li>
                                            <li><a class="s-wa--color-hover" href="#"><i class="fab fa-whatsapp"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--====== End - Product Detail Zoom ======-->
                </div>
                <div class="col-lg-7">
                    <!--====== Product Right Side Details ======-->
                    <div class="pd-detail">
                        <div class="pd-detail__inline">
                            <?php if ($p['em_promocao'] && $p['desconto'] > 0): ?>
                                <span class="pd-detail__price"><?= formatarPreco($p['preco'] * (1 - $p['desconto'] / 100)) ?></span>
                                <span class="pd-detail__discount">(<?= $p['desconto'] ?>% OFF)</span>
                                <del class="pd-detail__del"><?= formatarPreco($p['preco']) ?></del>
                            <?php else: ?>
                                <span class="pd-detail__price"><?= formatarPreco($p['preco']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="u-s-m-b-15">
    <div class="pd-detail__inline">
        <div class="pd-detail__stock">
            <?php if ($p['stock'] > 0): ?>
                <span class="text-success">Em estoque: <?= $p['stock'] ?> unidades</span>
            <?php else: ?>
                <span class="text-danger">Fora de estoque</span>
            <?php endif; ?>
        </div>
        <div class="col-md-7">  <!-- Corrigido: Contexto melhorado -->
            <p class="pd-detail__description"><?= htmlspecialchars($p['descricao']) ?></p>
            <ul class="pd-detail__specs">
                <?php if ($p['marca_nome']): ?>
                    <li><strong>Marca:</strong> <?= htmlspecialchars($p['marca_nome']) ?></li>
                <?php endif; ?>
                <?php if ($p['categoria_nome']): ?>
                    <li><strong>Categoria:</strong> <?= htmlspecialchars($p['categoria_nome']) ?></li>
                <?php endif; ?>
                <?php if ($p['capacidade_ml']): ?>
                    <li><strong>Capacidade:</strong> <?= htmlspecialchars($p['capacidade_ml']) ?> ml</li>
                <?php endif; ?>
                <?php if ($p['concentracao_tipo']): ?>
                    <li><strong>Concentração:</strong> <?= htmlspecialchars($p['concentracao_tipo']) ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="u-s-m-b-15">
        <form class="pd-detail__form">
            <div class="pd-detail-inline-2">
                <div class="u-s-m-b-15">
                    <!-- Adicione campos como quantidade se necessário -->
                </div>
                <div class="u-s-m-b-15">
                    <button class="btn btn--e-brand-b-2 add-to-cart" data-id="<?= htmlspecialchars($p['id']) ?>" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>Adicionar ao Carrinho</button>
                </div>
            </div>
        </form>
        <div id="cart-message"></div>  <!-- Adicionado: Para mensagens AJAX -->
        <div id="alert-message" class="alert d-none mt-3"></div>  <!-- Adicionado: Para alertas Bootstrap -->
    </div>
</div>
                    </div>
                    <!--====== End - Product Right Side Details ======-->
                </div>
            </div>
        </div>
    </div>

    <!--====== Product Detail Tab ======-->
    <div class="u-s-p-y-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="pd-tab">
                        <div class="u-s-m-b-30">
                            <ul class="nav pd-tab__list">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#pd-desc">DESCRIÇÃO DO PRODUTO</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <!--====== Tab 1 ======-->
                            <div class="tab-pane fade show active" id="pd-desc">
                                <div class="pd-tab__desc">
                                    <div class="u-s-m-b-15">
                                        <p><?= htmlspecialchars($p['descricao'] ?? 'Descrição não disponível.') ?></p>  <!-- Dinâmico -->
                                    </div>
                                    <?php if (!empty($p['video_url'])): ?>  <!-- Assuma campo 'video_url' no banco -->
                                        <div class="u-s-m-b-30">
                                            <iframe src="<?= htmlspecialchars($p['video_url']) ?>" allowfullscreen></iframe>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--====== End - Tab 1 ======-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== End - App Content ======-->



<?php require_once __DIR__ . '/../includes/footer.php'; ?>