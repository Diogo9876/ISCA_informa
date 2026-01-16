<?php
// eliminar_artigo.php
session_start();
require_once 'db_config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header("Location: login.php");
    exit();
}

// Verificar se foi passado um ID de artigo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gerir_artigos.php");
    exit();
}

$artigo_id = intval($_GET['id']);

// Verificar se o artigo existe
$sql_check = "SELECT * FROM artigos WHERE artigo_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $artigo_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    header("Location: gerir_artigos.php");
    exit();
}

$artigo = $result_check->fetch_assoc();
$stmt_check->close();

// Processar a eliminação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();
        
        // Eliminar registos relacionados primeiro (devido às chaves estrangeiras)
        
        // Conferências
        $conn->query("DELETE FROM conferencias WHERE artigo_id = $artigo_id");
        
        // Prémios
        $conn->query("DELETE FROM premios WHERE artigo_id = $artigo_id");
        
        // Eventos
        $conn->query("DELETE FROM eventos WHERE artigo_id = $artigo_id");
        
        // Finalmente, eliminar o artigo
        $sql_delete = "DELETE FROM artigos WHERE artigo_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $artigo_id);
        $stmt_delete->execute();
        
        if ($stmt_delete->affected_rows > 0) {
            $conn->commit();
            header("Location: gerir_artigos.php?sucesso=Artigo eliminado com sucesso");
            exit();
        } else {
            throw new Exception("Erro ao eliminar artigo");
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $erro = "Erro ao eliminar artigo: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Artigo - ISCA INFORMA</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .warning-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--danger-color), #e74c3c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2.5em;
        }
        
        h1 {
            color: var(--danger-color);
            margin-bottom: 15px;
            font-size: 1.8em;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 25px;
            font-size: 1.1em;
        }
        
        .article-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
            border-left: 4px solid var(--danger-color);
        }
        
        .article-info h3 {
            color: var(--danger-color);
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .article-info p {
            margin: 5px 0;
            color: #333;
        }
        
        .article-info strong {
            color: var(--primary-color);
        }
        
        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 14px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1em;
            min-width: 150px;
            justify-content: center;
        }
        
        .btn-danger {
            background: linear-gradient(45deg, var(--danger-color), #e74c3c);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
        }
        
        form {
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .card {
                padding: 30px 20px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1>Eliminar Artigo</h1>
            <p class="subtitle">Tem a certeza que deseja eliminar este artigo permanentemente?</p>
            
            <?php if (isset($erro)): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $erro; ?>
            </div>
            <?php endif; ?>
            
            <div class="article-info">
                <h3><i class="fas fa-file-alt"></i> Informações do Artigo</h3>
                <p><strong>ID:</strong> <?php echo $artigo_id; ?></p>
                <p><strong>Título:</strong> <?php echo htmlspecialchars($artigo['titulo']); ?></p>
                <p><strong>Criado em:</strong> <?php echo date('d/m/Y H:i', strtotime($artigo['data_criacao'])); ?></p>
                <p><strong>Visualizações:</strong> <?php echo $artigo['visualizacoes']; ?></p>
            </div>
            
            <div class="alert">
                <i class="fas fa-skull-crossbones"></i>
                <strong>Atenção:</strong> Esta ação não pode ser desfeita! Todos os dados relacionados serão eliminados permanentemente.
            </div>
            
            <form method="POST" id="deleteForm">
                <div class="btn-group">
                    <a href="gerir_artigos.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Sim, Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('deleteForm');
        
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const confirmMessage = "⚠️ ATENÇÃO FINAL ⚠️\n\n" +
                                 "Está prestes a eliminar permanentemente:\n" +
                                 "• Artigo ID: <?php echo $artigo_id; ?>\n" +
                                 "• Título: <?php echo addslashes($artigo['titulo']); ?>\n\n" +
                                 "Esta ação NÃO PODE ser desfeita!\n\n" +
                                 "Tem a ABSOLUTA certeza?";
            
            if (confirm(confirmMessage)) {
                // Desativar botão para evitar duplo clique
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
                
                // Enviar formulário
                this.submit();
            }
        });
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>