<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();
    $estudante_id = $_SESSION['id'];

    $tipo = $_GET['tipo'] ?? '';

    $sql = "
        SELECT p.titulo, p.prazo, s.estatus
        FROM projectos p
        LEFT JOIN submisoes s ON s.Id_projectos = p.id AND s.estudante_id = :estudante_id
        WHERE p.docente_id = p.docente_id
    ";

    switch($tipo){
        case 'emandamento': $sql .= " AND s.estatus = 'EmAndamento'"; break;
        case 'concluídos': $sql .= " AND s.estatus = 'Concluído'"; break;
        case 'atrasados': $sql .= " AND s.estatus = 'Atrasado'"; break;
        case 'em_falta': $sql .= " AND s.Id_projectos IS NULL"; break;
        case 'total': default: break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estudante_id'=>$estudante_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projetos);

} catch(Exception $e){
    echo json_encode([]);
}
