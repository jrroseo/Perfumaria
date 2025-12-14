<?php
/**
 * Redirecionador Principal - public_html/index.php
 * 
 * Este arquivo redireciona tudo para a pasta public/
 */

// ==============================================
// 1. CONFIGURAÇÕES BÁSICAS
// ==============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==============================================
// 2. ANALISAR A URL ATUAL
// ==============================================

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsed = parse_url($requestUri);
$path = $parsed['path'] ?? '/';
$query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

// ==============================================
// 3. VERIFICAR SE JÁ ESTÁ EM /PUBLIC/
// ==============================================

if (strpos($path, '/public/') === 0 || $path === '/public') {
    // Se já está tentando acessar /public/, mostrar erro
    http_response_code(400);
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acesso Incorreto</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                background: #f8f9fa;
                text-align: center;
            }
            .container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #dc3545;
            }
            code {
                background: #f1f1f1;
                padding: 2px 5px;
                border-radius: 3px;
            }
            .btn {
                display: inline-block;
                margin-top: 15px;
                padding: 10px 20px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>❌ Acesso Incorreto</h1>
            <p>Você está tentando acessar diretamente a pasta <code>public/</code>.</p>
            <p><strong>URL atual:</strong> <code><?php echo htmlspecialchars($requestUri); ?></code></p>
            <p><strong>Como acessar corretamente:</strong></p>
            <p>Acesse: <a href="/public_html/">http://localhost/public_html/</a></p>
            <p>O sistema redirecionará automaticamente para a página correta.</p>
            <a href="/public_html/" class="btn">↩️ Voltar para a página inicial</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==============================================
// 4. CONSTRUIR REDIRECIONAMENTO CORRETO
// ==============================================

// Para Laragon com public_html, redirecionar sempre para /public_html/public/
// Exceto se já estiver em /public_html/public/

$redirectTo = '';

// Se estiver na raiz ou em /public_html
if ($path === '/' || $path === '' || $path === '/index.php' || $path === '/public_html' || strpos($path, '/public_html/') === 0) {
    // Remover /public_html do início se existir
    $cleanPath = str_replace('/public_html', '', $path);
    $redirectTo = '/public_html/public' . $cleanPath . $query;
} else {
    // Para outras URLs
    $redirectTo = '/public_html/public' . $path . $query;
}

// ==============================================
// 5. VERIFICAR SE A PASTA PUBLIC EXISTE
// ==============================================

$publicDir = __DIR__ . '/public';
$publicIndex = $publicDir . '/index.php';

if (!is_dir($publicDir)) {
    die('<div style="padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
        <h3>Erro de Configuração</h3>
        <p>A pasta <code>public/</code> não foi encontrada.</p>
        <p>Crie a pasta <code>public</code> dentro de <code>' . __DIR__ . '</code></p>
    </div>');
}

if (!file_exists($publicIndex)) {
    die('<div style="padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
        <h3>Erro de Configuração</h3>
        <p>O arquivo <code>public/index.php</code> não foi encontrado.</p>
        <p>Crie o arquivo <code>index.php</code> dentro da pasta <code>public</code></p>
    </div>');
}

// ==============================================
// 6. EXECUTAR REDIRECIONAMENTO
// ==============================================

// Limpar qualquer output
while (ob_get_level()) {
    ob_end_clean();
}

// Redirecionar (302 temporário)
header('Location: ' . $redirectTo, true, 302);

// HTML de fallback
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2;url=<?php echo htmlspecialchars($redirectTo); ?>">
    <title>Redirecionando...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        a {
            color: white;
            text-decoration: underline;
            font-weight: bold;
        }
        .details {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9em;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h2>Redirecionando...</h2>
        <p>Você está sendo redirecionado para a página correta.</p>
        <p>Se não for redirecionado em 5 segundos, <a href="<?php echo htmlspecialchars($redirectTo); ?>">clique aqui</a>.</p>
        
        <div class="details">
            <p><strong>De:</strong> <?php echo htmlspecialchars($requestUri); ?></p>
            <p><strong>Para:</strong> <?php echo htmlspecialchars($redirectTo); ?></p>
        </div>
    </div>
    
    <script>
        // Fallback JavaScript
        setTimeout(function() {
            window.location.href = '<?php echo addslashes($redirectTo); ?>';
        }, 2000);
    </script>
</body>
</html>
<?php
exit;