<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$tipo = $_GET['tipo'] ?? '';
$docente_id = $_SESSION['id'];

require_once '../modelo/crud.php';
$conn = (new crud())->getConexao();

$lista = [];

try {
    switch($tipo) {
        case 'em_andamento':
            $sql = "SELECT p.titulo AS nome, DATE_FORMAT(s.data,'%d/%m/%Y') AS data
                    FROM projectos p
                    INNER JOIN submissoes s ON s.id_projecto = p.id
                    WHERE p.docente_id = :docente_id
                    AND LOWER(TRIM(s.status)) IN ('em andamento','emandamento')
                    ORDER BY s.data ASC";
            break;
        case 'concluidos':
            $sql = "SELECT p.titulo AS nome, DATE_FORMAT(s.data,'%d/%m/%Y') AS data
                    FROM projectos p
                    INNER JOIN submissoes s ON s.id_projecto = p.id
                    WHERE p.docente_id = :docente_id
                    AND LOWER(TRIM(s.status))='concluido'
                    ORDER BY s.data ASC";
            break;
        case 'atrasados':
            $sql = "SELECT p.titulo AS nome, DATE_FORMAT(s.data,'%d/%m/%Y') AS data
                    FROM projectos p
                    INNER JOIN submissoes s ON s.id_projecto = p.id
                    WHERE p.docente_id = :docente_id
                    AND LOWER(TRIM(s.status))='atrasado'
                    ORDER BY s.data ASC";
            break;
        case 'em_falta':
            $sql = "SELECT p.titulo AS nome, DATE_FORMAT(p.data,'%d/%m/%Y') AS data
                    FROM projectos p
                    WHERE p.docente_id = :docente_id
                    AND NOT EXISTS (
                        SELECT 1 FROM submissoes s WHERE s.id_projecto = p.id
                    )
                    ORDER BY p.data ASC";
            break;
        case 'total':
            $sql = "SELECT p.titulo AS nome, DATE_FORMAT(p.data,'%d/%m/%Y') AS data
                    FROM projectos p
                    WHERE p.docente_id = :docente_id
                    ORDER BY p.data ASC";
            break;
        default:
            $sql = '';
    }

    if ($sql) {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
        $stmt->execute();
        $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $lista = [];
}

echo json_encode($lista);
