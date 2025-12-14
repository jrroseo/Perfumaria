<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: /../public/login.php);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?erro=cliente_invalido");
    exit;
}

$id = (int)$_GET['id'];

// Verificar se o cliente existe
$stmt = $pdo->prepare("SELECT bloqueado FROM utilizadores WHERE id = ? AND tipo = 'cliente'");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: index.php?erro=nao_encontrado");
    exit;
}

// Inverter o estado de bloqueio
$novoEstado = $cliente['bloqueado'] ? 0 : 1;

$stmt = $pdo->prepare("UPDATE utilizadores SET bloqueado = ? WHERE id = ?");
$stmt->execute([$novoEstado, $id]);

header("Location: index.php?bloqueado=" . ($novoEstado ? 'sim' : 'nao'));
exit;
?>
