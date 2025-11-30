<?php
require_once("../modelo/conexao.php");

try {
    // Total de projetos cadastrados
    $stmtTotalProjetos = $pdo->query("SELECT COUNT(*) AS total FROM projectos");
    $totalProjetos = $stmtTotalProjetos->fetch(PDO::FETCH_ASSOC)['total'];

    // Projetos em andamento
    $stmtEmAndamento = $pdo->query("SELECT COUNT(*) AS total FROM submisoes WHERE estatus = 'emAndamento'");
    $totalEmAndamento = $stmtEmAndamento->fetch(PDO::FETCH_ASSOC)['total'];

    // Projetos concluídos
    $stmtConcluidos = $pdo->query("SELECT COUNT(*) AS total FROM submisoes WHERE estatus = 'concluido'");
    $totalConcluidos = $stmtConcluidos->fetch(PDO::FETCH_ASSOC)['total'];

    // Projetos atrasados
    $stmtAtrasados = $pdo->query("SELECT COUNT(*) AS total FROM submisoes WHERE estatus = 'atrasado'");
    $totalAtrasados = $stmtAtrasados->fetch(PDO::FETCH_ASSOC)['total'];

    $dados = [
        "totalProjetos" => $totalProjetos,
        "emAndamento" => $totalEmAndamento,
        "concluidos" => $totalConcluidos,
        "atrasados" => $totalAtrasados
    ];

    header('Content-Type: application/json');
    echo json_encode($dados);
} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}