<?php
/**
 * Página de Registro
 * 
 * @version 1.0.0
 * @package Auth
 */

// ==============================================
// 1. CARREGAR CONFIGURAÇÕES E INICIAR SESSÃO
// ==============================================

if (!defined('PUBLIC_INDEX')) {
    require_once __DIR__ . '/../config/config_remote.php';
    
    if (function_exists('secure_session_start')) {
        secure_session_start();
    } else {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

// ==============================================
// 2. REDIRECIONAR SE JÁ ESTIVER LOGADO
// ==============================================

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL);
    exit;
}

// ==============================================
// 3. INICIALIZAR VARIÁVEIS
// ==============================================

$erros = [];
$dados = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
    'nif' => ''
];

// Proteção CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==============================================
// 4. PROCESSAR FORMULÁRIO DE REGISTRO
// ==============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar inputs
    $dados['nome'] = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING));
    $dados['email'] = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $dados['telefone'] = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
    $dados['nif'] = preg_replace('/[^0-9]/', '', $_POST['nif'] ?? '');
    
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Validar CSRF
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $erros[] = "Token de segurança inválido.";
    }
    
    // Validar nome
    if (strlen($dados['nome']) < 3) {
        $erros[] = "O nome deve ter pelo menos 3 caracteres.";
    }
    
    // Validar email
    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Por favor, insira um email válido.";
    }
    
    // Validar telefone (Portugal: 9 dígitos)
    if (!preg_match('/^9[1236]\d{7}$/', $dados['telefone'])) {
        $erros[] = "Por favor, insira um número de telefone válido (9 dígitos, começando com 91, 92, 93 ou 96).";
    }
    
    // Validar NIF (Portugal: 9 dígitos)
    if (!preg_match('/^[0-9]{9}$/', $dados['nif'])) {
        $erros[] = "O NIF deve conter exatamente 9 dígitos.";
    }
    
    // Validar senha
    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    }
    
    if (!preg_match('/[A-Z]/', $senha)) {
        $erros[] = "A senha deve conter pelo menos uma letra maiúscula.";
    }
    
    if (!preg_match('/[a-z]/', $senha)) {
        $erros[] = "A senha deve conter pelo menos uma letra minúscula.";
    }
    
    if (!preg_match('/[0-9]/', $senha)) {
        $erros[] = "A senha deve conter pelo menos um número.";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $senha)) {
        $erros[] = "A senha deve conter pelo menos um caractere especial.";
    }
    
    if ($senha !== $confirmarSenha) {
        $erros[] = "As senhas não coincidem.";
    }
    
    // Verificar se email já existe (após validações básicas)
    if (empty($erros)) {
        try {
            require_once __DIR__ . '/../includes/db.php';
            
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $stmt->execute([$dados['email']]);
            
            if ($stmt->fetch()) {
                $erros[] = "Este email já está registrado. <a href='login.php?email=" . urlencode($dados['email']) . "'>Fazer login?</a>";
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            $erros[] = "Erro ao verificar disponibilidade do email.";
        }
    }
    
    // Registrar usuário se não houver erros
    if (empty($erros)) {
        try {
            // Hash da senha
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            
            // Iniciar transação
            $pdo->beginTransaction();
            
            // Inserir usuário
            $stmt = $pdo->prepare("
                INSERT INTO utilizadores (nome, email, palavra_passe, tipo, criado_em) 
                VALUES (?, ?, ?, 'cliente', NOW())
            ");
            $stmt->execute([$dados['nome'], $dados['email'], $hashSenha]);
            
            $idUsuario = $pdo->lastInsertId();
            
            // Inserir dados do cliente
            $stmt = $pdo->prepare("
                INSERT INTO clientes_dados (id_utilizador, nome, telefone, nif, criado_em) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$idUsuario, $dados['nome'], $dados['telefone'], $dados['nif']]);
            
            // Commit da transação
            $pdo->commit();
            
            // Novo token CSRF
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Redirecionar para login com mensagem de sucesso
            header('Location: login.php?success=registered&email=' . urlencode($dados['email']));
            exit;
            
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            error_log("Erro ao registrar usuário: " . $e->getMessage());
            $erros[] = "Erro ao criar conta. Por favor, tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - <?= htmlspecialchars(getenv('APP_NAME') ?: 'home Online') ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    
    <style>
        /* Estilos para página de registro */
        :root {
            --primary-color: #10b981;
            --primary-dark: #0da271;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --success-color: #28a745;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #0da271 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #10b981 0%, #0da271 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .register-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .register-body {
            padding: 30px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-danger ul {
            margin: 5px 0 0 20px;
        }
        
        .alert-danger li {
            margin-bottom: 3px;
        }
        
        .alert-danger a {
            color: #721c24;
            text-decoration: underline;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gray-dark);
            font-size: 0.9rem;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
        }
        
        .input-group .toggle-password:hover {
            color: var(--primary-color);
        }
        
        .password-strength {
            margin-top: 8px;
        }
        
        .strength-bar {
            height: 6px;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .strength-bar-inner {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
            border-radius: 3px;
        }
        
        .strength-text {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .strength-weak .strength-bar-inner {
            background: var(--danger-color);
            width: 33%;
        }
        
        .strength-medium .strength-bar-inner {
            background: var(--warning-color);
            width: 66%;
        }
        
        .strength-strong .strength-bar-inner {
            background: var(--success-color);
            width: 100%;
        }
        
        .strength-text-weak { color: var(--danger-color); }
        .strength-text-medium { color: var(--warning-color); }
        .strength-text-strong { color: var(--success-color); }
        
        .register-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #0da271 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .register-btn:hover {
            background: linear-gradient(135deg, #0da271 0%, #0b9665 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }
        
        .register-footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .register-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .register-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .terms {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 15px;
            text-align: center;
        }
        
        .terms a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .register-container {
                border-radius: 8px;
            }
            
            .register-header {
                padding: 20px;
            }
            
            .register-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Cabeçalho -->
        <div class="register-header">
            <h1><i class="fas fa-user-plus"></i> Criar Nova Conta</h1>
            <p>Registre-se para acessar recursos exclusivos e fazer compras com facilidade</p>
        </div>
        
        <!-- Corpo do Formulário -->
        <div class="register-body">
            <!-- Mensagens de Erro -->
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Por favor, corrija os seguintes erros:</strong>
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?= $erro ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Formulário de Registro -->
            <form method="POST" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <!-- Nome -->
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome Completo</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            value="<?= htmlspecialchars($dados['nome']) ?>" 
                            placeholder="Seu nome completo" 
                            required
                            minlength="3"
                        >
                    </div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?= htmlspecialchars($dados['email']) ?>" 
                            placeholder="seu@email.com" 
                            required
                        >
                    </div>
                </div>
                
                <!-- Telefone -->
                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                    <div class="input-group">
                        <input 
                            type="tel" 
                            id="telefone" 
                            name="telefone" 
                            value="<?= htmlspecialchars($dados['telefone']) ?>" 
                            placeholder="912345678" 
                            required
                            pattern="9[1236]\d{7}"
                            title="Número de telefone português válido (9 dígitos)"
                        >
                    </div>
                    <small class="text-muted">Formato: 912345678</small>
                </div>
                
                <!-- NIF -->
                <div class="form-group">
                    <label for="nif"><i class="fas fa-id-card"></i> NIF</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            id="nif" 
                            name="nif" 
                            value="<?= htmlspecialchars($dados['nif']) ?>" 
                            placeholder="123456789" 
                            required
                            pattern="[0-9]{9}"
                            title="NIF deve conter 9 dígitos"
                        >
                    </div>
                    <small class="text-muted">9 dígitos</small>
                </div>
                
                <!-- Senha -->
                <div class="form-group">
                    <label for="senha"><i class="fas fa-key"></i> Senha</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            placeholder="Crie uma senha forte" 
                            required
                            minlength="8"
                            oninput="checkPasswordStrength()"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('senha')">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    
                    <!-- Indicador de Força da Senha -->
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-bar-inner"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Digite sua senha</div>
                    </div>
                    
                    <small class="text-muted">
                        A senha deve conter pelo menos 8 caracteres, incluindo maiúsculas, minúsculas, números e caracteres especiais.
                    </small>
                </div>
                
                <!-- Confirmar Senha -->
                <div class="form-group">
                    <label for="confirmar_senha"><i class="fas fa-key"></i> Confirmar Senha</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="confirmar_senha" 
                            name="confirmar_senha" 
                            placeholder="Repita a senha" 
                            required
                            minlength="8"
                            oninput="checkPasswordMatch()"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmar_senha')">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    <div class="text-muted" id="passwordMatch"></div>
                </div>
                
                <!-- Termos e Condições -->
                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Concordo com os <a href="<?= BASE_URL ?>/terms.php" target="_blank">Termos de Serviço</a> 
                        e <a href="<?= BASE_URL ?>/privacy.php" target="_blank">Política de Privacidade</a>
                    </label>
                </div>
                
                <!-- Botão de Registro -->
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </button>
            </form>
            
            <!-- Links Úteis -->
            <div class="register-footer">
                <p>
                    Já tem uma conta? 
                    <a href="<?= BASE_URL ?>/login.php">Faça login</a>
                </p>
                <p>
                    <a href="<?= BASE_URL ?>">
                        <i class="fas fa-store"></i> Voltar à home
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Alternar visibilidade da senha
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'ri-eye-line';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'ri-eye-off-line';
            }
        }
        
        // Verificar força da senha
        function checkPasswordStrength() {
            const password = document.getElementById('senha').value;
            const strengthBar = document.querySelector('.strength-bar-inner');
            const strengthText = document.getElementById('strengthText');
            const strengthContainer = document.getElementById('passwordStrength');
            
            // Reset
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = '#ddd';
            strengthText.textContent = 'Digite sua senha';
            strengthText.className = 'strength-text';
            strengthContainer.className = 'password-strength';
            
            if (password.length === 0) return;
            
            let score = 0;
            
            // Comprimento
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            
            // Complexidade
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            // Determinar força
            let strength = '';
            let width = 0;
            let color = '';
            let text = '';
            
            if (score <= 2) {
                strength = 'weak';
                width = 33;
                color = '#dc3545';
                text = 'Senha fraca';
            } else if (score <= 4) {
                strength = 'medium';
                width = 66;
                color = '#ffc107';
                text = 'Senha média';
            } else {
                strength = 'strong';
                width = 100;
                color = '#28a745';
                text = 'Senha forte';
            }
            
            // Aplicar estilos
            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = text;
            strengthText.className = 'strength-text strength-text-' + strength;
            strengthContainer.className = 'password-strength strength-' + strength;
        }
        
        // Verificar se as senhas coincidem
        function checkPasswordMatch() {
            const password = document.getElementById('senha').value;
            const confirmPassword = document.getElementById('confirmar_senha').value;
            const matchElement = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchElement.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchElement.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> As senhas coincidem';
            } else {
                matchElement.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> As senhas não coincidem';
            }
        }
        
        // Formatar telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.substring(0, 9);
            e.target.value = value;
        });
        
        // Formatar NIF
        document.getElementById('nif').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.substring(0, 9);
            e.target.value = value;
        });
        
        // Validar formulário
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('senha').value;
            const confirmPassword = document.getElementById('confirmar_senha').value;
            const terms = document.getElementById('terms').checked;
            
            // Verificar se as senhas coincidem
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
                return false;
            }
            
            // Verificar termos
            if (!terms) {
                e.preventDefault();
                alert('Você deve concordar com os Termos de Serviço para continuar.');
                return false;
            }
            
            // Mostrar loading
            const submitBtn = this.querySelector('.register-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando conta...';
            submitBtn.disabled = true;
            
            // Continuar com o envio
            return true;
        });
        
        // Foco no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nome').focus();
        });
    </script>
</body>
</html>