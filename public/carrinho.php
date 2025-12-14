<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
// Ao clicar em "Finalizar Compra"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    } else {
        header("Location: confirmar_checkout.php");
        exit;
    }
}

$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    require_once __DIR__ . '/../includes/heade.php'; // Corrigido: era 'heade.php'
    echo "<div class='app-content'> 
            <div class='u-s-p-y-60'>
                <div class='section__content'>
                    <div class='container'>
                        <div class='row'>
                            <div class='col-lg-12 col-md-12 u-s-m-b-30'>
                                <div class='empty'>
                                     
                                <img src='images/carrinho.png' style='max-width: 300px; height: auto; display: block; margin: 0 auto;'>   
<div class='empty__wrap'>
                                        <span class='empty__text-1'>
Confira todos os produtos disponíveis na home..</span>

                                        <a class='empty__redirect-link btn--e-brand' href='index.php'>RETORNA A home</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>    
        </div>
       
       ";

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$ids = array_keys($carrinho);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    $stmt = $pdo->prepare("SELECT id, nome, descricao, preco, desconto, em_promocao, imagem FROM produtos WHERE id IN ($placeholders) AND eliminado = 0");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Erro ao carregar produtos: " . $e->getMessage() . "</p>";
    exit;
}

$total = 0;
?>

<!--====== App Content ======-->
<div class="app-content">
    <!-- Div para mensagens de alerta -->
    <div id="alert" class="alert d-none" role="alert"></div>

    <!--====== Section 2 ======-->
    <div class="u-s-p-b-60">
        <!--====== Section Intro ======-->
        <div class="section__intro u-s-m-b-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section__text-wrap">
                            <h1 class="section__heading u-c-secondary">CARRINHO DE COMPRAS</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Section Intro ======-->

        <!--====== Section Content ======-->
        <div class="section__content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 u-s-m-b-30">
                        <div class="table-responsive">
                            <table class="table-p">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço Unitário</th>
                                        <th>Quantidade</th>
                                        <th>Subtotal</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                        <?php
                                        $id = $produto['id'];
                                        $qtd = $carrinho[$id] ?? 0;
                                        if ($qtd <= 0) continue; // Pula quantidades inválidas

                                        // Calcula preço final
                                        $precoOriginal = (float) $produto['preco'];
                                        $precoFinal = $precoOriginal;
                                        if (!empty($produto['em_promocao']) && !empty($produto['desconto']) && $produto['desconto'] > 0) {
                                            $precoFinal = $precoOriginal * (1 - $produto['desconto'] / 100);
                                        }

                                        $subtotal = $precoFinal * $qtd;
                                        $total += $subtotal;
                                        ?>
                                        <!--====== Row ======-->
                                        <tr id="linha-<?= $id ?>">
                                            <td>
                                                <div class="table-p__box">
                                                    <div class="table-p__img-wrap">
                                                        <img class="u-img-fluid" src="images/product/Perfumaria/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                                                    </div>
                                                    <div class="table-p__info">
                                                        <span class="table-p__name">
                                                            <a href="product-detail.php?id=<?= $id ?>"><?= htmlspecialchars($produto['nome']) ?></a>
                                                        </span>
                                                        <span class="table-p__category">
                                                            <a href="shop-side-version-2.php"><?= htmlspecialchars($produto['descricao']) ?></a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

<<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 u-s-m-b-30 filter__item <?= htmlspecialchars($categoriaClasse) ?>">
                                                    <div class="product-o product-o--hover-on product-o--radius">
                                                        <div class="product-o__wrap">
                                                            <a href="product-detail.php?id=<?= htmlspecialchars($p['id']) ?>" class="aspect aspect--bg-grey aspect--square u-d-block">
                                                                <img class="aspect__img" src="images/product/Perfumaria/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                                                            </a>
                                                            <div class="product-o__action-wrap">
                                                                <ul class="product-o__action-list">
                                                                    <li>
                                                                        <a href="product-detail.php?id=<?= htmlspecialchars($p['id']) ?>" data-modal-id="#quick-look" data-tooltip="tooltip" data-placement="top" title="Visualizar"><i class="fas fa-search-plus"></i></a>
                                                                    </li>
                                                                    
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <span class="product-o__category">
                                                            <a href="shop-side-version-2.html"><?= htmlspecialchars($p['tipo'] ?? $p['nome_categoria'] ?? 'Categoria') ?></a>
                                                        </span>
                                                        <span class="product-o__name">
                                                            <a href="product-detail.php?id=<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['nome']) ?></a>
                                                        </span>
                                                        <div class="product-o__rating gl-rating-style">
                                                            R$ <?= number_format($subtotal, 2, ',', '.') ?>
                                                            
                                                        </div>
                                                        <?php if (($p['em_promocao'] ?? 0) && ($p['desconto'] ?? 0) > 0): ?>
                                                            <del>R$ <?= number_format($p['preco'], 2, ',', '.') ?></del><br>
                                                            <span class="product-o__price">R$ <?= number_format($p['preco'] * (1 - $p['desconto'] / 100), 2, ',', '.') ?></span>
                                                        <?php else: ?>
                                                            <span class="product-o__price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                                        <?php endif; ?>
                                                        <a href="product-detail.php?id=<?= $p['id'] ?>"class="gl-tag btn--e-transparent-hover-brand-b-2">+ Detalhes</a>
                                                        <a data-modal="modal" data-modal-id="#add-to-cart" data-tooltip="tooltip" class="gl-tag btn--e-transparent-hover-brand-b-2" onclick="removerItem(<?= $id ?>)" title="Remover"><i class="far fa-trash-alt table-p__delete-link"></i></a>
                                                    </div>
                                                </div>
                                            
                                        
                                            <div class="col-12">
                                                <p class="text-muted">Nenhum produto encontrado.</p>
                                            </div>
                                        
                                    </div>

                                            <td>
                                                <?php if (!empty($produto['em_promocao']) && !empty($produto['desconto']) && $produto['desconto'] > 0): ?>
                                                    <span class="text-danger"><del>R$ <?= number_format($precoOriginal, 2, ',', '.') ?></del></span><br>
                                                    <span class="table-p__price">R$ <?= number_format($precoFinal, 2, ',', '.') ?></span>
                                                <?php else: ?>
                                                    <span class="table-p__price">R$ <?= number_format($precoFinal, 2, ',', '.') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="table-p__quantity"><?= (int) $qtd ?></span>
                                            </td>
                                            <td>
                                                <span class="table-p__subtotal">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                                            </td>
                                            <td>
                                                <div class="table-p__del-wrap">
                                                    <a class="far fa-trash-alt table-p__delete-link" href="#" onclick="removerItem(<?= $id ?>)">Remover</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!--====== End - Row ======-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="route-box">
                            <div class="route-box__g1">
                                <a class="route-box__link" href="index.php"><i class="fas fa-long-arrow-alt-left"></i>
                                    <span>CONTINUE COMPRANDO</span></a>
                            </div>
                            <div class="route-box__g2">
                                <a class="route-box__link" href="carrinho.php"><i class="fas fa-trash"></i>
                                    <span>LIMPAR CARRINHO</span></a>
                                <a class="route-box__link" href="carrinho.php"><i class="fas fa-sync"></i>
                                    <span>ATUALIZAR CARRINHO</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Section Content ======-->
    </div>
    <!--====== End - Section 2 ======-->

    <!--====== Seção de Resumo e Checkout ======-->
    <div class="u-s-p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="checkout-summary">
                        <h3>Resumo do Pedido</h3>
                        <p><strong>Total: R$ <?= number_format($total, 2, ',', '.') ?></strong></p>
                        <form method="POST">
                            <button class="btn btn--e-brand-b-2" type="submit" name="finalizar_compra">FINALIZAR COMPRA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====== End - Seção de Resumo ======-->
</div>
  
<script>
    function removerItem(id) {
        if (!confirm('Tens a certeza que queres remover este produto?')) return;

        fetch(`remover_item.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('linha-' + id).remove();
                    mostrarMensagem('success', data.message);

                    if (document.querySelectorAll('tbody tr').length === 0) {
                        window.location.reload(); // Se carrinho vazio, recarrega
                    }
                } else {
                    mostrarMensagem('danger', data.message);
                }
            });
    }

    function mostrarMensagem(tipo, texto) {
        const alert = document.getElementById('alert');
        alert.className = `alert alert-${tipo}`;
        alert.textContent = texto;
        alert.classList.remove('d-none');
        setTimeout(() => alert.classList.add('d-none'), 3000);
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>