<?php
// Ficheiro: db_config.php

// Parâmetros de conexão
$servername = "localhost";
$username = "root";        // Padrão do XAMPP
$password = "";            // Padrão do XAMPP
$dbname = "isca_informa";  // Nome da Base de Dados que acabou de importar

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na Conexão à Base de Dados: " . $conn->connect_error);
}
?>