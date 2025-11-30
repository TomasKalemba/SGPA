<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();
    $docente_id = $_SESSION['id'];

    $tipo = $_GET['tipo'] ?? '';

    $query = "SELECT titulo, prazo, estatus FROM projectos WHERE docente_id = :docente_id";
    switch ($tipo) {
        case 'em_andamento':
            $query .= " AND estatus = 'Em Andamento'";
            break;
        case 'concluidos':
            $query .= " AND estatus = 'Concluído'";
            break;
        case 'atrasados':
            $query .= " AND estatus = 'Atrasado'";
            break;
        case 'em_falta':
            $query .= " AND estatus = 'Em Falta'";
            break;
        case 'total':
        default:
            // não filtra
            break;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sempre retornar um array
    echo json_encode($projetos ?: []);
} catch (Exception $e) {
    echo json_encode([]);
}
