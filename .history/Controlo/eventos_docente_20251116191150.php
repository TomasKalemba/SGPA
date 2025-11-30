<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/crud.php'; // Ajuste para o arquivo onde está a classe crud

try {
    // Instancia a classe crud
    $crud = new crud();

    // Pega a conexão PDO
    $pdo = $crud->getConexao();

    $docente_id = $_SESSION['id'];

    $stmt = $pdo->prepare("
        SELECT id, titulo, prazo, estatus, feedback
        FROM projectos
        WHERE docente_id = :docente_id
    ");
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = []; // Inicializa o array antes do loop

    foreach ($projetos as $row) {
        $data_submissao = strtotime($row['prazo']);
        $cor = '#3788d8'; // Pode ajustar conforme necessário

        $hoje = strtotime(date('Y-m-d'));
        $vencido = $data_submissao < $hoje;
        $alerta = false; // Ajuste sua lógica aqui
        $diasRestantes = max(0, ($data_submissao - $hoje) / 86400);

        $eventos[] = [
            'title' => $row['titulo'],
            'start' => date('Y-m-d', $data_submissao),
            'color' => $cor,
            'extendedProps' => [
                'projeto_id' => $row['id'],
                'status' => $vencido ? 'Atrasado (prazo vencido)' : $row['estatus'],
                'feedback' => $row['feedback'],
                'vencido' => $vencido,
                'alerta' => $alerta,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    echo json_encode($eventos);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao carregar eventos: ' . $e->getMessage()]);
}


