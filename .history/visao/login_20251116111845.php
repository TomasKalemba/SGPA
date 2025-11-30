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

// Recupera email antigo caso login dê erro
$old_email = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']);

// Recupera mensagem flash (se existir) e tipo da mensagem
$mensagem = $_SESSION['mensagem'] ?? null;
$tipoAlerta = $_SESSION['tipo_alerta'] ?? 'danger';

// Apaga mensagem da sessão para evitar reaparecer em outras páginas
unset($_SESSION['mensagem'], $_SESSION['tipo_alerta']);
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
    /* Seu CSS original */
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Roboto', sans-serif;
    }

    html, body {
      height: 100%;
      display: flex;
      flex-direction: column;
      background: url('../visao/img/login.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container-login {
      width: 100%;
      max-width: 850px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.15);
      display: flex;
      flex-wrap: wrap;
    }

    .left-box {
      flex: 1 1 30%;
      padding: 40px 20px;
      background: linear-gradient(to top left, rgba(255,170,0,0.4), rgba(255,255,255,0.1));
      color: black;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .left-box h2 {
      color: black;
      font-family: 'Oswald', sans-serif;
      font-size: 26px;
    }

    .left-box a {
      color: red;
      text-decoration: underline;
      font-weight: bold;
    }

    .right-box {
      flex: 1 1 70%;
      padding: 40px;
      background: linear-gradient(to bottom right, rgba(100, 0, 255, 0.3), rgba(255, 0, 100, 0.3));
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    h2 {
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: 700;
    }

    form {
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: 400px;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px 12px 40px;
      border: none;
      border-radius: 30px;
      outline: none;
      background: rgba(255,255,255,0.2);
      color: #fff;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: #fff;
    }

    .input-group .fa-user { left: 15px; }
    .input-group .fa-lock { left: 15px; }
    .input-group .fa-eye {
      right: 15px;
      left: auto;
      cursor: pointer;
    }

    .remember {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .remember input {
      margin-right: 8px;
    }

    .btn-login {
      background-color: #222;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .btn-login:hover {
      background-color: #000;
    }

    .links {
      margin-top: 15px;
      text-align: center;
    }

    .links a {
      color: #fff;
      font-size: 14px;
      text-decoration: underline;
    }

    /* ALERTAS PERSONALIZADOS - CORES VIVAS */
    .alert {
      border-radius: 15px;
      font-weight: 600;
      font-size: 1.1rem;
      max-width: 400px;
      width: 100%;
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

    .alert-warning {
      background: linear-gradient(135deg, #ffc107, #d39e00);
      color: #212529;
      border: none;
    }

    /* OVERLAY PROCESSANDO */
    #overlayProcessando {
      display: none;
      position: fixed;
      top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.75);
      z-index: 1050;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: #fff;
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      user-select: none;
    }

    #overlayProcessando .spinner-border {
      width: 5rem;
      height: 5rem;
      border-width: 0.5rem;
      margin-bottom: 20px;
      animation: spin 1s linear infinite;
      border-top-color: #ffb700;
      border-right-color: transparent;
      border-bottom-color: #ffb700;
      border-left-color: transparent;
      border-radius: 50%;
    }

    @keyframes spin {
      0% { transform: rotate(0deg);}
      100% { transform: rotate(360deg);}
    }
  </style>
</head>
<body>
<main>
  <div class="container-login">
    <div class="left-box">
      <h2>BEM-VINDO AO SGPA</h2>
      <p><strong><a href="register.php">Cadastre-se aqui</a></strong></p>
      <p style="margin-top: 20px; font-size: 14px;">Crie sua conta para começar</p>
    </div>

    <div class="right-box">
      <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        <h2>FAÇA LOGIN</h2>

        <!-- Mensagem flash -->
        <?php if ($mensagem): ?>
          <div class="alert alert-<?= htmlspecialchars($tipoAlerta) ?> alert-dismissible fade show" role="alert" style="width:100%; max-width:400px;">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
          </div>
        <?php endif; ?>

        <form id="loginForm" method="post" action="../controlo/login.php">
          <input type="hidden" name="logar" value="loginUsuario">

          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="email"
                   name="email"
                   id="email"
                   placeholder="Usuário"
                   required
                   autocomplete="off"
                   value="<?= htmlspecialchars($old_email) ?>">
          </div>

          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password"
                   name="senha"
                   id="senha"
                   placeholder="Senha"
                   required
                   autocomplete="off"
                   style="padding-left: 40px; padding-right: 40px;">
            <i class="fas fa-eye" id="toggleSenha"></i>
          </div>

          <div class="remember">
            <input type="checkbox" name="lembrar" id="lembrar">
            <label for="lembrar">Lembrar</label>
          </div>

          <button type="submit" class="btn-login">Entrar</button>

          <div class="links">
            <p><a href="password.php">Esqueceu a senha?</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<!-- OVERLAY PROCESSANDO -->
<div id="overlayProcessando">
  <div class="spinner-border"></div>
  <div>Processando login, por favor aguarde...</div>
</div>

<?php include_once('RodapeTelasInicial.php'); ?>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mostrar/ocultar senha -->
<script>
  const toggleSenha = document.getElementById('toggleSenha');
  const senhaInput = document.getElementById('senha');

  toggleSenha.addEventListener('click', () => {
    const tipo = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
    senhaInput.setAttribute('type', tipo);
    toggleSenha.classList.toggle('fa-eye-slash');
  });
</script>

<!-- Mostrar overlay ao submeter -->
<script>
  document.getElementById('loginForm').addEventListener('submit', function() {
    document.getElementById('overlayProcessando').style.display = 'flex';
  });
</script>

<!-- Mostrar overlay com sucesso por 2 segundos se mensagem success for exatamente "Login feito com sucesso!" -->
<script>
  window.addEventListener('load', () => {
    const alertBox = document.querySelector('.alert-success');
    if (alertBox) {
      const mensagem = alertBox.textContent.trim();
      if (mensagem === 'Login feito com sucesso!') {
        const overlay = document.getElementById('overlayProcessando');
        overlay.querySelector('div:nth-child(2)').textContent = 'Login feito com sucesso!';
        overlay.style.display = 'flex';

        setTimeout(() => {
          overlay.style.display = 'none';
        }, 2000);
      }
    }
  });
</script>

</body>
</html>
