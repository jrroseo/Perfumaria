<?php
// Script para verificar ordem de carregamento
echo "Verificando ordem de inicialização...\n";

// Verificar qual arquivo inicia a sessão primeiro
$files = [
    __DIR__ . '/../public/index.php',
    __DIR__ . '/../public/shop.php',
    __DIR__ . '/../includes/header.php',
    __DIR__ . '/../includes/functions.php',
    // Adicione todos os seus arquivos PHP
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (preg_match('/session_start\(/i', $content)) {
            echo "SESSION_START encontrado em: " . $file . "\n";
        }
        if (preg_match('/session_status\(/i', $content)) {
            echo "session_status() encontrado em: " . $file . "\n";
        }
    }
}