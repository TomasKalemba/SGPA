<?php
session_start();
require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$tipo = $_GET['tipo'] ?? '';
$docente_id = $_SESSION['id'];

$crud = new crud();
$conn = $crud->getConexao();

$lista = [];

try {
    switch($tipo) {
        case 'em_andamento':
        case 'concluidos':
        case 'atrasados':
        case 'em_falta':
            $sql = "SELECT titulo AS nome, DATE_FORMAT(data,'%d/%m/%Y') AS data 
                    FROM projetos 
                    WHERE status=:status AND docente_id=:docente_id 
                    ORDER BY data ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':status', $tipo);
            $stmt->bindValue(':docente_id', $docente_id);
            $stmt->execute();
            $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'total':
            $sql = "SELECT titulo AS nome, DATE_FORMAT(data,'%d/%m/%Y') AS data 
                    FROM projetos 
                    WHERE docente_id=:docente_id 
                    ORDER BY data ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':docente_id', $docente_id);
            $stmt->execute();
            $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            $lista = [];
    }
} catch (PDOException $e) {
    $lista = [];
}

header('Content-Type: application/json');
echo json_encode($lista);
