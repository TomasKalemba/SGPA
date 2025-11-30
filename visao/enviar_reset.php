<?php
session_start();
require_once '../modelo/crud.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$msg = ''; // Mensagem a exibir
$tipoAlerta = 'info'; // success / danger

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    try {
        $crud = new crud();
        $conn = $crud->getConexao();

        // Verificar se email existe no sistema
        $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Salvar token no banco
            $stmt = $conn->prepare("
                INSERT INTO reset_senhas (usuario_id, token, expiracao, usado)
                VALUES (:usuario_id, :token, :expiracao, :usado)
            ");
            $stmt->execute([
                ':usuario_id' => $usuario['id'],
                ':token'      => $token,
                ':expiracao'  => $expira,
                ':usado'      => 0
            ]);

            // Criar link de redefinição
            $link = "http://localhost/SGPA/visao/reset.php?token=$token&email=" . urlencode($email);

            // Enviar email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tomasferrazkalemba@gmail.com';
                $mail->Password   = 'dmzt atyo jbii zmsk';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('tomasferrazkalemba@gmail.com', 'SGPA - Recuperação de Senha');
                $mail->addAddress($email, $usuario['nome']);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperação de Senha - SGPA';
                $mail->Body    = "
                    <p>Olá <b>{$usuario['nome']}</b>,</p>
                    <p>Você solicitou a redefinição de senha no sistema SGPA.</p>
                    <p>Clique no link abaixo para redefinir sua senha (válido por 1 hora):</p>
                    <p><a href='$link'>$link</a></p>
                    <br>
                    <p>Se você não solicitou esta alteração, ignore este e-mail.</p>
                ";

                $mail->send();
                $msg = "Um link de redefinição foi enviado para seu e-mail.";
                $tipoAlerta = "success";

            } catch (Exception $e) {
                $msg = "Erro ao enviar email: {$mail->ErrorInfo}";
                $tipoAlerta = "danger";
            }
        } else {
            $msg = "E-mail não encontrado no sistema.";
            $tipoAlerta = "danger";
        }
    } catch (Exception $e) {
        $msg = "Erro: " . $e->getMessage();
        $tipoAlerta = "danger";
    }
} else {
    $msg = "Requisição inválida.";
    $tipoAlerta = "danger";
}

// Exibir mensagem usando alerta Bootstrap
if (!empty($msg)) {
    echo "
    <div class='container mt-4'>
        <div class='alert alert-$tipoAlerta alert-dismissible fade show' role='alert'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Fechar'></button>
        </div>
    </div>
    ";
}
?>



















           
            