<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
<?php require_once __DIR__ . '/../conexao.php'; // atualize se necessário

// Verificar se o cliente está logado
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: /../login.php');
    exit;
}

$idCliente = $_SESSION['id_utilizador'];

// Buscar encomendas do cliente
$sql = "
    SELECT e.id, e.total, e.criado_em,
           ep.estado AS estado_encomenda,
           mp.nome AS metodo_pagamento
    FROM encomendas e
    LEFT JOIN estados_encomenda ep ON e.id_estado = ep.id
    LEFT JOIN metodos_pagamento mp ON e.id_pagamento = mp.id
    WHERE e.id_utilizador = ?
    ORDER BY e.criado_em DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Minhas Encomendas</title>
    <link rel="stylesheet" href="../estilos.css"> <!-- se tiver -->
</head>
<body>
    <h1>Histórico de Encomendas</h1>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Pagamento</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($encomenda = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($encomenda['id']) ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($encomenda['criado_em'])) ?></td>
                        <td><?= number_format($encomenda['total'], 2) ?></td>
                        <td><?= htmlspecialchars($encomenda['estado_encomenda']) ?></td>
                        <td><?= htmlspecialchars($encomenda['metodo_pagamento']) ?></td>
                        <td><a href="detalhes.php?id=<?= $encomenda['id'] ?>">Ver</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não fez nenhuma encomenda.</p>
    <?php endif; ?>

    <a href="../index.php">Voltar à home</a>
</body>
</html>
