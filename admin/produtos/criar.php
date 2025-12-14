<?php
require_once __DIR__ . '..'/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'admin') {
    echo "<p class='text-danger'>Acesso negado.</p>";
    require_once __DIR__ . '//../includes/footer_admin.php';
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? null;
    $preco = floatval($_POST['preco']);
    $stock = intval($_POST['stock']);
    $id_categoria = $_POST['id_categoria'] ?? null;
    $id_marca = $_POST['id_marca'] ?? null;
    $id_capacidade = $_POST['id_capacidade'] ?? null;
    $id_concentracao = $_POST['id_concentracao'] ?? null;
    $em_promocao = isset($_POST['em_promocao']) ? 1 : 0;
    $desconto = isset($_POST['desconto']) ? intval($_POST['desconto']) : 0;

    if ($stock < 0) {
        $erro = '❌ O stock não pode ser negativo.';
    } elseif ($em_promocao && ($desconto < 0 || $desconto > 100)) {
        $erro = '❌ O desconto deve estar entre 0% e 100%.';
    } else {
        $imagem = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $nomeOriginal = $_FILES['imagem']['name'];
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png'];

            if (!in_array($extensao, $permitidas)) {
                $erro = '❌ Apenas imagens .jpg, .jpeg e .png são permitidas.';
            } else {
                $nomeFinal = uniqid('img_') . '.' . $extensao;
                $destino = __DIR__ . ''/../assets/images/' . $nomeFinal;
                move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
                $imagem = $nomeFinal;
            }
        }

        if (!$erro) {
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, descricao, preco, stock, id_categoria, id_marca, id_capacidade, id_concentracao, imagem, em_promocao, desconto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nome, $descricao, $preco, $stock,
                $id_categoria, $id_marca, $id_capacidade,
                $id_concentracao, $imagem, $em_promocao, $desconto
            ]);

            $sucesso = '✅ Produto adicionado com sucesso.';
        }
    }
}
?>

<div class="container mt-5 text-dark">
    <h2>➕ Adicionar Produto</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mt-4 bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label class="form-label">Nome do Produto</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">DESCRIÇÃO DO PRODUTO</label>
            <textarea name="descricao" class="form-control"></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Imagem</label>
                <input type="file" name="imagem" class="form-control" accept=".jpg,.jpeg,.png">
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="em_promocao" id="togglePromocao" onchange="document.getElementById('descontoBox').classList.toggle('d-none')">
                <label class="form-check-label" for="togglePromocao">Produto em promoção</label>
            </div>
        </div>

        <div class="mb-3 d-none" id="descontoBox">
            <label class="form-label">Desconto (%)</label>
            <input type="number" name="desconto" class="form-control" min="0" max="100">
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Categoria</label>
                <select name="id_categoria" class="form-select">
                    <option value="">-- Nenhuma --</option>
                    <?php
                    $categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
                    foreach ($categorias as $c) {
                        echo "<option value='{$c['id']}'>{$c['nome']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Marca</label>
                <select name="id_marca" class="form-select">
                    <option value="">-- Nenhuma --</option>
                    <?php
                    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nome")->fetchAll();
                    foreach ($marcas as $m) {
                        echo "<option value='{$m['id']}'>{$m['nome']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Capacidade</label>
                <select name="id_capacidade" class="form-select">
                    <option value="">-- Nenhuma --</option>
                    <?php
                    $caps = $pdo->query("SELECT * FROM capacidades")->fetchAll();
                    foreach ($caps as $c) {
                        echo "<option value='{$c['id']}'>{$c['ml']} ml</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Concentração</label>
                <select name="id_concentracao" class="form-select">
                    <option value="">-- Nenhuma --</option>
                    <?php
                    $concs = $pdo->query("SELECT * FROM concentracoes")->fetchAll();
                    foreach ($concs as $co) {
                        echo "<option value='{$co['id']}'>{$co['tipo']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Adicionar Produto</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once __DIR__ . '//../includes/footer_admin.php'; ?>
