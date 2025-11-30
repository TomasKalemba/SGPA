<?php
session_start();
require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../visao/NotificacaoDocente.php');
    exit;
}

$docente_id = $_SESSION['id'];
$data_envio = $_GET['data'] ?? '';
$titulo = $_GET['projeto'] ?? '';

try {
    $crud = new crud();
    $conn = $crud->getConexao();

    $sql = "UPDATE notificacoes 
            SET status = 'Lida' 
            WHERE docente_id = ? AND data_envio = ? AND projeto_id = (
                SELECT id FROM projectos WHERE titulo = ?
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$docente_id, $data_envio, $titulo]);

} catch (PDOException $e) {
    error_log("Erro ao marcar como lida: " . $e->getMessage());
}

header('Location: ../visao/NotificacaoDocente.php');
exit;
