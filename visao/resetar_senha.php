<?php
session_start();
require_once '../modelo/crud.php';

$crud = new crud();
$conn = $crud->getConexao();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar token
    $stmt = $conn->prepare("SELECT usuario_id, expiracao, usado FROM reset_senhas WHERE token = ? LIMIT 1");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        die("Token inválido!");
    }

    if ($reset['usado']) {
        die("Este token já foi usado!");
    }

    if (strtotime($reset['expiracao']) < time()) {
        die("Token expirado!");
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['senha'])) {
    $token = $_POST['token'];
    $novaSenha = $_POST['senha'];

    // Buscar dados do token
    $stmt = $conn->prepare("SELECT usuario_id, expiracao, usado FROM reset_senhas WHERE token = ? LIMIT 1");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        die("Token inválido!");
    }

    if ($reset['usado']) {
        die("Este token já foi usado!");
    }

    if (strtotime($reset['expiracao']) < time()) {
        die("Token expirado!");
    }

    // Atualizar senha do usuário
    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->execute([$hash, $reset['usuario_id']]);

    // Marcar token como usado
    $stmt = $conn->prepare("UPDATE reset_senhas SET usado = 1 WHERE token = ?");
    $stmt->execute([$token]);

    echo "Senha redefinida com sucesso! <a href='login.php'>Ir para login</a>";
    exit;
}
else {
    die("Requisição inválida.");
}
?>

<!-- Formulário para redefinir senha -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <h2>Nova Senha</h2>
        <form method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div>
                <label>Nova Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit">Redefinir</button>
        </form>
    </div>
</body>
</html>
