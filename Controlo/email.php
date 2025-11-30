<?php
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($para, $assunto, $mensagemHtml) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';         // ou outro SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'tomasferrazkalemba@gmail.com';
        $mail->Password = 'dmzt atyo jbii zmsk';      // use senha de app!
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom('tomasferrazkalemba@gmail.com', 'SGPA Notificações');
        $mail->addAddress($para);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
        return false;
    }
}
