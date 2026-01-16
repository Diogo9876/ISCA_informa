<?php
// Ficheiro: processa_subscricao.php
require 'db_config.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=invalid_email");
        exit();
    }
    
    // A sua tabela 'subscritores' tem colunas obrigatórias ('nome', 'aceitou_termos'). 
    // Definimos valores padrão para evitar erros de inserção.
    $nome_padrao = "Novo Subscritor";
    $aceitou_termos = 1;
    
    // Query de inserção na sua tabela 'subscritores'
    $sql = "INSERT INTO subscritores (nome, email, aceitou_termos) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssi", $nome_padrao, $email, $aceitou_termos);
        
        if ($stmt->execute()) {
            header("Location: index.php?status=success");
        } else {
            // Erro 1062 é para email duplicado (UNIQUE constraint)
            if ($conn->errno == 1062) {
                header("Location: index.php?status=duplicate");
            } else {
                header("Location: index.php?status=error");
            }
        }
        $stmt->close();
    } else {
        header("Location: index.php?status=prep_error");
    }

    $conn->close();
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>