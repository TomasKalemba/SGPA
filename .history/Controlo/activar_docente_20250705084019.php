<?php
ob_start();
session_start();
require_once '../modelo/crud.php';

// Verifica se é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../visao/login.php');
    exit;
}

// Verifica se veio via POST e com ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $crud = new crud();

    // Verifica se o usuário é docente antes de ativar
    $usuario = $crud->buscarPorId('usuarios', $id);

    if ($usuario && $usuario['tipo'] === 'Docente') {
        $resultado = $crud->ativarDocente($id);

        if ($resultado) {
            header('Location: ../visao/ActivarConta.php?status=sucesso');
            exit;
        }
    }

    // Falha ao ativar
    header('Location: ../visao/ActivarConta.php?status=erro');
    exit;
} else {
    // Acesso inválido
    header('Location: ../visao/ActivarConta.php?status=erro');
    exit;
}
