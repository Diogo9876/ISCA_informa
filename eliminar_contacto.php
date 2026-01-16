<?php
// Ficheiro: eliminar_contacto.php
require_once 'db_config.php';

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM contactos WHERE contacto_id = $id");
}
header("Location: admin.php");
exit();
?>