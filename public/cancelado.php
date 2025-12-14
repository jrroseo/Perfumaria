<?php
require_once __DIR__ . '/../includes/header.php';
?>
<body>

         
        <div class='app-content'> 
            <div class='u-s-p-y-60'>
                <div class='section__content'>
                    <div class='container'>
                        <div class='row'>
                            <div class='col-lg-12 col-md-12 u-s-m-b-30'>
                                <div class='empty'>
                                    <div class='empty__wrap'>

                                        <span class='empty__big-text'><img class='aspect__img' src='images/carrinho vazio.png'></span>

                                        <span class='empty__text-1'>Looks like you're in wrong place.</span>

                                        <a class='empty__redirect-link btn--e-brand' href='index.php'>GO TO HOME</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>    
        </div>
<div class="container mt-5 text-center">
    <h2 class="text-danger">⚠️ Pagamento cancelado</h2>
    <p>A sua encomenda foi cancelada ou interrompida. Pode tentar novamente.</p>
    <a href="public/carrinho.php" class="btn btn-secondary mt-3">Voltar ao carrinho</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
