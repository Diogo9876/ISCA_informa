<?php
// Ficheiro: contacto.php
session_start(); // Para manter consistência
require_once 'db_config.php'; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================== */
        /* ESTILOS GERAIS */
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
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }

        /* =========================================== */
        /* CABEÇALHO MODERNO */
        /* =========================================== */
        header {
            background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%);
            color: white;
            padding: 25px 0;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 0 0 25px 25px;
            margin-bottom: 40px;
        }

        header h1 { 
            margin: 0; 
            font-size: 2.8em; 
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        header h1 a {
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        header h1 a:hover {
            transform: scale(1.05);
        }

        header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        /* =========================================== */
        /* BOTÕES MODERNOS */
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

        /* Cores específicas para botões */
        .btn-sobre {
            background: linear-gradient(45deg, #17a2b8, #138496);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.25);
        }

        .btn-sobre:hover {
            background: linear-gradient(45deg, #138496, #17a2b8);
            box-shadow: 0 12px 30px rgba(23, 162, 184, 0.35);
        }

        .btn-contato {
            background: linear-gradient(45deg, #6f42c1, #6610f2);
            box-shadow: 0 6px 20px rgba(111, 66, 193, 0.25);
        }

        .btn-contato:hover {
            background: linear-gradient(45deg, #6610f2, #6f42c1);
            box-shadow: 0 12px 30px rgba(111, 66, 193, 0.35);
        }

        /* =========================================== */
        /* NAVEGAÇÃO */
        /* =========================================== */
        nav {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            padding: 0 20px;
        }

        /* =========================================== */
        /* CONTEÚDO PRINCIPAL - CONTACTOS */
        /* =========================================== */
        main {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 2fr 1fr; /* Igual ao index.php */
            gap: 40px;
        }

        #coluna-principal { 
            grid-column: 1 / 2; 
        }

        /* Card principal da página Contactos */
        .contacto-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #f0f0f0;
        }

        .contacto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Títulos da página Contactos */
        .contacto-titulo {
            color: var(--azul-isca);
            border-bottom: 3px solid var(--azul-isca);
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-size: 2.2em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .contacto-subtitulo {
            color: var(--verde);
            margin: 40px 0 20px;
            font-size: 1.6em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Estilos do formulário */
        .contacto-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #444;
            font-size: 1.1em;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--azul-isca);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 41, 128, 0.1);
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-enviar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(45deg, var(--verde), #20c997);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
            font-size: 1.1em;
            width: 100%;
            margin-top: 20px;
        }

        .btn-enviar:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(40, 167, 69, 0.35);
            background: linear-gradient(45deg, #20c997, var(--verde));
        }

        /* Mensagem de sucesso */
        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            border-left: 5px solid var(--verde);
            animation: fadeIn 0.5s ease;
        }

        /* Informações de contacto (apenas email) */
        .contacto-info-single {
            max-width: 500px;
            margin: 40px auto;
            background: linear-gradient(135deg, #ffffff 0%, var(--cinza-claro) 100%);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #e9ecef;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .contacto-info-single::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--azul-isca), var(--azul-claro));
        }

        .contacto-info-single:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .info-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            color: var(--azul-isca);
        }

        .info-title {
            color: var(--azul-isca);
            margin: 0 0 15px;
            font-size: 1.5rem;
        }

        .info-content {
            color: #555;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .info-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, var(--azul-isca), var(--azul-claro));
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(26, 41, 128, 0.25);
            margin-top: 20px;
            font-size: 1.1em;
        }

        .info-link:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(26, 41, 128, 0.35);
            background: linear-gradient(45deg, var(--azul-claro), var(--azul-isca));
        }

        /* =========================================== */
        /* BARRA LATERAL (NEWSLETTER) - IGUAL AO INDEX.PHP */
        /* =========================================== */
        #newsletter {
            grid-column: 2 / 3;
            position: sticky;
            top: 20px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            height: fit-content;
        }

        #newsletter h2 {
            border-bottom: 3px solid var(--verde);
            color: var(--verde);
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-size: 1.6em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #newsletter p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        #newsletter input[type="email"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1em;
            transition: all 0.3s;
        }

        #newsletter input[type="email"]:focus {
            border-color: var(--verde);
            outline: none;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        #newsletter button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, var(--verde), #20c997);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        #newsletter button:hover {
            background: linear-gradient(45deg, #20c997, var(--verde));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        /* =========================================== */
        /* MENSAGENS DE STATUS */
        /* =========================================== */
        .status-message {
            padding: 15px;
            margin-top: 15px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-message.duplicate, .status-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* =========================================== */
        /* RODAPÉ */
        /* =========================================== */
        footer {
            text-align: center;
            padding: 25px 0;
            margin-top: 50px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            font-size: 0.95em;
            border-radius: 25px 25px 0 0;
        }

        footer p {
            margin: 10px 0;
        }

        footer a {
            color: var(--amarelo);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        footer a:hover {
            color: white;
            text-decoration: underline;
        }

        /* =========================================== */
        /* RESPONSIVIDADE */
        /* =========================================== */
        @media (max-width: 1024px) {
            main {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            #newsletter {
                grid-column: 1 / 2;
                position: static;
            }
            
            .contacto-card {
                padding: 30px;
            }
            
            .contacto-titulo {
                font-size: 1.8em;
            }
            
            .contacto-info-single {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-moderno {
                width: 100%;
                justify-content: center;
            }
            
            .contacto-card {
                padding: 20px;
            }
            
            .contacto-info-single {
                padding: 30px;
            }
            
            .contacto-titulo {
                font-size: 1.8em;
            }
            
            header {
                padding: 20px 15px;
            }
            
            header h1 {
                font-size: 2.2em;
            }
            
            #newsletter {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .contacto-titulo {
                font-size: 1.5em;
            }
            
            .contacto-subtitulo {
                font-size: 1.3em;
            }
            
            .info-icon {
                font-size: 2.5rem;
            }
            
            .btn-enviar, .info-link {
                padding: 12px 30px;
                font-size: 1em;
            }
            
            .contacto-info-single {
                padding: 25px;
            }
        }

        /* =========================================== */
        /* ANIMAÇÕES */
        /* =========================================== */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<!-- =========================================== -->
<!-- CABEÇALHO -->
<!-- =========================================== -->
<header>
    <h1><a href="index.php">🎓 ISCA INFORMA</a></h1>
    <p>Entre em contacto connosco - A sua opinião é importante</p>
    
    <!-- Navegação Principal -->
    <nav>
        <a href="index.php" class="btn-moderno"><i class="fas fa-home"></i> Início</a>
        <a href="sobre.php" class="btn-moderno btn-sobre"><i class="fas fa-info-circle"></i> Sobre Nós</a>
        <a href="contacto.php" class="btn-moderno btn-contato active"><i class="fas fa-envelope"></i> Contactos</a>
    </nav>
</header>

<!-- =========================================== -->
<!-- CONTEÚDO PRINCIPAL - CONTACTOS -->
<!-- =========================================== -->
<main>
    <div id="coluna-principal">
        <article class="contacto-card fade-in">
            <h1 class="contacto-titulo">
                <i class="fas fa-headset"></i> Fala Connosco
            </h1>
            
            <p style="font-size: 1.1rem; line-height: 1.8; color: #444; margin-bottom: 30px; text-align: center;">
                Tem uma sugestão de notícia ou quer reportar um evento no ISCA? Preencha o formulário abaixo ou entre em contacto através do nosso email.
            </p>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
                <div class="success-message fade-in">
                    <i class="fas fa-check-circle" style="font-size: 1.5em; margin-bottom: 10px; display: block;"></i>
                    <strong style="font-size: 1.2em;">Sucesso!</strong> A sua mensagem foi enviada à equipa editorial.
                </div>
            <?php endif; ?>

            <!-- Cartão único de contacto por email -->
            <div class="contacto-info-single fade-in">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="info-title">Contacto por Email</h3>
                <p class="info-content">
                    Para questões gerais, sugestões ou informações,<br>
                    entre em contacto connosco através do nosso email oficial.
                </p>
                <a href="mailto:iscainforma@ua.pt" class="info-link">
                    <i class="fas fa-paper-plane"></i> iscainforma@ua.pt
                </a>
                <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
                    <i class="fas fa-clock"></i> Resposta em até 48h úteis
                </p>
            </div>

            <h2 class="contacto-subtitulo">
                <i class="fas fa-edit"></i> Formulário de Contacto
            </h2>

            <form action="processa_contacto.php" method="POST" class="contacto-form">
                <div class="form-group">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-input" required placeholder="Digite o seu nome completo">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Institucional</label>
                    <input type="email" name="email" class="form-input" required placeholder="seu.email@ua.pt">
                </div>

                <div class="form-group">
                    <label class="form-label">Assunto</label>
                    <select name="assunto" class="form-select">
                        <option value="noticia">📰 Sugestão de Notícia</option>
                        <option value="evento">📅 Divulgação de Evento</option>
                        <option value="parceria">🤝 Parcerias Académicas</option>
                        <option value="erro">🐛 Reportar Erro Técnico</option>
                        <option value="outro">❓ Outro Assunto</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Mensagem</label>
                    <textarea name="mensagem" class="form-textarea" required placeholder="Escreva aqui a sua mensagem..."></textarea>
                </div>

                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane"></i> ENVIAR MENSAGEM
                </button>
            </form>
        </article>
    </div>

    <!-- =========================================== -->
    <!-- BARRA LATERAL (NEWSLETTER) - IGUAL AO INDEX.PHP -->
    <!-- =========================================== -->
    <aside id="newsletter" class="fade-in">
        <h2><i class="fas fa-envelope-open-text"></i> Newsletter ISCA</h2>
        <p>Subscreva para receber as nossas atualizações na sua caixa de entrada.</p>
        
        <form action="processa_subscricao.php" method="POST">
            <input type="email" name="email" placeholder="📧 O seu email" required>
            <button type="submit" name="submit">
                <i class="fas fa-paper-plane"></i> Subscrever Agora
            </button>
        </form>
        
        <?php 
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $message = '';
                $class = 'error';
                
                if ($status == 'success') {
                    $message = "🎉 Subscrição realizada com sucesso!";
                    $class = 'success';
                } elseif ($status == 'duplicate') {
                    $message = "⚠️ Este email já está subscrito.";
                    $class = 'duplicate';
                } else {
                    $message = "❌ Ocorreu um erro na subscrição.";
                    $class = 'error';
                }
                echo "<div class='status-message $class'>$message</div>";
            }
        ?>
        
        <?php if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === false): ?>
        <!-- Apenas mostra para alunos logados -->
        <div style="background: linear-gradient(135deg, var(--verde) 0%, #20c997 100%); color: white; padding: 20px; border-radius: 15px; margin-top: 30px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.2em;">🎓 Área do Aluno</h3>
            <p style="margin: 0 0 15px 0; font-size: 0.9em; opacity: 0.9;">Aceda ao seu perfil completo</p>
            <a href="perfil_aluno.php" class="btn-moderno" style="background: white; color: var(--verde); padding: 8px 20px; font-size: 0.9em;">
                <i class="fas fa-user-graduate"></i> Ver Meu Perfil
            </a>
        </div>
        <?php endif; ?>
    </aside>
</main>

<!-- =========================================== -->
<!-- RODAPÉ -->
<!-- =========================================== -->
<footer>
    <p>&copy; <?php echo date('Y'); ?> ISCA INFORMA - Instituto Superior de Contabilidade e Administração.</p>
    <p>
        <?php if (isset($_SESSION['admin_logado'])): ?>
            <i class="fas fa-user-circle"></i> Conectado como: <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> | 
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Área de Login</a> | 
            <a href="contacto.php"><i class="fas fa-headset"></i> Suporte</a>
        <?php endif; ?>
    </p>
</footer>

<script>
// Adiciona interatividade aos elementos
document.addEventListener('DOMContentLoaded', function() {
    // Efeito nos botões
    const buttons = document.querySelectorAll('.btn-moderno, .btn-enviar, .info-link, #newsletter button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Efeito no cartão de informação
    const infoCard = document.querySelector('.contacto-info-single');
    if (infoCard) {
        infoCard.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        infoCard.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }
    
    // Efeito nos inputs do formulário
    const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
        });
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Animações de entrada com atrasos
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach((element, index) => {
        if (!element.style.animationDelay) {
            element.style.animationDelay = (index * 0.1) + 's';
        }
    });
    
    // Smooth scroll para a newsletter
    const newsletterLinks = document.querySelectorAll('a[href="#newsletter"]');
    newsletterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const newsletterSection = document.getElementById('newsletter');
            newsletterSection.scrollIntoView({ behavior: 'smooth' });
            
            // Foca no input da newsletter
            setTimeout(() => {
                const emailInput = newsletterSection.querySelector('input[type="email"]');
                if (emailInput) {
                    emailInput.focus();
                }
            }, 500);
        });
    });
});
</script>

</body>
</html>