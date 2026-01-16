<?php
// Ficheiro: processa_contacto.php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitização dos dados (Segurança)
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $assunto = mysqli_real_escape_string($conn, $_POST['assunto']);
    $mensagem = mysqli_real_escape_string($conn, $_POST['mensagem']);

    // 2. Inserção na Base de Dados
    $sql = "INSERT INTO contactos (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $assunto, $mensagem);

    if ($stmt->execute()) {
        // Redireciona com sucesso
        header("Location: contacto.php?status=sucesso");
    } else {
        // Em caso de erro técnico
        echo "Erro ao guardar contacto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    header("Location: contacto.php");
    exit();
}
?>
