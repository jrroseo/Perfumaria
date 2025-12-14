<?php
// debug-url.php
echo "<h1>Debug de URLs</h1>";

echo "<h2>Variáveis do Servidor:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h2>Caminhos do Sistema:</h2>";
echo "<pre>";
echo "__DIR__: " . __DIR__ . "\n";
echo "getcwd(): " . getcwd() . "\n";
echo "</pre>";

echo "<h2>Teste de Redirecionamento:</h2>";
$testUrls = [
    '/',
    '/public_html/',
    '/public_html/public/',
    '/public/',
    '/public_html/index.php'
];

foreach ($testUrls as $url) {
    echo "<a href='$url'>$url</a><br>";
}
?>