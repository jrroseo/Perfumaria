<?php
/**
 * Configurações do Banco de Dados
 * 
 * Este arquivo NUNCA deve ser commitado no git!
 * Adicione database.php ao .gitignore
 */

return [
    // Configurações de desenvolvimento
    'development' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'loja_online',
        'charset' => 'utf8mb4'
    ],
    
    // Configurações de produção
    'production' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'username' => getenv('DB_USERNAME') ?: 'db_user',
        'password' => getenv('DB_PASSWORD') ?: '',
        'dbname' => getenv('DB_NAME') ?: 'loja_online',
        'charset' => 'utf8mb4'
    ],
    
    // Configurações de teste
    'testing' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'loja_online_test',
        'charset' => 'utf8mb4'
    ]
];

// Determinar ambiente atual
$environment = getenv('APP_ENV') ?: 'development';

// Retornar configurações para o ambiente atual
return ${$environment} ?? ${'development'};

