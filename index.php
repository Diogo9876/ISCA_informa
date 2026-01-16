<?php
// Ficheiro: index.php
// Inicia sessão para verificar login
session_start();
require_once 'db_config.php'; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCA INFORMA - O teu Portal de Notícias</title>
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
        /* BOTÕES MODERNOS - NOVO ESTILO */
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
        .btn-admin {
            background: linear-gradient(45deg, var(--verde), #20c997);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
        }

        .btn-admin:hover {
            background: linear-gradient(45deg, #20c997, var(--verde));
            box-shadow: 0 12px 30px rgba(40, 167, 69, 0.35);
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
        /* MENU DROPDOWN MODERNO (NOVO!) */
        /* =========================================== */
        .user-menu {
            position: relative;
            display: inline-block;
        }

        .user-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, var(--roxo), var(--azul-claro));
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(138, 43, 226, 0.25);
            border: none;
        }

        .user-toggle:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(138, 43, 226, 0.35);
            background: linear-gradient(45deg, var(--azul-claro), var(--roxo));
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
        }

        .user-toggle i {
            font-size: 0.9em;
            transition: transform 0.3s;
        }

        .user-menu.active .user-toggle i {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .user-menu.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--azul-isca), var(--azul-claro));
            color: white;
            text-align: center;
        }

        .dropdown-header h4 {
            margin: 0 0 5px 0;
            font-size: 1.1em;
        }

        .dropdown-header p {
            margin: 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 1px solid #f5f5f5;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            padding-left: 25px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: var(--azul-isca);
        }

        .dropdown-item.logout {
            color: var(--vermelho);
            border-top: 1px solid #f0f0f0;
            margin-top: 5px;
        }

        .dropdown-item.logout i {
            color: var(--vermelho);
        }

        .dropdown-item.logout:hover {
            background: #fff5f5;
            color: #c82333;
        }

        /* =========================================== */
        /* CONTEÚDO PRINCIPAL */
        /* =========================================== */
        main {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        #coluna-principal { 
            grid-column: 1 / 2; 
        }

        /* =========================================== */
        /* SECÇÕES DE CONTEÚDO */
        /* =========================================== */
        section {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #f0f0f0;
        }

        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        section h2 {
            border-bottom: 3px solid var(--azul-isca);
            padding-bottom: 15px;
            margin-bottom: 25px;
            color: var(--azul-isca);
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .noticia-item, .conferencia-item, .premio-item {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }

        .noticia-item:hover, .conferencia-item:hover, .premio-item:hover {
            background: var(--cinza-claro);
            padding-left: 15px;
            padding-right: 15px;
            margin: 0 -15px;
            border-radius: 10px;
        }

        .noticia-item h3, .conferencia-item h3, .premio-item h3 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 1.4em;
            line-height: 1.4;
        }

        .noticia-item a, .conferencia-item a, .premio-item a {
            color: var(--verde);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .noticia-item a:hover, .conferencia-item a:hover, .premio-item a:hover {
            color: var(--azul-isca);
            gap: 10px;
        }

        /* =========================================== */
        /* BARRA LATERAL (NEWSLETTER) - ATUALIZADA */
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

        /* =========================================== */
        /* CAIXA DE PROMOÇÃO DE LOGIN - REMOVIDA */
        /* =========================================== */
        /* REMOVIDA: .login-promo e todos os estilos relacionados */

        .btn-gradiente {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, var(--amarelo), #ff9800);
            color: #212529;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.25);
        }

        .btn-gradiente:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(255, 193, 7, 0.35);
            background: linear-gradient(45deg, #ff9800, var(--amarelo));
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
            
            header h1 {
                font-size: 2.2em;
            }
            
            .user-menu {
                margin-top: 15px;
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
            
            .user-toggle {
                width: 100%;
                justify-content: center;
            }
            
            .dropdown-menu {
                right: 50%;
                transform: translateX(50%) translateY(-10px);
            }
            
            .user-menu.active .dropdown-menu {
                transform: translateX(50%) translateY(0);
            }
            
            section {
                padding: 20px;
            }
            
            header {
                padding: 20px 15px;
            }
        }

        /* =========================================== */
        /* ANIMAÇÕES ADICIONAIS */
        /* =========================================== */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
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
    <p>O teu portal de Notícias, Prémios e Atualidades do ISCA</p>
    
    <!-- Navegação Principal -->
    <nav>
        <!-- Botões principais -->
        <a href="sobre.php" class="btn-moderno btn-sobre"><i class="fas fa-info-circle"></i> Sobre Nós</a>
        <a href="contacto.php" class="btn-moderno btn-contato"><i class="fas fa-envelope"></i> Contactos</a>
        
        <?php if (isset($_SESSION['admin_logado'])): ?>
            <!-- Usuário logado - MENU DROPDOWN -->
            <div class="user-menu" id="userMenu">
                <button class="user-toggle" id="userToggle">
                    <div class="user-avatar">
                        <?php 
                        $nome = htmlspecialchars($_SESSION['admin_user']);
                        echo strtoupper(substr($nome, 0, 1)); 
                        ?>
                    </div>
                    <span><?php echo $nome; ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="dropdown-header">
                        <h4>Olá, <?php echo $nome; ?>!</h4>
                        <p>
                            <?php 
                            if ($_SESSION['admin_logado'] === true) {
                                echo '👑 Administrador';
                            } else {
                                echo '🎓 Aluno ISCA';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <?php if ($_SESSION['admin_logado'] === true): ?>
                        <!-- Opções para ADMIN -->
                        <a href="admin.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Painel Administrativo
                        </a>
                        <a href="gerir_enquetes.php" class="dropdown-item">
                            <i class="fas fa-poll"></i> Gerir Enquetes
                        </a>
                    <?php else: ?>
                        <!-- Opções para ALUNO -->
                        <a href="perfil_aluno.php" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> Meu Perfil
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-book"></i> Disciplinas
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-calendar-alt"></i> Calendário
                        </a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i> Terminar Sessão
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Usuário NÃO logado -->
            <a href="login.php" class="btn-moderno btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </nav>
    
    <?php if (!isset($_SESSION['admin_logado'])): ?>
        <p style="color: rgba(255,255,255,0.9); margin-top: 15px; text-align: center; font-size: 0.95em;">
            <i class="fas fa-lock"></i> Faça login para aceder a conteúdos exclusivos
        </p>
    <?php endif; ?>
</header>

<!-- =========================================== -->
<!-- CONTEÚDO PRINCIPAL -->
<!-- =========================================== -->
<main>
    <div id="coluna-principal">
        
        <!-- Seção: Últimas Notícias -->
        <section id="noticias" class="fade-in">
            <h2><i class="fas fa-bullhorn"></i> Últimas Notícias</h2>
            <?php
            $sql_noticias = "SELECT artigo_id, titulo, resumo, data_publicacao FROM artigos WHERE categoria_id NOT IN (13, 14) ORDER BY data_publicacao DESC LIMIT 5";
            $result_noticias = $conn->query($sql_noticias);
            
            if ($result_noticias && $result_noticias->num_rows > 0) {
                while($row = $result_noticias->fetch_assoc()) {
                    echo '<article class="noticia-item">';
                    echo '    <h3>' . htmlspecialchars($row['titulo']) . '</h3>';
                    echo '    <p><i class="fas fa-calendar-alt"></i> Publicado em: ' . date('d/m/Y', strtotime($row['data_publicacao'])) . '</p>';
                    echo '    <p>' . htmlspecialchars($row['resumo']) . ' <a href="artigo.php?id=' . $row['artigo_id'] . '"><i class="fas fa-arrow-right"></i> Ler Mais</a></p>';
                    echo '</article>';
                }
            } else {
                echo '<p>De momento, não existem notícias gerais publicadas.</p>';
            }
            ?>
        </section>

        <!-- Seção: Próximas Conferências -->
        <section id="conferencias" class="fade-in">
            <h2><i class="fas fa-calendar-check"></i> Próximas Conferências</h2>
            <?php
            $sql_conferencias = "SELECT a.artigo_id, a.titulo, a.resumo, c.local_conferencia, c.data_inicio 
                                 FROM artigos a 
                                 JOIN conferencias c ON a.artigo_id = c.artigo_id
                                 WHERE a.categoria_id = 14 AND c.data_inicio >= CURDATE()
                                 ORDER BY c.data_inicio ASC LIMIT 3";
            $result_conferencias = $conn->query($sql_conferencias);

            if ($result_conferencias && $result_conferencias->num_rows > 0) {
                while($row = $result_conferencias->fetch_assoc()) {
                    echo '<article class="conferencia-item">';
                    echo '    <h3>' . htmlspecialchars($row['titulo']) . '</h3>';
                    echo '    <p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['local_conferencia']) . ' | <i class="fas fa-clock"></i> ' . date('d/m/Y', strtotime($row['data_inicio'])) . '</p>';
                    echo '    <p>' . htmlspecialchars($row['resumo']) . ' <a href="artigo.php?id=' . $row['artigo_id'] . '"><i class="fas fa-external-link-alt"></i> Ver Detalhes</a></p>';
                    echo '</article>';
                }
            } else {
                echo '<p>Não há conferências agendadas de momento.</p>';
            }
            ?>
        </section>

        <!-- Seção: Prémios e Distinções -->
        <section id="premios" class="fade-in">
            <h2><i class="fas fa-trophy"></i> Prémios e Distinções</h2>
            <?php
            $sql_premios = "SELECT a.artigo_id, a.titulo, a.resumo, p.nome_premio, p.ano_concessao 
                            FROM artigos a 
                            JOIN premios p ON a.artigo_id = p.artigo_id
                            WHERE a.categoria_id = 13
                            ORDER BY p.ano_concessao DESC, a.data_publicacao DESC LIMIT 3";
            $result_premios = $conn->query($sql_premios);

            if ($result_premios && $result_premios->num_rows > 0) {
                while($row = $result_premios->fetch_assoc()) {
                    echo '<article class="premio-item">';
                    echo '    <h3>' . htmlspecialchars($row['titulo']) . '</h3>';
                    echo '    <p><i class="fas fa-award"></i> ' . htmlspecialchars($row['nome_premio']) . ' | <i class="fas fa-calendar"></i> Ano: ' . $row['ano_concessao'] . '</p>';
                    echo '    <p>' . htmlspecialchars($row['resumo']) . ' <a href="artigo.php?id=' . $row['artigo_id'] . '"><i class="fas fa-medal"></i> Ver Vencedor</a></p>';
                    echo '</article>';
                }
            } else {
                echo '<p>De momento, não há prémios ou distinções recentes para apresentar.</p>';
            }
            ?>
        </section>
    </div>

    <!-- =========================================== -->
    <!-- BARRA LATERAL (NEWSLETTER) -->
    <!-- =========================================== -->
    <aside id="newsletter" class="fade-in">
        <h2><i class="fas fa-envelope-open-text"></i> Newsletter ISCA</h2>
        <p>Subscreva para receber as nossas atualizações na sua caixa de entrada.</p>
        
        <form action="processa_subscricao.php" method="POST">
            <input type="email" name="email" placeholder="📧 O seu email" required>
            <button type="submit" name="submit" class="btn-moderno" style="width: 100%;">
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
        
        <?php if (isset($_SESSION['admin_logado']) && isset($_SESSION['admin_user'])): ?>
        <!-- ENQUETE PARA UTILIZADORES LOGADOS (ALUNOS E ADMIN) -->
        <?php
        $user_identifier = $_SESSION['admin_user'];
        $is_admin = ($_SESSION['admin_logado'] === true);
        
        // 1. Buscar enquete ativa (para logados)
        $sql_enquete = "SELECT * FROM enquetes 
                        WHERE ativa = 1 
                        AND CURDATE() BETWEEN data_inicio AND data_fim 
                        LIMIT 1";
        
        $result_enquete = $conn->query($sql_enquete);
        
        if ($result_enquete && $result_enquete->num_rows > 0) {
            $enquete = $result_enquete->fetch_assoc();
            $enquete_id = $enquete['id'];
            
            // 2. Buscar opções
            $sql_opcoes = "SELECT * FROM opcoes_enquete WHERE enquete_id = $enquete_id";
            $result_opcoes = $conn->query($sql_opcoes);
            $opcoes_array = [];
            while($opcao = $result_opcoes->fetch_assoc()) {
                $opcoes_array[] = $opcao;
            }
            
            // 3. Verificar se já votou (admin pode votar como utilizador normal)
            $ja_votou = false;
            $voto_usuario = null;
            
            $sql_check_voto = "SELECT * FROM votos_enquete 
                              WHERE enquete_id = $enquete_id 
                              AND user_identifier = '$user_identifier'";
            $result_check = $conn->query($sql_check_voto);
            
            if ($result_check && $result_check->num_rows > 0) {
                $ja_votou = true;
                $voto_data = $result_check->fetch_assoc();
                $voto_usuario = $voto_data['opcao_id'];
            }
            
            // 4. Calcular resultados se já votou
            if ($ja_votou) {
                $sql_resultados = "SELECT o.id, o.texto_opcao, o.cor_hex, COUNT(v.id) as votos,
                                  ROUND((COUNT(v.id) * 100.0 / (SELECT COUNT(*) FROM votos_enquete WHERE enquete_id = $enquete_id)), 1) as percentagem
                                  FROM opcoes_enquete o
                                  LEFT JOIN votos_enquete v ON o.id = v.opcao_id
                                  WHERE o.enquete_id = $enquete_id
                                  GROUP BY o.id
                                  ORDER BY votos DESC";
                
                $result_resultados = $conn->query($sql_resultados);
                $total_votos = 0;
                $resultados_html = '';
                
                // Cores do site para as barras
                $cores_site = ['#FF6B6B', '#4ECDC4', '#FFD166', '#06D6A0', '#118AB2', '#8e2de2'];
                $cor_index = 0;
                
                while($row = $result_resultados->fetch_assoc()) {
                    $total_votos += $row['votos'];
                    $percentagem = $row['percentagem'] ?? 0;
                    // Usa cor personalizada se existir, senão usa cores do site
                    $cor = !empty($row['cor_hex']) ? $row['cor_hex'] : $cores_site[$cor_index % count($cores_site)];
                    $cor_index++;
                    $destaque = ($row['id'] == $voto_usuario) ? 'box-shadow: 0 0 10px ' . $cor . '; border: 2px solid ' . $cor . ';' : '';
                    
                    $resultados_html .= '
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-size: 0.9em; color: #333;">' . htmlspecialchars($row['texto_opcao']) . '</span>
                            <span style="font-size: 0.9em; font-weight: bold; color: var(--azul-isca);">' . $percentagem . '%</span>
                        </div>
                        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="background: ' . $cor . '; height: 100%; width: ' . $percentagem . '%; transition: width 1s ease; ' . $destaque . '"></div>
                        </div>
                        <div style="font-size: 0.8em; opacity: 0.7; text-align: right; margin-top: 2px; color: #666;">
                            ' . $row['votos'] . ' voto' . ($row['votos'] != 1 ? 's' : '') . '
                        </div>
                    </div>';
                }
            }
            ?>
            
            <!-- CONTAINER DA ENQUETE - COM CORES DO SITE -->
            <div style="background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%); color: white; padding: 25px; border-radius: 15px; margin-top: 30px; box-shadow: 0 8px 25px rgba(26, 41, 128, 0.15); border: 1px solid rgba(255,255,255,0.2);">
                
                <!-- CABEÇALHO -->
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <div style="background: rgba(255,255,255,0.3); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-poll" style="font-size: 1.2em; color: white;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 5px 0; font-size: 1.2em; color: white;">📊 Enquete ISCA</h3>
                        <p style="margin: 0; font-size: 0.8em; opacity: 0.9; color: rgba(255,255,255,0.9);">
                            <i class="fas fa-users"></i> 
                            <?php if ($is_admin): ?>
                                Exclusivo para membros (Admin)
                            <?php else: ?>
                                Exclusivo para membros
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- PERGUNTA -->
                <p style="margin: 0 0 20px 0; font-size: 1.1em; font-weight: bold; line-height: 1.4; color: white;">
                    <?php echo htmlspecialchars($enquete['pergunta']); ?>
                </p>
                
                <?php if (isset($enquete['descricao']) && !empty($enquete['descricao'])): ?>
                <p style="margin: 0 0 20px 0; font-size: 0.9em; opacity: 0.9; background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; color: rgba(255,255,255,0.9);">
                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($enquete['descricao']); ?>
                </p>
                <?php endif; ?>
                
                <?php if (!$ja_votou): ?>
                <!-- FORMULÁRIO PARA VOTAR -->
                <form action="processa_voto.php" method="POST" id="formEnquete<?php echo $enquete_id; ?>">
                    <input type="hidden" name="enquete_id" value="<?php echo $enquete_id; ?>">
                    
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                        <?php 
                        $cores_opcoes = ['#FF6B6B', '#4ECDC4', '#FFD166', '#06D6A0', '#118AB2', '#8e2de2'];
                        $cor_idx = 0;
                        foreach ($opcoes_array as $opcao): 
                            $cor = !empty($opcao['cor_hex']) ? $opcao['cor_hex'] : $cores_opcoes[$cor_idx % count($cores_opcoes)];
                            $cor_idx++;
                        ?>
                        <label style="background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); 
                                     color: white; padding: 14px 15px; border-radius: 10px; 
                                     text-align: left; cursor: pointer; display: flex; align-items: center; 
                                     gap: 12px; transition: all 0.3s; backdrop-filter: blur(5px);"
                               onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.borderColor='<?php echo $cor; ?>'; this.style.transform='translateY(-2px)';"
                               onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)';">
                            <input type="radio" name="opcao_id" value="<?php echo $opcao['id']; ?>" required 
                                   style="accent-color: <?php echo $cor; ?>; transform: scale(1.2);">
                            <div style="flex: 1;">
                                <div style="font-size: 1em; color: white;"><?php echo htmlspecialchars($opcao['texto_opcao']); ?></div>
                            </div>
                            <div style="width: 20px; height: 20px; background: <?php echo $cor; ?>; 
                                        border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="submit" 
                            class="btn-moderno pulse" 
                            style="background: linear-gradient(45deg, var(--amarelo), #ff9800); color: #212529; 
                                   padding: 14px 25px; font-size: 1em; width: 100%; font-weight: bold;
                                   border: none; box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);">
                        <i class="fas fa-paper-plane"></i> Submeter Voto
                    </button>
                </form>
                
                <div style="margin-top: 15px; font-size: 0.8em; opacity: 0.8; text-align: center; color: rgba(255,255,255,0.8);">
                    <i class="fas fa-lock"></i> Voto anónimo • Não é possível alterar
                </div>
                
                <?php else: ?>
                <!-- RESULTADOS -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div style="font-size: 0.9em; background: rgba(255,215,0,0.3); color: #FFD700; 
                                    padding: 8px 15px; border-radius: 20px; display: inline-flex; 
                                    align-items: center; gap: 8px; backdrop-filter: blur(5px); border: 1px solid rgba(255,215,0,0.5);">
                            <i class="fas fa-check-circle"></i> Já votaste!
                        </div>
                        <div style="font-size: 0.9em; color: rgba(255,255,255,0.9);">
                            <i class="fas fa-chart-bar"></i> <?php echo $total_votos; ?> votos
                        </div>
                    </div>
                    
                    <!-- BARRAS DE RESULTADOS -->
                    <div style="margin-bottom: 25px; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; backdrop-filter: blur(5px);">
                        <?php echo $resultados_html; ?>
                    </div>
                    
                    <div style="text-align: center;">
                        <button onclick="location.reload()" 
                                class="btn-moderno" 
                                style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);
                                      padding: 12px 20px; font-size: 0.9em; width: 100%; backdrop-filter: blur(5px);">
                            <i class="fas fa-sync-alt"></i> Atualizar Resultados
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($is_admin): ?>
                <!-- BOTÃO ADMIN PARA GERIR ENQUETES -->
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2);">
                    <a href="gerir_enquetes.php" 
                       class="btn-moderno" 
                       style="background: rgba(40, 167, 69, 0.8); color: white; padding: 10px 20px; font-size: 0.9em; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-cog"></i> Gerir Enquetes (Admin)
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- RODAPÉ -->
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2); 
                            font-size: 0.75em; opacity: 0.8; display: flex; justify-content: space-between; color: rgba(255,255,255,0.8);">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <i class="fas fa-calendar-alt"></i> 
                        Até <?php echo date('d/m/Y', strtotime($enquete['data_fim'])); ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <i class="fas fa-user-check"></i> 
                        <?php echo isset($total_votos) ? $total_votos : '0'; ?> participantes
                    </div>
                </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formEnquete<?php echo $enquete_id; ?>');
                
                if (form) {
                    let isSubmitting = false;
                    
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        if (isSubmitting) return;
                        isSubmitting = true;
                        
                        const formData = new FormData(this);
                        const submitBtn = form.querySelector('button[type="submit"]');
                        
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A processar...';
                        
                        fetch('processa_voto.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostra mensagem de sucesso
                                const enqueteContainer = form.closest('div[style*="background: linear-gradient"]');
                                enqueteContainer.innerHTML = `
                                    <div style="text-align: center; padding: 20px 0;">
                                        <div style="font-size: 3em; color: var(--verde); margin-bottom: 15px;">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <h4 style="margin: 0 0 10px 0; font-size: 1.3em; color: white;">Voto Registado com Sucesso!</h4>
                                        <p style="margin: 0 0 20px 0; opacity: 0.9; color: rgba(255,255,255,0.9);">
                                            Obrigado pela tua participação. A página será atualizada em 3 segundos...
                                        </p>
                                        <div style="margin: 20px 0;">
                                            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                                                <p style="margin: 0; font-size: 0.9em; color: rgba(255,255,255,0.9);">
                                                    <i class="fas fa-chart-pie"></i> Total de votos: ${data.data.total_votos}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                // Atualiza a página após 3 segundos para mostrar os resultados
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else {
                                alert('Erro: ' + data.message);
                                isSubmitting = false;
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submeter Voto';
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro de conexão. Tenta novamente.');
                            isSubmitting = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submeter Voto';
                        });
                    });
                }
            });
            </script>
            
            <?php
        } else {
            // Se não houver enquetes ativas
            ?>
            <div style="background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%); color: white; padding: 20px; border-radius: 15px; margin-top: 30px; text-align: center; box-shadow: 0 8px 25px rgba(26, 41, 128, 0.15);">
                <h3 style="margin: 0 0 10px 0; font-size: 1.2em; color: white;"><i class="fas fa-poll"></i> Próxima Enquete</h3>
                <p style="margin: 0 0 15px 0; font-size: 0.9em; opacity: 0.9; color: rgba(255,255,255,0.9);">
                    Em breve novas enquetes exclusivas para membros!
                </p>
                <div style="font-size: 0.8em; background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; color: rgba(255,255,255,0.8);">
                    <i class="fas fa-lightbulb"></i> Sugere temas para próximas enquetes
                </div>
                <?php if ($is_admin): ?>
                <div style="margin-top: 15px;">
                    <a href="gerir_enquetes.php" 
                       class="btn-moderno" 
                       style="background: rgba(40, 167, 69, 0.8); color: white; padding: 10px 20px; font-size: 0.9em;">
                        <i class="fas fa-plus-circle"></i> Criar Nova Enquete
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
        
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

<?php $conn->close(); ?>

<script>
// Menu Dropdown Interativo
document.addEventListener('DOMContentLoaded', function() {
    const userMenu = document.getElementById('userMenu');
    const userToggle = document.getElementById('userToggle');
    const dropdownMenu = document.getElementById('dropdownMenu');
    
    if (userToggle) {
        // Abrir/fechar dropdown
        userToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });
        
        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });
        
        // Fechar ao pressionar ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userMenu.classList.remove('active');
            }
        });
    }
    
    // Adiciona classe de animação aos cards
    const cards = document.querySelectorAll('section');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.2) + 's';
    });
    
    // Efeito nos botões
    const buttons = document.querySelectorAll('.btn-moderno, .btn-gradiente');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Título do site é clicável para voltar ao início
    const siteTitle = document.querySelector('header h1 a');
    siteTitle.addEventListener('click', function(e) {
        if (window.location.pathname.includes('index.php')) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
    
    // Adiciona efeito de seleção nas opções da enquete
    const opcoesEnquete = document.querySelectorAll('input[type="radio"][name="opcao_id"]');
    opcoesEnquete.forEach(opcao => {
        opcao.addEventListener('change', function() {
            // Remove destaque de todas as opções
            document.querySelectorAll('label[style*="background: rgba"]').forEach(label => {
                label.style.background = 'rgba(255,255,255,0.1)';
                label.style.borderColor = 'rgba(255,255,255,0.2)';
                label.style.transform = 'translateY(0)';
            });
            
            // Destaque a opção selecionada
            if (this.checked) {
                const label = this.closest('label');
                const cor = this.style.accentColor || '#4ECDC4';
                label.style.background = 'rgba(255,255,255,0.2)';
                label.style.borderColor = cor;
                label.style.transform = 'translateY(-2px)';
                label.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
            }
        });
    });
});
</script>

</body>
</html>
