<?php
ob_start(); // Evita saída acidental antes do JSON

require_once("../modelo/crud.php");
session_start();

header('Content-Type: application/json');
ini_set('display_errors', 0); // ⛔ Não mostrar erros diretamente
error_reporting(E_ALL);

$crud = new crud();
$conn = $crud->getConexao();

// ================== LISTAR MENSAGENS DO PROJETO ==================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'listar') {

    $projeto_id = intval($_GET['projeto_id'] ?? 0);

    if ($projeto_id <= 0) {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Projeto não informado ou inválido.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT m.id, m.mensagem, m.data_envio, u.nome AS autor_nome
            FROM chat_mensagens m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.projeto_id = ?
            ORDER BY m.data_envio ASC
        ");
        $stmt->execute([$projeto_id]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_end_clean();
        echo json_encode(['status' => 'sucesso', 'dados' => $mensagens]);

    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao listar mensagens: ' . $e->getMessage()]);
    }
    exit;
}

// ================== ENVIAR MENSAGEM ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'enviar') {

    if (!isset($_SESSION['id'])) {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
        exit;
    }

    $usuario_id = $_SESSION['id'];
    $projeto_id = intval($_POST['projeto_id'] ?? 0);
    $mensagem   = trim($_POST['mensagem'] ?? '');

    if ($projeto_id <= 0) {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Projeto inválido.']);
        exit;
    }

    if ($mensagem === '') {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Mensagem vazia.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO chat_mensagens (projeto_id, usuario_id, mensagem, data_envio)
            VALUES (?, ?, ?, NOW())
        ");
        $ok = $stmt->execute([$projeto_id, $usuario_id, $mensagem]);

        ob_end_clean();
        echo json_encode(['status' => $ok ? 'sucesso' : 'erro']);

    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao enviar mensagem: ' . $e->getMessage()]);
    }
    exit;
}

// ================== AÇÃO INVÁLIDA ==================
ob_end_clean();
echo json_encode(['status' => 'erro', 'mensagem' => 'Ação inválida.']);
exit;
