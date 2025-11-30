<?php
require_once '../modelo/crud.php';
session_start();

$msg = '';
$tipoAlerta = 'info';

if (!isset($_GET['token'])) {
    $msg = "Token inválido.";
    $tipoAlerta = "danger";
} else {
    $token = $_GET['token'];
    $crud = new crud();
    $conn = $crud->getConexao();

    // Busca token válido
    $stmt = $conn->prepare("SELECT * FROM reset_senhas WHERE token = :token AND usado = 0 AND expiracao > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $msg = "Token expirado ou inválido.";
        $tipoAlerta = "danger";
    } else {
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        // Busca usuário
        $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmtUser->bindParam(':id', $reset['usuario_id']);
        $stmtUser->execute();
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $msg = "Usuário não encontrado.";
            $tipoAlerta = "danger";
        }

        // Processa redefinição
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['senha'])) {
            $senha = $_POST['senha'];
            $confirmar = $_POST['confirmar'] ?? '';

            if ($senha !== $confirmar) {
                $msg = "As senhas não coincidem.";
                $tipoAlerta = "danger";
            } elseif (strlen($senha) < 6) {
                $msg = "A senha deve ter pelo menos 6 caracteres.";
                $tipoAlerta = "danger";
            } else {
                // Hash Argon2id (mesma do login)
                $hash = password_hash($senha, PASSWORD_ARGON2ID, [
                    'memory_cost' => 1<<17,
                    'time_cost' => 4,
                    'threads' => 2
                ]);

                // Atualiza senha
                $stmtUpdate = $conn->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
                $stmtUpdate->execute([':senha' => $hash, ':id' => $usuario['id']]);

                // Marca token como usado
                $conn->prepare("UPDATE reset_senhas SET usado = 1 WHERE id = :id")->execute([':id' => $reset['id']]);

                $msg = "Senha redefinida com sucesso! <a href='login.php'>Fazer login</a>";
                $tipoAlerta = "success";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { height: 100%; }
        body { display: flex; flex-direction: column; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="bg-light">

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Redefinir Senha</h3>

                        <!-- Alertas -->
                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
                                <?= $msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($usuario) && $usuario): ?>
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="senha" class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" name="senha" id="senha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmar" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" name="confirmar" id="confirmar" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
                            </form>
                        <?php endif; ?>

                        <div class="mt-3 text-center">
                            <a href="login.php" class="text-decoration-none">← Voltar ao login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Rodapé -->
<footer class="bg-light text-center text-muted py-3 border-top">
    <?php include_once('RodapeTelasInicial.php'); ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
