<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$nomeUtilizador = null;

if (isset($_SESSION['id'])) {
    $stmt = $pdo->prepare("SELECT nome FROM utilizadores WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $res = $stmt->fetch();
    $nomeUtilizador = $res['nome'] ?? null;
}
?>

        <!--====== App Content ======-->
        <div class="app__content">
            <!--====== Section 2 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="faq">
                                    <h3 class="faq__heading">PERGUNTAS FREQUENTES</h3>
                                    <h3 class="faq__heading">Abaixo estão as perguntas mais frequentes. Você pode encontrar a resposta para si mesmo.</h3>
                                    <p class="faq__text">
Lorem Ipsum é simplesmente um texto fictício da indústria tipográfica e de impressão. Lorem Ipsum tem sido o texto fictício padrão da indústria desde os anos 1500, quando um impressor desconhecido pegou uma bandeja de tipos e os misturou para criar um livro de amostras de tipos. Ele sobreviveu não apenas a cinco séculos, mas também à transição para a editoração eletrônica, permanecendo essencialmente inalterado. Foi popularizado na década de 1960 com o lançamento das folhas Letraset.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 2 ======-->


            <!--====== Section 3 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="faq-accordion-group">
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-1" data-toggle="collapse">How can I get discount coupon ?</a>
                                        <div class="faq__answer collapse" id="faq-1" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-2" data-toggle="collapse">Do I need to create account for buy products ?</a>
                                        <div class="faq__answer collapse" id="faq-2" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-3" data-toggle="collapse">How can I track my order ?</a>
                                        <div class="faq__answer collapse" id="faq-3" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-4" data-toggle="collapse">What is the payment security system ?</a>
                                        <div class="faq__answer collapse" id="faq-4" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-5" data-toggle="collapse">What policy do you have for product sell ?</a>
                                        <div class="faq__answer collapse" id="faq-5" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-6" data-toggle="collapse">How I Return back my product ?</a>
                                        <div class="faq__answer collapse" id="faq-6" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-7" data-toggle="collapse">What Payment Methods are Available ?</a>
                                        <div class="faq__answer collapse" id="faq-7" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                    <div class="faq__list">

                                        <a class="faq__question collapsed" href="#faq-8" data-toggle="collapse">What Shipping Methods are Available ?</a>
                                        <div class="faq__answer collapse" id="faq-8" data-parent="#faq-accordion-group">
                                            <p class="faq__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 3 ======-->
        </div>
        <!--====== End - App Content ======-->
<?php require_once __DIR__ . '/../includes/footer.php';