<?php
/**
 * Configurações Gerais da Aplicação
 * 
 * IMPORTANTE: Este arquivo deve ser o PRIMEIRO a ser incluído
 * antes de qualquer session_start() ou definição de constante
 */

// ==============================================
// 1. VERIFICAR SE JÁ FOI CARREGADO
// ==============================================

if (defined('CONFIG_LOADED')) {
    return; // Já carregou, não precisa carregar novamente
}

define('CONFIG_LOADED', true);

// ==============================================
// 2. CARREGAR VARIÁVEIS DE AMBIENTE (CORRIGIDO)
// ==============================================

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Ignorar comentários
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Processar variáveis
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remover aspas se existirem
            if (!empty($value)) {
                $firstChar = substr($value, 0, 1);
                $lastChar = substr($value, -1);
                
                if (($firstChar === '"' && $lastChar === '"') || 
                    ($firstChar === "'" && $lastChar === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// ==============================================
// 3. DEFINIR CONSTANTES (APENAS SE NÃO EXISTIREM)
// ==============================================

// ENVIRONMENT
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('APP_ENV') ?: 'development');
}

// BASE_URL - Definir apenas se não existir
// NO config_remote.php (na parte do BASE_URL):
if (!defined('BASE_URL')) {
    $appUrl = getenv('APP_URL');
    
    if (!$appUrl) {
        // Detectar automaticamente SEM duplicar /public_html/
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        
        // IMPORTANTE: Remover /public se existir
        $scriptDir = str_replace('/public', '', $scriptDir);
        
        $appUrl = $protocol . $host . $scriptDir;
    }
    
    define('BASE_URL', rtrim($appUrl, '/'));
}

// DEBUG_MODE
if (!defined('DEBUG_MODE')) {
    $debugEnv = getenv('APP_DEBUG');
    define('DEBUG_MODE', $debugEnv === 'true' || ENVIRONMENT === 'development');
}

// ENCRYPTION_KEY
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', getenv('APP_KEY') ?: 'chave-temporaria-mudar-em-producao');
}

// ==============================================
// 4. CONFIGURAÇÕES DE SESSÃO (APENAS SE SESSÃO NÃO INICIADA)
// ==============================================

if (session_status() === PHP_SESSION_NONE) {
    // Configurar cookie da sessão
    $sessionLifetime = getenv('SESSION_LIFETIME') ?: 7200;
    $sessionDomain = parse_url(BASE_URL, PHP_URL_HOST) ?: 'localhost';
    
    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'domain' => $sessionDomain,
        'secure' => ENVIRONMENT === 'production',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    // Configurações antigas (para compatibilidade)
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
    ini_set('session.gc_maxlifetime', $sessionLifetime);
}

// ==============================================
// 5. CONFIGURAÇÕES DE ERRO
// ==============================================

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    
    // Criar diretório de logs se não existir
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    ini_set('error_log', $logDir . '/php_errors.log');
}

// ==============================================
// 6. CONFIGURAÇÕES GERAIS DO PHP
// ==============================================

// Timezone
$timezone = getenv('APP_TIMEZONE') ?: 'Europe/Lisbon';
date_default_timezone_set($timezone);

// Upload
ini_set('upload_max_filesize', getenv('UPLOAD_MAX_SIZE') ?: '10M');
ini_set('post_max_size', getenv('POST_MAX_SIZE') ?: '10M');
ini_set('max_execution_time', getenv('MAX_EXECUTION_TIME') ?: 300);
ini_set('max_input_time', getenv('MAX_INPUT_TIME') ?: 60);

// ==============================================
// 7. HEADERS DE SEGURANÇA (APENAS SE NÃO ENVIADOS)
// ==============================================

if (!headers_sent()) {
    // Headers básicos
    header('Content-Type: text/html; charset=utf-8');
    
    // Headers de segurança
    if (ENVIRONMENT === 'production') {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        
        // Apenas HTTPS em produção
        if (strpos(BASE_URL, 'https://') === 0) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

// ==============================================
// 8. CAMINHOS IMPORTANTES (APENAS SE NÃO DEFINIDOS)
// ==============================================

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/../'));
}

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}

if (!defined('LOG_PATH')) {
    define('LOG_PATH', ROOT_PATH . '/logs');
}

if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
}

if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', STORAGE_PATH . '/cache');
}

// ==============================================
// 9. CRIAR DIRETÓRIOS NECESSÁRIOS
// ==============================================

$requiredDirs = [LOG_PATH, STORAGE_PATH, UPLOAD_PATH, CACHE_PATH];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// ==============================================
// 10. FUNÇÕES AUXILIARES
// ==============================================

/**
 * Iniciar sessão de forma segura
 */
function secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => getenv('SESSION_LIFETIME') ?: 7200,
            'read_and_close'  => false,
            'use_strict_mode' => true,
            'use_cookies'     => true,
            'use_only_cookies' => true,
            'cookie_httponly' => true,
            'cookie_secure'   => ENVIRONMENT === 'production',
            'cookie_samesite' => 'Strict'
        ]);
        
        // Regenerar ID da sessão periodicamente
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutos
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Sanitizar entrada de dados
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return $data;
}

/**
 * Verificar se é ambiente de desenvolvimento
 */
function is_dev_environment() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
    
    return (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        $host === '::1' ||
        $serverAddr === '127.0.0.1' ||
        $serverAddr === '::1' ||
        ENVIRONMENT === 'development'
    );
}

/**
 * Carregar variável de ambiente com fallback
 */
function env($key, $default = null) {
    $value = getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    // Converter string "true"/"false" para booleano
    $lowerValue = strtolower($value);
    if ($lowerValue === 'true') {
        return true;
    }
    if ($lowerValue === 'false') {
        return false;
    }
    
    // Converter string numérica para número
    if (is_numeric($value)) {
        return strpos($value, '.') !== false ? (float)$value : (int)$value;
    }
    
    return $value;
}

/**
 * Gerar URL absoluta
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Gerar caminho de asset
 */
function asset($path) {
    $path = ltrim($path, '/');
    
    // Se já começar com http, retornar como está
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // Se for um caminho absoluto do servidor
    if (strpos($path, '/') === 0) {
        return $path;
    }
    
    // Para assets na pasta public
    return BASE_URL . '/' . $path;
}

// ==============================================
// 11. AUTO-LOADER SIMPLIFICADO
// ==============================================

spl_autoload_register(function ($className) {
    // Converter namespace para caminho de arquivo
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    $directories = [
        ROOT_PATH . '/app/Controllers/',
        ROOT_PATH . '/app/Models/',
        ROOT_PATH . '/app/Services/',
        ROOT_PATH . '/app/Helpers/',
        ROOT_PATH . '/includes/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ==============================================
// 12. INICIAR SESSÃO AUTOMATICAMENTE (OPCIONAL)
// ==============================================

// Se quiser iniciar sessão automaticamente, descomente:
// if (!defined('NO_AUTO_SESSION') && session_status() === PHP_SESSION_NONE) {
//     secure_session_start();
// }