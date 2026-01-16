<?php
// perfil_aluno.php - Perfil simplificado para alunos (sem novas tabelas)
session_start();
require_once 'db_config.php';

// Verificação de login
if (!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

// Bloqueia admin de entrar aqui
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header("Location: admin.php");
    exit();
}

// Dados do utilizador (apenas da sessão)
$usuario_nome  = $_SESSION['nome_completo'] ?? $_SESSION['admin_user'] ?? 'Aluno';
$usuario_email = $_SESSION['email'] ?? '';
$usuario_id = $_SESSION['user_id'] ?? 0;

// Dados simulados (sem tabelas novas)
$total_views = rand(5, 50); // Simulado
$artigos_diferentes = rand(3, 20); // Simulado
$primeiro_acesso = date('Y-m-d', strtotime('-'.rand(10, 180).' days')); // Simulado

// Buscar algumas notícias recentes para mostrar
$sql_noticias = "SELECT artigo_id, titulo, resumo, data_publicacao 
                 FROM artigos 
                 WHERE categoria_id NOT IN (13, 14) 
                 ORDER BY data_publicacao DESC LIMIT 5";
$result_noticias = $conn->query($sql_noticias);

// Buscar algumas conferências
$sql_conferencias = "SELECT a.artigo_id, a.titulo, a.resumo, c.local_conferencia, c.data_inicio 
                     FROM artigos a 
                     JOIN conferencias c ON a.artigo_id = c.artigo_id
                     WHERE a.categoria_id = 14 AND c.data_inicio >= CURDATE()
                     ORDER BY c.data_inicio ASC LIMIT 3";
$result_conferencias = $conn->query($sql_conferencias);

// Buscar alguns prémios
$sql_premios = "SELECT a.artigo_id, a.titulo, a.resumo, p.nome_premio, p.ano_concessao 
                FROM artigos a 
                JOIN premios p ON a.artigo_id = p.artigo_id
                WHERE a.categoria_id = 13
                ORDER BY p.ano_concessao DESC LIMIT 3";
$result_premios = $conn->query($sql_premios);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - <?php echo htmlspecialchars($usuario_nome); ?> | ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================== */
        /* ESTILOS GERAIS (IDÊNTICOS AO INDEX.PHP) */
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
        /* CABEÇALHO MODERNO (IGUAL AO INDEX.PHP) */
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
        /* MENU DROPDOWN MODERNO (IGUAL À CAPTURA) */
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
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
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
            min-width: 250px;
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
        /* NAVEGAÇÃO (IGUAL AO INDEX.PHP) */
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
        /* CONTEÚDO PRINCIPAL - LAYOUT DE GRID */
        /* =========================================== */
        main {
            width: 90%;
            max-width: 1400px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
        }

        /* =========================================== */
        /* CARDS E SEÇÕES */
        /* =========================================== */
        .card {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #f0f0f0;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .card h2 {
            border-bottom: 3px solid var(--azul-isca);
            padding-bottom: 15px;
            margin-bottom: 25px;
            color: var(--azul-isca);
            font-size: 1.6em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* =========================================== */
        /* PERFIL DO USUÁRIO (CARD GRANDE) */
        /* =========================================== */
        .card-perfil {
            grid-column: 1 / 4;
            background: linear-gradient(135deg, var(--azul-isca) 0%, var(--azul-claro) 100%);
            color: white;
            text-align: center;
        }

        .card-perfil h2 {
            border-bottom: 3px solid rgba(255,255,255,0.3);
            color: white;
        }

        .perfil-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 3em;
            margin: 0 auto 20px;
            border: 5px solid rgba(255,255,255,0.3);
        }

        .perfil-stats {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .stat-item {
            background: rgba(255,255,255,0.1);
            padding: 15px 25px;
            border-radius: 10px;
            text-align: center;
            min-width: 120px;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }

        /* =========================================== */
        /* LISTAS E ITEMS */
        /* =========================================== */
        .list-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }

        .list-item:hover {
            background: var(--cinza-claro);
            padding-left: 15px;
            padding-right: 15px;
            margin: 0 -15px;
            border-radius: 10px;
        }

        .list-item h4 {
            color: #2c3e50;
            margin: 0 0 5px 0;
            font-size: 1.2em;
        }

        .list-item p {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }

        .list-item .meta {
            display: flex;
            gap: 15px;
            font-size: 0.85em;
            color: #888;
        }

        .list-item .meta i {
            color: var(--azul-isca);
        }

        .btn-small {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--cinza-claro);
            color: #333;
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .btn-small:hover {
            background: var(--azul-isca);
            color: white;
        }

        /* =========================================== */
        /* BADGES E TAGS */
        /* =========================================== */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8em;
            font-weight: 600;
            margin: 2px;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-primary {
            background: #cce5ff;
            color: #004085;
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
        @media (max-width: 1200px) {
            main {
                grid-template-columns: 1fr 1fr;
            }
            
            .card-perfil {
                grid-column: 1 / 3;
            }
        }

        @media (max-width: 768px) {
            main {
                grid-template-columns: 1fr;
                width: 95%;
            }
            
            .card-perfil {
                grid-column: 1 / 2;
            }
            
            header h1 {
                font-size: 2.2em;
            }
            
            nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-moderno, .user-toggle {
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
            
            .perfil-stats {
                flex-direction: column;
                align-items: center;
            }
            
            .stat-item {
                width: 100%;
                max-width: 200px;
            }
        }

        /* =========================================== */
        /* ANIMAÇÕES */
        /* =========================================== */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
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
<!-- CABEÇALHO (IGUAL AO INDEX.PHP) -->
<!-- =========================================== -->
<header>
    <h1><a href="index.php">🎓 ISCA INFORMA</a></h1>
    <p>O teu portal de Notícias, Prémios e Atualidades do ISCA</p>
    
    <!-- Navegação Principal -->
    <nav>
        <!-- Botões principais -->
        <a href="sobre.php" class="btn-moderno btn-sobre"><i class="fas fa-info-circle"></i> Sobre Nós</a>
        <a href="contacto.php" class="btn-moderno btn-contato"><i class="fas fa-envelope"></i> Contactos</a>
        <a href="index.php" class="btn-moderno" style="background: linear-gradient(45deg, var(--amarelo), #ff9800);">
            <i class="fas fa-home"></i> Página Inicial
        </a>
        
        <!-- USUÁRIO LOGADO - MENU DROPDOWN (ALUNO) -->
        <div class="user-menu" id="userMenu">
            <button class="user-toggle" id="userToggle">
                <div class="user-avatar">
                    <?php 
                    $nome = htmlspecialchars($usuario_nome);
                    echo strtoupper(substr($nome, 0, 1)); 
                    ?>
                </div>
                <span><?php echo $nome; ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            
            <div class="dropdown-menu" id="dropdownMenu">
                <div class="dropdown-header">
                    <h4>Olá, <?php echo $nome; ?>!</h4>
                    <p>🎓 Aluno ISCA</p>
                </div>
                
                <!-- Opções para ALUNO -->
                <a href="perfil_aluno.php" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> Meu Perfil
                </a>
                <a href="index.php" class="dropdown-item">
                    <i class="fas fa-newspaper"></i> Notícias
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-book"></i> Materiais
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-calendar-alt"></i> Calendário
                </a>
                
                <a href="logout.php" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i> Terminar Sessão
                </a>
            </div>
        </div>
    </nav>
</header>

<!-- =========================================== -->
<!-- CONTEÚDO PRINCIPAL - PERFIL SIMPLIFICADO -->
<!-- =========================================== -->
<main>
    <!-- CARD DE PERFIL PRINCIPAL -->
    <div class="card card-perfil fade-in">
        <div class="perfil-avatar pulse">
            <?php echo strtoupper(substr($usuario_nome, 0, 1)); ?>
        </div>
        
        <h2><?php echo htmlspecialchars($usuario_nome); ?></h2>
        <p>Aluno do ISCA - Instituto Superior de Contabilidade e Administração</p>
        
        <div class="perfil-stats">
            <div class="stat-item">
                <span class="stat-value"><?php echo $total_views; ?></span>
                <span class="stat-label">Atividade Simulada</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo $artigos_diferentes; ?></span>
                <span class="stat-label">Interações</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">Aluno</span>
                <span class="stat-label">Categoria</span>
            </div>
        </div>
        
        <div style="margin-top: 20px; font-size: 0.95em;">
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario_email); ?></p>
            <p><i class="fas fa-calendar"></i> Primeiro acesso: <?php echo date('d/m/Y', strtotime($primeiro_acesso)); ?></p>
        </div>
    </div>

    <!-- NOTÍCIAS RECENTES -->
    <div class="card fade-in">
        <h2><i class="fas fa-newspaper"></i> Notícias Recentes</h2>
        
        <?php if ($result_noticias && $result_noticias->num_rows > 0): ?>
            <?php while($noticia = $result_noticias->fetch_assoc()): ?>
                <div class="list-item slide-in">
                    <h4><?php echo htmlspecialchars($noticia['titulo']); ?></h4>
                    <p><?php echo substr($noticia['resumo'], 0, 100); ?>...</p>
                    <div class="meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($noticia['data_publicacao'])); ?></span>
                    </div>
                    <a href="artigo.php?id=<?php echo $noticia['artigo_id']; ?>" class="btn-small">
                        <i class="fas fa-book-open"></i> Ler
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #666; text-align: center; padding: 20px;">
                <i class="fas fa-newspaper" style="font-size: 2em; color: #ddd;"></i>
                <br>Sem notícias recentes.
            </p>
        <?php endif; ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php#noticias" class="btn-small">
                <i class="fas fa-list"></i> Ver Todas as Notícias
            </a>
        </div>
    </div>

    <!-- PRÓXIMAS CONFERÊNCIAS -->
    <div class="card fade-in">
        <h2><i class="fas fa-calendar-alt"></i> Próximos Eventos</h2>
        
        <?php if ($result_conferencias && $result_conferencias->num_rows > 0): ?>
            <?php while($evento = $result_conferencias->fetch_assoc()): ?>
                <div class="list-item slide-in">
                    <h4><?php echo htmlspecialchars($evento['titulo']); ?></h4>
                    <p><?php echo substr($evento['resumo'], 0, 80); ?>...</p>
                    <div class="meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo $evento['local_conferencia']; ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo date('d/m/Y', strtotime($evento['data_inicio'])); ?></span>
                    </div>
                    <a href="artigo.php?id=<?php echo $evento['artigo_id']; ?>" class="btn-small">
                        <i class="fas fa-external-link-alt"></i> Detalhes
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #666; text-align: center; padding: 20px;">
                <i class="fas fa-calendar-times" style="font-size: 2em; color: #ddd;"></i>
                <br>Não há eventos próximos.
            </p>
        <?php endif; ?>
    </div>

    <!-- PRÉMIOS E DISTINÇÕES -->
    <div class="card fade-in">
        <h2><i class="fas fa-trophy"></i> Prémios Recentes</h2>
        
        <?php if ($result_premios && $result_premios->num_rows > 0): ?>
            <?php while($premio = $result_premios->fetch_assoc()): ?>
                <div class="list-item slide-in">
                    <h4><?php echo htmlspecialchars($premio['titulo']); ?></h4>
                    <p><i class="fas fa-award"></i> <?php echo $premio['nome_premio']; ?> (<?php echo $premio['ano_concessao']; ?>)</p>
                    <p><?php echo substr($premio['resumo'], 0, 80); ?>...</p>
                    <a href="artigo.php?id=<?php echo $premio['artigo_id']; ?>" class="btn-small">
                        <i class="fas fa-medal"></i> Ver Detalhes
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #666; text-align: center; padding: 20px;">
                <i class="fas fa-trophy" style="font-size: 2em; color: #ddd;"></i>
                <br>Não há prémios recentes.
            </p>
        <?php endif; ?>
    </div>

    <!-- INFORMAÇÕES ACADÉMICAS -->
    <div class="card fade-in">
        <h2><i class="fas fa-graduation-cap"></i> Informações Académicas</h2>
        
        <div style="padding: 20px; background: #f8f9fa; border-radius: 10px; margin-bottom: 20px;">
            <h3 style="color: var(--azul-isca); margin-bottom: 15px;">Estado da Conta</h3>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span>Acesso ao Portal:</span>
                <span class="badge badge-success">Ativo</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span>Newsletter:</span>
                <span class="badge badge-success">Subscrito</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span>Notificações:</span>
                <span class="badge badge-success">Ativas</span>
            </div>
        </div>
        
        <div style="padding: 20px; background: #e8f4fc; border-radius: 10px; margin-top: 15px;">
            <h3 style="color: var(--azul-isca); margin-bottom: 15px;">Recursos Úteis</h3>
            <div style="display: grid; gap: 10px;">
                <a href="#" class="btn-small" style="justify-content: start;">
                    <i class="fas fa-book"></i> Materiais de Estudo
                </a>
                <a href="#" class="btn-small" style="justify-content: start;">
                    <i class="fas fa-file-pdf"></i> Documentos Académicos
                </a>
                <a href="#" class="btn-small" style="justify-content: start;">
                    <i class="fas fa-question-circle"></i> Apoio ao Aluno
                </a>
            </div>
        </div>
    </div>

    <!-- CONFIGURAÇÕES DO PERFIL -->
    <div class="card fade-in">
        <h2><i class="fas fa-cog"></i> Configurações</h2>
        
        <div style="margin: 20px 0;">
            <h3 style="color: var(--azul-isca); margin-bottom: 15px;">Preferências</h3>
            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                <span>Notificações por email</span>
                <span class="badge badge-success">Ativo</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                <span>Novos artigos</span>
                <span class="badge badge-success">Ativo</span>
            </div>
        </div>
        
        <div style="margin: 20px 0;">
            <h3 style="color: var(--azul-isca); margin-bottom: 15px;">Conta</h3>
            
            <!-- BOTÃO RECUPERAR PASSWORD (NOVO) -->
            <a href="recuperar_senha.php" class="btn-small" style="width: 100%; text-align: center; margin: 10px 0; background: linear-gradient(45deg, #17a2b8, #138496); color: white;">
                <i class="fas fa-key"></i> Recuperar Password
            </a>
            
            <a href="contacto.php" class="btn-small" style="width: 100%; text-align: center; margin: 10px 0;">
                <i class="fas fa-headset"></i> Suporte
            </a>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="logout.php" class="btn-moderno" style="background: linear-gradient(45deg, var(--vermelho), #c82333);">
                <i class="fas fa-sign-out-alt"></i> Terminar Sessão
            </a>
        </div>
    </div>
</main>

<!-- =========================================== -->
<!-- RODAPÉ -->
<!-- =========================================== -->
<footer>
    <p>&copy; <?php echo date('Y'); ?> ISCA INFORMA - Instituto Superior de Contabilidade e Administração.</p>
    <p>
        <i class="fas fa-user-circle"></i> Conectado como: <strong><?php echo htmlspecialchars($usuario_nome); ?></strong> | 
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
    </p>
</footer>

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
    
    // Animações dos cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.2) + 's';
    });
    
    // Animações dos items da lista
    const listItems = document.querySelectorAll('.list-item');
    listItems.forEach((item, index) => {
        item.style.animationDelay = (index * 0.1) + 's';
    });
});
</script>

</body>
</html>

<?php $conn->close(); ?>