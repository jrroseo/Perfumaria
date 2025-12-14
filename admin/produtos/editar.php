<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header_admin.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de produto inválido.");
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    die("Produto não encontrado.");
}

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
$marcas = $pdo->query("SELECT * FROM marcas")->fetchAll();
$capacidades = $pdo->query("SELECT * FROM capacidades")->fetchAll();
$concentracoes = $pdo->query("SELECT * FROM concentracoes")->fetchAll();
?>

<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-pencil-square text-primary"></i> Editar Produto</h3>

    <form action="atualizar.php" method="post" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
        <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id']) ?>">

        <div class="mb-3">
            <label class="form-label">Nome do Produto</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">DESCRIÇÃO DO PRODUTO</label>
            <textarea name="descricao" class="form-control" rows="4" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="<?= $produto['preco'] ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= $produto['stock'] ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Imagem</label>
                <input type="file" name="imagem" class="form-control" accept=".jpg,.jpeg,.png">
                <?php if ($produto['imagem']): ?>
                    <img src="../../assets/images/<?= htmlspecialchars($produto['imagem']) ?>" width="80" class="mt-2 border rounded" alt="Imagem atual">
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="check-promocao" name="em_promocao" value="1" <?= $produto['em_promocao'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="check-promocao">Em promoção</label>
                </div>
                <div class="mt-2 <?= $produto['em_promocao'] ? '' : 'd-none' ?>" id="campo-desconto">
                    <label class="form-label">Desconto (%)</label>
                    <input type="number" name="desconto" class="form-control" value="<?= $produto['desconto'] ?? 0 ?>" min="0" max="100">
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <select name="id_categoria" class="form-select" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $produto['id_categoria'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Marca</label>
                <div class="input-group">
                    <select name="id_marca" id="marca-select" class="form-select" required>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= $marca['id'] ?>" <?= $produto['id_marca'] == $marca['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($marca['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalMarca">➕</button>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Capacidade</label>
                <select name="id_capacidade" class="form-select" required>
                    <?php foreach ($capacidades as $cap): ?>
                        <option value="<?= $cap['id'] ?>" <?= $produto['id_capacidade'] == $cap['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cap['ml']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Concentração</label>
                <select name="id_concentracao" class="form-select" required>
                    <?php foreach ($concentracoes as $conc): ?>
                        <option value="<?= $conc['id'] ?>" <?= $produto['id_concentracao'] == $conc['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($conc['tipo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Alterações</button>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>

<!-- Modal Nova Marca -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="form-marca">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMarcaLabel">Adicionar Nova Marca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nome" id="nova-marca" class="form-control" placeholder="Nome da marca" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('check-promocao').addEventListener('change', function () {
    document.getElementById('campo-desconto').classList.toggle('d-none', !this.checked);
});

document.getElementById('form-marca').addEventListener('submit', function (e) {
    e.preventDefault();
    const nome = document.getElementById('nova-marca').value;

    fetch('adicionar_marca.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nome=' + encodeURIComponent(nome)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('marca-select');
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = data.nome;
            option.selected = true;
            select.appendChild(option);
            document.getElementById('nova-marca').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalMarca')).hide();
        } else {
            alert('Erro ao adicionar marca.');
        }
    });
});
</script>

<?php 
require_once __DIR__ . '//../includes/footer_admin.php';
?>
