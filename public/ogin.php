<?php
/**
 * Página de Login
 * 
 * @version 1.0.0
 * @package Auth
 */

// ==============================================
// 1. CARREGAR CONFIGURAÇÕES E INICIAR SESSÃO
// ==============================================

// Verificar se config já foi carregado
if (!defined('PUBLIC_INDEX')) {
    // Carregar configuração
    require_once __DIR__ . '/../config/config_remote.php';
    
    // Iniciar sessão segura
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

if (isset($_SESSION['user_id']) && isset($_SESSION['tipo'])) {
    $redirectUrl = ($_SESSION['tipo'] === 'admin') 
        ? BASE_URL . '/admin/dashboard.php' 
        : BASE_URL;
    
    header('Location: ' . $redirectUrl);
    exit;
}

// ==============================================
// 3. INICIALIZAR VARIÁVEIS
// ==============================================

$erro = '';
$mostrarTimer = false;
$tempoRestante = 0;
$emailValue = '';

// Proteção CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==============================================
// 4. PROCESSAR FORMULÁRIO DE LOGIN
// ==============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Validar CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $erro = "Token de segurança inválido. Por favor, recarregue a página.";
    } elseif (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Carregar banco de dados
            require_once __DIR__ . '/../includes/db.php';
            
            // Buscar usuário
            $stmt = $pdo->prepare("
                SELECT id, email, palavra_passe, tipo, tentativas_login, bloqueado, ultima_tentativa 
                FROM utilizadores 
                WHERE email = ? AND eliminado = 0
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Verificar se a conta está bloqueada
                $agora = time();
                $ultimaTentativa = strtotime($user['ultima_tentativa'] ?? '2000-01-01');
                $tempoDesdeUltimaTentativa = $agora - $ultimaTentativa;
                $bloqueioMinutos = 5; // 5 minutos de bloqueio
                
                if ($user['bloqueado'] && $tempoDesdeUltimaTentativa < ($bloqueioMinutos * 60)) {
                    $tempoRestante = ($bloqueioMinutos * 60) - $tempoDesdeUltimaTentativa;
                    $mostrarTimer = true;
                    $emailValue = htmlspecialchars($email);
                } else {
                    // Verificar senha
                    if (password_verify($senha, $user['palavra_passe'])) {
                        // Login bem-sucedido
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['tipo'] = $user['tipo'];
                        $_SESSION['email'] = $user['email'];
                        
                        // Registrar data de login
                        $_SESSION['login_time'] = time();
                        
                        // Resetar tentativas de login
                        $stmt = $pdo->prepare("
                            UPDATE utilizadores 
                            SET tentativas_login = 0, bloqueado = 0, ultima_tentativa = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$user['id']]);
                        
                        // Registrar login no histórico (se existir tabela)
                        try {
                            $pdo->prepare("
                                INSERT INTO historico_logins (id_utilizador, ip_address, user_agent) 
                                VALUES (?, ?, ?)
                            ")->execute([
                                $user['id'],
                                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                                $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido'
                            ]);
                        } catch (PDOException $e) {
                            // Ignorar se tabela não existir
                        }
                        
                        // Redirecionar com base no tipo de usuário
                        $redirectUrl = ($user['tipo'] === 'admin') 
                            ? BASE_URL . '/admin/dashboard.php' 
                            : BASE_URL;
                        
                        // Regenerar ID da sessão após login bem-sucedido
                        session_regenerate_id(true);
                        
                        // Criar novo token CSRF
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        
                        header('Location: ' . $redirectUrl);
                        exit;
                    } else {
                        // Senha incorreta
                        $tentativasAtuais = $user['tentativas_login'] + 1;
                        $maxTentativas = 5;
                        $bloquear = ($tentativasAtuais >= $maxTentativas) ? 1 : 0;
                        
                        // Atualizar tentativas
                        $stmt = $pdo->prepare("
                            UPDATE utilizadores 
                            SET tentativas_login = ?, bloqueado = ?, ultima_tentativa = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$tentativasAtuais, $bloquear, $user['id']]);
                        
                        $tentativasRestantes = max(0, $maxTentativas - $tentativasAtuais);
                        
                        if ($bloquear) {
                            $mostrarTimer = true;
                            $tempoRestante = $bloqueioMinutos * 60;
                            $erro = "Conta bloqueada devido a muitas tentativas falhadas. Tente novamente em {$bloqueioMinutos} minutos.";
                        } else {
                            $erro = "Senha incorreta. Você tem {$tentativasRestantes} tentativa(s) restante(s).";
                        }
                        
                        $emailValue = htmlspecialchars($email);
                    }
                }
            } else {
                // Usuário não encontrado
                $erro = "Email ou senha incorretos.";
                $emailValue = htmlspecialchars($email);
            }
        } catch (PDOException $e) {
            error_log("Erro de login: " . $e->getMessage());
            $erro = "Erro interno do sistema. Por favor, tente novamente mais tarde.";
        }
    }
} elseif (isset($_GET['email'])) {
    // Preencher email se fornecido via GET (ex: após registro)
    $emailValue = htmlspecialchars($_GET['email']);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars(getenv('APP_NAME') ?: 'home Online') ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Estilos para página de login */
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --warning-color: #ffc107;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #5a67d8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
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
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
        
        .input-group {
            position: relative;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            padding-right: 45px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #5a67d8 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #4c51bf 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .login-footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .login-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .login-links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .timer {
            display: inline-block;
            background: var(--danger-color);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 5px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                border-radius: 8px;
            }
            
            .login-header {
                padding: 20px;
            }
            
            .login-body {
                padding: 20px;
            }
            
            .login-links {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Cabeçalho -->
        <div class="login-header">
            <h1><i class="fas fa-lock"></i> Acesso à Conta</h1>
            <p>Faça login para acessar sua conta</p>
        </div>
        
        <!-- Corpo do Formulário -->
        <div class="login-body">
            <!-- Mensagens de Erro/Sucesso -->
            <?php if ($erro && !$mostrarTimer): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Conta criada com sucesso! Faça login para continuar.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['expired']) && $_GET['expired'] == 'true'): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Sua sessão expirou. Por favor, faça login novamente.
                </div>
            <?php endif; ?>
            
            <!-- Timer de Bloqueio -->
            <?php if ($mostrarTimer): ?>
                <div class="alert alert-danger" id="blocked-alert">
                    <i class="fas fa-ban"></i> 
                    Conta temporariamente bloqueada. 
                    Tente novamente em <span id="timer" class="timer"><?= ceil($tempoRestante/60) ?>:<?= str_pad($tempoRestante%60, 2, '0', STR_PAD_LEFT) ?></span>
                </div>
                
                <script>
                    // Timer para bloqueio
                    let seconds = <?= $tempoRestante ?>;
                    const timerElement = document.getElementById('timer');
                    
                    function updateTimer() {
                        const minutes = Math.floor(seconds / 60);
                        const remainingSeconds = seconds % 60;
                        timerElement.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                        
                        if (seconds > 0) {
                            seconds--;
                            setTimeout(updateTimer, 1000);
                        } else {
                            document.getElementById('blocked-alert').innerHTML = 
                                '<i class="fas fa-check-circle"></i> Você já pode tentar fazer login novamente.';
                            document.getElementById('blocked-alert').className = 'alert alert-success';
                        }
                    }
                    
                    updateTimer();
                </script>
            <?php endif; ?>
            
            <!-- Formulário de Login -->
            <form method="POST" id="loginForm" <?= $mostrarTimer ? 'onsubmit="return false;"' : '' ?>>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?= $emailValue ?>" 
                            placeholder="seu@email.com" 
                            required
                            <?= $mostrarTimer ? 'disabled' : '' ?>
                        >
                    </div>
                </div>
                
                <!-- Senha -->
                <div class="form-group">
                    <label for="senha"><i class="fas fa-key"></i> Senha</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            placeholder="Sua senha" 
                            required
                            <?= $mostrarTimer ? 'disabled' : '' ?>
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Botão de Login -->
                <button type="submit" class="login-btn" <?= $mostrarTimer ? 'disabled' : '' ?>>
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
            
            <!-- Links Úteis -->
            <div class="login-footer">
                <div class="login-links">
                    <a href="<?= BASE_URL ?>/register.php">
                        <i class="fas fa-user-plus"></i> Criar Conta
                    </a>
                    <a href="<?= BASE_URL ?>/forgot-password.php">
                        <i class="fas fa-question-circle"></i> Esqueceu a Senha?
                    </a>
                    <a href="<?= BASE_URL ?>">
                        <i class="fas fa-store"></i> Voltar à home
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Alternar visibilidade da senha
        function togglePassword() {
            const passwordInput = document.getElementById('senha');
            const toggleIcon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'ri-eye-line';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'ri-eye-off-line';
            }
        }
        
        // Validar formulário
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();
            
            if (!email || !senha) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos obrigatórios',
                    text: 'Por favor, preencha todos os campos.',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            // Validação básica de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Email inválido',
                    text: 'Por favor, insira um email válido.',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            // Mostrar loading
            const submitBtn = this.querySelector('.login-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
            submitBtn.disabled = true;
            
            // Continuar com o envio do formulário
            return true;
        });
        
        // Prevenir reenvio do formulário
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Foco no campo de email
        <?php if (!$mostrarTimer): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        <?php endif; ?>
    </script>
</body>
</html>