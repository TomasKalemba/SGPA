<?php
session_start();
require_once '../modelo/crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);

    $crud = new crud();
    $conn = $crud->getConexao();

    // Verifica se o email existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['msg'] = "Se este email estiver registado, enviaremos o link de recuperação.";
        header("Location: ../visao/password.php");
        exit;
    }

    // Gera token seguro
    $token = bin2hex(random_bytes(32));
    $expiracao = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Salva token na tabela reset_senhas
    $stmt = $conn->prepare("INSERT INTO reset_senhas (usuario_id, token, expiracao, usado) VALUES (?, ?, ?, 0)");
    $stmt->execute([$usuario['id'], $token, $expiracao]);

    // Link para redefinir senha
    $url = "http://" . $_SERVER['HTTP_HOST'] . "/SGPA/Visao/nova_senha.php?token=" . $token;

    // Enviar email (ou exibir o link caso não tenha servidor de email)
    $assunto = "Recuperação de senha";
    $mensagem = "Clique no link para redefinir sua senha: $url";
    $headers = "From: no-reply@seudominio.com\r\n";

    if (mail($email, $assunto, $mensagem, $headers)) {
        $_SESSION['msg'] = "Um link de redefinição foi enviado para seu email.";
    } else {
        $_SESSION['msg'] = "Não foi possível enviar email. Copie este link: <a href='$url'>$url</a>";
    }

    header("Location: ../Visao/password.php");
    exit;
}

