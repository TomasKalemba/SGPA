<?php
session_start();
if (!isset($_SESSION['mensagem']) || strpos($_SESSION['mensagem'], 'sucesso') === false) {
    // Se não tem mensagem de sucesso, redireciona para login
    header("Location: login.php");
    exit();
}
$mensagem = $_SESSION['mensagem'];
unset($_SESSION['mensagem']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Conta Criada com Sucesso</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    background: #f0f2f5;
    display: flex;
    height: 100vh;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .card-success {
    text-align: center;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    background: white;
    max-width: 400px;
  }
  .circle-check {
    margin: 0 auto 1rem auto;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #28a745;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 4rem;
  }
  .btn-login {
    margin-top: 1.5rem;
  }
</style>
</head>
<body>

<div class="card-success">
  <div class="circle-check">
    <i class="fas fa-check"></i>
  </div>
  <h2>Conta Criada com Sucesso!</h2>
  <p><?= htmlspecialchars($mensagem) ?></p>
  <a href="login.php" class="btn btn-success btn-login">Ir para Login</a>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
