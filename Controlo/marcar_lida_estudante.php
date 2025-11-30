<?php
session_start();

require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
   header("Location: http://localhost/sgpa/visao/notificacoes_estudante.php");

    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $notificacao_id = intval($_GET['id']);
    $crud = new crud();
    $conn = $crud->getConexao();

    $stmt = $conn->prepare("UPDATE notificacoes SET status = 'Lida' WHERE id = ? AND estudante_id = ?");
    $stmt->execute([$notificacao_id, $_SESSION['id']]);
}

header("Location: ../visao/NotificacaoEstudante.php");
exit;
