<?php
// Verificação de sessão e carregamento de dependências
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega configuração
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/footer.php';
// Função de sanitização para PHP 8.1+
function sanitizeInput($input, $type = 'string') {
    if ($input === null || $input === '') {
        return '';
    }
    
    switch($type) {
        case 'string':
            return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT) ?: '';
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) ?: '';
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        default:
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// Validação de parâmetros GET com segurança (PHP 8.1+ compatível)
$pesquisa = sanitizeInput($_GET['pesquisa'] ?? '', 'string');
$marca = sanitizeInput($_GET['marca'] ?? '', 'string');
$categoria = sanitizeInput($_GET['categoria'] ?? '', 'string');
$capacidade = sanitizeInput($_GET['capacidade'] ?? '', 'int');
$concentracao = sanitizeInput($_GET['concentracao'] ?? '', 'string');

// Construção segura da query SQL
$sql = "
SELECT p.*, 
       m.nome AS marca_nome, 
       ca.ml AS capacidade_ml, 
       co.tipo AS concentracao_tipo, 
       cat.nome AS categoria_nome
FROM produtos p
LEFT JOIN marcas m ON p.id_marca = m.id
LEFT JOIN capacidades ca ON p.id_capacidade = ca.id
LEFT JOIN concentracoes co ON p.id_concentracao = co.id
LEFT JOIN categorias cat ON p.id_categoria = cat.id
WHERE p.eliminado = 0 
  AND p.stock > 0  -- Mostrar apenas produtos com estoque disponível
";

$params = [];

// Filtros dinâmicos com prepared statements
if (!empty($pesquisa)) {
    $sql .= " AND (p.nome LIKE ? OR p.descricao LIKE ?)";
    $params[] = "%{$pesquisa}%";
    $params[] = "%{$pesquisa}%";
}

if (!empty($marca)) {
    $sql .= " AND m.nome = ?";
    $params[] = $marca;
}

if (!empty($categoria)) {
    $sql .= " AND cat.nome = ?";
    $params[] = $categoria;
}

if (!empty($capacidade)) {
    $sql .= " AND ca.ml = ?";
    $params[] = $capacidade;
}

if (!empty($concentracao)) {
    $sql .= " AND co.tipo = ?";
    $params[] = $concentracao;
}

// Ordenação com prioridade para promoções
$sql .= " ORDER BY 
    p.em_promocao DESC, 
    p.desconto DESC, 
    p.criado_em DESC, 
    p.nome ASC
LIMIT 50";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Erro na consulta de produtos: " . $e->getMessage());
    $produtos = [];
    echo '<div class="alert alert-danger">Erro ao carregar produtos. Tente novamente.</div>';
}

// Classe CSS geral baseada na categoria principal
$categoriaClasseGlobal = !empty($categoria) ? strtolower(str_replace(' ', '-', $categoria)) : 'todas';
?>

<!--====== App Content ======-->
<div class="app-content">
    <!--====== Section 2 ======-->
    <div class="u-s-p-b-60">
        <!--====== Section Content ======-->
        <div class="section__content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tool-style__group u-s-m-b-8">
                            <span class="js-shop-filter-target" data-side="#side-filter">FILTRAR PRODUTO</span>
                        </div>
                        <div class="filter__grid-wrapper u-s-m-t-30">
                            <div class="row">
                                <?php if (count($produtos) > 0): ?>
                                    <?php foreach ($produtos as $p):
                                        // Calcular preço com desconto
                                        $precoOriginal = floatval($p['preco']);
                                        $desconto = intval($p['desconto'] ?? 0);
                                        $precoFinal = $desconto > 0 ? $precoOriginal * (1 - $desconto / 100) : $precoOriginal;

                                        // Determinar texto de estoque
                                        $estoqueDisponivel = intval($p['stock'] ?? 0);
                                        $statusEstoque = $estoqueDisponivel > 10 ? 'Em estoque' : ($estoqueDisponivel > 0 ? 'Últimas unidades' : 'Esgotado');
                                        $classeEstoque = $estoqueDisponivel > 10 ? 'em-estoque' : ($estoqueDisponivel > 0 ? 'ultimas-unidades' : 'esgotado');

                                        // Definir categoria para exibição
                                        $categoriaDisplay = htmlspecialchars($p['categoria_nome'] ?? $p['tipo'] ?? 'Perfume');
                                        
                                        // Mapeamento de categorias para classes CSS de filtro
                                        $categoriaClasse = '';
                                        if (isset($p['categoria_nome'])) {
                                            switch (strtolower($p['categoria_nome'])) {
                                                case 'perfumes masculino':
                                                    $categoriaClasse = 'masculino';
                                                    break;
                                                case 'perfumes feminino':
                                                    $categoriaClasse = 'feminino';
                                                    break;
                                                case 'perfumes infantil':
                                                    $categoriaClasse = 'infantil';
                                                    break;
                                                case 'corpo':
                                                case 'cosméticos':
                                                    $categoriaClasse = 'cremes';
                                                    break;
                                                case 'maquiagem':
                                                    $categoriaClasse = 'maquiagem';
                                                    break;
                                                case 'cabelos':
                                                    $categoriaClasse = 'cabelos';
                                                    break;
                                                default:
                                                    $categoriaClasse = 'outros';
                                                    break;
                                            }
                                        }
                                    ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 u-s-m-b-30 filter__item <?= $categoriaClasse ?>">
                                            <div class="product-o product-o--hover-on product-o--radius">
                                                <div class="product-o__wrap">
                                                    <a href="<?= BASE_URL ?>/product-detail.php?id=<?= urlencode($p['id']) ?>"
                                                        class="aspect aspect--bg-grey aspect--square u-d-block">
                                                        <?php 
                                                        $imagemPath = !empty($p['imagem']) ? 
                                                            BASE_URL . '/images/product/Perfumaria/' . htmlspecialchars($p['imagem']) : 
                                                            BASE_URL . '/images/placeholder.jpg';
                                                        ?>
                                                        <img class="aspect__img lazyload"
                                                            data-src="<?= $imagemPath ?>"
                                                            src="<?= BASE_URL ?>/images/placeholder-small.jpg"
                                                            alt="<?= htmlspecialchars($p['nome']) ?>"
                                                            title="<?= htmlspecialchars($p['nome']) ?>"
                                                            loading="lazy">
                                                    </a>

                                                    <!-- Tag de promoção -->
                                                    <?php if ($p['em_promocao'] && $desconto > 0): ?>
                                                        <div class="product-tag product-tag--sale">
                                                            -<?= $desconto ?>%
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="product-o__action-wrap">
                                                        <ul class="product-o__action-list">
                                                            <li>
                                                                <a href="<?= BASE_URL ?>/product-detail.php?id=<?= urlencode($p['id']) ?>"
                                                                    data-tooltip="tooltip"
                                                                    data-placement="top"
                                                                    title="Visualizar detalhes">
                                                                    <i class="fas fa-search-plus"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);"
                                                                    class="add-to-wishlist"
                                                                    data-id="<?= htmlspecialchars($p['id']) ?>"
                                                                    data-tooltip="tooltip"
                                                                    data-placement="top"
                                                                    title="Adicionar à lista de desejos">
                                                                    <i class="far fa-heart"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <span class="product-o__category">
                                                    <a href="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($categoriaDisplay) ?>">
                                                        <?= $categoriaDisplay ?>
                                                    </a>
                                                </span>

                                                <span class="product-o__name">
                                                    <a href="<?= BASE_URL ?>/product-detail.php?id=<?= urlencode($p['id']) ?>">
                                                        <?= htmlspecialchars($p['nome']) ?>
                                                    </a>
                                                </span>

                                                <!-- Marca e especificações -->
                                                <div class="product-specs">
                                                    <?php if (!empty($p['marca_nome'])): ?>
                                                        <span class="spec-item"><?= htmlspecialchars($p['marca_nome']) ?></span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($p['capacidade_ml'])): ?>
                                                        <span class="spec-item"><?= htmlspecialchars($p['capacidade_ml']) ?>ml</span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($p['concentracao_tipo'])): ?>
                                                        <span class="spec-item"><?= htmlspecialchars($p['concentracao_tipo']) ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Status do estoque -->
                                                <div class="product-o__stock-status <?= $classeEstoque ?>">
                                                    <i class="fas fa-box"></i> <?= $statusEstoque ?>
                                                </div>

                                                <!-- Preço -->
                                                <div class="product-o__price">
                                                    <?php if ($p['em_promocao'] && $desconto > 0): ?>
                                                        <del class="price-old">R$ <?= number_format($precoOriginal, 2, ',', '.') ?></del>
                                                        <span class="price-new">R$ <?= number_format($precoFinal, 2, ',', '.') ?></span>
                                                    <?php else: ?>
                                                        <span class="price-regular">R$ <?= number_format($precoFinal, 2, ',', '.') ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Botão de adicionar ao carrinho -->
                                                <?php if ($estoqueDisponivel > 0): ?>
                                                    <button class="btn btn--e-brand add-to-cart-btn"
                                                        data-id="<?= htmlspecialchars($p['id']) ?>"
                                                        data-name="<?= htmlspecialchars($p['nome']) ?>"
                                                        data-price="<?= $precoFinal ?>"
                                                        data-stock="<?= $estoqueDisponivel ?>">
                                                        <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn--e-secondary" disabled>
                                                        <i class="fas fa-times-circle"></i> Produto Esgotado
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5">
                                        <div class="no-products-found">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h4 class="text-muted">Nenhum produto encontrado</h4>
                                            <p class="text-muted">Tente ajustar os filtros ou realizar uma nova busca.</p>
                                            <a href="<?= BASE_URL ?>/index.php" class="btn btn--e-brand">
                                                <i class="fas fa-redo"></i> Limpar Filtros
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== End - Section Content ======-->
    </div>
</div>
<!--====== End - App Content ======-->

<!--====== Side FILTROS ======-->
<div class="shop-a" id="side-filter">
    <div class="shop-a__wrap">
        <div class="shop-a__inner gl-scroll">
            <div class="shop-w-master">
                <div class="shop-w-master__sidebar">
                    <div class="u-s-m-b-30">
                        <div class="shop-w">
                            <button class="btn dismiss-button fas fa-times" type="button" aria-label="Fechar filtros"></button>
                            <div class="shop-w__intro-wrap">
                                <form method="GET" id="filtro-form">
                                    <h1 class="shop-w-master__heading u-s-m-b-30">
                                        <i class="fas fa-filter u-s-m-r-8"></i>
                                        <span>FILTROS</span>
                                    </h1>

                                    <!-- Campo de Pesquisa -->
                                    <div class="u-s-m-b-20">
                                        <label for="pesquisa" class="shop-w__h">Pesquisar</label>
                                        <input type="text" 
                                               name="pesquisa" 
                                               id="pesquisa" 
                                               class="input-text input-text--primary-style" 
                                               placeholder="Digite o nome do produto..."
                                               value="<?= htmlspecialchars($pesquisa) ?>">
                                    </div>

                                    <!-- Filtro de Marca -->
                                    <div class="u-s-m-b-20">
                                        <label for="marca" class="shop-w__h">Marca</label>
                                        <select name="marca" id="marca" class="select-box select-box--transparent-b-2">
                                            <option value="">Todas</option>
                                            <?php
                                            try {
                                                $marcasStmt = $pdo->query("SELECT nome FROM marcas ORDER BY nome");
                                                $marcas = $marcasStmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($marcas as $m) {
                                                    $selected = $marca === $m['nome'] ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($m['nome']) . "' $selected>" . 
                                                         htmlspecialchars($m['nome']) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Erro ao carregar marcas: " . $e->getMessage());
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Filtro de Categoria -->
                                    <div class="u-s-m-b-20">
                                        <label for="categoria" class="shop-w__h">Categoria</label>
                                        <select name="categoria" id="categoria" class="select-box select-box--transparent-b-2">
                                            <option value="">Todas</option>
                                            <?php
                                            try {
                                                $categoriasStmt = $pdo->query("SELECT nome FROM categorias ORDER BY nome");
                                                $categorias = $categoriasStmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($categorias as $cat) {
                                                    $selected = $categoria === $cat['nome'] ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($cat['nome']) . "' $selected>" . 
                                                         htmlspecialchars($cat['nome']) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Erro ao carregar categorias: " . $e->getMessage());
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Filtro de Capacidade -->
                                    <div class="u-s-m-b-20">
                                        <label for="capacidade" class="shop-w__h">Capacidade (ml)</label>
                                        <select name="capacidade" id="capacidade" class="select-box select-box--transparent-b-2">
                                            <option value="">Todas</option>
                                            <?php
                                            try {
                                                $capacidadesStmt = $pdo->query("SELECT DISTINCT ml FROM capacidades ORDER BY ml");
                                                $capacidades = $capacidadesStmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($capacidades as $cap) {
                                                    $selected = $capacidade == $cap['ml'] ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($cap['ml']) . "' $selected>" . 
                                                         htmlspecialchars($cap['ml']) . " ml</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Erro ao carregar capacidades: " . $e->getMessage());
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Filtro de Concentração -->
                                    <div class="u-s-m-b-20">
                                        <label for="concentracao" class="shop-w__h">Concentração</label>
                                        <select name="concentracao" id="concentracao" class="select-box select-box--transparent-b-2">
                                            <option value="">Todas</option>
                                            <?php
                                            try {
                                                $concentracoesStmt = $pdo->query("SELECT DISTINCT tipo FROM concentracoes ORDER BY tipo");
                                                $concentracoes = $concentracoesStmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($concentracoes as $conc) {
                                                    $selected = $concentracao === $conc['tipo'] ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($conc['tipo']) . "' $selected>" . 
                                                         htmlspecialchars($conc['tipo']) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("Erro ao carregar concentrações: " . $e->getMessage());
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Botões de Ação -->
                                    <div class="u-s-m-t-30">
                                        <div class="row">
                                            <div class="col-6">
                                                <button type="submit" class="btn btn--e-brand btn-block">
                                                    <i class="fas fa-search"></i> Aplicar
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <a href="<?= BASE_URL ?>/index.php" class="btn btn--e-transparent-secondary btn-block">
                                                    <i class="fas fa-redo"></i> Limpar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== End - Side FILTROS ======-->

<!-- JavaScript para funcionalidades -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar ao carrinho
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;
            const productStock = this.dataset.stock;
            
            if (parseInt(productStock) <= 0) {
                showNotification('Produto esgotado!', 'error');
                return;
            }
            
            // Enviar requisição AJAX para adicionar ao carrinho
            fetch('<?= BASE_URL ?>/includes/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Atualizar contador do carrinho
                    updateCartCount(data.cart_count || data.total_items || 0);
                    
                    // Feedback visual
                    showNotification('Produto adicionado ao carrinho!', 'success');
                    
                    // Desabilitar botão temporariamente
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Adicionado';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 2000);
                } else {
                    showNotification(data.message || 'Erro ao adicionar produto', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro de conexão', 'error');
            });
        });
    });
    
    // Adicionar à lista de desejos
    document.querySelectorAll('.add-to-wishlist').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            
            // Implementar funcionalidade de wishlist
            fetch('<?= BASE_URL ?>/includes/add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + encodeURIComponent(productId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Produto adicionado à lista de desejos!', 'success');
                    this.innerHTML = '<i class="fas fa-heart"></i>';
                } else {
                    showNotification(data.message || 'Erro ao adicionar à lista de desejos', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro de conexão', 'error');
            });
        });
    });
    
    // Fechar filtro sidebar
    document.querySelector('.dismiss-button').addEventListener('click', function() {
        document.getElementById('side-filter').classList.remove('active');
    });
    
    // Abrir filtro sidebar
    document.querySelector('.js-shop-filter-target').addEventListener('click', function() {
        document.getElementById('side-filter').classList.add('active');
    });
    
    // Funções auxiliares
    function updateCartCount(count) {
        const cartCounts = document.querySelectorAll('.cart-count, .cart-badge');
        cartCounts.forEach(element => {
            element.textContent = count;
            element.classList.add('updated');
            setTimeout(() => element.classList.remove('updated'), 500);
        });
    }
    
    function showNotification(message, type = 'info') {
        // Criar elemento de notificação
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;
        notification.innerHTML = `
            <div class="notification__content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification__close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Fechar notificação
        notification.querySelector('.notification__close').addEventListener('click', () => {
            notification.remove();
        });
        
        // Remover automaticamente após 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Estilos para notificações
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease;
        }
        .notification--success { background: #28a745; }
        .notification--error { background: #dc3545; }
        .notification--info { background: #17a2b8; }
        .notification__content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .notification__close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-left: 15px;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php  ?>