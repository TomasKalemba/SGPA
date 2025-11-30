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

    // Base SQL: pegar projetos e status a partir das submissões
    $sql = "
        SELECT p.id, p.titulo, p.prazo,
        s.estatus
        FROM projectos p
        LEFT JOIN submisoes s ON s.Id_projectos = p.id
        WHERE p.docente_id = :docente_id
    ";

    // Filtro por tipo
    $params = [':docente_id' => $docente_id];
    switch ($tipo) {
        case 'em_andamento':
            $sql .= " AND s.estatus = 'Em Andamento'";
            break;
        case 'concluidos':
            $sql .= " AND s.estatus = 'Concluído'";
            break;
        case 'atrasados':
            $sql .= " AND s.estatus = 'Atrasado'";
            break;
        case 'em_falta':
            $sql .= " AND s.Id_projectos IS NULL"; // projetos sem submissões
            break;
        case 'total':
        default:
            // não filtra
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projetos);

} catch (Exception $e) {
    echo json_encode([]);
}
