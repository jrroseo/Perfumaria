<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Carrega configuração (BASE_URL)
require_once __DIR__ . '/../config/config_remote.php';
session_unset();    // Limpa todas as variáveis de sessão
session_destroy();  // Destroi a sessão
header("Location: index.php");
exit;
