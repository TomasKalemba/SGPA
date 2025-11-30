<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/conexao.php'; // ajuste conforme necessário

$docente_id = $_SESSION['id'];

try {
    $pdo = conectar();

    $stmt = $pdo->prepare("
        SELECT id, titulo, prazo
        FROM projectos
        WHERE docente_id = :docente_id
    ");
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $eventos[] = [
    'title' => $row['titulo'],
    'start' => date('Y-m-d', $data_submissao),
    'color' => $cor,
    'extendedProps' => [
        'projeto_id' => $row['Id_projectos'], // 🔹 Adiciona o ID do projeto
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
