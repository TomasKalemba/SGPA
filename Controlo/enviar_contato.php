<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Verifica se usuário está logado
if (!isset($_SESSION['tipo'])) {
    header('Location: ../visao/login.php');
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (empty($nome) || empty($email) || empty($mensagem)) {
        header("Location: ../visao/Contato.php?status=erro");
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.seudominio.com'; // Substitua pelo seu SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tomasferrazkalemba@gmail.com'; // Seu email
        $mail->Password   = 'dmzt atyo jbii zmsk';       // Sua senha
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom($email, $nome);
        $mail->addAddress('tomasferrazkalemba@gmail.com', 'Admin'); // Email que receberá a mensagem

        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Nova mensagem de contato';
        $mail->Body    = "<strong>Nome:</strong> {$nome}<br>
                          <strong>Email:</strong> {$email}<br>
                          <strong>Mensagem:</strong><br>{$mensagem}";

        $mail->send();
        header("Location: ../visao/Contato.php?status=enviado");
        exit;
    } catch (Exception $e) {
        // Em caso de erro no envio
        header("Location: ../visao/Contato.php?status=erro");
        exit;
    }
} else {
    header("Location: ../visao/Contato.php");
    exit;
}
