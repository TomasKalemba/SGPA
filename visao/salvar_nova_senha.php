<?php
session_start();
require_once '../modelo/crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $novaSenha = $_POST['senha'];

    $crud = new crud();
    $conn = $crud->getConexao();

    // Busca token válido
    $stmt = $conn->prepare("SELECT usuario_id, expiracao FROM reset_senhas WHERE token = :token AND usado = 0 LIMIT 1");
    $stmt->bindParam(":token", $token);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (strtotime($row['expiracao']) > time()) {
            // Atualiza senha com hash seguro
            $hash = password_hash($novaSenha, PASSWORD_ARGON2ID);

            $stmt = $conn->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $stmt->bindParam(":senha", $hash);
            $stmt->bindParam(":id", $row['usuario_id']);
            $stmt->execute();

            // Marca token como usado
            $stmt = $conn->prepare("UPDATE reset_senhas SET usado = 1 WHERE token = :token");
            $stmt->bindParam(":token", $token);
            $stmt->execute();

            $_SESSION['mensagem'] = "Senha atualizada com sucesso!";
        } else {
            $_SESSION['mensagem'] = "Token expirado. Solicite novamente.";
        }
    } else {
        $_SESSION['mensagem'] = "Token inválido!";
    }

    header("Location: ../visao/login.php");
    exit;
}
?>

