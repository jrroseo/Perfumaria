<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';




?>

    <!--====== App Content ======-->
        <div class="app-content">

            <!--====== Section 3 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 u-s-m-b-30">
                                <div class="contact-o u-h-100">
                                    <div class="contact-o__wrap">
                                        <div class="contact-o__icon"><i class="fas fa-phone-volume"></i></div>

                                        <span class="contact-o__info-text-1">Vamos conversar por telefone.</span>

                                        <span class="contact-o__info-text-2"> (11) 1234-5678</span>

                                        <span class="contact-o__info-text-2"> (11) 1234-5678</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 u-s-m-b-30">
                                <div class="contact-o u-h-100">
                                    <div class="contact-o__wrap">
                                        <div class="contact-o__icon"><i class="fas fa-map-marker-alt"></i></div>

                                        <span class="contact-o__info-text-1">NOSSA LOCALIZAÇÃO</span>

                                        <span class="contact-o__info-text-2"> Rua: Além do Arco-Íris, 12345 Bairro: Da Esperança </span>

                                        <span class="contact-o__info-text-2">Cidade: O Mágico de Oz</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 u-s-m-b-30">
                                <div class="contact-o u-h-100">
                                    <div class="contact-o__wrap">
                                        <div class="contact-o__icon"><i class="far fa-clock"></i></div>

                                        <span class="contact-o__info-text-1">HORÁRIO DE FUNCIONAMENTO</span>

                                        <span class="contact-o__info-text-2">5 dias por semana</span>

                                        <span class="contact-o__info-text-2">Das 9h às 19h</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 3 ======-->


            <!--====== Section 4 ======-->
            <div class="u-s-p-b-60">

                <!--====== Section Content ======-->
                <div class="section__content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="contact-area u-h-100">
                                    <div class="contact-area__heading">
                                        <h2>Entre em contato</h2>
                                    </div>
                                    <form class="contact-f" method="post" action="index.html">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 u-h-100">
                                                <div class="u-s-m-b-30">

                                                    <label for="c-name"></label>

                                                    <input class="input-text input-text--border-radius input-text--primary-style" type="text" id="c-name" placeholder="Name (Obrigatório)" required></div>
                                                <div class="u-s-m-b-30">

                                                    <label for="c-email"></label>

                                                    <input class="input-text input-text--border-radius input-text--primary-style" type="text" id="c-email" placeholder="Email (Obrigatório)" required></div>
                                                <div class="u-s-m-b-30">

                                                    <label for="c-subject"></label>

                                                    <input class="input-text input-text--border-radius input-text--primary-style" type="text" id="c-subject" placeholder="Assunto (Obrigatório)" required></div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 u-h-100">
                                                <div class="u-s-m-b-30">

                                                    <label for="c-message"></label><textarea class="text-area text-area--border-radius text-area--primary-style" id="c-message" placeholder="Escreva uma Mensagem (Obrigatório)" required></textarea></div>
                                            </div>
                                            <div class="col-lg-12">

                                                <button class="btn btn--e-brand-b-2" type="submit">Enviar Mensagem</button></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--====== End - Section Content ======-->
            </div>
            <!--====== End - Section 4 ======-->
        </div>
        <!--====== End - App Content ======-->

        <?php require_once __DIR__ . '/../includes/footer.php'; ?>