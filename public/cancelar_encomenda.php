<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /public/conta.php");
    exit;
}

$id_encomenda = $_POST['id_encomenda'];
$motivo = trim($_POST['motivo']);
$id_user = $_SESSION['user_id'];

// Verificar se a encomenda é do cliente e está pendente
$stmt = $pdo->prepare("SELECT * FROM encomendas WHERE id = ? AND id_utilizador = ?");
$stmt->execute([$id_encomenda, $id_user]);
$encomenda = $stmt->fetch();

if (!$encomenda || $encomenda['id_estado'] != 1) { // 1 = Pendente
    header("Location: /public/conta.php");
    exit;
}

// Atualizar estado para 'Cancelado' (id_estado = 4)
$pdo->prepare("UPDATE encomendas SET id_estado = 4 WHERE id = ?")->execute([$id_encomenda]);

// Guardar motivo
$pdo->prepare("INSERT INTO cancelamentos (id_encomenda, motivo, cancelado_por, data_cancelamento)
               VALUES (?, ?, 'cliente', NOW())")->execute([$id_encomenda, $motivo]);

header("Location: /public/conta.php");
exit;
