<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['carrinho'])) {
    header("Location: index.php");
    exit;
}
?>

<div class="col-mt-5 filtros">
    <h2>Seleciona o método de pagamento</h2>

    <form action="checkout.php" method="POST" class="mt-4">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="pagamento" id="paypal" value="paypal" checked>
            <label class="form-check-label" for="paypal">PayPal</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="pagamento" id="mbway" value="mbway">
            <label class="form-check-label" for="mbway">MBWay (em desenvolvimento)</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="pagamento" id="referencia" value="referencia">
            <label class="form-check-label" for="referencia">Referência Multibanco (em desenvolvimento)</label>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="radio" name="pagamento" id="cartao" value="cartao">
            <label class="form-check-label" for="cartao">Cartão de Crédito (em desenvolvimento)</label>
        </div>

        <button type="submit" class="btn btn-success">💳 Confirmar e Finalizar</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
