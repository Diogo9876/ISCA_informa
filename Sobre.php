<?php
// Ficheiro: sobre.php
session_start(); // Adicionado para manter consistência
require_once 'db_config.php'; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - ISCA INFORMA</title>
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
        /* CONTEÚDO PRINCIPAL - SOBRE */
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

        /* Card principal da página Sobre */
        .sobre-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #f0f0f0;
        }

        .sobre-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Títulos da página Sobre */
        .sobre-titulo {
            color: var(--azul-isca);
            border-bottom: 3px solid var(--azul-isca);
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-size: 2.2em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .sobre-subtitulo {
            color: var(--verde);
            margin: 40px 0 20px;
            font-size: 1.6em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Grid para visão e valores */
        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .mission-card {
            background: linear-gradient(135deg, #ffffff 0%, var(--cinza-claro) 100%);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }

        .mission-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            transition: width 0.3s;
        }

        .vision-card::before {
            background: linear-gradient(to bottom, var(--verde), #20c997);
        }

        .values-card::before {
            background: linear-gradient(to bottom, var(--azul-isca), var(--azul-claro));
        }

        .mission-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .mission-card:hover::before {
            width: 8px;
        }

        .mission-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            text-align: center;
        }

        .mission-title {
            color: var(--azul-isca);
            margin: 0 0 15px;
            font-size: 1.5rem;
            text-align: center;
        }

        .mission-description {
            color: #555;
            line-height: 1.8;
            text-align: center;
            font-size: 1.05rem;
        }

        /* Lista de cobertura */
        .coverage-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .coverage-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            margin: 15px 0;
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid var(--amarelo);
            transition: all 0.3s;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .coverage-item:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
        }

        .coverage-category {
            color: var(--azul-isca);
            font-size: 1.2rem;
            font-weight: bold;
            min-width: 140px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .coverage-category::before {
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .coverage-item:nth-child(1) .coverage-category::before {
            content: '\f0ac';
            color: var(--verde);
        }

        .coverage-item:nth-child(2) .coverage-category::before {
            content: '\f091';
            color: var(--amarelo);
        }

        .coverage-item:nth-child(3) .coverage-category::before {
            content: '\f2bb';
            color: var(--azul-claro);
        }

        .coverage-description {
            color: #555;
            line-height: 1.8;
            flex: 1;
            font-size: 1.05rem;
        }

        /* Callout box */
        .callout-box {
            background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin: 50px 0 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(26, 41, 128, 0.2);
            position: relative;
            overflow: hidden;
        }

        .callout-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .callout-title {
            color: white;
            margin: 0 0 15px;
            font-size: 2rem;
            position: relative;
            z-index: 1;
        }

        .callout-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            line-height: 1.6;
            margin: 0 0 25px;
            position: relative;
            z-index: 1;
        }

        .callout-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .callout-link:hover {
            background: white;
            color: var(--azul-isca);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            
            .mission-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .sobre-card {
                padding: 30px;
            }
            
            .sobre-titulo {
                font-size: 1.8em;
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
            
            .sobre-card {
                padding: 20px;
            }
            
            .mission-card {
                padding: 20px;
            }
            
            .coverage-item {
                flex-direction: column;
                gap: 10px;
                padding: 20px;
            }
            
            .coverage-category {
                min-width: auto;
            }
            
            .callout-box {
                padding: 25px;
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
            .sobre-titulo {
                font-size: 1.5em;
            }
            
            .sobre-subtitulo {
                font-size: 1.3em;
            }
            
            .mission-icon {
                font-size: 2.5rem;
            }
            
            .callout-title {
                font-size: 1.6em;
            }
            
            .callout-text {
                font-size: 1em;
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
    <p>Conheça a missão por trás do seu portal de notícias acadêmicas</p>
    
    <!-- Navegação Principal -->
    <nav>
        <a href="index.php" class="btn-moderno"><i class="fas fa-home"></i> Início</a>
        <a href="sobre.php" class="btn-moderno btn-sobre"><i class="fas fa-info-circle"></i> Sobre Nós</a>
        <a href="contacto.php" class="btn-moderno btn-contato"><i class="fas fa-envelope"></i> Contactos</a>
    </nav>
</header>

<!-- =========================================== -->
<!-- CONTEÚDO PRINCIPAL - SOBRE -->
<!-- =========================================== -->
<main>
    <div id="coluna-principal">
        <article class="sobre-card fade-in">
            <h1 class="sobre-titulo">
                <i class="fas fa-university"></i> Sobre o ISCA INFORMA
            </h1>
            
            <div class="sobre-conteudo">
                <h2 class="sobre-subtitulo">
                    <i class="fas fa-bullseye"></i> A Nossa Missão
                </h2>
                <p style="font-size: 1.1rem; line-height: 1.8; color: #444; margin-bottom: 30px;">
                    O <strong style="color: var(--azul-isca);">ISCA INFORMA</strong> nasceu da necessidade de centralizar a comunicação de excelência do Instituto Superior de Contabilidade e Administração. O nosso objetivo é ser a ponte entre a academia e o mercado de trabalho, mantendo alunos, docentes e alumni informados sobre o que de mais relevante acontece no campus.
                </p>

                <div class="mission-grid">
                    <div class="mission-card vision-card fade-in">
                        <div class="mission-icon">🎯</div>
                        <h3 class="mission-title">Visão</h3>
                        <p class="mission-description">Ser o portal de referência para notícias de gestão, contabilidade e marketing na comunidade académica da Universidade de Aveiro.</p>
                    </div>
                    
                    <div class="mission-card values-card fade-in" style="animation-delay: 0.2s;">
                        <div class="mission-icon">🤝</div>
                        <h3 class="mission-title">Valores</h3>
                        <p class="mission-description">Rigor informativo, atualidade, apoio ao estudante e promoção da excelência académica e profissional.</p>
                    </div>
                </div>

                <h2 class="sobre-subtitulo">
                    <i class="fas fa-coverage"></i> O que cobrimos?
                </h2>
                
                <ul class="coverage-list">
                    <li class="coverage-item fade-in">
                        <strong class="coverage-category">Conferências</strong>
                        <span class="coverage-description">Cobertura de eventos nacionais e internacionais realizados no ISCA, com entrevistas exclusivas e resumos detalhados das apresentações.</span>
                    </li>
                    <li class="coverage-item fade-in" style="animation-delay: 0.1s;">
                        <strong class="coverage-category">Prémios</strong>
                        <span class="coverage-description">Celebração do mérito e distinções dos nossos alunos e parceiros, destacando as conquistas que enobrecem a nossa comunidade.</span>
                    </li>
                    <li class="coverage-item fade-in" style="animation-delay: 0.2s;">
                        <strong class="coverage-category">Carreiras</strong>
                        <span class="coverage-description">Divulgação de oportunidades profissionais, eventos de networking, estágios e todas as informações que impulsionam as carreiras dos nossos estudantes.</span>
                    </li>
                </ul>

                <div class="callout-box fade-in" style="animation-delay: 0.3s;">
                    <h3 class="callout-title">Faça parte da nossa rede</h3>
                    <p class="callout-text">Mantenha-se atualizado com as últimas notícias e oportunidades do ISCA.</p>
                    <a href="#newsletter" class="callout-link">
                        <i class="fas fa-envelope-open-text"></i> Subscreva a nossa newsletter
                    </a>
                </div>
            </div>
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
    const buttons = document.querySelectorAll('.btn-moderno, .callout-link, #newsletter button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Efeito nos cards de missão
    const missionCards = document.querySelectorAll('.mission-card');
    missionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Efeito nos itens da lista
    const coverageItems = document.querySelectorAll('.coverage-item');
    coverageItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(10px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Título do site clica para ir para a página inicial
    const siteTitle = document.querySelector('header h1 a');
    // O link já está definido no HTML com href="index.php"
    // Removemos qualquer evento que possa estar a interferir
    siteTitle.removeEventListener('click', siteTitle.clickHandler);
    
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