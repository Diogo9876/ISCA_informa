<?php
// Ficheiro: artigo.php
session_start();
require_once 'db_config.php';

// Verifica se foi passado um ID de artigo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$artigo_id = intval($_GET['id']);

// Busca o artigo na base de dados
$sql_artigo = "SELECT a.*, c.nome as nome_categoria, 
                      CASE 
                          WHEN a.categoria_id = 14 THEN conf.local_conferencia 
                          WHEN a.categoria_id = 13 THEN prem.nome_premio 
                          ELSE NULL 
                      END as info_extra
               FROM artigos a 
               LEFT JOIN categorias c ON a.categoria_id = c.categoria_id
               LEFT JOIN conferencias conf ON a.artigo_id = conf.artigo_id AND a.categoria_id = 14
               LEFT JOIN premios prem ON a.artigo_id = prem.artigo_id AND a.categoria_id = 13
               WHERE a.artigo_id = ?";
               
$stmt = $conn->prepare($sql_artigo);
$stmt->bind_param("i", $artigo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$artigo = $result->fetch_assoc();
$stmt->close();

// Incrementa o contador de visualizações
$sql_views = "UPDATE artigos SET visualizacoes = visualizacoes + 1 WHERE artigo_id = ?";
$stmt_views = $conn->prepare($sql_views);
$stmt_views->bind_param("i", $artigo_id);
$stmt_views->execute();
$stmt_views->close();

// Busca artigos relacionados (mesma categoria, excluindo o atual)
$sql_relacionados = "SELECT artigo_id, titulo, resumo, imagem_url as imagem_destaque 
                     FROM artigos 
                     WHERE categoria_id = ? AND artigo_id != ? 
                     ORDER BY data_publicacao DESC 
                     LIMIT 3";
                     
$stmt_rel = $conn->prepare($sql_relacionados);
$stmt_rel->bind_param("ii", $artigo['categoria_id'], $artigo_id);
$stmt_rel->execute();
$relacionados = $stmt_rel->get_result();
$stmt_rel->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artigo['titulo']); ?> - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================== */
        /* ESTILOS GERAIS - DO INDEX.PHP */
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
        /* CABEÇALHO MODERNO - DO INDEX.PHP */
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
        /* BOTÕES MODERNOS - DO INDEX.PHP */
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

        /* Cores específicas para botões - DO INDEX.PHP */
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
        /* NAVEGAÇÃO - DO INDEX.PHP */
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
        /* MENU DROPDOWN MODERNO - DO INDEX.PHP */
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
        /* CONTEÚDO PRINCIPAL - DO INDEX.PHP */
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
        /* ESTILOS ESPECÍFICOS DO ARTIGO */
        /* =========================================== */
        /* Card principal do artigo - Mantido do artigo.php original */
        .artigo-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
            margin-bottom: 40px;
        }

        /* Cabeçalho do artigo - Mantido do artigo.php original */
        .artigo-header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--azul-isca);
        }

        .artigo-categoria {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(45deg, var(--verde), #20c997);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .artigo-titulo {
            color: var(--azul-isca);
            margin: 0 0 20px;
            font-size: 2.5em;
            line-height: 1.3;
        }

        .artigo-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            color: #666;
            font-size: 0.95em;
            margin-bottom: 15px;
        }

        .artigo-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .artigo-views {
            background: var(--cinza-claro);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Imagem de destaque - Mantido do artigo.php original */
        .artigo-imagem {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 15px;
            margin: 30px 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Conteúdo do artigo - Mantido do artigo.php original */
        .artigo-conteudo {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 40px;
        }

        .artigo-conteudo h2, .artigo-conteudo h3 {
            color: var(--azul-isca);
            margin: 30px 0 15px;
        }

        .artigo-conteudo p {
            margin-bottom: 20px;
        }

        .artigo-conteudo ul, .artigo-conteudo ol {
            margin: 20px 0 20px 30px;
        }

        .artigo-conteudo li {
            margin-bottom: 10px;
        }

        /* Informação extra para conferências e prémios - Mantido do artigo.php original */
        .artigo-extra {
            background: linear-gradient(135deg, var(--cinza-claro) 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            border-left: 5px solid var(--amarelo);
        }

        .artigo-extra-title {
            color: var(--azul-isca);
            margin: 0 0 15px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Botões de ação - REMOVIDOS OS BOTÕES DE PARTILHA */
        .artigo-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-voltar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, #6c757d, #495057);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-voltar:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        /* REMOVIDO: .artigo-share e .share-btn */

        /* Artigos relacionados - Mantido do artigo.php original */
        .relacionados-section {
            margin-top: 60px;
        }

        .relacionados-title {
            color: var(--azul-isca);
            border-bottom: 3px solid var(--azul-isca);
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .relacionados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .relacionado-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border: 1px solid #f0f0f0;
        }

        .relacionado-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .relacionado-imagem {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .relacionado-conteudo {
            padding: 25px;
        }

        .relacionado-categoria {
            display: inline-block;
            background: var(--cinza-claro);
            color: var(--azul-isca);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .relacionado-titulo {
            color: var(--azul-isca);
            margin: 0 0 15px;
            font-size: 1.3em;
            line-height: 1.4;
        }

        .relacionado-titulo a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }

        .relacionado-titulo a:hover {
            color: var(--verde);
        }

        .relacionado-resumo {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .relacionado-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--verde);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .relacionado-link:hover {
            gap: 12px;
            color: var(--azul-isca);
        }

        /* =========================================== */
        /* BARRA LATERAL (NEWSLETTER) - DO INDEX.PHP */
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
        /* MENSAGENS DE STATUS - DO INDEX.PHP */
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
        /* RODAPÉ - DO INDEX.PHP */
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
        /* RESPONSIVIDADE - COMBINADO */
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
            
            .relacionados-grid {
                grid-template-columns: 1fr;
            }
            
            .artigo-card {
                padding: 30px;
            }
            
            .artigo-titulo {
                font-size: 2em;
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
            
            .artigo-card {
                padding: 20px;
            }
            
            .artigo-titulo {
                font-size: 1.8em;
            }
            
            .artigo-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .artigo-actions {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            header {
                padding: 20px 15px;
            }
            
            #newsletter {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .artigo-titulo {
                font-size: 1.5em;
            }
            
            .artigo-conteudo {
                font-size: 1rem;
            }
            
            .relacionado-card {
                margin-bottom: 20px;
            }
        }

        /* =========================================== */
        /* ANIMAÇÕES - COMBINADO */
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
<!-- CABEÇALHO - ATUALIZADO COM DESIGN DO INDEX.PHP -->
<!-- =========================================== -->
<header>
    <h1><a href="index.php">🎓 ISCA INFORMA</a></h1>
    <p>O teu portal de Notícias, Prémios e Atualidades do ISCA</p>
    
    <!-- Navegação Principal - Atualizado com design do index.php -->
    <nav>
        <!-- Botões principais -->
        <a href="index.php" class="btn-moderno"><i class="fas fa-home"></i> Início</a>
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
<!-- CONTEÚDO PRINCIPAL - ARTIGO -->
<!-- =========================================== -->
<main>
    <div id="coluna-principal">
        <article class="artigo-card fade-in">
            <!-- Cabeçalho do artigo -->
            <div class="artigo-header">
                <div class="artigo-categoria">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($artigo['nome_categoria']); ?>
                </div>
                
                <h1 class="artigo-titulo"><?php echo htmlspecialchars($artigo['titulo']); ?></h1>
                
                <div class="artigo-meta">
                    <div class="artigo-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Publicado em: <?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></span>
                    </div>
                    
                    <?php if (isset($artigo['autor']) && $artigo['autor']): ?>
                    <div class="artigo-meta-item">
                        <i class="fas fa-user-edit"></i>
                        <span>Por: <?php echo htmlspecialchars($artigo['autor']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="artigo-views">
                        <i class="fas fa-eye"></i> <?php echo number_format($artigo['visualizacoes'] + 1, 0, ',', '.'); ?> visualizações
                    </div>
                </div>
            </div>

            <!-- Imagem de destaque (se existir) -->
            <?php if (isset($artigo['imagem_url']) && !empty($artigo['imagem_url'])): ?>
            <img src="<?php echo htmlspecialchars($artigo['imagem_url']); ?>" 
                 alt="<?php echo htmlspecialchars($artigo['titulo']); ?>" 
                 class="artigo-imagem">
            <?php endif; ?>

            <!-- Informação extra para conferências/prémios -->
            <?php if (isset($artigo['info_extra']) && $artigo['info_extra']): ?>
                <div class="artigo-extra fade-in">
                    <h3 class="artigo-extra-title">
                        <?php if ($artigo['categoria_id'] == 14): ?>
                            <i class="fas fa-map-marker-alt"></i> Local da Conferência
                        <?php elseif ($artigo['categoria_id'] == 13): ?>
                            <i class="fas fa-award"></i> Prémio
                        <?php endif; ?>
                    </h3>
                    <p style="font-size: 1.1em; margin: 0;"><?php echo htmlspecialchars($artigo['info_extra']); ?></p>
                    
                    <?php if ($artigo['categoria_id'] == 14): ?>
                        <?php 
                        $sql_data = "SELECT data_inicio, data_fim FROM conferencias WHERE artigo_id = ?";
                        $stmt_data = $conn->prepare($sql_data);
                        $stmt_data->bind_param("i", $artigo_id);
                        $stmt_data->execute();
                        $result_data = $stmt_data->get_result();
                        if ($data_info = $result_data->fetch_assoc()): ?>
                            <p style="margin: 10px 0 0; font-weight: 600;">
                                <i class="fas fa-clock"></i> 
                                <?php echo date('d/m/Y', strtotime($data_info['data_inicio'])); ?>
                                <?php if ($data_info['data_fim'] && $data_info['data_fim'] != $data_info['data_inicio']): ?>
                                    a <?php echo date('d/m/Y', strtotime($data_info['data_fim'])); ?>
                                <?php endif; ?>
                            </p>
                        <?php endif;
                        $stmt_data->close();
                        ?>
                    <?php elseif ($artigo['categoria_id'] == 13): ?>
                        <?php 
                        $sql_ano = "SELECT ano_concessao FROM premios WHERE artigo_id = ?";
                        $stmt_ano = $conn->prepare($sql_ano);
                        $stmt_ano->bind_param("i", $artigo_id);
                        $stmt_ano->execute();
                        $result_ano = $stmt_ano->get_result();
                        if ($ano_info = $result_ano->fetch_assoc()): ?>
                            <p style="margin: 10px 0 0; font-weight: 600;">
                                <i class="fas fa-calendar"></i> Ano de Concessão: <?php echo $ano_info['ano_concessao']; ?>
                            </p>
                        <?php endif;
                        $stmt_ano->close();
                        ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Conteúdo do artigo -->
            <div class="artigo-conteudo">
                <?php echo nl2br(htmlspecialchars($artigo['conteudo'])); ?>
            </div>

            <!-- Botões de ação - APENAS O BOTÃO VOLTAR -->
            <div class="artigo-actions">
                <a href="index.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar às Notícias
                </a>
            </div>

            <!-- Artigos relacionados -->
            <?php if ($relacionados->num_rows > 0): ?>
            <div class="relacionados-section fade-in">
                <h2 class="relacionados-title">
                    <i class="fas fa-newspaper"></i> Artigos Relacionados
                </h2>
                
                <div class="relacionados-grid">
                    <?php while ($relacionado = $relacionados->fetch_assoc()): ?>
                    <article class="relacionado-card">
                        <?php if (isset($relacionado['imagem_destaque']) && !empty($relacionado['imagem_destaque'])): ?>
                        <img src="<?php echo htmlspecialchars($relacionado['imagem_destaque']); ?>" 
                             alt="<?php echo htmlspecialchars($relacionado['titulo']); ?>" 
                             class="relacionado-imagem">
                        <?php endif; ?>
                        
                        <div class="relacionado-conteudo">
                            <div class="relacionado-categoria">
                                <?php echo htmlspecialchars($artigo['nome_categoria']); ?>
                            </div>
                            
                            <h3 class="relacionado-titulo">
                                <a href="artigo.php?id=<?php echo $relacionado['artigo_id']; ?>">
                                    <?php echo htmlspecialchars($relacionado['titulo']); ?>
                                </a>
                            </h3>
                            
                            <p class="relacionado-resumo">
                                <?php echo htmlspecialchars(mb_substr($relacionado['resumo'], 0, 150)) . '...'; ?>
                            </p>
                            
                            <a href="artigo.php?id=<?php echo $relacionado['artigo_id']; ?>" class="relacionado-link">
                                Ler Mais <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
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
<!-- RODAPÉ - IGUAL AO INDEX.PHP -->
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
// Menu Dropdown Interativo - DO INDEX.PHP
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
    
    // Adiciona classe de animação aos elementos
    const cards = document.querySelectorAll('.fade-in');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
    });
    
    // Efeito nos botões
    const buttons = document.querySelectorAll('.btn-moderno, .btn-voltar, .relacionado-link, #newsletter button, .user-toggle');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Efeito nos cards relacionados
    const relacionadosCards = document.querySelectorAll('.relacionado-card');
    relacionadosCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Título do site é clicável para voltar ao início
    const siteTitle = document.querySelector('header h1 a');
    siteTitle.addEventListener('click', function(e) {
        if (window.location.pathname.includes('artigo.php')) {
            e.preventDefault();
            window.location.href = 'index.php';
        }
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>