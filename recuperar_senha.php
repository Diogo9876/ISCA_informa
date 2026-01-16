<?php
// recuperar_senha.php - Página de recuperação de senha
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - ISCA INFORMA</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .recovery-box { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.2); 
            width: 400px; 
            text-align: center;
        }
        h2 { color: #1a2980; margin-bottom: 20px; }
        p { color: #666; margin-bottom: 25px; }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 2px solid #e1e5eb; 
            border-radius: 8px; 
            font-size: 16px; 
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(to right, #1a2980, #26d0ce); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 16px; 
            margin-top: 15px; 
        }
        .back-link { margin-top: 20px; }
        .back-link a { color: #1a2980; text-decoration: none; }
    </style>
</head>
<body>
    <div class="recovery-box">
        <h2>🔐 Recuperar Senha</h2>
        <p>Digite o seu email e enviaremos instruções para redefinir a sua senha.</p>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Seu email" required>
            <button type="submit">Enviar Instruções</button>
        </form>
        
        <div class="back-link">
            <a href="login.php">← Voltar ao Login</a>
        </div>
    </div>
</body>
</html>