<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode([]);
    exit;
}

$estudante_id = $_SESSION['id'];
$tipo = $_GET['tipo'] ?? '';
require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();
    $hoje = date('Y-m-d');

    $sql = "
        SELECT p.Id, p.titulo, p.prazo, s.Id AS submissao
        FROM projectos p
        LEFT JOIN submisoes s ON p.Id = s.projeto_id AND s.estudante_id = :estudante_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estudante_id' => $estudante_id]);
    $projetos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = '';
        if ($row['submissao']) {
            $status = 'Concluído';
        } else {
            if ($row['prazo'] >= $hoje) $status = 'Em Andamento';
            else $status = 'Atrasado';
        }

        if ($tipo === 'em_andamento' && $status !== 'Em Andamento') continue;
        if ($tipo === 'concluidos' && $status !== 'Concluído') continue;
        if ($tipo === 'atrasados' && $status !== 'Atrasado') continue;
        if ($tipo === 'em_falta' && $status === 'Concluído') continue;

        $projetos[] = [
            'titulo' => $row['titulo'],
            'prazo' => $row['prazo'],
            'status' => $status
        ];
    }

    echo json_encode($projetos);
} catch (PDOException $e) {
    echo json_encode([]);
}
