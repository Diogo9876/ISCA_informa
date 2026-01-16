<?php
// criar_artigo.php
session_start();
require_once 'db_config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header("Location: login.php");
    exit();
}

$admin_username = $_SESSION['admin_user'] ?? 'Administrador';

// Buscar categorias para o dropdown
$categorias = $conn->query("SELECT categoria_id, nome FROM categorias WHERE ativo = 1 ORDER BY nome");

$mensagem = '';
$tipo_mensagem = '';
$artigo_id = null;

// Processar o formulário quando submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    try {
        $conn->begin_transaction();
        
        // Coletar dados básicos do artigo
        $titulo = trim($_POST['titulo']);
        $conteudo = trim($_POST['conteudo']);
        $resumo = trim($_POST['resumo']);
        $categoria_id = intval($_POST['categoria_id']);
        $imagem_url = trim($_POST['imagem_url']);
        $autor = trim($_POST['autor'] ?? '');
        $tempo_leitura = trim($_POST['tempo_leitura'] ?? '5 min');
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        $data_publicacao = $_POST['data_publicacao'] ?? date('Y-m-d');
        
        // Inserir o artigo
        $sql = "INSERT INTO artigos (titulo, conteudo, resumo, categoria_id, imagem_url, autor, tempo_leitura, destaque, data_publicacao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssis", $titulo, $conteudo, $resumo, $categoria_id, $imagem_url, $autor, $tempo_leitura, $destaque, $data_publicacao);
        
        if ($stmt->execute()) {
            $artigo_id = $stmt->insert_id;
            $stmt->close();
            
            // Se for uma conferência (categoria_id = 14)
            if ($categoria_id == 14 && isset($_POST['tema_principal'])) {
                $sql_conf = "INSERT INTO conferencias (artigo_id, tema_principal, area_tematica, data_inicio, data_fim, local_conferencia, tipo_conferencia, oradores_principais, organizador_interno, departamento_responsavel, link_inscricao, publico_alvo, credito_profissional, idioma_principal, custo_inscricao, patrocinadores)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt_conf = $conn->prepare($sql_conf);
                $credito_profissional = isset($_POST['credito_profissional']) ? 1 : 0;
                $custo_inscricao = !empty($_POST['custo_inscricao']) ? $_POST['custo_inscricao'] : 0.00;
                
                $stmt_conf->bind_param("isssssssssssisds", 
                    $artigo_id,
                    $_POST['tema_principal'] ?? '',
                    $_POST['area_tematica'] ?? 'Contabilidade',
                    $_POST['data_inicio'] ?? date('Y-m-d'),
                    $_POST['data_fim'] ?? $_POST['data_inicio'] ?? date('Y-m-d'),
                    $_POST['local_conferencia'] ?? '',
                    $_POST['tipo_conferencia'] ?? 'Nacional',
                    $_POST['oradores_principais'] ?? '',
                    $_POST['organizador_interno'] ?? '',
                    $_POST['departamento_responsavel'] ?? '',
                    $_POST['link_inscricao'] ?? '',
                    $_POST['publico_alvo'] ?? 'Estudantes',
                    $credito_profissional,
                    $_POST['idioma_principal'] ?? 'Português',
                    $custo_inscricao,
                    $_POST['patrocinadores'] ?? ''
                );
                $stmt_conf->execute();
                $stmt_conf->close();
            }
            
            // Se for um prémio (categoria_id = 13)
            if ($categoria_id == 13 && isset($_POST['nome_premio'])) {
                $sql_prem = "INSERT INTO premios (artigo_id, nome_premio, tipo_premio, area_premio, instituicao_concedente, ano_concessao, valor_monetario, parceiros)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt_prem = $conn->prepare($sql_prem);
                $valor = !empty($_POST['valor_monetario']) ? $_POST['valor_monetario'] : NULL;
                $stmt_prem->bind_param("issssids", 
                    $artigo_id,
                    $_POST['nome_premio'] ?? '',
                    $_POST['tipo_premio'] ?? 'Excelência Académica',
                    $_POST['area_premio'] ?? 'Contabilidade',
                    $_POST['instituicao_concedente'] ?? '',
                    $_POST['ano_concessao'] ?? date('Y'),
                    $valor,
                    $_POST['parceiros'] ?? ''
                );
                $stmt_prem->execute();
                $stmt_prem->close();
            }
            
            // Se for um evento (outras categorias)
            if ($categoria_id != 13 && $categoria_id != 14 && isset($_POST['data_evento'])) {
                $sql_event = "INSERT INTO eventos (artigo_id, data_evento, hora_inicio, hora_fim, local_evento, tipo_evento, publico_alvo, organizador, departamento_responsavel)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt_event = $conn->prepare($sql_event);
                $hora_inicio = !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : NULL;
                $hora_fim = !empty($_POST['hora_fim']) ? $_POST['hora_fim'] : NULL;
                $stmt_event->bind_param("issssssss", 
                    $artigo_id,
                    $_POST['data_evento'] ?? date('Y-m-d'),
                    $hora_inicio,
                    $hora_fim,
                    $_POST['local_evento'] ?? '',
                    $_POST['tipo_evento'] ?? 'Workshop',
                    $_POST['publico_alvo'] ?? 'Estudantes',
                    $_POST['organizador'] ?? '',
                    $_POST['departamento_responsavel'] ?? ''
                );
                $stmt_event->execute();
                $stmt_event->close();
            }
            
            $conn->commit();
            
            $mensagem = "Artigo criado com sucesso! ID: " . $artigo_id;
            $tipo_mensagem = "success";
            
            // Limpar o formulário após sucesso
            $_POST = array();
            
        } else {
            throw new Exception("Erro ao inserir artigo: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $mensagem = "Erro ao criar artigo: " . $e->getMessage();
        $tipo_mensagem = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Artigo - ISCA INFORMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a2980;
            --secondary-color: #26d0ce;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .header h1 {
            color: var(--primary-color);
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #20c997);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #e74c3c);
            color: white;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-section-title {
            color: var(--primary-color);
            font-size: 1.4em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group input[type="number"],
        .form-group input[type="email"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 41, 128, 0.1);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group.checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group.checkbox input {
            width: auto;
        }
        
        .form-group.checkbox label {
            margin-bottom: 0;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.5s ease;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .conditional-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-color);
            display: none;
        }
        
        .conditional-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .char-count {
            text-align: right;
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        .success-actions {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid var(--success-color);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .success-actions .btn {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Criar Novo Artigo</h1>
            <div class="header-actions">
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Admin
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Ver Site
                </a>
            </div>
        </div>
        
        <!-- Mensagens -->
        <?php if ($mensagem): ?>
        <div class="message <?php echo $tipo_mensagem; ?>">
            <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $mensagem; ?>
        </div>
        
        <?php if ($tipo_mensagem == 'success' && $artigo_id): ?>
        <div class="success-actions">
            <a href="artigo.php?id=<?php echo $artigo_id; ?>" class="btn btn-primary" target="_blank">
                <i class="fas fa-eye"></i> Ver Artigo Publicado
            </a>
            <a href="editar_artigo.php?id=<?php echo $artigo_id; ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Editar Este Artigo
            </a>
            <a href="criar_artigo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Criar Outro Artigo
            </a>
            <a href="admin.php" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i> Voltar ao Dashboard
            </a>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
        
        <?php if ($tipo_mensagem != 'success' || !$artigo_id): ?>
        <!-- Formulário -->
        <form method="POST" class="form-container" id="artigoForm">
            
            <!-- Informações Básicas -->
            <div class="form-section">
                <h2 class="form-section-title"><i class="fas fa-info-circle"></i> Informações Básicas</h2>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="titulo">Título do Artigo *</label>
                        <input type="text" id="titulo" name="titulo" required 
                               value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>"
                               placeholder="Ex: CICA 2025 - Congresso Internacional de Contabilidade">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="resumo">Resumo * (máx. 300 caracteres)</label>
                        <textarea id="resumo" name="resumo" maxlength="300" required 
                                  placeholder="Breve descrição do artigo que aparecerá nas listagens"><?php echo isset($_POST['resumo']) ? htmlspecialchars($_POST['resumo']) : ''; ?></textarea>
                        <div class="char-count"><span id="resumoCount"><?php echo isset($_POST['resumo']) ? strlen($_POST['resumo']) : 0; ?></span>/300 caracteres</div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="conteudo">Conteúdo Completo *</label>
                        <textarea id="conteudo" name="conteudo" required 
                                  placeholder="Conteúdo completo do artigo"><?php echo isset($_POST['conteudo']) ? htmlspecialchars($_POST['conteudo']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria_id">Categoria *</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php 
                            $categorias_result = $conn->query("SELECT categoria_id, nome FROM categorias WHERE ativo = 1 ORDER BY nome");
                            while($cat = $categorias_result->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $cat['categoria_id']; ?>" 
                                <?php echo (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $cat['categoria_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="autor">Autor</label>
                        <input type="text" id="autor" name="autor" 
                               value="<?php echo isset($_POST['autor']) ? htmlspecialchars($_POST['autor']) : ''; ?>"
                               placeholder="Ex: Direção do ISCA">
                    </div>
                    
                    <div class="form-group">
                        <label for="imagem_url">URL da Imagem</label>
                        <input type="url" id="imagem_url" name="imagem_url" 
                               value="<?php echo isset($_POST['imagem_url']) ? htmlspecialchars($_POST['imagem_url']) : ''; ?>"
                               placeholder="https://exemplo.com/imagem.jpg">
                    </div>
                    
                    <div class="form-group">
                        <label for="tempo_leitura">Tempo de Leitura</label>
                        <input type="text" id="tempo_leitura" name="tempo_leitura" 
                               value="<?php echo isset($_POST['tempo_leitura']) ? htmlspecialchars($_POST['tempo_leitura']) : '5 min'; ?>"
                               placeholder="Ex: 5 min">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_publicacao">Data de Publicação</label>
                        <input type="date" id="data_publicacao" name="data_publicacao" 
                               value="<?php echo isset($_POST['data_publicacao']) ? $_POST['data_publicacao'] : date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group checkbox">
                        <input type="checkbox" id="destaque" name="destaque" value="1"
                            <?php echo (isset($_POST['destaque']) && $_POST['destaque'] == 1) ? 'checked' : ''; ?>>
                        <label for="destaque">Destacar este artigo</label>
                    </div>
                </div>
            </div>
            
            <!-- Seção para Conferências (aparece apenas quando categoria = 14) -->
            <div class="conditional-section" id="conferenciaSection">
                <h2 class="form-section-title"><i class="fas fa-calendar-alt"></i> Detalhes da Conferência</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tema_principal">Tema Principal</label>
                        <input type="text" id="tema_principal" name="tema_principal" 
                               value="<?php echo isset($_POST['tema_principal']) ? htmlspecialchars($_POST['tema_principal']) : ''; ?>"
                               placeholder="Tema da conferência">
                    </div>
                    
                    <div class="form-group">
                        <label for="area_tematica">Área Temática</label>
                        <select id="area_tematica" name="area_tematica">
                            <?php
                            $areas = ['Contabilidade', 'Auditoria', 'Fiscalidade', 'Gestão Empresarial', 'Finanças', 'Marketing', 'Recursos Humanos', 'Empreendedorismo', 'Tecnologias de Informação', 'Ética nos Negócios'];
                            foreach($areas as $area): ?>
                            <option value="<?php echo $area; ?>"
                                <?php echo (isset($_POST['area_tematica']) && $_POST['area_tematica'] == $area) ? 'selected' : ''; ?>>
                                <?php echo $area; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_inicio">Data de Início</label>
                        <input type="date" id="data_inicio" name="data_inicio" 
                               value="<?php echo isset($_POST['data_inicio']) ? $_POST['data_inicio'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_fim">Data de Fim</label>
                        <input type="date" id="data_fim" name="data_fim" 
                               value="<?php echo isset($_POST['data_fim']) ? $_POST['data_fim'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="local_conferencia">Local da Conferência</label>
                        <input type="text" id="local_conferencia" name="local_conferencia" 
                               value="<?php echo isset($_POST['local_conferencia']) ? htmlspecialchars($_POST['local_conferencia']) : ''; ?>"
                               placeholder="Ex: Auditório do ISCA-UA">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_conferencia">Tipo de Conferência</label>
                        <select id="tipo_conferencia" name="tipo_conferencia">
                            <option value="Anual do ISCA" <?php echo (isset($_POST['tipo_conferencia']) && $_POST['tipo_conferencia'] == 'Anual do ISCA') ? 'selected' : ''; ?>>Anual do ISCA</option>
                            <option value="Nacional" <?php echo (isset($_POST['tipo_conferencia']) && $_POST['tipo_conferencia'] == 'Nacional') ? 'selected' : ''; ?>>Nacional</option>
                            <option value="Internacional" <?php echo (isset($_POST['tipo_conferencia']) && $_POST['tipo_conferencia'] == 'Internacional') ? 'selected' : ''; ?>>Internacional</option>
                            <option value="Parceria" <?php echo (isset($_POST['tipo_conferencia']) && $_POST['tipo_conferencia'] == 'Parceria') ? 'selected' : ''; ?>>Parceria</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="oradores_principais">Oradores Principais</label>
                        <textarea id="oradores_principais" name="oradores_principais" 
                                  placeholder="Lista de oradores principais"><?php echo isset($_POST['oradores_principais']) ? htmlspecialchars($_POST['oradores_principais']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="organizador_interno">Organizador Interno</label>
                        <input type="text" id="organizador_interno" name="organizador_interno" 
                               value="<?php echo isset($_POST['organizador_interno']) ? htmlspecialchars($_POST['organizador_interno']) : ''; ?>"
                               placeholder="Ex: Direção do ISCA-UA">
                    </div>
                    
                    <div class="form-group">
                        <label for="departamento_responsavel">Departamento Responsável</label>
                        <input type="text" id="departamento_responsavel" name="departamento_responsavel" 
                               value="<?php echo isset($_POST['departamento_responsavel']) ? htmlspecialchars($_POST['departamento_responsavel']) : ''; ?>"
                               placeholder="Ex: Contabilidade">
                    </div>
                    
                    <div class="form-group">
                        <label for="link_inscricao">Link de Inscrição</label>
                        <input type="url" id="link_inscricao" name="link_inscricao" 
                               value="<?php echo isset($_POST['link_inscricao']) ? htmlspecialchars($_POST['link_inscricao']) : ''; ?>"
                               placeholder="https://www.ua.pt/pt/isca/inscricao">
                    </div>
                    
                    <div class="form-group">
                        <label for="publico_alvo">Público-Alvo</label>
                        <select id="publico_alvo" name="publico_alvo">
                            <option value="Estudantes" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Estudantes') ? 'selected' : ''; ?>>Estudantes</option>
                            <option value="Alumni" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                            <option value="Docentes" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Docentes') ? 'selected' : ''; ?>>Docentes</option>
                            <option value="Profissionais" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Profissionais') ? 'selected' : ''; ?>>Profissionais</option>
                            <option value="Empresas" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Empresas') ? 'selected' : ''; ?>>Empresas</option>
                            <option value="Todos" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                        </select>
                    </div>
                    
                    <div class="form-group checkbox">
                        <input type="checkbox" id="credito_profissional" name="credito_profissional" value="1"
                            <?php echo (isset($_POST['credito_profissional']) && $_POST['credito_profissional'] == 1) ? 'checked' : ''; ?>>
                        <label for="credito_profissional">Oferece Crédito Profissional</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="idioma_principal">Idioma Principal</label>
                        <select id="idioma_principal" name="idioma_principal">
                            <option value="Português" <?php echo (isset($_POST['idioma_principal']) && $_POST['idioma_principal'] == 'Português') ? 'selected' : ''; ?>>Português</option>
                            <option value="Inglês" <?php echo (isset($_POST['idioma_principal']) && $_POST['idioma_principal'] == 'Inglês') ? 'selected' : ''; ?>>Inglês</option>
                            <option value="Bilingue" <?php echo (isset($_POST['idioma_principal']) && $_POST['idioma_principal'] == 'Bilingue') ? 'selected' : ''; ?>>Bilingue</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="custo_inscricao">Custo de Inscrição (€)</label>
                        <input type="number" id="custo_inscricao" name="custo_inscricao" 
                               step="0.01" min="0" 
                               value="<?php echo isset($_POST['custo_inscricao']) ? $_POST['custo_inscricao'] : '0.00'; ?>"
                               placeholder="0.00">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="patrocinadores">Patrocinadores</label>
                        <textarea id="patrocinadores" name="patrocinadores" 
                                  placeholder="Lista de patrocinadores"><?php echo isset($_POST['patrocinadores']) ? htmlspecialchars($_POST['patrocinadores']) : ''; ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Seção para Prémios (aparece apenas quando categoria = 13) -->
            <div class="conditional-section" id="premioSection">
                <h2 class="form-section-title"><i class="fas fa-award"></i> Detalhes do Prémio</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome_premio">Nome do Prémio *</label>
                        <input type="text" id="nome_premio" name="nome_premio" 
                               value="<?php echo isset($_POST['nome_premio']) ? htmlspecialchars($_POST['nome_premio']) : ''; ?>"
                               placeholder="Ex: Prémio de Mérito Académico">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_premio">Tipo de Prémio</label>
                        <select id="tipo_premio" name="tipo_premio">
                            <option value="Excelência Académica" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Excelência Académica') ? 'selected' : ''; ?>>Excelência Académica</option>
                            <option value="Melhor Tese" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Melhor Tese') ? 'selected' : ''; ?>>Melhor Tese</option>
                            <option value="Inovação Empresarial" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Inovação Empresarial') ? 'selected' : ''; ?>>Inovação Empresarial</option>
                            <option value="Empreendedorismo" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Empreendedorismo') ? 'selected' : ''; ?>>Empreendedorismo</option>
                            <option value="Responsabilidade Social" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Responsabilidade Social') ? 'selected' : ''; ?>>Responsabilidade Social</option>
                            <option value="Desempenho Profissional" <?php echo (isset($_POST['tipo_premio']) && $_POST['tipo_premio'] == 'Desempenho Profissional') ? 'selected' : ''; ?>>Desempenho Profissional</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="area_premio">Área do Prémio</label>
                        <select id="area_premio" name="area_premio">
                            <option value="Contabilidade" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Contabilidade') ? 'selected' : ''; ?>>Contabilidade</option>
                            <option value="Auditoria" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Auditoria') ? 'selected' : ''; ?>>Auditoria</option>
                            <option value="Fiscalidade" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Fiscalidade') ? 'selected' : ''; ?>>Fiscalidade</option>
                            <option value="Gestão" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Gestão') ? 'selected' : ''; ?>>Gestão</option>
                            <option value="Marketing" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                            <option value="Finanças" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'Finanças') ? 'selected' : ''; ?>>Finanças</option>
                            <option value="RH" <?php echo (isset($_POST['area_premio']) && $_POST['area_premio'] == 'RH') ? 'selected' : ''; ?>>Recursos Humanos</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="instituicao_concedente">Instituição Concedente</label>
                        <input type="text" id="instituicao_concedente" name="instituicao_concedente" 
                               value="<?php echo isset($_POST['instituicao_concedente']) ? htmlspecialchars($_POST['instituicao_concedente']) : ''; ?>"
                               placeholder="Ex: Universidade de Aveiro">
                    </div>
                    
                    <div class="form-group">
                        <label for="ano_concessao">Ano de Concessão</label>
                        <input type="number" id="ano_concessao" name="ano_concessao" 
                               min="2000" max="2100" 
                               value="<?php echo isset($_POST['ano_concessao']) ? $_POST['ano_concessao'] : date('Y'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="valor_monetario">Valor Monetário (€)</label>
                        <input type="number" id="valor_monetario" name="valor_monetario" 
                               step="0.01" min="0" 
                               value="<?php echo isset($_POST['valor_monetario']) ? $_POST['valor_monetario'] : ''; ?>"
                               placeholder="Ex: 1000.00">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="parceiros">Parceiros</label>
                        <textarea id="parceiros" name="parceiros" 
                                  placeholder="Empresas ou instituições parceiras"><?php echo isset($_POST['parceiros']) ? htmlspecialchars($_POST['parceiros']) : ''; ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Seção para Eventos (aparece para outras categorias) -->
            <div class="conditional-section" id="eventoSection">
                <h2 class="form-section-title"><i class="fas fa-calendar-check"></i> Detalhes do Evento</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="data_evento">Data do Evento</label>
                        <input type="date" id="data_evento" name="data_evento" 
                               value="<?php echo isset($_POST['data_evento']) ? $_POST['data_evento'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_inicio">Hora de Início</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" 
                               value="<?php echo isset($_POST['hora_inicio']) ? $_POST['hora_inicio'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_fim">Hora de Fim</label>
                        <input type="time" id="hora_fim" name="hora_fim" 
                               value="<?php echo isset($_POST['hora_fim']) ? $_POST['hora_fim'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="local_evento">Local do Evento</label>
                        <input type="text" id="local_evento" name="local_evento" 
                               value="<?php echo isset($_POST['local_evento']) ? htmlspecialchars($_POST['local_evento']) : ''; ?>"
                               placeholder="Ex: Sala de Atos - ISCA">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_evento">Tipo de Evento</label>
                        <select id="tipo_evento" name="tipo_evento">
                            <option value="Workshop" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
                            <option value="Seminário" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Seminário') ? 'selected' : ''; ?>>Seminário</option>
                            <option value="Palestra" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Palestra') ? 'selected' : ''; ?>>Palestra</option>
                            <option value="Networking" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Networking') ? 'selected' : ''; ?>>Networking</option>
                            <option value="Social" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Social') ? 'selected' : ''; ?>>Social</option>
                            <option value="Formação" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Formação') ? 'selected' : ''; ?>>Formação</option>
                            <option value="Outro" <?php echo (isset($_POST['tipo_evento']) && $_POST['tipo_evento'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="publico_alvo_evento">Público-Alvo</label>
                        <select id="publico_alvo_evento" name="publico_alvo">
                            <option value="Estudantes" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Estudantes') ? 'selected' : ''; ?>>Estudantes</option>
                            <option value="Alumni" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                            <option value="Docentes" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Docentes') ? 'selected' : ''; ?>>Docentes</option>
                            <option value="Profissionais" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Profissionais') ? 'selected' : ''; ?>>Profissionais</option>
                            <option value="Todos" <?php echo (isset($_POST['publico_alvo']) && $_POST['publico_alvo'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="organizador">Organizador</label>
                        <input type="text" id="organizador" name="organizador" 
                               value="<?php echo isset($_POST['organizador']) ? htmlspecialchars($_POST['organizador']) : ''; ?>"
                               placeholder="Ex: Gabinete de Saídas Profissionais">
                    </div>
                    
                    <div class="form-group">
                        <label for="departamento_responsavel_evento">Departamento Responsável</label>
                        <input type="text" id="departamento_responsavel_evento" name="departamento_responsavel" 
                               value="<?php echo isset($_POST['departamento_responsavel']) ? htmlspecialchars($_POST['departamento_responsavel']) : ''; ?>"
                               placeholder="Ex: ISCA-UA">
                    </div>
                </div>
            </div>
            
            <!-- Ações do Formulário -->
            <div class="form-actions">
                <div>
                    <a href="admin.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
                <div>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Publicar Artigo
                    </button>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoria_id');
        const conferenciaSection = document.getElementById('conferenciaSection');
        const premioSection = document.getElementById('premioSection');
        const eventoSection = document.getElementById('eventoSection');
        const resumoTextarea = document.getElementById('resumo');
        const resumoCount = document.getElementById('resumoCount');
        
        // Contador de caracteres para o resumo
        resumoTextarea.addEventListener('input', function() {
            resumoCount.textContent = this.value.length;
            if (this.value.length > 300) {
                this.value = this.value.substring(0, 300);
                resumoCount.textContent = 300;
            }
        });
        
        // Mostrar/esconder seções condicionais baseadas na categoria
        function atualizarSecoes() {
            const categoriaId = parseInt(categoriaSelect.value);
            
            // Esconder todas as seções primeiro
            conferenciaSection.classList.remove('active');
            premioSection.classList.remove('active');
            eventoSection.classList.remove('active');
            
            // Mostrar a seção apropriada
            if (categoriaId === 14) { // Conferências
                conferenciaSection.classList.add('active');
            } else if (categoriaId === 13) { // Prémios
                premioSection.classList.add('active');
            } else if (categoriaId > 0) { // Outras categorias (eventos)
                eventoSection.classList.add('active');
            }
        }
        
        categoriaSelect.addEventListener('change', atualizarSecoes);
        
        // Verificar se já tem uma categoria selecionada (após erro ou recarga)
        if (categoriaSelect.value) {
            atualizarSecoes();
        }
        
        // Validação do formulário
        document.getElementById('artigoForm').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const resumo = document.getElementById('resumo').value.trim();
            const conteudo = document.getElementById('conteudo').value.trim();
            const categoria = document.getElementById('categoria_id').value;
            
            if (!titulo || !resumo || !conteudo || !categoria) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios (*)');
                return false;
            }
            
            // Validações específicas para cada tipo
            const categoriaId = parseInt(categoria);
            
            if (categoriaId === 14) { // Conferência
                const tema = document.getElementById('tema_principal').value.trim();
                const local = document.getElementById('local_conferencia').value.trim();
                const dataInicio = document.getElementById('data_inicio').value;
                
                if (!tema || !local || !dataInicio) {
                    e.preventDefault();
                    alert('Para conferências, preencha pelo menos Tema Principal, Local e Data de Início');
                    return false;
                }
            }
            
            if (categoriaId === 13) { // Prémio
                const nomePremio = document.getElementById('nome_premio').value.trim();
                
                if (!nomePremio) {
                    e.preventDefault();
                    alert('Para prémios, preencha pelo menos o Nome do Prémio');
                    return false;
                }
            }
            
            // Validação de datas
            const dataInicio = document.getElementById('data_inicio');
            const dataFim = document.getElementById('data_fim');
            
            if (dataInicio && dataFim && dataInicio.value && dataFim.value) {
                if (new Date(dataFim.value) < new Date(dataInicio.value)) {
                    e.preventDefault();
                    alert('A data de fim não pode ser anterior à data de início');
                    return false;
                }
            }
            
            return true;
        });
        
        // Preencher automaticamente alguns campos
        document.getElementById('titulo').addEventListener('blur', function() {
            const titulo = this.value;
            if (titulo && !document.getElementById('resumo').value) {
                // Cria um resumo automático baseado no título
                const resumoAuto = titulo.length > 100 ? 
                    titulo.substring(0, 97) + '...' : 
                    titulo;
                document.getElementById('resumo').value = resumoAuto;
                resumoCount.textContent = resumoAuto.length;
            }
        });
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>