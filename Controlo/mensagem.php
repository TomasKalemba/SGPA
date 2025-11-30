<?php
require_once("../modelo/crud.php");
session_start();

ob_start();

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Luanda');

$crud = new crud();
$conn = $crud->getConexao();

function limpar_buffer() {
    if (ob_get_level()) {
        ob_end_clean();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'enviar') {
        $emissor = isset($_POST['de']) ? intval($_POST['de']) : 0;
        $receptor = isset($_POST['para']) ? intval($_POST['para']) : 0;
        $mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
        $data = date('Y-m-d');
        $hora = date('H:i:s');

        if ($emissor <= 0 || $receptor <= 0 || empty($mensagem)) {
            limpar_buffer();
            echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos corretamente.']);
            exit;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO mensagem (emissor, receptor, mensagem, data, hora) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$emissor, $receptor, $mensagem, $data, $hora]);

            limpar_buffer();
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Mensagem enviada com sucesso!']);
        } catch (PDOException $e) {
            limpar_buffer();
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao enviar mensagem: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($acao === 'ler') {
        $id_conversa = isset($_POST['conversacom']) ? intval($_POST['conversacom']) : 0;
        $usuarioLogadoId = isset($_POST['online']) ? intval($_POST['online']) : 0;

        if ($id_conversa <= 0 || $usuarioLogadoId <= 0) {
            limpar_buffer();
            echo json_encode(['status' => 'erro', 'mensagem' => 'IDs inválidos para leitura.']);
            exit;
        }

        try {
         $stmt = $conn->prepare("
    SELECT * FROM (
        SELECT 
            m.id, 
            m.emissor, 
            m.receptor, 
            m.mensagem, 
            u.foto 
        FROM mensagem AS m 
        LEFT JOIN usuarios AS u ON u.id = m.emissor 
        WHERE 
            (m.emissor = :online AND m.receptor = :conversa) 
            OR 
            (m.emissor = :conversa AND m.receptor = :online) 
        ORDER BY m.id DESC 
        LIMIT 10
    ) AS ultimas_mensagens
    ORDER BY id ASC
");


$stmt->execute([
    ':online' => $usuarioLogadoId,
    ':conversa' => $id_conversa
]);

$mensagens = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mensagens[] = [
        'id' => (int)$row['id'],
        'id_de' => (int)$row['emissor'],
        'mensagem' => htmlspecialchars($row['mensagem'], ENT_QUOTES, 'UTF-8'),
        'foto' => !empty($row['foto']) ? "../uploads/fotos/" . $row['foto'] : ''
    ];
}


            limpar_buffer();
            echo json_encode(['status' => 'sucesso', 'mensagens' => $mensagens]);
        } catch (PDOException $e) {
            limpar_buffer();
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao ler mensagens: ' . $e->getMessage()]);
        }
        exit;
    }
}

limpar_buffer();
echo json_encode(['status' => 'erro', 'mensagem' => 'Ação inválida.']);
exit;
