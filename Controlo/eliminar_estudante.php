<?php
session_start();
require_once '../modelo/crud.php';

// Verifica se é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../visao/login.php');
    exit;
}

// Verifica se recebeu o ID por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $crud = new crud();

    // Chama o método para eliminar estudante (ajusta se o nome for diferente)
    $resultado = $crud->eliminarEstudante($id);

    if ($resultado) {
        header("Location: ../visao/EliminarEstudante.php?status_estudante=removido");
        exit;
    } else {
        header("Location: ../visao/EliminarEstudante.php?status_estudante=erro");
        exit;
    }
} else {
    header("Location: ../visao/EliminarEstudante.php?status_estudante=erro");
    exit;
}
