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
    header("Location: index.php?erro=id_invalido");
    exit;
}

$id = (int)$_GET['id'];

// Verificar se o cliente existe
$stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE id = ? AND tipo = 'cliente' AND eliminado = 0");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    header("Location: index.php?erro=nao_encontrado");
    exit;
}

// Marcar como eliminado
$stmt = $pdo->prepare("UPDATE utilizadores SET eliminado = 1 WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?removido=ok");
exit;
?>
