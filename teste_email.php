<?php
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tomasferrazkalemba@gmail.com'; // teu e-mail
    $mail->Password   = 'dmzt atyo jbii zmsk'; // senha de app
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('tomasferrazkalemba@gmail.com', 'SGPA Notificações');
    $mail->addAddress('tomasferrazkalemba@gmail.com', 'Usuário Teste');

    $mail->isHTML(true);
    $mail->Subject = 'Teste de E-mail - SGPA';
    $mail->Body    = '<h3>Este é um teste de envio de e-mail com PHPMailer.</h3><p>Funcionou!</p>';

    $mail->send();
    echo 'E-mail enviado com sucesso!';
} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
