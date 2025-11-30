<?php
ob_start();
session_start();
require_once '../modelo/crud.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../visao/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $crud = new crud();

    $resultado = $crud->eliminarDocente($id);

    if ($resultado) {
        header('Location: ../visao/ActivarConta.php?status=removido');
    } else {
        header('Location: ../visao/ActivarConta.php?status=erro');
    }
    exit;
} else {
    header('Location: ../visao/ActivarConta.php?status=erro');
    exit;
}
