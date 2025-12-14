<?php
// fix-urls.php
session_start();

echo "<h1>Correção de URLs</h1>";

// Testar diferentes métodos para obter BASE_URL
$methods = [];

// Método 1: Via .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $methods['.env'] = $env['APP_URL'] ?? 'N/A';
}

// Método 2: Detecção automática
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$methods['auto'] = $protocol . $host;

// Método 3: Com script path
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$methods['script'] = $protocol . $host . $scriptDir;

// Método 4: Sem /public
$cleanDir = str_replace('/public', '', $scriptDir);
$methods['clean'] = $protocol . $host . $cleanDir;

echo "<h2>Possíveis BASE_URLs:</h2>";
foreach ($methods as $name => $url) {
    echo "<strong>$name:</strong> $url<br>";
}

echo "<h2>Recomendação:</h2>";
echo "Use no .env: <code>APP_URL=http://localhost/public_html</code>";
echo "<br>E nos links use: <code>&lt;a href=\"&lt;?php echo BASE_URL; ?&gt;/pagina.php\"&gt;</code>";
?>