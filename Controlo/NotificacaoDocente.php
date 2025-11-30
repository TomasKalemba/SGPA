<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../login.php');
    exit;
}

// Carrega o modelo correto
require_once '../modelo/Notificacao.php';

// Cria a instância do modelo
$notificacaoModel = new Notificacoes();

// Busca as notificações do docente logado
$docente_id = $_SESSION['id'];
$notificacoes = $notificacaoModel->listarNotificacoesDocente($docente_id);

// Carrega a visão e passa os dados
require_once '../visao/NotificacaoDocente.php';
