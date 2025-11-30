<?php
session_start();
header('Content-Type: application/json');

require_once '../modelo/crud.php';

// Verifica se é docente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$docente_id = $_SESSION['id'];
$tipo = $_GET['tipo'] ?? '';

try {
    $conexao = (new crud())->getConexao();
    $lista = [];

    switch ($tipo) {
        case 'em_andamento':
        case 'concluidos':
        case 'atrasados':
        case 'em_falta':
            if ($tipo === 'em_falta') {
                // Projetos sem submissões
                $sql = "
                    SELECT p.titulo AS nome, DATE_FORMAT(p.data, '%d/%m/%Y') AS data
                    FROM projectos p
                    WHERE p.docente_id = :docente_id
                    AND NOT EXISTS (
                        SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id
                    )
                    ORDER BY p.data ASC
                ";
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
                $stmt->execute();
                $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Projetos com submissões filtrados por status
                $statusMap = [
                    'em_andamento' => ['em andamento', 'emandamento'],
                    'concluidos' => ['concluido', 'concluídos'],
                    'atrasados' => ['atrasado', 'atrasados']
                ];
                $sql = "
                    SELECT p.titulo AS nome, DATE_FORMAT(s.data,'%d/%m/%Y') AS data
                    FROM submisoes s
                    INNER JOIN projectos p ON s.Id_projectos = p.id
                    WHERE p.docente_id = :docente_id
                    AND LOWER(TRIM(s.estatus)) IN (" . implode(',', array_fill(0, count($statusMap[$tipo]), '?')) . ")
                    ORDER BY s.data ASC
                ";
                $stmt = $conexao->prepare($sql);
                $params = array_merge([$docente_id], $statusMap[$tipo]);
                $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
                // Bind dos status dinamicamente
                foreach ($statusMap[$tipo] as $k => $st) {
                    $stmt->bindValue($k+1, strtolower($st));
                }
                $stmt->execute();
                $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        case 'total':
            // Todos os projetos do docente (submissões + sem submissões)
            $sql = "
                SELECT p.titulo AS nome, DATE_FORMAT(p.data,'%d/%m/%Y') AS data
                FROM projectos p
                WHERE p.docente_id = :docente_id
                ORDER BY p.data ASC
            ";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
            $stmt->execute();
            $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        default:
            $lista = [];
    }

    echo json_encode($lista);

} catch (PDOException $e) {
    echo json_encode([]);
}
