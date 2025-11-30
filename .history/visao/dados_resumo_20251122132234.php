<?php
session_start();
header('Content-Type: application/json');
require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode(['erro'=>'Acesso não autorizado']);
    exit;
}

$docente_id = $_SESSION['id'];

try {
    $conexao = (new crud())->getConexao();

    $resumo = [
        'emAndamento'=>0,
        'concluidos'=>0,
        'atrasados'=>0,
        'emFalta'=>0,
        'total'=>0
    ];

    // 1. Projetos com submissões
    $sql = "SELECT LOWER(TRIM(s.estatus)) AS estatus, COUNT(*) AS total
            FROM submisoes s
            INNER JOIN projectos p ON s.Id_projectos = p.id
            WHERE p.docente_id = :docente_id
            GROUP BY LOWER(TRIM(s.estatus))";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':docente_id',$docente_id,PDO::PARAM_INT);
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $status = $row['estatus'];
        $quant = (int)$row['total'];
        if($status=='em andamento'||$status=='emandamento') $resumo['emAndamento'] += $quant;
        if($status=='concluido'||$status=='concluídos') $resumo['concluidos'] += $quant;
        if($status=='atrasado'||$status=='atrasados') $resumo['atrasados'] += $quant;
        $resumo['total'] += $quant;
    }

    // 2. Projetos sem submissões (Em Falta)
    $sqlF = "SELECT COUNT(*) AS emFalta FROM projectos p
             WHERE p.docente_id=:docente_id
             AND NOT EXISTS (SELECT 1 FROM submisoes s WHERE s.Id_projectos=p.id)";
    $stmtF = $conexao->prepare($sqlF);
    $stmtF->bindParam(':docente_id',$docente_id,PDO::PARAM_INT);
    $stmtF->execute();
    $resumo['emFalta'] = (int)($stmtF->fetch(PDO::FETCH_ASSOC)['emFalta']??0);
    $resumo['total'] += $resumo['emFalta'];

    echo json_encode($resumo);

}catch(PDOException $e){
    echo json_encode(['erro'=>'Erro: '.$e->getMessage()]);
}
