<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php'; 

header('Content-Type: application/json');

// 🔐 Validar ID do produto recebido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

// 🔎 Verificar se o produto existe e tem stock
$stmt = $pdo->prepare("SELECT id, nome, stock FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    echo json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
    exit;
}

// ✅ Inicializar carrinho se ainda não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// 🧮 Quantidade atual no carrinho
$quantidadeAtual = $_SESSION['carrinho'][$id] ?? 0;

// ⚠️ Verificar stock disponível
if ($quantidadeAtual + 1 > $produto['stock']) {
    echo json_encode([
        'success' => false,
        'message' => 'Não há stock suficiente para adicionar mais deste produto.'
    ]);
    exit;
}

// ➕ Adicionar ao carrinho (incrementar)
$_SESSION['carrinho'][$id] = $quantidadeAtual + 1;

// 🔁 Calcular total de itens no carrinho
$totalItens = array_sum($_SESSION['carrinho']);

echo json_encode([
    'success' => true,
    'count' => $totalItens,
    'message' => "{$produto['nome']} adicionado ao carrinho."
]);
