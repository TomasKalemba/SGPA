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

    // Query base: seleciona todos os projetos do docente
    $query = "
        SELECT 
            p.id,
            p.titulo,
            p.prazo,
            COALESCE(
                (SELECT LOWER(TRIM(s.estatus)) 
                 FROM submisoes s 
                 WHERE s.Id_projectos = p.id 
                 ORDER BY s.Id DESC LIMIT 1),
                'em falta'
            ) AS estatus
        FROM projectos p
        WHERE p.docente_id = :docente_id
    ";

    // Filtrar por tipo
    switch ($tipo) {
        case 'em_andamento':
            $query .= " AND COALESCE(
                            (SELECT LOWER(TRIM(s.estatus)) 
                             FROM submisoes s 
                             WHERE s.Id_projectos = p.id 
                             ORDER BY s.Id DESC LIMIT 1),
                            'em falta'
                        ) = 'em andamento'";
            break;
        case 'concluidos':
            $query .= " AND COALESCE(
                            (SELECT LOWER(TRIM(s.estatus)) 
                             FROM submisoes s 
                             WHERE s.Id_projectos = p.id 
                             ORDER BY s.Id DESC LIMIT 1),
                            'em falta'
                        ) = 'concluido'";
            break;
        case 'atrasados':
            $query .= " AND COALESCE(
                            (SELECT LOWER(TRIM(s.estatus)) 
                             FROM submisoes s 
                             WHERE s.Id_projectos = p.id 
                             ORDER BY s.Id DESC LIMIT 1),
                            'em falta'
                        ) = 'atrasado'";
            break;
        case 'em_falta':
            $query .= " AND NOT EXISTS (
                            SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id
                        )";
            break;
        case 'total':
        default:
            // não filtra
            break;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projetos ?: []);
} catch (Exception $e) {
    echo json_encode([]);
}
