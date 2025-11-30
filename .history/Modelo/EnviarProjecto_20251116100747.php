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

    // --- Exemplo de inserção ---
    $crud = new crud();
    $con = $crud->getConexao();

    $sql = "INSERT INTO projectos_submetidos (projeto_id, docente_nome, descricao, data_submissao, estatus, feedback, arquivo)
            VALUES (:p, :d, :desc, :data, :st, :fb, :arq)";

    $stmt = $con->prepare($sql);

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
