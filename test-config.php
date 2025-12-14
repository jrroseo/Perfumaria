<?php
// test-config.php
echo "<h1>Teste de Configuração</h1>";

// Testar carregamento do config
echo "<h2>1. Testando config_remote.php</h2>";
try {
    require_once __DIR__ . '/config/config_remote.php';
    echo "✅ config_remote.php carregado com sucesso!<br>";
    
    echo "<strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NÃO DEFINIDO') . "<br>";
    echo "<strong>ENVIRONMENT:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'NÃO DEFINIDO') . "<br>";
    echo "<strong>DEBUG_MODE:</strong> " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'true' : 'false') : 'NÃO DEFINIDO') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro ao carregar config_remote.php: " . $e->getMessage();
}

// Testar .env
echo "<h2>2. Testando arquivo .env</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ Arquivo .env encontrado<br>";
    echo "<pre>" . htmlspecialchars(file_get_contents($envFile)) . "</pre>";
} else {
    echo "❌ Arquivo .env NÃO encontrado em: " . $envFile;
}

// Testar sessão
echo "<h2>3. Testando sessão</h2>";
echo "Status da sessão: " . session_status() . "<br>";
echo "PHP_SESSION_NONE: " . PHP_SESSION_NONE . "<br>";
echo "PHP_SESSION_ACTIVE: " . PHP_SESSION_ACTIVE . "<br>";

// Testar caminhos
echo "<h2>4. Testando caminhos</h2>";
echo "ROOT_PATH: " . (defined('ROOT_PATH') ? ROOT_PATH : 'NÃO DEFINIDO') . "<br>";
echo "PUBLIC_PATH: " . (defined('PUBLIC_PATH') ? PUBLIC_PATH : 'NÃO DEFINIDO') . "<br>";
echo "__DIR__: " . __DIR__ . "<br>";
?>