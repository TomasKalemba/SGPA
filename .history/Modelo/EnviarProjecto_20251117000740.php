<?php
session_start();
require_once '../modelo/crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Guarda os valores enviados caso dê erro
    $_SESSION['old'] = $_POST;

    // Validações simples
    if (empty($_POST['projeto_id']) || empty($_POST['docente_nome'])) {
        $_SESSION['mensagem'] = "Erro: Preencha todos os campos obrigatórios.";
        header("Location: ../visao/EnviarProjecto.php");
        exit;
    }

    if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] != 0) {
        $_SESSION['mensagem'] = "Erro: Selecione um arquivo válido.";
        header("Location: ../visao/EnviarProjecto.php");
        exit;
    }

    // --- Verificação de título duplicado ---
    $crud = new crud();
    $con = $crud->getConexao();

    // Verifica se já existe um projeto com o mesmo título para o mesmo docente
    $sqlVerificaTitulo = "SELECT COUNT(*) FROM projectos_submetidos WHERE docente_nome = :docente_nome AND projeto_id = :projeto_id";
    $stmtVerificaTitulo = $con->prepare($sqlVerificaTitulo);
    $stmtVerificaTitulo->bindParam(':docente_nome', $_POST['docente_nome']);
    $stmtVerificaTitulo->bindParam(':projeto_id', $_POST['projeto_id']);
    $stmtVerificaTitulo->execute();
    
    // Se o resultado for maior que 0, significa que já existe um projeto com o mesmo título para o docente
    if ($stmtVerificaTitulo->fetchColumn() > 0) {
        $_SESSION['mensagem'] = "Erro: Já existe um projeto com o mesmo título para este docente.";
        header("Location: ../visao/EnviarProjecto.php");
        exit;
    }

    // --- Exemplo de inserção ---
    $sql = "INSERT INTO projectos_submetidos (projeto_id, docente_nome, descricao, data_submissao, estatus, feedback, arquivo)
            VALUES (:p, :d, :desc, :data, :st, :fb, :arq)";

    $stmt = $con->prepare($sql);

    // Lê o arquivo enviado
    $arquivo = file_get_contents($_FILES['arquivo']['tmp_name']);

    $stmt->bindParam(':p', $_POST['projeto_id']);
    $stmt->bindParam(':d', $_POST['docente_nome']);
    $stmt->bindParam(':desc', $_POST['descricao']);
    $stmt->bindParam(':data', $_POST['data_submissao']);
    $stmt->bindParam(':st', $_POST['estatus']);
    $stmt->bindParam(':fb', $_POST['feedback']);
    $stmt->bindParam(':arq', $arquivo, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        unset($_SESSION['old']); // limpa valores
        $_SESSION['mensagem'] = "Projeto enviado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao enviar projeto.";
    }

    header("Location: ../visao/EnviarProjecto.php");
}
