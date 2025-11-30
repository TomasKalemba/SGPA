<?php
require_once '../modelo/crud.php';

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];
$crud = new crud();
$conn = $crud->getConexao();

// Busca token na tabela reset_senhas
$stmt = $conn->prepare("SELECT * FROM reset_senhas WHERE token = ? AND usado = 0 AND expiracao > NOW()");
$stmt->execute([$token]);

if ($stmt->rowCount() === 0) {
    die("Token expirado ou inválido.");
}

$reset = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca usuário pelo id
$stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmtUser->execute([$reset['usuario_id']]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuário não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['senha'])) {
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'] ?? '';

    if ($senha !== $confirmar) {
        echo "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        echo "A senha deve ter pelo menos 6 caracteres.";
    } else {
        // Gera hash seguro
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Atualiza senha do usuário
        $stmt = $conn->prepare("UPDATE usuarios SET senha=? WHERE id=?");
        $stmt->execute([$hash, $usuario['id']]);

        // Marca token como usado
        $conn->prepare("UPDATE reset_senhas SET usado=1 WHERE id=?")->execute([$reset['id']]);

        echo "Senha redefinida com sucesso. <a href='login.php'>Fazer login</a>";
        exit;
    }
}
?>

<form method="post">
    <label>Nova Senha:</label>
    <input type="password" name="senha" required><br>

    <label>Confirmar Senha:</label>
    <input type="password" name="confirmar" required><br>

    <button type="submit">Redefinir</button>
</form>
