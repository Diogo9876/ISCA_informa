<?php
// 1. Iniciar a sessão
session_start();

// 2. Proteção: Se não houver sessão admin, volta para o login
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header("Location: login.php");
    exit();
}

// 3. Ligar à base de dados
require_once 'db_config.php';

// Obter informações do admin logado
$admin_username = $_SESSION['admin_user'] ?? 'Administrador';

// 4. Obter estatísticas para o Dashboard
$total_artigos = $conn->query("SELECT COUNT(*) as total FROM artigos")->fetch_assoc()['total'];
$total_subscritores = $conn->query("SELECT COUNT(*) as total FROM subscritores WHERE ativo = 1")->fetch_assoc()['total'];
$total_mensagens = $conn->query("SELECT COUNT(*) as total FROM contactos WHERE lido = 0")->fetch_assoc()['total'];
$total_utilizadores = $conn->query("SELECT COUNT(*) as total FROM utilizadores WHERE tipo = 'user'")->fetch_assoc()['total'];

// Estatísticas de enquetes - CORRIGIDO
$total_enquetes = $conn->query("SELECT COUNT(*) as total FROM enquetes")->fetch_assoc()['total'];
$enquetes_ativas = $conn->query("SELECT COUNT(*) as total FROM enquetes WHERE ativa = 1 AND CURDATE() BETWEEN data_inicio AND data_fim")->fetch_assoc()['total'];
$total_votos = $conn->query("SELECT COUNT(*) as total FROM votos_enquete")->fetch_assoc()['total'];

// Artigos recentes
$artigos_recentes = $conn->query("SELECT COUNT(*) as total FROM artigos WHERE data_publicacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['total'];

// Inscrições recentes
$subscritores_recentes = $conn->query("SELECT COUNT(*) as total FROM subscritores WHERE data_inscricao >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['total'];

// Enquetes recentes - CORRIGIDO (removida referência a data_criacao)
$enquetes_recentes = $conn->query("SELECT COUNT(*) as total FROM enquetes WHERE data_inicio >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['total'];

// 5. Obter dados para as tabelas
$res_contactos = $conn->query("SELECT * FROM contactos ORDER BY lido ASC, data_envio DESC LIMIT 10");
$res_artigos = $conn->query("SELECT a.artigo_id, a.titulo, a.data_publicacao, c.nome as categoria FROM artigos a LEFT JOIN categorias c ON a.categoria_id = c.categoria_id ORDER BY a.data_publicacao DESC LIMIT 10");
$res_subscritores = $conn->query("SELECT * FROM subscritores ORDER BY data_inscricao DESC LIMIT 10");

// Enquetes recentes - CORRIGIDO
$res_enquetes = $conn->query("SELECT e.*, 
    (SELECT COUNT(*) FROM votos_enquete WHERE enquete_id = e.id) as total_votos,
    (SELECT texto_opcao FROM opcoes_enquete WHERE enquete_id = e.id ORDER BY (SELECT COUNT(*) FROM votos_enquete WHERE opcao_id = opcoes_enquete.id) DESC LIMIT 1) as opcao_mais_votada
    FROM enquetes e 
    ORDER BY e.data_inicio DESC LIMIT 10");

// 6. Obter dados para gráficos (últimos 7 dias)
$dias = [];
$inscricoes_data = [];
$artigos_data = [];
$votos_data = [];

for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i days"));
    $dia_nome = date('d/m', strtotime("-$i days"));
    $dias[] = "'$dia_nome'";
    
    // Inscrições
    $sql_insc = "SELECT COUNT(*) as total FROM subscritores WHERE DATE(data_inscricao) = '$data'";
    $result_insc = $conn->query($sql_insc);
    $inscricoes = $result_insc->fetch_assoc()['total'];
    $inscricoes_data[] = $inscricoes;
    
    // Artigos
    $sql_art = "SELECT COUNT(*) as total FROM artigos WHERE DATE(data_publicacao) = '$data'";
    $result_art = $conn->query($sql_art);
    $artigos = $result_art->fetch_assoc()['total'];
    $artigos_data[] = $artigos;
    
    // Votos em enquetes - CORRIGIDO (assumindo que há coluna data_voto)
    $sql_votos = "SELECT COUNT(*) as total FROM votos_enquete WHERE DATE(data_voto) = '$data'";
    $result_votos = $conn->query($sql_votos);
    // Verificar se a query funcionou
    if ($result_votos) {
        $votos = $result_votos->fetch_assoc()['total'];
    } else {
        $votos = 0; // Se não houver coluna data_voto
    }
    $votos_data[] = $votos;
}

// Obter resultados das enquetes ativas para o gráfico - CORRIGIDO
$enquetes_resultados = [];
$res_enquetes_grafico = $conn->query("
    SELECT e.id as enquete_id, e.pergunta as titulo,
    (SELECT COUNT(*) FROM votos_enquete WHERE enquete_id = e.id) as total_votos
    FROM enquetes e 
    WHERE e.ativa = 1 AND CURDATE() BETWEEN e.data_inicio AND e.data_fim
    ORDER BY e.data_inicio DESC LIMIT 3
");

if ($res_enquetes_grafico) {
    while ($enquete = $res_enquetes_grafico->fetch_assoc()) {
        $opcoes = $conn->query("
            SELECT o.id, o.texto_opcao, o.cor_hex,
            (SELECT COUNT(*) FROM votos_enquete WHERE opcao_id = o.id) as votos
            FROM opcoes_enquete o 
            WHERE o.enquete_id = {$enquete['enquete_id']}
            ORDER BY votos DESC
        ");
        
        if ($opcoes) {
            $enquetes_resultados[] = [
                'titulo' => $enquete['titulo'],
                'total_votos' => $enquete['total_votos'],
                'opcoes' => []
            ];
            
            while ($opcao = $opcoes->fetch_assoc()) {
                $enquetes_resultados[count($enquetes_resultados)-1]['opcoes'][] = $opcao;
            }
        }
    }
}

// Formatando dados para o JavaScript
$dias_js = implode(', ', $dias);
$inscricoes_js = implode(', ', $inscricoes_data);
$artigos_js = implode(', ', $artigos_data);
$votos_js = implode(', ', $votos_data);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #1a2980;
            --secondary-color: #26d0ce;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --purple-color: #9c27b0;
            --orange-color: #ff5722;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --sidebar-width: 250px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 1.5em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .sidebar-header .user-info {
            margin-top: 15px;
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            position: relative;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }
        
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
            font-size: 1.2em;
        }
        
        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s;
        }
        
        .top-bar {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .top-bar h1 {
            color: var(--primary-color);
            font-size: 1.8em;
            font-weight: 700;
        }
        
        .top-bar .welcome {
            color: #666;
            font-size: 1em;
        }
        
        .top-bar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(26, 41, 128, 0.2);
        }
        
        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #e74c3c);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #20c997);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(45deg, var(--warning-color), #ff9800);
            color: #212529;
        }
        
        .btn-info {
            background: linear-gradient(45deg, var(--info-color), #5bc0de);
            color: white;
        }
        
        .btn-purple {
            background: linear-gradient(45deg, var(--purple-color), #ba68c8);
            color: white;
        }
        
        .btn-orange {
            background: linear-gradient(45deg, var(--orange-color), #ff8a65);
            color: white;
        }
        
        /* ===== STATS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }
        
        .stat-card-artigos::before { background: var(--primary-color); }
        .stat-card-subscritores::before { background: var(--success-color); }
        .stat-card-mensagens::before { background: var(--info-color); }
        .stat-card-utilizadores::before { background: var(--warning-color); }
        .stat-card-enquetes::before { background: var(--purple-color); }
        .stat-card-votos::before { background: var(--orange-color); }
        
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .stat-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8em;
            color: white;
        }
        
        .stat-card-artigos .stat-card-icon { background: var(--primary-color); }
        .stat-card-subscritores .stat-card-icon { background: var(--success-color); }
        .stat-card-mensagens .stat-card-icon { background: var(--info-color); }
        .stat-card-utilizadores .stat-card-icon { background: var(--warning-color); }
        .stat-card-enquetes .stat-card-icon { background: var(--purple-color); }
        .stat-card-votos .stat-card-icon { background: var(--orange-color); }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .stat-card .number {
            font-size: 2.5em;
            font-weight: 800;
            color: var(--dark-color);
            line-height: 1;
        }
        
        .stat-card-trend {
            margin-top: 10px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .trend-up { color: var(--success-color); }
        .trend-down { color: var(--danger-color); }
        
        /* ===== CHARTS ===== */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .chart-title {
            margin-bottom: 20px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2em;
            font-weight: 600;
        }
        
        /* ===== ENQUETE RESULTS ===== */
        .enquete-result-item {
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--purple-color);
        }
        
        .enquete-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.1em;
        }
        
        .enquete-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 6px;
            border: 1px solid #eee;
        }
        
        .enquete-option-name {
            flex: 1;
        }
        
        .enquete-option-votes {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .enquete-option-bar {
            height: 10px;
            background: linear-gradient(90deg, var(--purple-color), #ba68c8);
            border-radius: 5px;
            transition: width 0.5s ease;
        }
        
        /* ===== TABLES ===== */
        .dashboard-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .dashboard-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4em;
            font-weight: 600;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #eee;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        .admin-table thead {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .admin-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table tbody tr {
            transition: background 0.3s;
        }
        
        .admin-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #e3f2fd; color: var(--primary-color); }
        .badge-purple { background: #f3e5f5; color: var(--purple-color); }
        .badge-orange { background: #ffe5d0; color: #e65100; }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        
        .btn-view { background: #e3f2fd; color: var(--primary-color); }
        .btn-edit { background: #fff3cd; color: #856404; }
        .btn-delete { background: #f8d7da; color: #721c24; }
        .btn-mark { background: #d1ecf1; color: #0c5460; }
        .btn-results { background: #f3e5f5; color: var(--purple-color); }
        .btn-status { background: #ffe5d0; color: #e65100; }
        
        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        /* ===== NOTIFICATIONS ===== */
        .notification-badge {
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
            position: absolute;
            top: 5px;
            right: 5px;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar-header h2 span,
            .sidebar-menu a span,
            .sidebar-header .user-info {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .top-bar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .top-bar-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 20px 15px;
            }
            
            .dashboard-section {
                padding: 20px;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 0.9em;
            }
        }
        
        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* ===== FOOTER ===== */
        .admin-footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
            margin-top: 40px;
            border-top: 1px solid #eee;
        }
        
        /* ===== LOADING ===== */
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        /* ===== UTILITY CLASSES ===== */
        .text-success { color: var(--success-color); }
        .text-danger { color: var(--danger-color); }
        .text-warning { color: var(--warning-color); }
        .text-purple { color: var(--purple-color); }
        .text-orange { color: var(--orange-color); }
        .text-muted { color: #6c757d; }
        .text-center { text-align: center; }
        .mb-3 { margin-bottom: 1rem; }
        .mt-3 { margin-top: 1rem; }
        
        .status-lido {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .status-nlido {
            color: var(--danger-color);
            font-weight: 600;
        }
        
        .status-ativa {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .status-inativa {
            color: var(--danger-color);
            font-weight: 600;
        }
        
        .status-em-andamento {
            color: var(--orange-color);
            font-weight: 600;
        }
        
        /* Progress bars for poll results */
        .progress-container {
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            height: 20px;
            margin: 5px 0;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease-in-out;
            text-align: center;
            color: white;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Poll results styling */
        .poll-result-item {
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #eee;
        }
        
        .poll-result-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .poll-result-stats {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        /* Warning message */
        .warning-message {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    
    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-graduation-cap"></i> <span>ISCA ADMIN</span></h2>
            <div class="user-info">
                <p><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($admin_username); ?></p>
                <p><i class="fas fa-shield-alt"></i> Administrador</p>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="gerir_artigos.php"><i class="fas fa-newspaper"></i> <span>Artigos</span></a></li>
            <li><a href="gerir_enquetes.php"><i class="fas fa-poll"></i> <span>Enquetes</span></a></li>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> <span>Eventos</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i> <span>Subscritores</span></a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> <span>Mensagens</span> <?php if($total_mensagens > 0): ?><span class="notification-badge"><?php echo $total_mensagens; ?></span><?php endif; ?></a></li>
            <li><a href="#"><i class="fas fa-user-cog"></i> <span>Utilizadores</span></a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> <span>Relatórios</span></a></li>
            <li><a href="#"><i class="fas fa-cog"></i> <span>Configurações</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
        </ul>
    </div>
    
    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">
        
        <!-- Top Bar -->
        <div class="top-bar fade-in">
            <div>
                <h1>Painel Administrativo</h1>
                <p class="welcome">Bem-vindo, <?php echo htmlspecialchars($admin_username); ?>! Último acesso: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
            
            <div class="top-bar-actions">
                <a href="index.php" class="btn btn-info">
                    <i class="fas fa-external-link-alt"></i> Ver Site
                </a>
                <a href="criar_artigo.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Novo Artigo
                </a>
                <a href="criar_enquete.php" class="btn btn-purple">
                    <i class="fas fa-plus-circle"></i> Nova Enquete
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
        
        <!-- Warning if no data_voto column -->
        <?php 
        $check_data_voto = $conn->query("SHOW COLUMNS FROM votos_enquete LIKE 'data_voto'");
        if ($check_data_voto->num_rows == 0): 
        ?>
        <div class="warning-message fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Aviso:</strong> A tabela 'votos_enquete' não tem a coluna 'data_voto'. 
                Os gráficos de votos por dia podem não funcionar corretamente.
                <br>
                <small>Para adicionar: ALTER TABLE votos_enquete ADD COLUMN data_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP</small>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="stats-grid fade-in">
            <div class="stat-card stat-card-artigos">
                <div class="stat-card-header">
                    <div>
                        <h3>Total Artigos</h3>
                        <div class="number"><?php echo $total_artigos; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <?php if($artigos_recentes > 0): ?>
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +<?php echo $artigos_recentes; ?> esta semana</span>
                    <?php else: ?>
                        <span class="text-muted">Sem novos artigos esta semana</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stat-card stat-card-subscritores">
                <div class="stat-card-header">
                    <div>
                        <h3>Subscritores</h3>
                        <div class="number"><?php echo $total_subscritores; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <?php if($subscritores_recentes > 0): ?>
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +<?php echo $subscritores_recentes; ?> esta semana</span>
                    <?php else: ?>
                        <span class="text-muted">Sem novas inscrições esta semana</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stat-card stat-card-mensagens">
                <div class="stat-card-header">
                    <div>
                        <h3>Mensagens</h3>
                        <div class="number"><?php echo $total_mensagens; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <span class="<?php echo $total_mensagens > 0 ? 'trend-up text-danger' : 'text-success'; ?>">
                        <?php echo $total_mensagens > 0 ? $total_mensagens . ' não lidas' : 'Todas lidas'; ?>
                    </span>
                </div>
            </div>
            
            <div class="stat-card stat-card-enquetes">
                <div class="stat-card-header">
                    <div>
                        <h3>Enquetes</h3>
                        <div class="number"><?php echo $total_enquetes; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-poll"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <span class="<?php echo $enquetes_ativas > 0 ? 'trend-up text-success' : 'text-muted'; ?>">
                        <i class="fas fa-circle"></i> <?php echo $enquetes_ativas; ?> ativas
                    </span>
                    <?php if($enquetes_recentes > 0): ?>
                        <div class="trend-up"><i class="fas fa-arrow-up"></i> +<?php echo $enquetes_recentes; ?> esta semana</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stat-card stat-card-votos">
                <div class="stat-card-header">
                    <div>
                        <h3>Total Votos</h3>
                        <div class="number"><?php echo $total_votos; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <?php 
                    $votos_semana = array_sum($votos_data);
                    if($votos_semana > 0): ?>
                        <span class="trend-up"><i class="fas fa-arrow-up"></i> +<?php echo $votos_semana; ?> esta semana</span>
                    <?php else: ?>
                        <span class="text-muted">Sem votos esta semana</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stat-card stat-card-utilizadores">
                <div class="stat-card-header">
                    <div>
                        <h3>Utilizadores</h3>
                        <div class="number"><?php echo $total_utilizadores; ?></div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="stat-card-trend">
                    <span class="text-muted">Registados no sistema</span>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="charts-grid fade-in">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-line"></i> Atividade Recente (7 dias)
                </div>
                <canvas id="activityChart" height="250"></canvas>
            </div>
            
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-pie"></i> Distribuição por Tipo
                </div>
                <canvas id="typeChart" height="250"></canvas>
            </div>
        </div>
        
        <!-- Resultados de Enquetes Ativas -->
        <?php if(!empty($enquetes_resultados)): ?>
        <div class="dashboard-section fade-in">
            <div class="dashboard-section-header">
                <h3 class="section-title"><i class="fas fa-poll-h"></i> Resultados de Enquetes Ativas</h3>
                <a href="gerir_enquetes.php" class="btn btn-purple">
                    <i class="fas fa-cog"></i> Gerir Todas
                </a>
            </div>
            
            <div class="charts-grid">
                <?php foreach($enquetes_resultados as $index => $enquete): 
                    if ($enquete['total_votos'] > 0): ?>
                <div class="chart-container">
                    <h4 class="poll-result-title"><?php echo htmlspecialchars($enquete['titulo']); ?></h4>
                    <p class="poll-result-stats">Total de votos: <strong><?php echo $enquete['total_votos']; ?></strong></p>
                    
                    <?php foreach($enquete['opcoes'] as $opcao): 
                        $percentagem = round(($opcao['votos'] / $enquete['total_votos']) * 100, 1);
                        $cor = !empty($opcao['cor_hex']) ? $opcao['cor_hex'] : '#9c27b0';
                    ?>
                    <div class="poll-result-item">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: 500;"><?php echo htmlspecialchars($opcao['texto_opcao']); ?></span>
                            <span style="font-weight: bold; color: var(--primary-color);"><?php echo $percentagem; ?>%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?php echo $percentagem; ?>%; background: <?php echo $cor; ?>;">
                                <?php echo $opcao['votos']; ?> votos
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Enquetes Recentes -->
        <div class="dashboard-section fade-in">
            <div class="dashboard-section-header">
                <h3 class="section-title"><i class="fas fa-poll"></i> Enquetes Recentes</h3>
                <a href="criar_enquete.php" class="btn btn-purple">
                    <i class="fas fa-plus"></i> Nova Enquete
                </a>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pergunta</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Votos</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($res_enquetes && $res_enquetes->num_rows > 0):
                            while($e = $res_enquetes->fetch_assoc()): 
                                $estado = '';
                                $estado_class = '';
                                $data_atual = date('Y-m-d');
                                $data_inicio = $e['data_inicio'];
                                $data_fim = $e['data_fim'];
                                
                                if ($e['ativa'] == 0) {
                                    $estado = 'Inativa';
                                    $estado_class = 'status-inativa';
                                } elseif ($data_atual < $data_inicio) {
                                    $estado = 'Agendada';
                                    $estado_class = 'status-em-andamento';
                                } elseif ($data_atual > $data_fim) {
                                    $estado = 'Terminada';
                                    $estado_class = 'status-inativa';
                                } else {
                                    $estado = 'Ativa';
                                    $estado_class = 'status-ativa';
                                }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars(mb_strimwidth($e['pergunta'], 0, 60, '...')); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($e['data_inicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($e['data_fim'])); ?></td>
                            <td><span class="badge badge-info"><?php echo $e['total_votos'] ?? 0; ?></span></td>
                            <td>
                                <span class="<?php echo $estado_class; ?>">
                                    <i class="fas fa-circle"></i> <?php echo $estado; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="ver_enquete.php?id=<?php echo $e['id']; ?>" 
                                       class="btn-action btn-view" 
                                       title="Ver Resultados" 
                                       target="_blank">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="editar_enquete.php?id=<?php echo $e['id']; ?>" 
                                       class="btn-action btn-edit" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="alternar_estado_enquete.php?id=<?php echo $e['id']; ?>" 
                                       class="btn-action btn-status" 
                                       title="<?php echo $e['ativa'] ? 'Desativar' : 'Ativar'; ?>">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="eliminar_enquete.php?id=<?php echo $e['id']; ?>" 
                                       class="btn-action btn-delete" 
                                       title="Eliminar"
                                       onclick="return confirm('Tem a certeza que deseja eliminar esta enquete? Todos os votos serão perdidos!')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-info-circle"></i> Não existem enquetes criadas.
                                <a href="criar_enquete.php">Criar primeira enquete</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Mensagens Recentes -->
        <div class="dashboard-section fade-in">
            <div class="dashboard-section-header">
                <h3 class="section-title"><i class="fas fa-envelope"></i> Mensagens Recentes</h3>
                <a href="#" class="btn btn-primary" onclick="alert('Funcionalidade em desenvolvimento')">
                    <i class="fas fa-eye"></i> Ver Todas
                </a>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Assunto</th>
                            <th>Data</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = $res_contactos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['nome']); ?></td>
                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                            <td><?php echo htmlspecialchars($c['assunto']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($c['data_envio'])); ?></td>
                            <td>
                                <?php if($c['lido'] == 0): ?>
                                    <span class="status-nlido"><i class="fas fa-circle"></i> Não lida</span>
                                <?php else: ?>
                                    <span class="status-lido"><i class="fas fa-check-circle"></i> Lida</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-view" title="Ver Mensagem" onclick="alert('Funcionalidade em desenvolvimento')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="eliminar_contacto.php?id=<?php echo $c['contacto_id']; ?>" 
                                       class="btn-action btn-delete" 
                                       title="Eliminar"
                                       onclick="return confirm('Tem a certeza que deseja eliminar esta mensagem?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php if($c['lido'] == 0): ?>
                                    <a href="#" class="btn-action btn-mark" title="Marcar como lida" onclick="alert('Funcionalidade em desenvolvimento')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Artigos Recentes -->
        <div class="dashboard-section fade-in">
            <div class="dashboard-section-header">
                <h3 class="section-title"><i class="fas fa-newspaper"></i> Artigos Recentes</h3>
                <a href="criar_artigo.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Novo Artigo
                </a>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($a = $res_artigos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['titulo']); ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($a['categoria']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($a['data_publicacao'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="artigo.php?id=<?php echo $a['artigo_id']; ?>" 
                                       class="btn-action btn-view" 
                                       title="Ver" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar_artigo.php?id=<?php echo $a['artigo_id']; ?>" 
                                       class="btn-action btn-edit" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="eliminar_artigo.php?id=<?php echo $a['artigo_id']; ?>" 
                                       class="btn-action btn-delete" 
                                       title="Eliminar"
                                       onclick="return confirm('Tem a certeza que deseja eliminar este artigo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Subscritores Recentes -->
        <div class="dashboard-section fade-in">
            <div class="dashboard-section-header">
                <h3 class="section-title"><i class="fas fa-user-plus"></i> Subscritores Recentes</h3>
                <a href="#" class="btn btn-info" onclick="alert('Funcionalidade em desenvolvimento')">
                    <i class="fas fa-download"></i> Exportar
                </a>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = $res_subscritores->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['nome']); ?></td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($s['tipo_subscritor']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($s['data_inscricao'])); ?></td>
                            <td>
                                <?php if($s['ativo'] == 1): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="admin-footer fade-in">
            <p>&copy; <?php echo date('Y'); ?> ISCA INFORMA - Painel Administrativo</p>
            <p class="text-muted">Versão 1.0 | Última atualização: <?php echo date('d/m/Y H:i'); ?></p>
        </div>
        
    </div>
    
    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: [<?php echo $dias_js; ?>],
                datasets: [
                    {
                        label: 'Inscrições',
                        data: [<?php echo $inscricoes_js; ?>],
                        borderColor: '#26d0ce',
                        backgroundColor: 'rgba(38, 208, 206, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Artigos',
                        data: [<?php echo $artigos_js; ?>],
                        borderColor: '#1a2980',
                        backgroundColor: 'rgba(26, 41, 128, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Votos',
                        data: [<?php echo $votos_js; ?>],
                        borderColor: '#ff5722',
                        backgroundColor: 'rgba(255, 87, 34, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Artigos', 'Subscritores', 'Mensagens', 'Enquetes', 'Votos'],
                datasets: [{
                    data: [
                        <?php echo $total_artigos; ?>,
                        <?php echo $total_subscritores; ?>,
                        <?php echo $total_mensagens; ?>,
                        <?php echo $total_enquetes; ?>,
                        <?php echo $total_votos; ?>
                    ],
                    backgroundColor: [
                        '#1a2980',
                        '#28a745',
                        '#17a2b8',
                        '#9c27b0',
                        '#ff5722'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        
        // Animate progress bars
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
        
        // Sidebar toggle for mobile
        const sidebarToggle = document.createElement('button');
        sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
        sidebarToggle.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            display: none;
        `;
        document.body.appendChild(sidebarToggle);
        
        function updateSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 992) {
                sidebarToggle.style.display = 'block';
                let sidebarVisible = false;
                
                sidebarToggle.onclick = function() {
                    sidebarVisible = !sidebarVisible;
                    sidebar.style.transform = sidebarVisible ? 'translateX(0)' : 'translateX(-100%)';
                    sidebar.style.boxShadow = sidebarVisible ? '3px 0 15px rgba(0,0,0,0.1)' : 'none';
                };
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (sidebarVisible && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebarVisible = false;
                        sidebar.style.transform = 'translateX(-100%)';
                        sidebar.style.boxShadow = 'none';
                    }
                });
            } else {
                sidebarToggle.style.display = 'none';
                sidebar.style.transform = 'translateX(0)';
            }
        }
        
        updateSidebar();
        window.addEventListener('resize', updateSidebar);
        
        // Delete confirmation
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem a certeza que deseja eliminar este item?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Status toggle confirmation
        document.querySelectorAll('.btn-status').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const action = this.title.toLowerCase();
                if (!confirm(`Tem a certeza que deseja ${action} esta enquete?`)) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
    
</body>
</html>
<?php $conn->close(); ?>