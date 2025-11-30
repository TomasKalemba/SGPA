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

    $sql = "
        SELECT p.id, p.titulo, p.prazo,
        CASE
            WHEN NOT EXISTS (SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id) THEN 'Em Falta'
            WHEN EXISTS (SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id AND s.estatus = 'concluido') THEN 'Concluído'
            WHEN p.prazo < CURDATE() THEN 'Atrasado'
            ELSE 'Em Andamento'
        END AS status
        FROM projectos p
        WHERE p.docente_id = :docente_id
    ";

    // Filtro por tipo
    $params = [':docente_id' => $docente_id];
    if ($tipo === 'em_andamento') $sql .= " AND p.prazo >= CURDATE()";
    if ($tipo === 'atrasados') $sql .= " AND p.prazo < CURDATE()";
    if ($tipo === 'em_falta') $sql .= " AND NOT EXISTS (SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id)";
    if ($tipo === 'concluidos') $sql .= " AND EXISTS (SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id AND s.estatus = 'concluido')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projetos);

} catch (Exception $e) {
    echo json_encode([]);
}
