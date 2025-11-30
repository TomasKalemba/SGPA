<?php
session_start();

// Protege a página (somente Docente)
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../modelo/conexao.php'); // ajuste conforme seu arquivo de conexão

$idDocente = $_SESSION['id'];

// Inicializa contadores
$em_andamento = 0;
$concluidos   = 0;
$atrasados    = 0;
$em_falta     = 0;
$total        = 0;

try {
    // Consulta para contar projetos do docente
    $sql = "SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'Em Andamento' THEN 1 ELSE 0 END) AS em_andamento,
                SUM(CASE WHEN status = 'Concluído' THEN 1 ELSE 0 END) AS concluidos,
                SUM(CASE WHEN status = 'Atrasado' THEN 1 ELSE 0 END) AS atrasados,
                SUM(CASE WHEN status = 'Em Falta' THEN 1 ELSE 0 END) AS em_falta
            FROM projetos
            WHERE docente_id = :idDocente";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':idDocente', $idDocente, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $em_andamento = (int)$row['em_andamento'];
        $concluidos   = (int)$row['concluidos'];
        $atrasados    = (int)$row['atrasados'];
        $em_falta     = (int)$row['em_falta'];
        $total        = (int)$row['total'];
    }

    echo json_encode([
        'em_andamento' => $em_andamento,
        'concluidos'   => $concluidos,
        'atrasados'    => $atrasados,
        'em_falta'     => $em_falta,
        'total'        => $total
    ]);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao consultar o banco: '.$e->getMessage()]);
}
