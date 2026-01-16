<?php
// processa_voto.php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logado']) || !isset($_SESSION['admin_user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Precisa fazer login para votar.'
    ]);
    exit();
}

// Verificar se é um POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método inválido.'
    ]);
    exit();
}

// Obter dados do formulário
$enquete_id = isset($_POST['enquete_id']) ? intval($_POST['enquete_id']) : 0;
$opcao_id = isset($_POST['opcao_id']) ? intval($_POST['opcao_id']) : 0;
$user_identifier = $_SESSION['admin_user'];

// Validar dados
if ($enquete_id <= 0 || $opcao_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos. Por favor, selecione uma opção.'
    ]);
    exit();
}

try {
    // 1. Verificar se a enquete existe e está ativa
    $sql_enquete = "SELECT * FROM enquetes 
                    WHERE id = ? 
                    AND ativa = 1 
                    AND CURDATE() BETWEEN data_inicio AND data_fim";

    $stmt = $conn->prepare($sql_enquete);
    $stmt->bind_param("i", $enquete_id);
    $stmt->execute();
    $result_enquete = $stmt->get_result();

    if ($result_enquete->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Enquete não encontrada ou fora do período de votação.'
        ]);
        $stmt->close();
        exit();
    }

    $enquete = $result_enquete->fetch_assoc();
    $stmt->close();

    // 2. Verificar se a opção pertence à enquete
    $sql_opcao = "SELECT * FROM opcoes_enquete 
                  WHERE id = ? AND enquete_id = ?";

    $stmt = $conn->prepare($sql_opcao);
    $stmt->bind_param("ii", $opcao_id, $enquete_id);
    $stmt->execute();
    $result_opcao = $stmt->get_result();

    if ($result_opcao->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Opção inválida para esta enquete.'
        ]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // 3. Verificar se o usuário já votou nesta enquete
    $sql_check_voto = "SELECT * FROM votos_enquete 
                       WHERE enquete_id = ? AND user_identifier = ?";

    $stmt = $conn->prepare($sql_check_voto);
    $stmt->bind_param("is", $enquete_id, $user_identifier);
    $stmt->execute();
    $result_check = $stmt->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Já votaste nesta enquete.'
        ]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // 4. Inserir o voto
    $sql_insert_voto = "INSERT INTO votos_enquete (enquete_id, opcao_id, user_identifier) 
                        VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql_insert_voto);
    $stmt->bind_param("iis", $enquete_id, $opcao_id, $user_identifier);

    if ($stmt->execute()) {
        // Sucesso - retornar estatísticas atualizadas
        $sql_stats = "SELECT 
                        o.id, 
                        o.texto_opcao, 
                        o.cor_hex,
                        COUNT(v.id) as votos,
                        ROUND((COUNT(v.id) * 100.0 / (
                            SELECT COUNT(*) 
                            FROM votos_enquete 
                            WHERE enquete_id = ?
                        )), 1) as percentagem
                      FROM opcoes_enquete o
                      LEFT JOIN votos_enquete v ON o.id = v.opcao_id
                      WHERE o.enquete_id = ?
                      GROUP BY o.id
                      ORDER BY votos DESC";
        
        $stmt_stats = $conn->prepare($sql_stats);
        $stmt_stats->bind_param("ii", $enquete_id, $enquete_id);
        $stmt_stats->execute();
        $result_stats = $stmt_stats->get_result();
        
        $estatisticas = [];
        $total_votos = 0;
        
        while($row = $result_stats->fetch_assoc()) {
            $estatisticas[] = $row;
            $total_votos += $row['votos'];
        }
        $stmt_stats->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Voto registado com sucesso!',
            'data' => [
                'enquete_id' => $enquete_id,
                'total_votos' => $total_votos,
                'estatisticas' => $estatisticas,
                'voto_usuario' => $opcao_id
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao registar o voto no banco de dados.'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro no servidor: ' . $e->getMessage()
    ]);
}

$conn->close();
?>