<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php'; 
 

header('Content-Type: application/json'); // Resposta JSON

// Verifica se o usuário está logado (opcional, mas recomendado para carrinho persistente)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado.']);
    exit;
}

// Verifica se o ID do produto foi passado via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do produto inválido.']);
    exit;
}

$id_produto = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verifica se o carrinho existe e se o produto está nele
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho']) || !isset($_SESSION['carrinho'][$id_produto])) {
    echo json_encode(['success' => false, 'message' => 'Produto não encontrado no carrinho.']);
    exit;
}

$quantidade_remover = $_SESSION['carrinho'][$id_produto];

// Verifica se o produto existe no banco e não foi eliminado
try {
    $stmt = $pdo->prepare("SELECT stock FROM produtos WHERE id = ? AND eliminado = 0");
    $stmt->execute([$id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado ou indisponível.']);
        exit;
    }

    // Remove o item do carrinho da sessão
    unset($_SESSION['carrinho'][$id_produto]);

    // Se o carrinho ficar vazio, pode opcionalmente limpar a sessão
    if (empty($_SESSION['carrinho'])) {
        unset($_SESSION['carrinho']);
    }

    // Atualiza o stock no banco (adiciona de volta ao stock, pois foi removido do carrinho)
    // Nota: Isso assume que o stock foi reduzido quando o item foi adicionado ao carrinho.
    // Se o stock só for reduzido na finalização da compra, remova esta parte.
    $novo_stock = $produto['stock'] + $quantidade_remover;
    $update_stmt = $pdo->prepare("UPDATE produtos SET stock = ? WHERE id = ?");
    $update_stmt->execute([$novo_stock, $id_produto]);

    // Registra no histórico de stock (saída revertida, ou entrada por remoção)
    $historico_stmt = $pdo->prepare("INSERT INTO historico_stock (id_produto, quantidade, tipo, nota) VALUES (?, ?, 'entrada', 'Remoção do carrinho - stock restaurado')");
    $historico_stmt->execute([$id_produto, $quantidade_remover]);

    echo json_encode(['success' => true, 'message' => 'Produto removido do carrinho com sucesso.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao remover produto: ' . $e->getMessage()]);
}
?>
