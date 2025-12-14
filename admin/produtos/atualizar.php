<?php
// atualizar.php na pasta admin/produtos 
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $stock = intval($_POST['stock']);
    $id_categoria = intval($_POST['id_categoria']);
    $id_marca = intval($_POST['id_marca']);
    $id_capacidade = intval($_POST['id_capacidade']);
    $id_concentracao = intval($_POST['id_concentracao']);
    $em_promocao = isset($_POST['em_promocao']) ? 1 : 0;
    $desconto = isset($_POST['desconto']) ? intval($_POST['desconto']) : 0;

    // Buscar imagem atual
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $imagemAtual = $stmt->fetchColumn();
    $imagem = $imagemAtual;

    // Novo upload
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $nomeImagem = basename($_FILES['imagem']['name']);
        $destino = __DIR__ . ''/../assets/images/' . $nomeImagem;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $imagem = $nomeImagem;
        }
    }

    // Atualizar produto
    $stmt = $pdo->prepare("
        UPDATE produtos
        SET nome = ?, descricao = ?, preco = ?, stock = ?, id_categoria = ?, imagem = ?, em_promocao = ?, desconto = ?, id_marca = ?, id_capacidade = ?, id_concentracao = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $nome, $descricao, $preco, $stock, $id_categoria,
        $imagem, $em_promocao, $desconto,
        $id_marca, $id_capacidade, $id_concentracao,
        $id
    ]);

    header("Location: index.php?msg=editado");
    exit;
}
?>
