<?php
session_start();

// Só permite acesso se existir mensagem de sucesso do registro na sessão
if (!isset($_SESSION['mensagem_registro']) || strpos(strtolower($_SESSION['mensagem_registro']), 'sucesso') === false) {
    header("Location: login.php");
    exit();
}

$msgSucesso = $_SESSION['mensagem_registro'];
unset($_SESSION['mensagem_registro']);
unset($_SESSION['tipo_alerta_registro']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8" />
<title>Conta Criada com Sucesso</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    body {
        background: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        font-family: Arial, sans-serif;
        margin: 0;
    }
    .success-box {
        background: white;
        padding: 40px 50px;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 400px;
        position: relative;
        z-index: 1;
    }
    .success-icon {
        font-size: 4rem;
        color: #28a745;
        margin-bottom: 20px;
        animation: popIn 0.6s ease forwards;
    }
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .btn-login {
        margin-top: 30px;
    }
</style>
</head>
<body>

<div class="success-box">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>

    <!-- Exibe alerta Bootstrap -->
    <div class="alert alert-success" role="alert" style="font-weight: 600;">
        <?= htmlspecialchars($msgSucesso) ?>
    </div>

    <p>Sua conta foi criada com sucesso! Agora você pode fazer login.</p>
    <a href="login.php" class="btn btn-success btn-login">Ir para Login</a>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
