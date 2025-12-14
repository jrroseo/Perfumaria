<!--====== Modal Section ======-->





<!--====== Modal Section ======-->


<!--====== Visão geral Modal ======-->
<div class="modal fade" id="quick-look">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal--shadow">

            <button class="btn dismiss-button fas fa-times" type="button" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-5">

                        <!--====== Product Breadcrumb ======-->

                        <!--====== End - Product Breadcrumb ======-->


                        <!--====== Detalhes do produto ======-->
                        <div class="pd u-s-m-b-30">
                            <div class="pd-wrap">
                                <div id="js-product-detail-modal">
                                    <div>

                                        <a href="product-detail.php?id=<?= htmlspecialchars($p['id']) ?>">
                                            <img class="u-img-fluid" src="images/product/Perfumaria/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!--====== End - Detalhes do produto ======-->
                    </div>
                    <div class="col-lg-7">

                        <!--====== Product Right Side Details ======-->
                        <div class="pd-detail">
                            <div>

                                <span class="pd-detail__name"><?= htmlspecialchars($produto['nome']) ?></span>
                            </div>
                            <div>
                                <div class="pd-detail__inline">

                                    <span class="pd-detail__price">R$6.99</span>

                                    <span class="pd-detail__discount">(76% OFF)</span><del class="pd-detail__del">R$28.97</del>
                                </div>
                            </div>

                            <div class="u-s-m-b-15">
                                <div class="pd-detail__inline">

                                    <span class="pd-detail__stock">200 em estoque</span>


                                </div>
                            </div>
                            <div class="u-s-m-b-15">

                                <span class="pd-detail__preview-desc">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</span>
                            </div>

                            <div class="u-s-m-b-15">
                                <ul class="pd-social-list">
                                    <li>

                                        <a class="s-fb--color-hover" href="#"><i class="fab fa-facebook-f"></i></a>
                                    </li>

                                    <li>

                                        <a class="s-insta--color-hover" href="#"><i class="fab fa-instagram"></i></a>
                                    </li>
                                    <li>

                                        <a class="s-wa--color-hover" href="#"><i class="fab fa-whatsapp"></i></a>
                                    </li>

                                    </li>
                                </ul>
                            </div>
                            <div class="u-s-m-b-15">
                                <form class="pd-detail__form">
                                    <div class="pd-detail-inline-2">

                                        <div class="u-s-m-b-15">

                                            <button class="btn btn--e-brand-b-2" type="submit">Adicionar ao Carrinho</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                        <!--====== End - Product Right Side Details ======-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== End - Visão geral Modal ======-->


<!--====== Add to Cart Modal ======-->
<div class="modal fade" id="add-to-cart">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-radius modal-shadow">

            <button class="btn dismiss-button fas fa-times" type="button" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="success u-s-m-b-30">
                            <div class="success__text-wrap"><i class="fas fa-check"></i>

                                <span>Item adicionado com sucesso!!</span>
                            </div>


                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="s-option">


                            <div class="s-option__link-box">

                                <a class="s-option__link btn--e-white-brand-shadow" data-dismiss="modal">CONTINUE COMPRANDO</a>

                                <a class="s-option__link btn--e-white-brand-shadow" href="carrinho.php">VER CARRINHO</a>

                                <a class="s-option__link btn--e-brand-shadow" href="checkout.html">PROSSEGUIR PARA O PAGAMENTO</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== End - Add to Cart Modal ======-->
<!--====== End - Modal Section ======-->
</div>
<!--====== End - Main App ======-->

<!--====== Add to Cart Modal ======-->
<div class="modal fade" id="add-to-cart">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-radius modal-shadow">

            <button class="btn dismiss-button fas fa-times" type="button" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="success u-s-m-b-30">
                            <div class="success__text-wrap"><i class="fas fa-check"></i>

                                <span>Item adicionado com sucesso!!</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="s-option">

                            <span class="s-option__text">1 item no seu carrinho</span>
                            <div class="s-option__link-box">

                                <a class="s-option__link btn--e-white-brand-shadow" data-dismiss="modal">CONTINUE COMPRANDO</a>

                                <a class="s-option__link btn--e-white-brand-shadow" href="carrinho.php">VER CARRINHO</a>

                                <a class="s-option__link btn--e-brand-shadow" href="checkout.php">PROSSEGUIR PARA O PAGAMENTO</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== End - Add to Cart Modal ======-->


<!--====== Newsletter Subscribe Modal
        <div class="modal fade new-l" id="newsletter-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal--shadow">

                    <button class="btn new-l__dismiss fas fa-times" type="button" data-dismiss="modal"></button>
                    <div class="modal-body">
                        <div class="row u-s-m-x-0">
                            <div class="col-lg-6 new-l__col-1 u-s-p-x-0">

                                <a class="new-l__img-wrap u-d-block" href="shop-side-version-2.html">

                                    <img class="u-img-fluid u-d-block" src="images/newsletter/newsletter.jpg" alt=""></a>
                            </div>
                            <div class="col-lg-6 new-l__col-2">
                                <div class="new-l__section u-s-m-t-30">
                                    <div class="u-s-m-b-8 new-l--center">
                                        <h3 class="new-l__h3">Site em construção</h3>
                                    </div>
                                    <div class="u-s-m-b-30 new-l--center">
                                        <p class="new-l__p1">O site em construção permitem que os visitantes obtenham mais informações sobre o produto, mantenham-se atualizados sobre um lançamento. inscrevam-se no Email e entrem em contato.

.</p>
                                    </div>
                                    <form class="new-l__form">
                                        <div class="u-s-m-b-15">

                                            <input class="news-l__input" type="text" placeholder="E-mail Address">
                                        </div>
                                        <div class="u-s-m-b-15">

                                            <button class="btn btn--e-brand-b-2" type="submit">Inscreva-se!</button>
                                        </div>
                                    </form>
                                    
                                    <div class="u-s-m-b-15 new-l--center">

                                        <a class="new-l__link" data-dismiss="modal">Não, obrigado.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> ======-->
<!--====== End - Newsletter Subscribe Modal ======-->
<!--====== End - Modal Section ======-->
<?php
// includes/footer.php

// Verificar se BASE_URL está definido
if (!defined('BASE_URL')) {
    die('Erro: BASE_URL não está definido. Verifique o config_remote.php');
}
?>

<!--====== Main Footer ======-->
<footer class="footer">
    <div class="outer-footer">
        <div class="container">
            <div class="row">
                <!-- Coluna 1: Contato -->
                <div class="col-lg-4 col-md-6">
                    <div class="outer-footer__content u-s-m-b-40">
                        <span class="outer-footer__content-title">Contate-nos</span>
                        
                        <div class="outer-footer__text-wrap">
                            <i class="fas fa-home"></i>
                            <span>Rua Além do Arco-Íris, 12345<br>Bairro da Esperança<br>Cidade: O Mágico de Oz</span>
                        </div>
                        
                        <div class="outer-footer__text-wrap">
                            <i class="fas fa-phone-volume"></i>
                            <span>(11) 1234-5678</span>
                        </div>
                        
                        <div class="outer-footer__text-wrap">
                            <i class="far fa-envelope"></i>
                            <span>contato@perfumariasara.com</span>
                        </div>
                        
                        <div class="outer-footer__social">
                            <ul class="pd-social-list">
                                <li>
                                    <a class="s-fb--color-hover" href="https://facebook.com" target="_blank" title="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="s-wa--color-hover" href="https://wa.me/551112345678" target="_blank" title="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="s-insta--color-hover" href="https://instagram.com" target="_blank" title="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Coluna 2: Informações -->
                <div class="col-lg-4 col-md-6">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="outer-footer__content u-s-m-b-40">
                                <span class="outer-footer__content-title">Informações</span>
                                <div class="outer-footer__list-wrap">
                                    <ul>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/carrinho.php">
                                                <i class="fas fa-shopping-cart u-s-m-r-6"></i>Carrinho
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/minha-conta.php">
                                                <i class="fas fa-user u-s-m-r-6"></i>Minha Conta
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/marcas.php">
                                                <i class="fas fa-tags u-s-m-r-6"></i>Marcas
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/pagamentos.php">
                                                <i class="fas fa-credit-card u-s-m-r-6"></i>Pagamentos
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/shop.php">
                                                <i class="fas fa-store u-s-m-r-6"></i>Loja
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 col-md-6">
                            <div class="outer-footer__content u-s-m-b-40">
                                <span class="outer-footer__content-title">Nossa Empresa</span>
                                <div class="outer-footer__list-wrap">
                                    <ul>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/sobre.php">
                                                <i class="fas fa-info-circle u-s-m-r-6"></i>Sobre Nós
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/contato.php">
                                                <i class="fas fa-envelope u-s-m-r-6"></i>Contato
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/sitemap.php">
                                                <i class="fas fa-sitemap u-s-m-r-6"></i>Mapa do Site
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/entregas.php">
                                                <i class="fas fa-truck u-s-m-r-6"></i>Entregas
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo BASE_URL; ?>/lojas.php">
                                                <i class="fas fa-map-marker-alt u-s-m-r-6"></i>Nossas Lojas
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coluna 3: Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <div class="outer-footer__content u-s-m-b-40">
                        <span class="outer-footer__content-title">Newsletter</span>
                        <p>Cadastre-se para receber ofertas exclusivas e novidades!</p>
                        
                        <form id="newsletter-form" class="newsletter-form">
                            <div class="u-s-m-b-15">
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" placeholder="Seu e-mail" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-check u-s-m-b-15">
                                <input type="checkbox" class="form-check-input" id="newsletter-agree" required>
                                <label class="form-check-label" for="newsletter-agree">
                                    Concordo em receber comunicações
                                </label>
                            </div>
                        </form>
                        
                        <div id="newsletter-message" class="d-none"></div>
                        
                        <div class="payment-methods u-s-m-t-20">
                            <p class="u-s-m-b-10">Formas de Pagamento:</p>
                            <div class="payment-icons">
                                <img src="<?php echo BASE_URL; ?>/images/payments/visa.png" alt="Visa" width="40">
                                <img src="<?php echo BASE_URL; ?>/images/payments/mastercard.png" alt="Mastercard" width="40">
                                <img src="<?php echo BASE_URL; ?>/images/payments/paypal.png" alt="PayPal" width="40">
                                <img src="<?php echo BASE_URL; ?>/images/payments/pix.png" alt="PIX" width="40">
                                <img src="<?php echo BASE_URL; ?>/images/payments/boleto.png" alt="Boleto" width="40">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Inferior -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="copyright">
                        &copy; <?php echo date('Y'); ?> Perfumaria Sara Corrêa. Todos os direitos reservados.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="footer-links">
                        <a href="<?php echo BASE_URL; ?>/termos.php">Termos de Uso</a> |
                        <a href="<?php echo BASE_URL; ?>/privacidade.php">Política de Privacidade</a> |
                        <a href="<?php echo BASE_URL; ?>/trocas.php">Trocas e Devoluções</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!--====== Scripts ======-->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.4/dist/sweetalert2.min.js"></script>

<!-- JS Principal -->
    <?php if (isset($page_js) && is_array($page_js)): ?>
        <?php foreach ($page_js as $js_file): ?>
            <script src="<?= BASE_URL ?>/js/<?= $js_file ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Scripts inline se necessário -->
    <?php if (isset($inline_script) && !empty($inline_script)): ?>
        <script>
            $(document).ready(function() {
                <?= $inline_script ?>
            });
        </script>
    <?php endif; ?>

<script>
    // Adicionar ao carrinho
    document.addEventListener('DOMContentLoaded', function() {
        // Delegar evento para todos os botões "add-to-cart"
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-cart')) {
                e.preventDefault();
                const button = e.target.closest('.add-to-cart');
                const productId = button.dataset.id;
                const productName = button.dataset.name || 'Produto';
                
                if (!productId) {
                    Swal.fire({
                        icon: "error",
                        title: "Produto inválido",
                        text: "Tente novamente."
                    });
                    return;
                }
                
                // Mostrar loading
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                // Enviar requisição
                fetch('<?php echo BASE_URL; ?>/includes/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta do servidor');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Atualizar contador do carrinho
                        updateCartCount(data.count);
                        
                        // Mostrar notificação
                        Swal.fire({
                            icon: "success",
                            title: "Adicionado ao carrinho!",
                            text: productName + " foi adicionado ao seu carrinho.",
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Erro",
                            text: data.message || 'Não foi possível adicionar ao carrinho.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: "error",
                        title: "Erro de conexão",
                        text: "Tente novamente mais tarde."
                    });
                })
                .finally(() => {
                    // Restaurar botão
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        });
        
        // Formulário de newsletter
        const newsletterForm = document.getElementById('newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const messageDiv = document.getElementById('newsletter-message');
                
                fetch('<?php echo BASE_URL; ?>/includes/newsletter_subscribe.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    messageDiv.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('d-none');
                    
                    if (data.success) {
                        newsletterForm.reset();
                        setTimeout(() => messageDiv.classList.add('d-none'), 5000);
                    }
                })
                .catch(() => {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = 'Erro ao processar sua inscrição.';
                    messageDiv.classList.remove('d-none');
                });
            });
        }
        
        // Função para atualizar contador do carrinho
        function updateCartCount(count) {
            const cartElements = document.querySelectorAll('#cart-count, .cart-count, .total-item-round');
            cartElements.forEach(element => {
                element.textContent = count;
                
                // Animação
                element.classList.add('updated');
                setTimeout(() => {
                    element.classList.remove('updated');
                }, 300);
            });
            
            // Se carrinho está vazio, esconder contador
            if (count === 0) {
                cartElements.forEach(el => {
                    if (el.classList.contains('total-item-round')) {
                        el.style.display = 'none';
                    }
                });
            } else {
                cartElements.forEach(el => {
                    if (el.classList.contains('total-item-round')) {
                        el.style.display = 'flex';
                    }
                });
            }
        }
        
        // Verificar carrinho periodicamente
        function checkCart() {
            fetch('<?php echo BASE_URL; ?>/includes/get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartCount(data.count);
                    }
                })
                .catch(error => console.error('Erro ao verificar carrinho:', error));
        }
        
        // Verificar a cada 30 segundos
        setInterval(checkCart, 30000);
        
        // Inicializar tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Google Analytics (substitua UA-XXXXX-Y pelo seu ID)
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-XXXXX-Y');
</script>

<!-- Google Analytics Script -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-Y"></script>

<!--====== Noscript ======-->
<noscript>
    <div class="app-setting">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="app-setting__wrap">
                        <h1 class="app-setting__h1">JavaScript está desabilitado no seu navegador.</h1>
                        <span class="app-setting__text">
                            Por favor, habilite o JavaScript no seu navegador ou atualize para um navegador compatível com JavaScript.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</noscript>
</body>
</html>