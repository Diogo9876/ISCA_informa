<?php
// logout.php - Sistema avançado de logout
session_start();

// Configurações
$redirecionar_para = 'login.php'; // Para onde redirecionar após logout
$mensagem_logout = 'success';      // Tipo de mensagem a mostrar

// Informações do usuário (para logging)
$dados_usuario = [
    'username' => isset($_SESSION['admin_user']) ? $_SESSION['admin_user'] : 'Visitante',
    'tipo' => isset($_SESSION['tipo_utilizador']) ? $_SESSION['tipo_utilizador'] : 'Desconhecido',
    'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
    'login_time' => isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0
];

// Calcular tempo de sessão (se disponível)
if ($dados_usuario['login_time'] > 0) {
    $tempo_sessao = time() - $dados_usuario['login_time'];
    $tempo_formatado = gmdate("H:i:s", $tempo_sessao);
} else {
    $tempo_formatado = "Desconhecido";
}

// Registrar o logout (opcional - para auditoria)
$log_entry = sprintf(
    "[%s] LOGOUT: Usuário: %s (ID: %d, Tipo: %s) | Tempo de sessão: %s | IP: %s | User-Agent: %s",
    date('Y-m-d H:i:s'),
    $dados_usuario['username'],
    $dados_usuario['user_id'],
    $dados_usuario['tipo'],
    $tempo_formatado,
    $_SERVER['REMOTE_ADDR'],
    substr($_SERVER['HTTP_USER_AGENT'], 0, 100)
);

// Escrever no log (opcional)
error_log($log_entry);

// Se quiser guardar num arquivo de log próprio
$log_file = 'logs/logout_log.txt';
if (is_writable(dirname($log_file))) {
    file_put_contents($log_file, $log_entry . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Limpar todos os dados da sessão
$_SESSION = array();

// Opção: destruir cookie da sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir a sessão
if (session_destroy()) {
    $logout_success = true;
} else {
    $logout_success = false;
    error_log("ERRO: Não foi possível destruir a sessão para usuário: " . $dados_usuario['username']);
}

// Preparar redirecionamento com parâmetros
$params = array();
if ($logout_success) {
    $params['logout'] = $mensagem_logout;
    $params['username'] = urlencode($dados_usuario['username']);
    $params['time'] = $tempo_formatado;
} else {
    $params['logout'] = 'error';
    $params['message'] = 'erro_sessao';
}

// Construir URL de redirecionamento
$redirect_url = $redirecionar_para . '?' . http_build_query($params);

// Redirecionar
header("Location: " . $redirect_url);
exit();
?>