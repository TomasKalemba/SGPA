<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    header('Location: ../login.php');
    exit;
}

// Carrega o modelo de notificações
require_once '../modelo/Notificacao.php';

// Instancia o modelo
$notificacaoModel = new Notificacoes();

// Busca as notificações do estudante logado
$estudante_id = $_SESSION['id'];
$notificacoes = $notificacaoModel->listarNotificacoesEstudante($estudante_id);

// Carrega a visão e passa os dados
require_once '../visao/NotificacaoEstudante.php';
