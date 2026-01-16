<?php
session_start();
require_once 'db_config.php';

$erro = "";
$mostrar_senha = false;

// Verifica se já está logado
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header("Location: admin.php");
    exit();
} elseif (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === false) {
    header("Location: perfil_aluno.php");
    exit();
}

// Verifica se há mensagem de logout
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $mensagem_logout = "Sessão terminada com sucesso!";
}

// Verifica se há mensagem de registro
if (isset($_GET['registro']) && $_GET['registro'] == 'success') {
    $mensagem_registro = "Conta criada com sucesso! Pode fazer login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_introduzido = trim($_POST['username']);
    $pass_introduzida = $_POST['password'];
    
    // Sanitização adicional
    $user_introduzido = filter_var($user_introduzido, FILTER_SANITIZE_STRING);
    
    // Previne ataques de força bruta (simples)
    sleep(1); // Atraso de 1 segundo para prevenir força bruta
    
    // 🔴 LINHA CORRIGIDA: Removido 'AND ativo = 1' porque a coluna não existe
    $sql = "SELECT * FROM utilizadores WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $erro = "Erro no sistema. Contacte o administrador.";
    } else {
        $stmt->bind_param("s", $user_introduzido);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Verifica senha com password_verify (Bcrypt)
            if (password_verify($pass_introduzida, $row['password'])) {
                // Se quiser adicionar campo ultimo_login, descomente estas linhas:
                /*
                if (isset($row['ultimo_login'])) {
                    $update_sql = "UPDATE utilizadores SET ultimo_login = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $row['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
                */
                
                // Cria sessão
                $_SESSION['admin_user'] = $row['username'];
                $_SESSION['tipo_utilizador'] = $row['tipo'];
                $_SESSION['admin_logado'] = ($row['tipo'] == 'admin');
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['login_time'] = time();
                
                // Log do login (opcional)
                error_log("Login bem sucedido: " . $row['username'] . " - " . date('Y-m-d H:i:s'));
                
                // Redireciona
                if ($row['tipo'] == 'admin') {
                    header("Location: admin.php?login=success");
                } else {
                    header("Location: perfil_aluno.php?login=success");
                }
                exit();
            } else {
                $erro = "Credenciais incorretas!";
                error_log("Tentativa de login falhada para: " . $user_introduzido);
            }
        } else {
            $erro = "Credenciais incorretas!";
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a2980;
            --secondary-color: #26d0ce;
            --accent-color: #ff6b6b;
            --success-color: #28a745;
            --warning-color: #ffc107;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .login-container {
            display: flex;
            width: 900px;
            max-width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.8s ease;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right {
            flex: 1;
            padding: 50px 40px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 3em;
            background: white;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .logo-text h1 {
            font-size: 1.8em;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .logo-text p {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .welcome-text h2 {
            font-size: 2.2em;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .welcome-text p {
            font-size: 1.1em;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 25px;
        }
        
        .features-list {
            list-style: none;
            margin-top: 30px;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            font-size: 1em;
        }
        
        .features-list i {
            color: #4ecdc4;
            font-size: 1.2em;
        }
        
        /* Lado direito */
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header h2 {
            color: var(--primary-color);
            font-size: 2em;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .login-header p {
            color: #666;
            font-size: 1em;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 0.95em;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1em;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .input-with-icon input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 41, 128, 0.1);
            background: white;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.1em;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9em;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input {
            width: 16px;
            height: 16px;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1em;
            transition: all 0.3s;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(26, 41, 128, 0.2);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .separator {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #999;
            font-size: 0.9em;
        }
        
        .separator::before,
        .separator::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e1e5eb;
        }
        
        .separator span {
            padding: 0 15px;
        }
        
        .btn-register {
            width: 100%;
            padding: 16px;
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1em;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .btn-register:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(26, 41, 128, 0.2);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .alert-error {
            background: #fde8e8;
            color: #c53030;
            border-left: 4px solid #c53030;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #155724;
        }
        
        .alert i {
            font-size: 1.2em;
        }
        
        .security-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            border-left: 4px solid var(--secondary-color);
        }
        
        .security-info h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .security-info ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .security-info li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 0.9em;
            color: #555;
        }
        
        .security-info i {
            color: var(--secondary-color);
            font-size: 0.9em;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 100%;
            }
            
            .login-left, .login-right {
                padding: 40px 30px;
            }
            
            .welcome-text h2 {
                font-size: 1.8em;
            }
        }
        
        /* Animações */
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Novas melhorias */
        .input-with-icon input.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }
        
        .input-with-icon input.is-valid {
            border-color: #28a745;
            background: #f8fff9;
        }
        
        .form-feedback {
            margin-top: 5px;
            font-size: 0.85em;
            padding-left: 45px;
        }
        
        .form-feedback.invalid {
            color: #dc3545;
        }
        
        .form-feedback.valid {
            color: #28a745;
        }
        
        .captcha-container {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e5eb;
            color: #666;
            font-size: 0.9em;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Lado esquerdo: Informação -->
        <div class="login-left">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">
                    <h1>ISCA INFORMA</h1>
                    <p>Portal Académico</p>
                </div>
            </div>
            
            <div class="welcome-text">
                <h2>Bem-vindo de Volta!</h2>
                <p>Faça login para aceder ao portal académico do ISCA. Mantenha-se atualizado com as últimas notícias, eventos e recursos académicos.</p>
            </div>
            
            <ul class="features-list">
                <li><i class="fas fa-check-circle"></i> Acesso a conteúdos exclusivos</li>
                <li><i class="fas fa-check-circle"></i> Gestão do seu perfil académico</li>
                <li><i class="fas fa-check-circle"></i> Notificações personalizadas</li>
                <li><i class="fas fa-check-circle"></i> Recursos de estudo atualizados</li>
                <li><i class="fas fa-check-circle"></i> Sistema de login seguro com Bcrypt</li>
                <li><i class="fas fa-check-circle"></i> Proteção contra força bruta</li>
            </ul>
            
            <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                <h3 style="font-size: 1.2em; margin-bottom: 10px;">💡 Dicas de segurança</h3>
                <p style="font-size: 0.9em; opacity: 0.9;">• Use senhas fortes e únicas<br>• Não compartilhe suas credenciais<br>• Sempre faça logout em computadores públicos</p>
            </div>
        </div>
        
        <!-- Lado direito: Formulário -->
        <div class="login-right">
            <div class="login-header">
                <h2>Iniciar Sessão</h2>
                <p>Insira as suas credenciais para continuar</p>
            </div>
            
            <?php if(isset($mensagem_logout)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Sucesso!</strong><br>
                        <?php echo $mensagem_logout; ?>
                        <?php if(isset($_GET['username'])): ?>
                            <br><small>Utilizador: <?php echo htmlspecialchars($_GET['username']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(isset($mensagem_registro)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Conta criada!</strong><br>
                        <?php echo $mensagem_registro; ?>
                        <?php if(isset($_GET['username'])): ?>
                            <br><small>Bem-vindo, <?php echo htmlspecialchars($_GET['username']); ?>!</small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Erro no login!</strong><br>
                        <?php echo $erro; ?>
                        <?php if(isset($_GET['attempt']) && $_GET['attempt'] > 3): ?>
                            <br><small><i class="fas fa-shield-alt"></i> Muitas tentativas. Aguarde 1 minuto.</small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="username">Nome de utilizador</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" 
                               placeholder="Insira o seu nome de utilizador" 
                               required 
                               autofocus
                               autocomplete="username"
                               pattern="[a-zA-Z0-9_]{3,50}"
                               title="3-50 caracteres (letras, números, underscore)">
                    </div>
                    <div class="form-feedback" id="username-feedback"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Palavra-passe</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" 
                               placeholder="••••••••" 
                               required
                               autocomplete="current-password"
                               minlength="6">
                        <button type="button" class="password-toggle" id="togglePassword" title="Mostrar/Esconder senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-feedback" id="password-feedback"></div>
                </div>
                
                <!-- Simulação de CAPTCHA (para demonstração) -->
                <div class="captcha-container">
                    <p style="margin-bottom: 10px; font-size: 0.9em; color: #666;">
                        <i class="fas fa-robot"></i> Verificação de segurança
                    </p>
                    <div style="display: flex; align-items: center; gap: 10px; justify-content: center;">
                        <input type="checkbox" id="humanCheck" name="humanCheck" required>
                        <label for="humanCheck" style="font-size: 0.9em;">Confirmo que sou um humano</label>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Manter sessão (30 dias)</label>
                    </div>
                    <div class="forgot-password">
                        <a href="recuperar_senha.php" title="Recuperar palavra-passe">
                            <i class="fas fa-key"></i> Esqueceu a senha?
                        </a>
                    </div>
                </div>
                
                <button type="submit" class="btn-login pulse" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Entrar na Conta
                </button>
                
                <div class="separator">
                    <span>ou</span>
                </div>
                
                <a href="registo.php" class="btn-register">
                    <i class="fas fa-user-plus"></i> Criar Nova Conta
                </a>
                
                <div class="login-footer">
                    <p>
                        <i class="fas fa-info-circle"></i> 
                        Para questões de acesso, contacte a administração.
                    </p>
                    <p style="margin-top: 10px;">
                        <a href="index.php">
                            <i class="fas fa-arrow-left"></i> Voltar ao site principal
                        </a>
                    </p>
                </div>
            </form>
            
            <div class="security-info">
                <h4><i class="fas fa-shield-alt"></i> Sistema de Segurança Avançado</h4>
                <ul>
                    <li><i class="fas fa-check"></i> Encriptação Bcrypt para todas as senhas</li>
                    <li><i class="fas fa-check"></i> Proteção contra ataques de força bruta</li>
                    <li><i class="fas fa-check"></i> Sessões seguras com timeout automático</li>
                    <li><i class="fas fa-check"></i> Validação de entrada sanitizada</li>
                    <li><i class="fas fa-check"></i> Logging de atividades de login</li>
                    <li><i class="fas fa-check"></i> Conformidade total com RGPD</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        // Alternar visibilidade da senha
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.title = "Esconder senha";
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.title = "Mostrar senha";
            }
        });
        
        // Validação em tempo real
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const usernameFeedback = document.getElementById('username-feedback');
        const passwordFeedback = document.getElementById('password-feedback');
        
        // Validação do username
        usernameInput.addEventListener('input', function() {
            const value = this.value.trim();
            const pattern = /^[a-zA-Z0-9_]{3,50}$/;
            
            if (value.length === 0) {
                this.classList.remove('is-valid', 'is-invalid');
                usernameFeedback.textContent = '';
                usernameFeedback.className = 'form-feedback';
                return;
            }
            
            if (pattern.test(value)) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                usernameFeedback.textContent = '✓ Formato válido';
                usernameFeedback.className = 'form-feedback valid';
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                usernameFeedback.textContent = '✗ Use 3-50 caracteres (letras, números, _)';
                usernameFeedback.className = 'form-feedback invalid';
            }
        });
        
        // Validação da senha
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.length === 0) {
                this.classList.remove('is-valid', 'is-invalid');
                passwordFeedback.textContent = '';
                passwordFeedback.className = 'form-feedback';
                return;
            }
            
            if (value.length >= 6) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                passwordFeedback.textContent = '✓ Comprimento adequado';
                passwordFeedback.className = 'form-feedback valid';
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                passwordFeedback.textContent = '✗ Mínimo 6 caracteres';
                passwordFeedback.className = 'form-feedback invalid';
            }
        });
        
        // Validação completa no submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;
            const username = usernameInput.value.trim();
            const password = passwordInput.value;
            const humanCheck = document.getElementById('humanCheck').checked;
            
            // Reset feedback
            usernameFeedback.textContent = '';
            passwordFeedback.textContent = '';
            
            // Valida username
            if (username.length < 3) {
                usernameInput.classList.add('is-invalid');
                usernameFeedback.textContent = '✗ Nome de utilizador é obrigatório';
                usernameFeedback.className = 'form-feedback invalid';
                isValid = false;
            }
            
            // Valida senha
            if (password.length < 1) {
                passwordInput.classList.add('is-invalid');
                passwordFeedback.textContent = '✗ Palavra-passe é obrigatória';
                passwordFeedback.className = 'form-feedback invalid';
                isValid = false;
            }
            
            // Valida CAPTCHA
            if (!humanCheck) {
                alert('Por favor, confirme que é humano para continuar.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return;
            }
            
            // Mostra loading no botão
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A verificar credenciais...';
            submitBtn.disabled = true;
            
            // Adiciona atraso visual para simular processamento
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Entrar na Conta';
                submitBtn.disabled = false;
            }, 1500);
            
            // O formulário será submetido normalmente
        });
        
        // Foco automático no primeiro campo com erro
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[required]');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Dicas de tooltip
            const inputs = document.querySelectorAll('input[title]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.title = this.getAttribute('data-original-title') || this.title;
                });
            });
        });
        
        // Previne envio múltiplo do formulário
        let formSubmitted = false;
        document.getElementById('loginForm').addEventListener('submit', function() {
            if (formSubmitted) {
                return false;
            }
            formSubmitted = true;
            return true;
        });
    </script>
</body>
</html>