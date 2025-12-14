<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['carrinho']) || !isset($_POST['pagamento'])) {
    header("Location: carrinho.php");
    exit;
}

$_SESSION['checkout'] = [
    'user_id' => $_SESSION['user_id'],
    'carrinho' => $_SESSION['carrinho'],
    'pagamento' => $_POST['pagamento']
];

$metodo = $_POST['pagamento'];

if ($metodo === 'paypal') {
    $total = 0;
    $carrinho = $_SESSION['carrinho'];
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, preco, em_promocao, desconto FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();

    foreach ($produtos as $produto) {
        $id = $produto['id'];
        $qtd = $carrinho[$id];
        $preco = $produto['preco'];

        if ($produto['em_promocao'] && $produto['desconto'] > 0) {
            $preco *= (1 - $produto['desconto'] / 100);
        }

        $total += $preco * $qtd;
    }

    // Simulação de PayPal
    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $paypal_email = "sb-ve4dr39410659@business.example.com"; // Substitui pelo teu email de sandbox

    ?>
    <form action="<?= $paypal_url ?>" method="post" id="paypal-form">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?= $paypal_email ?>">
        <input type="hidden" name="item_name" value="Compra Perfumes Verdes">
        <input type="hidden" name="amount" value="<?= number_format($total, 2, '.', '') ?>">
        <input type="hidden" name="currency_code" value="EUR">
        <input type="hidden" name="return" value="http://localhost/perfumes_verde/public/pagamento_sucesso.php">
        <input type="hidden" name="cancel_return" value="http://localhost/perfumes_verde/public/cancelado.php">
    </form>

    <script>document.getElementById('paypal-form').submit();</script>
    <?php
    exit;
} else {
    header("Location: obrigado.php?metodo=$metodo");
    exit;
}