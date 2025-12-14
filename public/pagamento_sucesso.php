<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['checkout'], $_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$carrinho = $_SESSION['checkout']['carrinho'];
$user_id = $_SESSION['checkout']['user_id'];
$metodo = $_SESSION['checkout']['pagamento'];

$ids = array_keys($carrinho);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, nome, preco, em_promocao, desconto FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtos = $stmt->fetchAll();

$total = 0;
$precosFinais = [];

foreach ($produtos as $produto) {
    $id = $produto['id'];
    $qtd = $carrinho[$id];
    $preco = $produto['preco'];

    if ($produto['em_promocao'] && $produto['desconto'] > 0) {
        $preco *= (1 - $produto['desconto'] / 100);
    }

    $precosFinais[$id] = $preco;
    $total += $preco * $qtd;
}

// Inserir encomenda agora (após sucesso)
$mapa_pagamentos = ['pix' => 1, 'mbway' => 2, 'referencia' => 3, 'cartao' => 4];
$id_pagamento = $mapa_pagamentos[$metodo] ?? 1;

$stmt = $pdo->prepare("INSERT INTO encomendas (id_utilizador, total, id_pagamento, id_estado, data)
                       VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$user_id, $total, $id_pagamento, 2]); // 2 = Paga
$id_encomenda = $pdo->lastInsertId();

// Guardar produtos da encomenda
$stmt = $pdo->prepare("INSERT INTO produtos_encomenda (id_encomenda, id_produto, quantidade, preco_unitario)
                       VALUES (?, ?, ?, ?)");
foreach ($carrinho as $id => $qtd) {
    $stmt->execute([$id_encomenda, $id, $qtd, $precosFinais[$id]]);
}

// Limpa carrinho e sessão de checkout
unset($_SESSION['carrinho'], $_SESSION['checkout']);

header("Location: obrigado.php?id=$id_encomenda");
exit;
