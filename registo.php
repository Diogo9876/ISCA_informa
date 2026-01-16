<?php
// registo.php - Página de Registo
session_start();
require_once 'db_config.php';

// Se já está logado, redireciona para a página adequada
if (isset($_SESSION['admin_user'])) {
    if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização dos dados
    $nome_completo = trim($_POST['nome_completo']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validações
    $erros = [];
    
    if (strlen($nome_completo) < 3) {
        $erros[] = "O nome completo deve ter pelo menos 3 caracteres!";
    }
    
    if (strlen($username) < 3) {
        $erros[] = "O username deve ter pelo menos 3 caracteres!";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido!";
    }
    
    if (strlen($password) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres!";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $erros[] = "A senha deve conter pelo menos uma letra maiúscula!";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $erros[] = "A senha deve conter pelo menos um número!";
    }
    if ($password !== $confirm_password) {
        $erros[] = "As senhas não coincidem!";
    }
    
    if (empty($erros)) {
        // Verifica se username já existe
        $check_sql = "SELECT id FROM utilizadores WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            $erro = "Erro no sistema. Contacte o administrador.";
        } else {
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $erro = "Username já está em uso!";
            } else {
                // Verifica se email já existe
                $check_email_sql = "SELECT id FROM utilizadores WHERE email = ?";
                $check_email_stmt = $conn->prepare($check_email_sql);
                $check_email_stmt->bind_param("s", $email);
                $check_email_stmt->execute();
                $check_email_result = $check_email_stmt->get_result();
                
                if ($check_email_result->num_rows > 0) {
                    $erro = "Email já está em uso!";
                    $check_email_stmt->close();
                } else {
                    $check_email_stmt->close();
                    
                    // Cria hash da senha
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insere novo utilizador
                    $insert_sql = "INSERT INTO utilizadores (username, password, tipo, nome_completo, email) VALUES (?, ?, 'user', ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    
                    if ($insert_stmt === false) {
                        $erro = "Erro ao preparar a inserção: " . $conn->error;
                    } else {
                        $insert_stmt->bind_param("ssss", $username, $hash, $nome_completo, $email);
                        
                        if ($insert_stmt->execute()) {
                            // Redireciona para login com sucesso
                            header("Location: login.php?registro=success&username=" . urlencode($username));
                            exit();
                        } else {
                            $erro = "Erro ao criar conta: " . $conn->error;
                        }
                        $insert_stmt->close();
                    }
                }
            }
            $check_stmt->close();
        }
    } else {
        $erro = implode("<br>", $erros);
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================== */
        /* ESTILOS GERAIS (IGUAL AO INDEX) */
        /* =========================================== */
        :root {
            --azul-isca: #1a2980;
            --azul-claro: #26d0ce;
            --roxo: #8a2be2;
            --verde: #28a745;
            --vermelho: #dc3545;
            --amarelo: #ffc107;
            --cinza-claro: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* =========================================== */
        /* CABEÇALHO (IGUAL AO INDEX) */
        /* =========================================== */
        .registro-header {
            background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%);
            color: white;
            padding: 25px 0;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 0 0 25px 25px;
            margin-bottom: 40px;
        }

        .registro-header h1 { 
            margin: 0; 
            font-size: 2.8em; 
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .registro-header h1 a {
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .registro-header h1 a:hover {
            transform: scale(1.05);
        }

        .registro-header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        /* =========================================== */
        /* BOTÕES (IGUAL AO INDEX) */
        /* =========================================== */
        .btn-moderno {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(45deg, var(--azul-isca), var(--azul-claro));
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(26, 41, 128, 0.25);
            font-size: 1em;
            min-width: 150px;
        }

        .btn-moderno:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(26, 41, 128, 0.35);
            background: linear-gradient(45deg, var(--azul-claro), var(--azul-isca));
        }

        .btn-moderno i {
            font-size: 1.1em;
        }

        .btn-login {
            background: linear-gradient(45deg, var(--amarelo), #ff9800);
            color: #212529;
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.25);
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #ff9800, var(--amarelo));
            box-shadow: 0 12px 30px rgba(255, 193, 7, 0.35);
        }

        .btn-voltar {
            background: linear-gradient(45deg, #6c757d, #495057);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.25);
        }

        .btn-voltar:hover {
            background: linear-gradient(45deg, #495057, #6c757d);
            box-shadow: 0 12px 30px rgba(108, 117, 125, 0.35);
        }

        .btn-registrar {
            background: linear-gradient(45deg, var(--verde), #20c997);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
            font-size: 1.1em;
            padding: 15px 35px;
            width: 100%;
        }

        .btn-registrar:hover {
            background: linear-gradient(45deg, #20c997, var(--verde));
            box-shadow: 0 12px 30px rgba(40, 167, 69, 0.35);
        }

        /* =========================================== */
        /* CONTAINER DE REGISTO */
        /* =========================================== */
        .registro-container {
            max-width: 500px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }

        .registro-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .registro-card h2 {
            color: var(--azul-isca);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        /* =========================================== */
        /* MENSAGENS DE ERRO/SUCESSO */
        /* =========================================== */
        .message-box {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-box {
            background: linear-gradient(135deg, #ffeaea 0%, #ffcdcd 100%);
            color: #c62828;
            border: 2px solid #ffcccc;
        }

        .success-box {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
            border: 2px solid #c3e6cb;
        }

        /* =========================================== */
        /* FORMULÁRIO */
        /* =========================================== */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.95em;
        }

        .form-group label i {
            color: var(--azul-isca);
            width: 20px;
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
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s;
            background: #f8f9fa;
            font-family: inherit;
        }

        .input-with-icon input:focus {
            border-color: var(--azul-isca);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 41, 128, 0.1);
            background: white;
        }

        .input-with-icon input.is-valid {
            border-color: var(--verde);
            background: #f8fff9;
        }

        .input-with-icon input.is-invalid {
            border-color: var(--vermelho);
            background: #fff5f5;
        }

        .form-feedback {
            margin-top: 5px;
            font-size: 0.85em;
            padding-left: 45px;
        }

        .form-feedback.valid {
            color: var(--verde);
        }

        .form-feedback.invalid {
            color: var(--vermelho);
        }

        /* =========================================== */
        /* FORÇA DA SENHA */
        /* =========================================== */
        .password-strength {
            margin-top: 10px;
            height: 5px;
            background: #e1e5eb;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .strength-weak .strength-bar {
            background: #ff4757;
            width: 25%;
        }

        .strength-medium .strength-bar {
            background: #ffa502;
            width: 50%;
        }

        .strength-good .strength-bar {
            background: #2ed573;
            width: 75%;
        }

        .strength-strong .strength-bar {
            background: #1e90ff;
            width: 100%;
        }

        .strength-text {
            margin-top: 5px;
            font-size: 0.85em;
            text-align: right;
        }

        .strength-weak .strength-text { color: #ff4757; }
        .strength-medium .strength-text { color: #ffa502; }
        .strength-good .strength-text { color: #2ed573; }
        .strength-strong .strength-text { color: #1e90ff; }

        /* =========================================== */
        /* CHECKBOX TERMOS */
        /* =========================================== */
        .terms-checkbox {
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
            accent-color: var(--azul-isca);
        }

        .checkbox-group label {
            font-size: 0.9em;
            color: #666;
            line-height: 1.5;
        }

        .checkbox-group a {
            color: var(--azul-isca);
            text-decoration: none;
            font-weight: 600;
        }

        .checkbox-group a:hover {
            text-decoration: underline;
        }

        /* =========================================== */
        /* LINKS E FOOTER */
        /* =========================================== */
        .registro-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e1e5eb;
            color: #666;
            font-size: 0.95em;
        }

        .registro-links a {
            color: var(--azul-isca);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .registro-links a:hover {
            color: var(--azul-claro);
            text-decoration: underline;
        }

        .registro-footer {
            text-align: center;
            padding: 25px 0;
            color: #666;
            font-size: 0.9em;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            border-radius: 25px 25px 0 0;
            margin-top: 50px;
        }

        .registro-footer a {
            color: var(--amarelo);
            text-decoration: none;
            font-weight: bold;
        }

        .registro-footer a:hover {
            color: white;
            text-decoration: underline;
        }

        /* =========================================== */
        /* INFORMAÇÕES DE SEGURANÇA */
        /* =========================================== */
        .security-info {
            background: linear-gradient(135deg, #e8f4fd 0%, #d1e7ff 100%);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
            border-left: 4px solid var(--azul-isca);
        }

        .security-info h4 {
            color: var(--azul-isca);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1em;
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
            color: var(--azul-isca);
            font-size: 0.9em;
        }

        /* =========================================== */
        /* RESPONSIVIDADE */
        /* =========================================== */
        @media (max-width: 768px) {
            .registro-container {
                padding: 0 15px;
            }
            
            .registro-card {
                padding: 30px 20px;
            }
            
            .registro-header h1 {
                font-size: 2.2em;
            }
            
            .registro-card h2 {
                font-size: 1.8em;
            }
            
            .btn-moderno {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .registro-header {
                padding: 20px 15px;
            }
            
            .registro-card {
                padding: 25px 15px;
            }
            
            .input-with-icon input {
                padding: 12px 12px 12px 40px;
            }
        }
    </style>
</head>
<body>

<!-- =========================================== -->
<!-- CABEÇALHO -->
<!-- =========================================== -->
<header class="registro-header">
    <h1><a href="index.php">🎓 ISCA INFORMA</a></h1>
    <p>Criar Conta - Portal Acadêmico</p>
    
    <div style="margin-top: 20px;">
        <a href="login.php" class="btn-moderno btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar ao Login
        </a>
    </div>
</header>

<!-- =========================================== -->
<!-- CONTEÚDO PRINCIPAL -->
<!-- =========================================== -->
<main class="registro-container">
    <div class="registro-card">
        <h2><i class="fas fa-user-plus"></i> Criar Nova Conta</h2>
        
        <?php if($erro): ?>
            <div class="message-box error-box">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="registroForm" novalidate>
            <!-- Nome Completo -->
            <div class="form-group">
                <label for="nome_completo"><i class="fas fa-user"></i> Nome Completo</label>
                <div class="input-with-icon">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" 
                           id="nome_completo" 
                           name="nome_completo" 
                           value="<?php echo isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : ''; ?>" 
                           placeholder="Ex: João Silva Santos"
                           required>
                </div>
                <div class="form-feedback" id="nome-feedback"></div>
            </div>
            
            <!-- Username -->
            <div class="form-group">
                <label for="username"><i class="fas fa-at"></i> Nome de Utilizador</label>
                <div class="input-with-icon">
                    <i class="fas fa-user-tag"></i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           placeholder="Ex: joao.silva"
                           required>
                </div>
                <div class="form-feedback" id="username-feedback"></div>
            </div>
            
            <!-- Email -->
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope-open"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           placeholder="exemplo@email.com"
                           required>
                </div>
                <div class="form-feedback" id="email-feedback"></div>
            </div>
            
            <!-- Senha -->
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Senha</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Mínimo 8 caracteres (com maiúscula e número)"
                           required>
                    <button type="button" class="password-toggle" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer;" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="password-strength">
                    <div class="strength-bar"></div>
                </div>
                <div class="strength-text" id="strength-text">Força da senha: Não definida</div>
                <div class="form-feedback" id="password-feedback"></div>
            </div>
            
            <!-- Confirmar Senha -->
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Senha</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           placeholder="Repita a sua senha"
                           required>
                    <button type="button" class="password-toggle" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer;" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="form-feedback" id="confirm-feedback"></div>
            </div>
            
            <!-- Termos e Condições -->
            <div class="terms-checkbox">
                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Concordo com os <a href="#">Termos de Serviço</a> e <a href="#">Política de Privacidade</a> do ISCA INFORMA.
                    </label>
                </div>
            </div>
            
            <!-- Botão de Registro -->
            <button type="submit" class="btn-moderno btn-registrar">
                <i class="fas fa-user-check"></i> Criar Minha Conta
            </button>
        </form>
        
        <!-- Links -->
        <div class="registro-links">
            <p>Já tem uma conta? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Faça Login Aqui</a></p>
        </div>
        
        <!-- Informações de Segurança -->
        <div class="security-info">
            <h4><i class="fas fa-shield-alt"></i> A sua segurança é importante para nós</h4>
            <ul>
                <li><i class="fas fa-check-circle"></i> Todas as senhas são encriptadas com Bcrypt</li>
                <li><i class="fas fa-check-circle"></i> Proteção contra ataques de força bruta</li>
                <li><i class="fas fa-check-circle"></i> Conformidade total com o RGPD</li>
                <li><i class="fas fa-check-circle"></i> Dados armazenados em servidores seguros</li>
            </ul>
        </div>
    </div>
</main>

<!-- =========================================== -->
<!-- RODAPÉ -->
<!-- =========================================== -->
<footer class="registro-footer">
    <p>&copy; <?php echo date('Y'); ?> ISCA INFORMA - Instituto Superior de Contabilidade e Administração</p>
    <p style="margin-top: 10px;">
        <a href="index.php"><i class="fas fa-home"></i> Voltar ao Site</a> | 
        <a href="sobre.php"><i class="fas fa-info-circle"></i> Sobre Nós</a> | 
        <a href="contacto.php"><i class="fas fa-headset"></i> Suporte</a>
    </p>
</footer>

<script>
// Funções de validação
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const strengthClasses = ['strength-weak', 'strength-weak', 'strength-medium', 'strength-good', 'strength-strong'];
    const strengthTexts = ['Muito fraca', 'Fraca', 'Média', 'Boa', 'Muito forte'];
    
    return {
        class: strengthClasses[strength],
        text: strengthTexts[strength],
        score: strength
    };
}

function updatePasswordStrength() {
    const password = document.getElementById('password').value;
    const strength = checkPasswordStrength(password);
    const strengthElement = document.getElementById('password-strength');
    const strengthText = document.getElementById('strength-text');
    const passwordField = document.getElementById('password');
    
    // Atualiza visual
    strengthElement.className = 'password-strength ' + strength.class;
    strengthText.textContent = 'Força da senha: ' + strength.text;
    strengthText.className = 'strength-text';
    
    // Atualiza campo
    if (strength.score >= 3) {
        passwordField.classList.add('is-valid');
        passwordField.classList.remove('is-invalid');
    } else if (password.length > 0) {
        passwordField.classList.add('is-invalid');
        passwordField.classList.remove('is-valid');
    } else {
        passwordField.classList.remove('is-valid', 'is-invalid');
    }
    
    // Valida confirmação
    validateConfirmPassword();
}

function validateConfirmPassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const confirmField = document.getElementById('confirm_password');
    const confirmFeedback = document.getElementById('confirm-feedback');
    
    if (confirmPassword.length === 0) {
        confirmField.classList.remove('is-valid', 'is-invalid');
        confirmFeedback.textContent = '';
        return;
    }
    
    if (password === confirmPassword && password.length >= 8) {
        confirmField.classList.add('is-valid');
        confirmField.classList.remove('is-invalid');
        confirmFeedback.textContent = '✓ Senhas coincidem';
        confirmFeedback.className = 'form-feedback valid';
    } else {
        confirmField.classList.add('is-invalid');
        confirmField.classList.remove('is-valid');
        confirmFeedback.textContent = '✗ As senhas não coincidem';
        confirmFeedback.className = 'form-feedback invalid';
    }
}

function validateUsername() {
    const username = document.getElementById('username').value;
    const usernameField = document.getElementById('username');
    const usernameFeedback = document.getElementById('username-feedback');
    const usernamePattern = /^[a-zA-Z0-9_]{3,20}$/;
    
    if (username.length === 0) {
        usernameField.classList.remove('is-valid', 'is-invalid');
        usernameFeedback.textContent = '';
        return;
    }
    
    if (usernamePattern.test(username)) {
        usernameField.classList.add('is-valid');
        usernameField.classList.remove('is-invalid');
        usernameFeedback.textContent = '✓ Formato válido';
        usernameFeedback.className = 'form-feedback valid';
    } else {
        usernameField.classList.add('is-invalid');
        usernameField.classList.remove('is-valid');
        usernameFeedback.textContent = '✗ Use 3-20 caracteres (letras, números, _)';
        usernameFeedback.className = 'form-feedback invalid';
    }
}

function validateEmail() {
    const email = document.getElementById('email').value;
    const emailField = document.getElementById('email');
    const emailFeedback = document.getElementById('email-feedback');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email.length === 0) {
        emailField.classList.remove('is-valid', 'is-invalid');
        emailFeedback.textContent = '';
        return;
    }
    
    if (emailPattern.test(email)) {
        emailField.classList.add('is-valid');
        emailField.classList.remove('is-invalid');
        emailFeedback.textContent = '✓ Email válido';
        emailFeedback.className = 'form-feedback valid';
    } else {
        emailField.classList.add('is-invalid');
        emailField.classList.remove('is-valid');
        emailFeedback.textContent = '✗ Por favor, insira um email válido';
        emailFeedback.className = 'form-feedback invalid';
    }
}

function validateName() {
    const name = document.getElementById('nome_completo').value;
    const nameField = document.getElementById('nome_completo');
    const nameFeedback = document.getElementById('nome-feedback');
    
    if (name.length === 0) {
        nameField.classList.remove('is-valid', 'is-invalid');
        nameFeedback.textContent = '';
        return;
    }
    
    if (name.length >= 3) {
        nameField.classList.add('is-valid');
        nameField.classList.remove('is-invalid');
        nameFeedback.textContent = '✓ Nome válido';
        nameFeedback.className = 'form-feedback valid';
    } else {
        nameField.classList.add('is-invalid');
        nameField.classList.remove('is-valid');
        nameFeedback.textContent = '✗ Mínimo 3 caracteres';
        nameFeedback.className = 'form-feedback invalid';
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentNode.querySelector('.password-toggle i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa validações
    validateName();
    validateUsername();
    validateEmail();
    updatePasswordStrength();
    validateConfirmPassword();
    
    // Event listeners em tempo real
    document.getElementById('nome_completo').addEventListener('input', validateName);
    document.getElementById('username').addEventListener('input', validateUsername);
    document.getElementById('email').addEventListener('input', validateEmail);
    document.getElementById('password').addEventListener('input', updatePasswordStrength);
    document.getElementById('confirm_password').addEventListener('input', validateConfirmPassword);
    
    // Validação completa no submit
    document.getElementById('registroForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Valida todos os campos
        validateName();
        validateUsername();
        validateEmail();
        updatePasswordStrength();
        validateConfirmPassword();
        
        // Verifica se há campos inválidos
        const invalidFields = document.querySelectorAll('.is-invalid');
        if (invalidFields.length > 0) {
            isValid = false;
            e.preventDefault();
            
            // Scroll para o primeiro erro
            invalidFields[0].scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Mostra alerta
            alert('Por favor, corrija os erros destacados no formulário antes de continuar.');
        }
        
        // Verifica termos
        if (!document.getElementById('terms').checked) {
            isValid = false;
            e.preventDefault();
            alert('Por favor, aceite os Termos de Serviço para continuar.');
        }
        
        if (isValid) {
            // Animação de loading
            const submitBtn = this.querySelector('.btn-registrar');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A processar...';
            submitBtn.disabled = true;
            
            // Restaura após 2 segundos (caso não redirecione)
            setTimeout(() => {
                submitBtn.innerHTML = originalHTML;
                submitBtn.disabled = false;
            }, 2000);
        }
    });
    
    // Efeito hover nos botões
    const buttons = document.querySelectorAll('.btn-moderno');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

</body>
</html>