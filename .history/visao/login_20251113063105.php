
<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login SGPA</title>

  <!-- Fontes e ícones -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Oswald:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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

    @media (max-width: 768px) {
      .container-login {
        flex-direction: column;
      }
      .left-box, .right-box {
        flex: 1 1 100%;
        padding: 30px;
      }
    }

    footer {
      background-color: rgba(0, 0, 0, 0.6);
      color: #fff;
      padding: 10px 20px;
      text-align: center;
      font-size: 14px;
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

        <!-- Mensagem de Erro -->
        <?php if (isset($_SESSION['mensagem']) && !empty($_SESSION['mensagem'])): ?>
          <?php
            $mensagem = $_SESSION['mensagem'];
            $tipoAlerta = 'danger';

            if (str_contains(strtolower($mensagem), 'sucesso')) {
              $tipoAlerta = 'success';
            } elseif (str_contains(strtolower($mensagem), 'aguarde')) {
              $tipoAlerta = 'warning';
            }
          ?>
          <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert" style="width:100%; max-width:400px;">
            <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
          </div>
          <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <form id="loginForm" method="post" action="../controlo/login.php">
          <input type="hidden" name="logar" value="loginUsuario">

          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="email" name="email" id="email" placeholder="Usuário" required>
          </div>

          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="senha" id="senha" placeholder="Senha" required style="padding-left: 40px; padding-right: 40px;">
            <i class="fas fa-eye" id="toggleSenha"></i>
          </div>

          <div class="remember">
            <input type="checkbox" id="lembrar">
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

<?php include_once('RodapeTelasInicial.php'); ?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- Mostrar/ocultar senha -->
<script>
  const toggleSenha = document.getElementById('toggleSenha');
  const senhaInput = document.getElementById('senha');

  toggleSenha.addEventListener('click', () => {
    const tipo = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
    senhaInput.setAttribute('type', tipo);
    toggleSenha.classList.toggle('fa-eye-slash');
  });

  document.getElementById('loginForm').addEventListener('submit', function (e) {
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert('Por favor, insira um email válido.');
      e.preventDefault();
      return;
    }

    if (senha.length < 3) {
      alert('A senha deve ter no mínimo 3 caracteres.');
      e.preventDefault();
      return;
    }
  });
</script>
<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // impede voltar
};
</script>

</body>
</html>
