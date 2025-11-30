<?php
ob_start();
session_start();

// Configuração do banco de dados
$host = 'localhost';
$db   = 'sgpa';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id              = $_POST['id'];
    $nome            = $_POST['nome'];
    $tipo            = $_POST['tipo'];
    $email           = $_POST['email'];
    $senha           = !empty($_POST['senha']) ? $_POST['senha'] : null;
    $numeroMatricula = $_POST['numero_matricula'] ?? null;
    $cursoId         = $_POST['curso_id'] ?? null;
    $anoCurricular   = $_POST['ano_curricular'] ?? null;

    // --- Upload da foto ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $pastaFotos = '../visao/fotos/';
        if (!is_dir($pastaFotos)) {
            mkdir($pastaFotos, 0777, true);
        }

        $nomeArquivo = time() . '_' . basename($_FILES['foto']['name']);
        $caminhoCompleto = $pastaFotos . $nomeArquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
            $stmtFoto = $pdo->prepare("UPDATE usuarios SET foto = :foto WHERE id = :id");
            $stmtFoto->bindParam(':foto', $nomeArquivo);
            $stmtFoto->bindParam(':id', $id);
            $stmtFoto->execute();
            $_SESSION['foto'] = $nomeArquivo;
        }
    }

    // --- Atualizar dados do usuário ---
    if ($senha) {
        $stmt = $pdo->prepare("UPDATE usuarios 
            SET nome=:nome, tipo=:tipo, email=:email, senha=:senha, 
                numero_matricula=:matricula, curso_id=:curso, ano_curricular=:ano 
            WHERE id=:id");
        $stmt->bindParam(':senha', $senha);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios 
            SET nome=:nome, tipo=:tipo, email=:email, 
                numero_matricula=:matricula, curso_id=:curso, ano_curricular=:ano 
            WHERE id=:id");
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':matricula', $numeroMatricula);
    $stmt->bindParam(':curso', $cursoId);
    $stmt->bindParam(':ano', $anoCurricular);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Atualiza sessão
        $_SESSION['nome']             = $nome;
        $_SESSION['tipo']             = $tipo;
        $_SESSION['email']            = $email;
        $_SESSION['numero_matricula'] = $numeroMatricula;
        $_SESSION['curso_id']         = $cursoId;
        $_SESSION['ano_curricular']   = $anoCurricular;

        if ($senha) {
            $_SESSION['senha'] = $senha;
        }

        $_SESSION['mensagem'] = "Perfil do Aluno Editado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao Editar o Perfil do Aluno.";
    }

    header('Location: ../Visao/EditarPerfilAluno.php');
    exit();
}
?>
