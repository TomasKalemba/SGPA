<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

$estudante_id = $_SESSION['id'];
require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();

    // Inicializa os contadores
    $resumo = [
        'emAndamento' => 0,
        'concluidos' => 0,
        'atrasados' => 0,
        'emFalta' => 0,
        'totalProjetos' => 0
    ];

    // Pega todos os projetos do estudante
    $sql = "
        SELECT p.Id, p.titulo, p.prazo, s.Id AS submissao
        FROM projectos p
        LEFT JOIN submisoes s ON p.Id = s.projeto_id AND s.estudante_id = :estudante_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estudante_id' => $estudante_id]);
    $hoje = date('Y-m-d');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resumo['totalProjetos']++;

        if ($row['submissao']) {
            $resumo['concluidos']++;
        } else {
            if ($row['prazo'] >= $hoje) {
                $resumo['emAndamento']++;
            } else {
                $resumo['atrasados']++;
            }
        }
    }

    // Em Falta = projetos não submetidos
    $resumo['emFalta'] = $resumo['emAndamento'] + $resumo['atrasados'];

    echo json_encode($resumo);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar dados: ' . $e->getMessage()]);
}
