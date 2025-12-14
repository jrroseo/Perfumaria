 <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Carrega configuração (BASE_URL)
    require_once __DIR__ . '/../config/config_remote.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/heade.php';

    $erro = '';
    $mostrarTimer = false;
    $tempoRestante = 0;

    // Proteção CSRF: Gere token se não existir
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (isset($_SESSION['tipo'])) {
        if ($_SESSION['tipo'] === 'admin') {
            header("Location: /../admin/dashboard.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifique CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $erro = "Token CSRF inválido.";
        } else {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (empty($email) || empty($senha)) {
                $erro = "Email e senha são obrigatórios.";
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND eliminado = 0");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
if ($user) {
    $agora = time();
    $ultimaTentativa = strtotime($user['ultima_tentativa'] ?? '2000-01-01');
    $diferenca = $agora - $ultimaTentativa;

    if ($user['bloqueado'] && $diferenca < 300) {
        $tempoRestante = 300 - $diferenca;
        $mostrarTimer = true;
        
        ?>
        <script>
            let tempoRestante = <?php echo $tempoRestante; ?>;
            const timerElement = document.getElementById('timer');
            
            function atualizarTimer() {
                const minutos = Math.floor(tempoRestante / 60);
                const segundos = tempoRestante % 60;
                timerElement.textContent = `${minutos}:${segundos.toString().padStart(2, '0')}`;
                
                if (tempoRestante > 0) {
                    tempoRestante--;
                    setTimeout(atualizarTimer, 1000);
                } else {
                    location.reload(); // Recarrega a página quando o tempo acabar
                }
            }
            
            Swal.fire({
                icon: "error",
                title: "Conta bloqueada",
                html: "Tente novamente em <span id='timer'></span>.",
                didOpen: () => {
                    atualizarTimer();
                }
            });
        </script>
        <?php
    } else {
        if (password_verify($senha, $user['palavra_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tipo'] = $user['tipo'];
            // Reseta tentativas e bloqueio
            $pdo->prepare("UPDATE utilizadores SET tentativas_login = 0, bloqueado = 0 WHERE id = ?")
                ->execute([$user['id']]);
            if ($user['tipo'] === 'admin') {
                header("Location: /admin/dashboard.php"); // Corrigido: removido "/.." desnecessário
            } else {
                header("Location: index.php");
            }
            exit;
                            } else {
                                $tentativas = $user['tentativas_login'] + 1;
                                $bloqueado = ($tentativas >= 3) ? 1 : 0;
                                $pdo->prepare("UPDATE utilizadores SET tentativas_login = ?, bloqueado = ?, ultima_tentativa = NOW() WHERE id = ?")
                                    ->execute([$tentativas, $bloqueado, $user['id']]);
                                $restantes = max(0, 3 - $tentativas);
    ?>
                             <script>
                                 Swal.fire({
                                     icon: "error",
                                     title: "Usuário não encontrado",
                                     text: "Você tem <?php echo $restantes; ?> tentativas restantes."
                                 });
                             </script>

                     <?php
                            }
                        }
                    } else {
                        ?>
                     <script>
                         Swal.fire({
                             icon: "error",
                             title: "Os campos estão vazios",
                             text: "Por favor, preencha todos os campos."
                         });
                     </script>

 <?php
                    }
                } catch (PDOException $e) {
                    $erro = "Erro interno. Tente novamente.";
                    $conn->close();
                }
            }
        }
    }
    ?>

 <!--====== Section 2 ======-->
 <div class="u-s-p-b-60">


     <!--====== End - Section Intro ======-->


     <!--====== Section Content ======-->
     <div class="section__content">
         <div class="container">
             <div class="row row--center">
                 <div class="col-lg-6 col-md-8 u-s-m-b-30">
                     <div class="l-f-o">
                         <div class="l-f-o__pad-box">
                             <?php if ($erro): ?>
                                 <p class='text-danger'><?= htmlspecialchars($erro) ?></p>
                             <?php endif; ?>
                             <?php if ($mostrarTimer): ?>
                                 <script>
                                     let segundos = <?= $tempoRestante ?>;
                                     const timer = document.getElementById('timer');
                                     const interval = setInterval(() => {
                                         segundos--;
                                         const m = Math.floor(segundos / 60);
                                         const s = segundos % 60;
                                         timer.textContent = `${m}m ${s}s`;
                                         if (segundos <= 0) {
                                             clearInterval(interval);
                                             location.reload(); // Recarrega para permitir tentativa
                                         }
                                     }, 1000);
                                 </script>
                             <?php endif; ?>

                             <h1 class="gl-h1">Acesse Sua Conta</h1>
                             <!-- Checking for any kind of -->
                             <?php if (isset($GET['error'])) {
                                ?>
                                 <p class="error">
                                     <?php echo $_GET['error']; ?>
                                 </p>
                             <?php } ?>
                             <span class="gl-text u-s-m-b-30">Se você já possui uma conta conosco, faça login.</span>
                             <form method="POST" class="l-f-o__form">
                                 <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                 <div class="u-s-m-b-30">

                                     <label class="gl-label" for="login-email">E-MAIL *</label>

                                     <input class="input-text input-text--primary-style" type="email" name="email" id="login-email" placeholder="Enter E-mail" required>
                                 </div>
                                 <div class="u-s-m-b-30">

                                     <label class="gl-label" for="login-password">SENHA *</label>

                                     <input class="input-text input-text--primary-style" type="password" name="senha" id="senha" placeholder="Enter Password" required><i class="ri-eye-off-line login__eye" id="input-icon"></i>
                                 </div>
                                 <div class="gl-inline">
                                     <div class="u-s-m-b-30">

                                         <button class="btn btn--e-transparent-brand-b-2" type="submit">ENTRAR</button>
                                     </div>
                                     <div class="u-s-m-b-30">

                                         <a class="gl-link" href="lost-password.html">Esqueceu sua senha?</a>
                                     </div>
                                     <div class="u-s-m-b-30">
                                         <a class="gl-link" href="index.php">Voltar à home</a>

                                     </div>


                                 </div>

                             </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!--====== End - Section Content ======-->
 </div>
 <!--====== End - Section 2 ======-->
 </div>
 <!--====== End - App Content ======-->
 </main>

 <script>
     function toggleTheme() {
         const body = document.body;
         const current = localStorage.getItem('theme');
         const next = current === 'theme-light' ? 'theme-dark' : 'theme-light';
         body.classList.remove('theme-light', 'theme-dark');
         body.classList.add(next);
         localStorage.setItem('theme', next);
     }

     document.addEventListener('DOMContentLoaded', () => {
         const saved = localStorage.getItem('theme') || 'theme-dark';
         document.body.classList.add(saved);
     });
 </script>
 <script>
     function toggleSenha() {
         const input = document.getElementById('senha');
         input.type = input.type === 'password' ? 'text' : 'password';
     }

     /*=============== SHOW HIDDEN - PASSWORD ===============*/
     const showHiddenPass = (inputPass, inputIcon) => {
         const input = document.getElementById(inputPass),
             iconEye = document.getElementById(inputIcon)

         iconEye.addEventListener('click', () => {
             // Change password to text
             if (input.type === 'password') {
                 // Switch to text
                 input.type = 'text'

                 // Add icon
                 iconEye.classList.add('ri-eye-off-line')
                 // Remove icon
                 iconEye.classList.remove('ri-eye-line')
             } else {
                 // Change to password
                 input.type = 'password'
                 // Add icon
                 iconEye.classList.add('ri-eye-line')
                 // Remove icon
                 iconEye.classList.remove('ri-eye-off-line')
             }
         })
     }

     showHiddenPass('password', 'input-icon')
 </script>
 <?php require_once __DIR__ . '/../includes/footer.php'; ?>