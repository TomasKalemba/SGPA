<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

header('Content-Type: application/json');
include_once('../modelo/conexao.php');

$idDocente = $_SESSION['id'];
$tipo = $_GET['tipo'] ?? 'total';

$where = '';
switch($tipo) {
    case 'em_andamento':
        $where = "AND status = 'Em Andamento'";
        break;
    case 'concluidos':
        $where = "AND status = 'Concluído'";
        break;
    case 'atrasados':
        $where = "AND status = 'Atrasado'";
        break;
    case 'em_falta':
        $where = "AND status = 'Em Falta'";
        break;
    case 'total':
    default:
        $where = "";
        break;
}

try {
    $sql = "SELECT titulo, status, DATE_FORMAT(data_entrega,'%d/%m/%Y') AS data 
            FROM projetos 
            WHERE docente_id = :idDocente $where 
            ORDER BY data_entrega ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':idDocente', $idDocente, PDO::PARAM_INT);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projetos);

} catch (PDOException $e) {
    echo json_encode([]);
}
