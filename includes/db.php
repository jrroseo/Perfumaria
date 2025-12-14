<?php
/**
 * Conexão com a Base de Dados
 * 
 * @category Database
 * @package  homeOnline
 * @version  1.0.0
 */

// ==============================================
// 1. VERIFICAR SE JÁ FOI CARREGADO
// ==============================================

if (defined('DB_CONNECTED')) {
    return;
}

define('DB_CONNECTED', true);

// ==============================================
// 2. DETECTAR AMBIENTE
// ==============================================

// Verificar se estamos em desenvolvimento local
function isLocalEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
    
    return (strpos($host, 'localhost') !== false || 
            strpos($host, '127.0.0.1') !== false ||
            $host === '::1' ||
            $serverAddr === '127.0.0.1' ||
            $serverAddr === '::1');
}

$isLocal = isLocalEnvironment();

// ==============================================
// 3. CONFIGURAÇÕES DO BANCO DE DADOS
// ==============================================

// Carregar configurações do arquivo .env ou usar padrões
$dbConfig = [];

// Tentar carregar do .env primeiro
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            switch ($key) {
                case 'DB_HOST':
                    $dbConfig['host'] = $value;
                    break;
                case 'DB_PORT':
                    $dbConfig['port'] = $value;
                    break;
                case 'DB_NAME':
                    $dbConfig['database'] = $value;
                    break;
                case 'DB_USERNAME':
                    $dbConfig['username'] = $value;
                    break;
                case 'DB_PASSWORD':
                    $dbConfig['password'] = $value;
                    break;
                case 'DB_CHARSET':
                    $dbConfig['charset'] = $value;
                    break;
            }
        }
    }
}

// Valores padrão se não encontrados no .env
$dbConfig = array_merge([
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'loja_online',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
], $dbConfig);

// ==============================================
// 4. CONEXÃO COM O BANCO DE DADOS
// ==============================================

try {
    // Construir DSN
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    
    // Opções do PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$dbConfig['charset']}"
    ];
    
    // NO LOCAL: Desabilitar SSL completamente
    if ($isLocal) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        $options[PDO::MYSQL_ATTR_SSL_CA] = null;
        // Laragon não requer SSL, então não definimos MYSQL_ATTR_SSL_CAPATH
    }
    
    // Criar conexão PDO
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
    
    // Configurações adicionais
    $pdo->exec("SET time_zone = '+00:00'");
    $pdo->exec("SET sql_mode = 'STRICT_ALL_TABLES'");
    
    // Log de sucesso (apenas em desenvolvimento)
    if ($isLocal) {
        error_log("✅ Conexão com banco de dados estabelecida com sucesso em " . date('Y-m-d H:i:s'));
    }
    
} catch (PDOException $e) {
    // Log do erro
    error_log("❌ Erro de conexão com banco de dados: " . $e->getMessage());
    error_log("📁 Arquivo: " . $e->getFile() . ":" . $e->getLine());
    error_log("🔧 DSN tentada: " . ($dsn ?? 'N/A'));
    error_log("🌍 Ambiente: " . ($isLocal ? 'Local' : 'Produção'));
    
    // Mensagem amigável baseada no ambiente
    if ($isLocal) {
        // Mensagem detalhada para desenvolvimento
        die('<div style="font-family: Arial, sans-serif; padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; max-width: 800px; margin: 50px auto;">
            <h2 style="margin-top: 0; color: #721c24;">🚨 Erro de Conexão com o Banco de Dados (Desenvolvimento)</h2>
            
            <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h3 style="margin-top: 0;">📋 Detalhes do Erro:</h3>
                <p><strong>Mensagem:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                <p><strong>Arquivo:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>
            </div>
            
            <div style="background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #0c5460;">
                <h3 style="margin-top: 0; color: #0c5460;">🔧 Soluções para Laragon:</h3>
                <ol>
                    <li>Abra o Laragon e verifique se o MySQL está rodando (ícone verde)</li>
                    <li>Clique em "Menu" → "MySQL" → "Configurar my.ini" e verifique as configurações</li>
                    <li>Tente estas credenciais alternativas:
                        <ul>
                            <li><strong>Host:</strong> localhost</li>
                            <li><strong>Usuário:</strong> root</li>
                            <li><strong>Senha:</strong> (deixe em branco)</li>
                            <li><strong>Porta:</strong> 3306</li>
                        </ul>
                    </li>
                    <li>Reinicie o Laragon completamente</li>
                </ol>
            </div>
            
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h3 style="margin-top: 0;">⚙️ Configuração Atual:</h3>
                <pre style="background: white; padding: 10px; border-radius: 3px; overflow: auto;">' . print_r($dbConfig, true) . '</pre>
            </div>
            
            <div style="margin-top: 20px;">
                <button onclick="location.reload()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
                    🔄 Tentar Novamente
                </button>
                <a href="https://laragon.org/docs/" target="_blank" style="padding: 10px 20px; background: #6c757d; color: white; border-radius: 5px; text-decoration: none;">
                    📚 Documentação do Laragon
                </a>
            </div>
        </div>');
    } else {
        // Mensagem genérica para produção
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 300');
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Site em Manutenção</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; }
                h1 { color: #333; }
                p { color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>⚠️ Site em Manutenção</h1>
                <p>Estamos realizando manutenções no sistema.</p>
                <p>Por favor, tente novamente em alguns minutos.</p>
                <p>Desculpe pelo inconveniente.</p>
            </div>
        </body>
        </html>';
    }
    exit;
}

// ==============================================
// 5. FUNÇÕES ÚTEIS
// ==============================================

/**
 * Executa uma query com parâmetros
 */
function db_query($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erro na query: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Parâmetros: " . print_r($params, true));
        throw $e;
    }
}

/**
 * Busca todos os resultados
 */
function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Busca um único resultado
 */
function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt->fetch();
}

/**
 * Insere dados e retorna o ID
 */
function db_insert($table, $data) {
    global $pdo;
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $values = array_values($data);
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    db_query($sql, $values);
    
    return $pdo->lastInsertId();
}

/**
 * Atualiza dados
 */
function db_update($table, $data, $where, $where_params = []) {
    $set_parts = [];
    $values = [];
    
    foreach ($data as $column => $value) {
        $set_parts[] = "$column = ?";
        $values[] = $value;
    }
    
    $set_clause = implode(', ', $set_parts);
    $values = array_merge($values, $where_params);
    
    $sql = "UPDATE $table SET $set_clause WHERE $where";
    return db_query($sql, $values)->rowCount();
}

// ==============================================
// 6. EXPORTAR VARIÁVEL GLOBAL
// ==============================================

// Tornar $pdo disponível globalmente
global $pdo;

// Função para obter a conexão (se necessário)
function get_db_connection() {
    global $pdo;
    return $pdo;
}

// Retornar a conexão (para require_once)
return $pdo;