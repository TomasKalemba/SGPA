<?php
session_start();
header('Content-Type: application/json');
ob_start(); // Evita qualquer output antes do JSON

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();

    $docente_id = $_SESSION['id'];
    $stmt = $pdo->prepare("
        SELECT id, titulo, prazo, estatus, feedback
        FROM projectos
        WHERE docente_id = :docente_id
    ");
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = [];

    $hoje = strtotime(date('Y-m-d')); // Calcula hoje apenas uma vez
    foreach ($projetos as $row) {
        $data_submissao = strtotime($row['prazo']);
        $vencido = $data_submissao < $hoje;
        $diasRestantes = ($data_submissao - $hoje) / 86400;

        $eventos[] = [
            'title' => $row['titulo'],
            'start' => date('Y-m-d', $data_submissao),
            'color' => $vencido ? '#dc3545' : '#3788d8', // vermelho se atrasado
            'extendedProps' => [
                'projeto_id' => $row['id'],
                'status' => $vencido ? 'Atrasado (prazo vencido)' : $row['estatus'],
                'feedback' => $row['feedback'],
                'vencido' => $vencido,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    echo json_encode($eventos);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao carregar eventos: ' . $e->getMessage()]);
}
ob_end_flush();
