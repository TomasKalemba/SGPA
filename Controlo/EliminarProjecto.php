<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do projeto não foi fornecido.";
    header("Location: ../visao/ListaProjectos.php");
    exit;
}

$projecto_id = $_GET['id'];

// Conexão
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sgpa", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transação
    $pdo->beginTransaction();

    // Buscar grupo associado ao projeto
    $stmtGrupo = $pdo->prepare("SELECT id FROM grupo WHERE projeto_id = ?");
    $stmtGrupo->execute([$projecto_id]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if ($grupo) {
        $grupo_id = $grupo['id'];

        // Deletar estudantes do grupo
        $stmtDeleteGE = $pdo->prepare("DELETE FROM grupo_estudante WHERE grupo_id = ?");
        $stmtDeleteGE->execute([$grupo_id]);

        // Deletar grupo
        $stmtDeleteGrupo = $pdo->prepare("DELETE FROM grupo WHERE id = ?");
        $stmtDeleteGrupo->execute([$grupo_id]);
    }

    // Deletar submissões relacionadas ao projeto
    $stmtDeleteSubmisoes = $pdo->prepare("DELETE FROM submisoes WHERE id_projectos = ?");
    $stmtDeleteSubmisoes->execute([$Id_projectos]);

    // Deletar o próprio projeto
    $stmtDeleteProjecto = $pdo->prepare("DELETE FROM projectos WHERE Id = ?");
    $stmtDeleteProjecto->execute([$projecto_id]);

    $pdo->commit();
    $_SESSION['mensagem'] = "Projeto eliminado com sucesso.";

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['mensagem'] = "Erro ao eliminar o projeto: " . $e->getMessage();
}

header("Location: ../visao/ListaProjectos.php");
exit;
