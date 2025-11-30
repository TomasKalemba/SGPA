<?php
session_start();
require_once '../modelo/crud.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$msg = '';
$tipoAlerta = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    try {
        $crud = new crud();
        $conn = $crud->getConexao();

        // Verificar se email existe
        $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Salvar token
            $stmt = $conn->prepare("
                INSERT INTO reset_senhas (usuario_id, token, expiracao, usado) 
                VALUES (:usuario_id, :token, :expiracao, :usado)
            ");
            $stmt->execute([
                ':usuario_id' => $usuario['id'],
                ':token' => $token,
                ':expiracao' => $expira,
                ':usado' => 0
            ]);

            // Criar link
            $link = "http://localhost/SGPA/visao/reset.php?token=$token&email=" . urlencode($email);

            // Enviar email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'tomasferrazkalemba@gmail.com';
                $mail->Password = 'dmzt atyo jbii zmsk';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('tomasferrazkalemba@gmail.com', 'SGPA - Recuperação de Senha');
                $mail->addAddress($email, $usuario['nome']);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperação de Senha - SGPA';
                $mail->Body = "
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
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #007bff, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0px 10px 25px rgba(0,0,0,0.2);
        }
        .card h3 {
            font-weight: 600;
            color: #333;
        }
        .btn-custom {
            background: #007bff;
            border: none;
            border-radius: .5rem;
            padding: 12px;
            font-size: 1rem;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        a {
            color: #007bff;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="col-md-6 offset-md-3">
        <div class="card p-4">
            <h3 class="text-center mb-4">🔑 Recuperar Senha</h3>

            <!-- Alertas -->
            <?php if(!empty($msg)): ?>
                <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Digite seu e-mail</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="exemplo@email.com" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Enviar link de recuperação</button>
            </form>

            <div class="mt-3 text-center">
                <a href="../index.php">← Voltar ao login</a>
            </div>
            
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
