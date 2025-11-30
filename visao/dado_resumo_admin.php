<?php
session_start();
header('Content-Type: application/json');

require_once("../modelo/crud.php");
$crud = new crud();
$con = $crud->getConexao();

try {
    // Filtrar por período se enviado
    $filtroDataProjectos = '';
    $filtroDataSubmisoes = '';
    if (isset($_GET['periodo']) && in_array($_GET['periodo'], ['7','30'])) {
        $dias = (int)$_GET['periodo'];
        $filtroDataProjectos = "WHERE data_criacao >= DATE_SUB(NOW(), INTERVAL $dias DAY)";
        $filtroDataSubmissoes = "WHERE data_submisao >= DATE_SUB(NOW(), INTERVAL $dias DAY)";
    }

    // Contar Projetos Docentes
    $sqlProjectos = "SELECT COUNT(*) AS total FROM projectos $filtroDataProjectos";
    $totalProjectos = $con->query($sqlProjectos)->fetchColumn();

    // Contar Submissões Estudantes
    $sqlSubmisoes = "SELECT COUNT(*) AS total FROM submisoes $filtroDataSubmisoes";
    $totalSubmisoes = $con->query($sqlSubmisoes)->fetchColumn();

    // Total geral
    $totalGeral = $totalProjectos + $totalSubmisoes;

    echo json_encode([
        'totalProjetos' => (int)$totalProjectos,
        'totalSubmissoes' => (int)$totalSubmisoes,
        'totalGeral' => (int)$totalGeral
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
