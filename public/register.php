 <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Carrega configuração (BASE_URL)
    require_once __DIR__ . '/../config/config_remote.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/heade.php';

    $erros = [];
    $dados = [
        'nome' => '',
        'email' => '',
        'telefone' => '',
        'nif' => ''
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($dados as $campo => &$valor) {
            $valor = trim($_POST[$campo] ?? '');
        }
        unset($valor);

        $senha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        // Email já registado?
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->execute([$dados['email']]);
        if ($stmt->fetch()) {
            $erros[] = "Este email já está registado.";
        }

        // Validação do telefone
        if (!preg_match('/^\d{9}$/', $dados['telefone'])) {
            $erros[] = "O telefone deve conter 9 dígitos numéricos.";
        }

        // Validação do NIF
        if (!preg_match('/^[125689]\d{8}$/', $dados['nif'])) {
            $erros[] = "O NIF deve ter 9 dígitos e começar por 1, 2, 5, 6, 8 ou 9.";
        }

        // Validação da palavra-passe
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{9,}$/', $senha)) {
            $erros[] = "A palavra-passe deve ter pelo menos 9 caracteres, incluindo uma letra maiúscula e um número.";
        }

        if ($senha !== $confirmar) {
            $erros[] = "As palavras-passe não coincidem.";
        }

        // Tudo OK → Criar conta
        if (empty($erros)) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $pdo->prepare("INSERT INTO utilizadores (nome, email, palavra_passe) VALUES (?, ?, ?)")
                ->execute([$dados['nome'], $dados['email'], $hash]);

            $id_utilizador = $pdo->lastInsertId();

            $pdo->prepare("INSERT INTO clientes_dados (id_utilizador, nome, telefone, nif) VALUES (?, ?, ?, ?)")
                ->execute([$id_utilizador, $dados['nome'], $dados['telefone'], $dados['nif']]);
            header("Location: login.php");
            exit;
        }
    }
    ?>


 <!--====== App Content ======-->
 <div class="app-content">

     <!--====== Section 2 ======-->

     <!--====== Section 2 ======-->
     <div class="u-s-p-b-60">

         <!--====== Section Content ======-->
         <div class="section__content">
             <div class="container">
                 <div class="row row--center">
                     <div class="col-lg-6 col-md-8 u-s-m-b-30">
                         <div class="l-f-o">
                             <div class="l-f-o__pad-box">
                                 <h1 class="gl-h1">SOU UM NOVO CLIENTE</h1>

                                 <span class="gl-text u-s-m-b-30">Ao criar uma conta em nossa home, você poderá concluir o processo de compra mais rapidamente, armazenar endereços de entrega, visualizar e acompanhar seus pedidos em sua conta e muito mais.
                                     <?php if (!empty($erros)): ?>
                                         <div class="alert alert-danger">
                                             <ul>
                                                 <?php foreach ($erros as $erro): ?>
                                                     <li><?= htmlspecialchars($erro) ?></li>
                                                 <?php endforeach; ?>
                                             </ul>
                                         </div>
                                     <?php endif; ?>
                                 </span>
                                 <form method="POST" class="l-f-o__form">

                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-fname">NOME *</label>

                                         <input class="input-text input-text--primary-style" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>" placeholder="Nome" required>
                                     </div>
                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-email">CPF *</label>

                                         <input class="input-text input-text--primary-style" type="text" name="nif" value="<?= htmlspecialchars($dados['nif']) ?>" placeholder="Digite o CPF" required>
                                     </div>
                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-email">E-MAIL *</label>

                                         <input class="input-text input-text--primary-style" type="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" placeholder="Digite o e-mail" required>
                                     </div>
                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-email">TELEFONE *</label>

                                         <input class="input-text input-text--primary-style" type="text" name="telefone" value="<?= htmlspecialchars($dados['telefone']) ?>" required>
                                     </div>

                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-password">SENHA *</label>

                                         <input class="input-text input-text--primary-style" type="password" name="senha" id="senha" oninput="avaliarSenha()" placeholder="Digite a senha" required>
                                     </div>
                                     <div class="u-s-m-b-30">

                                         <label class="gl-label" for="reg-password-confirm">CONFIRMAR SENHA *</label>

                                         <input class="input-text input-text--primary-style" type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirme a senha" required>
                                     </div>
                                     <div class="gl-inline">

                                         <div class="u-s-m-b-15">
                                           
                                                 <button class="btn btn--e-transparent-brand-b-2" type="submit">CADASTRAR</button>
                                           
                                         </div>

                                         <a class="gl-link" href="index.php">Voltar à home</a>
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

 <?php require_once __DIR__ . '/../includes/footer.php'; ?>

 <!--====== End - Main App ======-->
 </div>

 <script>
     function toggleSenha(id) {
         const campo = document.getElementById(id);
         campo.type = campo.type === 'password' ? 'text' : 'password';
     }

     function avaliarSenha() {
         const senha = document.getElementById("senha").value;
         const barra = document.getElementById("barraForca");
         const texto = document.getElementById("mensagemForca");

         let forca = 0;
         if (senha.length >= 9) forca++;
         if (/[A-Z]/.test(senha)) forca++;
         if (/\d/.test(senha)) forca++;

         if (forca === 0) {
             barra.style.width = "0%";
             barra.className = "progress-bar";
             texto.textContent = "";
         } else if (forca === 1) {
             barra.style.width = "33%";
             barra.className = "progress-bar bg-danger";
             texto.textContent = "Senha fraca";
         } else if (forca === 2) {
             barra.style.width = "66%";
             barra.className = "progress-bar bg-warning";
             texto.textContent = "Senha média";
         } else {
             barra.style.width = "100%";
             barra.className = "progress-bar bg-success";
             texto.textContent = "Senha forte";
         }
     }
 </script>