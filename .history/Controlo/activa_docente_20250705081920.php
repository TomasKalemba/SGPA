<?php
ob_start(); // evita problemas de header já enviado
session_start();
require_once '../modelo/crud.php';

// ✅ Garante que só o Admin pode ativar
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../index.php');
    exit;
}

// ✅ Verifica se veio um ID via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $crud = new crud();

    // ✅ Ativa o docente no banco
    if ($crud->ativarDocente($id)) {
        header("Location: ../visao/ActivarConta.php?status=sucesso");
    } else {
        header("Location: ../visao/ActivarConta.php?status=erro");
    }
    exit;
} else {
    // Se acessado diretamente sem POST válido
    header("Location: ../visao/ActivarConta.php");
    exit;
}

