<?php
/**
 * Ponto de Entrada da Aplicação - index.php na raiz
 * 
 * Este arquivo serve como:
 * 1. Ponto central de segurança
 * 2. Redirecionador para a pasta public/
 * 3. Configurador de headers de segurança
 */

// ==============================================
// 1. CONFIGURAÇÕES INICIAIS E DETECÇÃO DE AMBIENTE
// ==============================================

// Detectar ambiente
$isLocalhost = false;
$isDevelopment = false;

if (isset($_SERVER['HTTP_HOST'])) {
    $host = strtolower($_SERVER['HTTP_HOST']);
    $isLocalhost = (strpos($host, 'localhost') !== false || 
                   strpos($host, '127.0.0.1') !== false ||
                   $host === '::1');
    $isDevelopment = $isLocalhost || (isset($_SERVER['SERVER_ADDR']) && 
                     ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || 
                      $_SERVER['SERVER_ADDR'] === '::1'));
}

// Configurar exibição de erros baseado no ambiente
if ($isDevelopment) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    
    // Configurar arquivo de log de erros
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/php_errors.log');
}

// Definir encoding
header('Content-Type: text/html; charset=utf-8');

// ==============================================
// 2. HEADERS DE SEGURANÇA
// ==============================================

// Lista de headers de segurança
$securityHeaders = [
    // Previne clickjacking
    'X-Frame-Options' => 'DENY',
    
    // Previne MIME sniffing
    'X-Content-Type-Options' => 'nosniff',
    
    // Controla referências
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    
    // Proteção contra XSS
    'X-XSS-Protection' => '1; mode=block',
    
    // Permissões de recursos
    'Permissions-Policy' => 'geolocation=(), camera=(), microphone=(), payment=()',
];

// Adicionar CSP baseado no ambiente
if ($isDevelopment) {
    // Modo mais permissivo para desenvolvimento
    $securityHeaders['Content-Security-Policy-Report-Only'] = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
        "style-src 'self' 'unsafe-inline'",
        "img-src 'self' data: blob:",
        "font-src 'self'",
        "connect-src 'self'",
        "frame-ancestors 'none'"
    ]);
} else {
    // Modo restrito para produção
    $securityHeaders['Content-Security-Policy'] = implode('; ', [
        "default-src 'self'",
        "script-src 'self'",
        "style-src 'self' 'unsafe-inline'",
        "img-src 'self' data: https:",
        "font-src 'self'",
        "connect-src 'self'",
        "frame-ancestors 'none'",
        "base-uri 'self'",
        "form-action 'self'"
    ]);
}

// Forçar HTTPS apenas em produção (não em localhost)
if (!$isLocalhost) {
    $securityHeaders['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
    
    // Redirecionar para HTTPS se não estiver usando
    if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') && 
        !$isLocalhost) {
        $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $httpsUrl);
        exit;
    }
}

// Enviar todos os headers de segurança
foreach ($securityHeaders as $name => $value) {
    if (!headers_sent()) {
        header($name . ': ' . $value);
    }
}

// ==============================================
// 3. VERIFICAR E CONFIGURAR PASTA PUBLIC
// ==============================================

// Caminho da pasta public
$publicDir = __DIR__ . '/public';
$publicIndex = $publicDir . '/index.php';

// Verificar se a estrutura necessária existe
if (!is_dir($publicDir)) {
    showErrorPage(500, 'Erro de Configuração', 
        'A pasta <code>public/</code> não foi encontrada.',
        'Crie a pasta public no diretório raiz do projeto.');
}

if (!file_exists($publicIndex)) {
    showErrorPage(500, 'Erro de Configuração', 
        'O arquivo <code>public/index.php</code> não foi encontrado.',
        'Crie o arquivo index.php dentro da pasta public.');
}

// ==============================================
// 4. PROCESSAR A REQUISIÇÃO E REDIRECIONAR
// ==============================================

// Obter informações da requisição
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$requestPath = $parsedUrl['path'] ?? '/';
$queryString = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

// Evitar redirecionamento infinito (se já estiver em /public/)
if (strpos($requestPath, '/public/') === 0 || $requestPath === '/public') {
    showErrorPage(400, 'Acesso Incorreto', 
        'Você está tentando acessar diretamente a pasta public.',
        'Acesse a aplicação através da URL base (sem /public/ no caminho).');
}

// Construir caminho de redirecionamento
$redirectPath = '/public_html' . $requestPath . $queryString;

// Se for a raiz, verificar se já existe redirecionamento em andamento
if ($requestPath === '/' || $requestPath === '') {
    // Verificar se já estamos no meio de um redirecionamento
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, '/public') !== false) {
        showErrorPage(400, 'Loop de Redirecionamento', 
            'Foi detectado um loop de redirecionamento.',
            'Verifique a configuração do servidor.');
    }
}

// ==============================================
// 5. EXECUTAR REDIRECIONAMENTO
// ==============================================

// Limpar qualquer output buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Redirecionar com código apropriado
$redirectCode = $isDevelopment ? 302 : 301; // 302 temporário em dev, 301 permanente em produção
header('Location: ' . $redirectPath, true, $redirectCode);

// HTML de fallback para navegadores que não seguem redirecionamento imediatamente
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($redirectPath); ?>">
    <title>Redirecionando...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .redirect-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        
        .spinner {
            border: 4px solid rgba(0,0,0,0.1);
            border-left-color: #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        
        p {
            color: #666;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .redirect-link {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .redirect-link:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .details {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
            color: #888;
        }
        
        .details code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="redirect-container">
        <div class="spinner"></div>
        <h1>Redirecionando...</h1>
        <p>Você está sendo redirecionado para a página correta.</p>
        
        <a href="<?php echo htmlspecialchars($redirectPath); ?>" class="redirect-link">
            Clique aqui se o redirecionamento não funcionar automaticamente
        </a>
        
        <div class="details">
            <p><strong>Destino:</strong> <code><?php echo htmlspecialchars($redirectPath); ?></code></p>
            <p><strong>Ambiente:</strong> <?php echo $isDevelopment ? 'Desenvolvimento' : 'Produção'; ?></p>
        </div>
    </div>
    
    <script>
        // Redirecionamento via JavaScript como fallback
        setTimeout(function() {
            window.location.href = '<?php echo addslashes($redirectPath); ?>';
        }, 2000);
        
        // Analytics para desenvolvimento (opcional)
        <?php if ($isDevelopment): ?>
        console.log('Redirecionamento executado:');
        console.log('- Origem: <?php echo htmlspecialchars($requestUri); ?>');
        console.log('- Destino: <?php echo htmlspecialchars($redirectPath); ?>');
        console.log('- Ambiente: <?php echo $isDevelopment ? "Desenvolvimento" : "Produção"; ?>');
        <?php endif; ?>
    </script>
</body>
</html>
<?php
exit;

// ==============================================
// FUNÇÕES AUXILIARES
// ==============================================

/**
 * Mostra uma página de erro formatada
 */
function showErrorPage($httpCode, $title, $message, $solution = '') {
    http_response_code($httpCode);
    
    // Enviar headers de conteúdo antes de qualquer output
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
    
    // Limpar qualquer output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Gerar HTML da página de erro
    $html = '<!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . ' - ' . htmlspecialchars($httpCode) . '</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #333;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            
            .error-container {
                background: white;
                border-radius: 12px;
                padding: 40px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                max-width: 700px;
                width: 100%;
            }
            
            .error-header {
                display: flex;
                align-items: center;
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 2px solid #f0f0f0;
            }
            
            .error-icon {
                background: #dc3545;
                color: white;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                font-weight: bold;
                margin-right: 20px;
                flex-shrink: 0;
            }
            
            .error-title {
                color: #dc3545;
                font-size: 1.8rem;
            }
            
            .error-code {
                color: #666;
                font-size: 1rem;
                font-weight: normal;
            }
            
            .error-message {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 25px;
                border-left: 4px solid #dc3545;
            }
            
            .error-message p {
                margin-bottom: 10px;
            }
            
            .error-message p:last-child {
                margin-bottom: 0;
            }
            
            .solution {
                background: #d1ecf1;
                border-left: 4px solid #0c5460;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 25px;
            }
            
            .solution h3 {
                color: #0c5460;
                margin-bottom: 10px;
            }
            
            .solution ol {
                margin-left: 20px;
            }
            
            .solution li {
                margin-bottom: 8px;
            }
            
            .solution li:last-child {
                margin-bottom: 0;
            }
            
            code {
                background: rgba(0,0,0,0.05);
                padding: 2px 6px;
                border-radius: 4px;
                font-family: "Courier New", monospace;
                font-size: 0.9em;
            }
            
            pre code {
                display: block;
                padding: 15px;
                overflow-x: auto;
                margin: 10px 0;
            }
            
            .details {
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                font-size: 0.9rem;
                color: #666;
            }
            
            .details h4 {
                color: #333;
                margin-bottom: 10px;
            }
            
            .details ul {
                list-style: none;
            }
            
            .details li {
                margin-bottom: 5px;
                padding-left: 15px;
                position: relative;
            }
            
            .details li:before {
                content: "•";
                position: absolute;
                left: 0;
                color: #999;
            }
            
            .actions {
                margin-top: 25px;
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                font-size: 1rem;
            }
            
            .btn:hover {
                background: #5a67d8;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            
            .btn-secondary {
                background: #6c757d;
            }
            
            .btn-secondary:hover {
                background: #5a6268;
                box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
            }
            
            @media (max-width: 768px) {
                .error-container {
                    padding: 20px;
                }
                
                .error-header {
                    flex-direction: column;
                    text-align: center;
                }
                
                .error-icon {
                    margin-right: 0;
                    margin-bottom: 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-header">
                <div class="error-icon">!</div>
                <div>
                    <h1 class="error-title">' . htmlspecialchars($title) . ' 
                        <span class="error-code">(' . $httpCode . ')</span>
                    </h1>
                </div>
            </div>
            
            <div class="error-message">
                ' . $message . '
            </div>';
    
    if ($solution) {
        $html .= '<div class="solution">
                <h3>Solução:</h3>
                ' . $solution . '
            </div>';
    }
    
    $html .= '<div class="details">
                <h4>Detalhes técnicos:</h4>
                <ul>
                    <li><strong>Timestamp:</strong> ' . date('Y-m-d H:i:s') . '</li>
                    <li><strong>URI:</strong> ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') . '</li>
                    <li><strong>Método:</strong> ' . htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'GET') . '</li>
                    <li><strong>User Agent:</strong> ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido') . '</li>
                </ul>
            </div>
            
            <div class="actions">
                <a href="javascript:location.reload()" class="btn">🔄 Tentar Novamente</a>
                <a href="/" class="btn btn-secondary">🏠 Página Inicial</a>
                <a href="javascript:history.back()" class="btn btn-secondary">← Voltar</a>
            </div>
        </div>
        
        <script>
            // Log para desenvolvedores
            console.error("Erro ' . $httpCode . ': ' . addslashes($title) . '");
            console.error("Mensagem: ' . addslashes(strip_tags($message)) . '");
            
            // Auto-refresh após 30 segundos (apenas para erros temporários)
            ' . ($httpCode >= 500 ? 'setTimeout(function() { location.reload(); }, 30000);' : '') . '
        </script>
    </body>
    </html>';
    
    echo $html;
    exit;
}