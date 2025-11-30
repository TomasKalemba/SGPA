<?php
require_once '../modelo/crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $crud = new crud();
    $id = $_POST['id'];

    if ($crud->ativarDocente($id)) {
        header("Location: ../visao/ActivarConta.php?status=sucesso");
    } else {
        header("Location: ../visao/ActivarConta.php?status=erro");
    }
    exit;
} else {
    header("Location: ../visao/ActivarConta.php");
    exit;
}
