<?php
session_start();

// 🔒 Impede acesso ao login se o usuário já estiver logado
if (isset($_SESSION['id']) && isset($_SESSION['tipo'])) {
    switch ($_SESSION['tipo']) {
        case 'Admin':
            header('Location: indexAdmin.php');
            exit;
        case 'Docente':
            header('Location: indexDocente.php');
            exit;
        case 'Estudante':
            header('Location: indexEstudante.php');
            exit;
    }
}

// Recupera mensagens de login e logout
$mensagem = $_SESSION['mensagem_login'] ?? $_SESSION['mensagem_logout'] ?? null;
$tipoAlerta = $_SESSION['tipo_alerta_login'] ?? $_SESSION['tipo_alerta_logout'] ?? 'danger';

// Apaga mensagens para não reaparecer
unset(
    $_SESSION['mensagem_login'], $_SESSION['tipo_alerta_login'],
    $_SESSION['mensagem_logout'], $_SESSION['tipo_alerta_logout']
);

// Recupera email antigo caso login dê erro
$old_email = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']);

// Escolhe ícone conforme tipo de alerta
$icone = $tipoAlerta === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login SGPA</title>

  <!-- Fontes e ícones -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Oswald:wght@400;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background: url('../visao/img/login.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Roboto', sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: rgba(255,255,255,0.15);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      padding: 40px;
      max-width: 400px;
      width: 100%;
      color: #fff;
      backdrop-filter: blur(10px);
    }
    .input-group {
      position: relative;
      margin-bottom: 20px;
    }
    .input-group input {
      width: 100%;
      padding: 12px 15px 12px 40px;
      border-radius: 30px;
      border: none;
      outline: none;
      background: rgba(255,255,255,0.2);
      color: #fff;
    }
    .input-group i {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: #fff;
      left: 15px;
    }
    .input-group .fa-eye {
      right: 15px;
      left: auto;
      cursor: pointer;
    }
    .btn-login {
      width: 100%;
      padding: 10px;
      border-radius: 30px;
      border: none;
      background-color: #222;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #000;
    }
    .alert {
      border-radius: 15px;
      font-weight: 600;
      font-size: 1.1rem;
      text-align: center;
      margin-bottom: 20px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      padding: 15px 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-success {
      background: linear-gradient(135deg, #28a745, #218838);
      color: #fff;
      border: none;
    }
    .alert-danger {
      background: linear-gradient(135deg, #dc3545, #b02a37);
      color: #fff;
      border: none;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h2 class="mb-4 text-center">FAÇA LOGIN</h2>

  <?php if ($mensagem): ?>
    <div class="alert alert-<?= htmlspecialchars($tipoAlerta) ?> alert-dismissible fade show" role="alert">
      <i class="fas <?= $icone ?>"></i>
      <?= htmlspecialchars($mensagem) ?>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
  <?php endif; ?>

  <form id="loginForm" method="post" action="../controlo/login.php">
    <input type="hidden" name="logar" value="1">

    <div class="input-group">
      <i class="fas fa-user"></i>
      <input type="email" name="email" id="email" placeholder="E-mail" required autocomplete="email" value="<?= htmlspecialchars($old_email) ?>">
    </div>

    <div class="input-group">
      <i class="fas fa-lock"></i>
      <input type="password" name="senha" id="senha" placeholder="Senha" required autocomplete="current-password" style="padding-left: 40px; padding-right: 40px;">
      <i class="fas fa-eye" id="toggleSenha"></i>
    </div>

    <button type="submit" class="btn-login">Entrar</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
  // Mostrar/ocultar senha
  const toggleSenha = document.getElementById('toggleSenha');
  const senhaInput = document.getElementById('senha');

  toggleSenha.addEventListener('click', () => {
    const tipo = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
    senhaInput.setAttribute('type', tipo);
    toggleSenha.classList.toggle('fa-eye-slash');
  });
</script>

</body>
</html>
