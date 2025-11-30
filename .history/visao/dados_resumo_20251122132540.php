<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

$docente_id = $_SESSION['id'];

require_once '../modelo/crud.php';

try {
    $conexao = (new crud())->getConexao();

    // Inicializa contadores
    $resumo = [
        'em_andamento' => 0,
        'concluidos'   => 0,
        'atrasados'    => 0,
        'em_falta'     => 0,
        'total'        => 0
    ];

    // 1️⃣ Conta projetos com submissões por status
    $sql = "
        SELECT LOWER(TRIM(s.status)) AS status, COUNT(*) AS total
        FROM submissoes s
        INNER JOIN projectos p ON s.id_projecto = p.id
        WHERE p.docente_id = :docente_id
        GROUP BY LOWER(TRIM(s.status))
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'];
        $quant = (int)$row['total'];

        if (in_array($status, ['em andamento', 'emandamento'])) $resumo['em_andamento'] += $quant;
        elseif ($status === 'concluido') $resumo['concluidos'] += $quant;
        elseif ($status === 'atrasado') $resumo['atrasados'] += $quant;

        $resumo['total'] += $quant;
    }

    // 2️⃣ Projetos do docente sem submissões (Em Falta)
    $sqlFalta = "
        SELECT COUNT(*) AS em_falta
        FROM projectos p
        WHERE p.docente_id = :docente_id
        AND NOT EXISTS (
            SELECT 1 FROM submissoes s WHERE s.id_projecto = p.id
        )
    ";
    $stmtFalta = $conexao->prepare($sqlFalta);
    $stmtFalta->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
    $stmtFalta->execute();
    $resumo['em_falta'] = (int)($stmtFalta->fetch(PDO::FETCH_ASSOC)['em_falta'] ?? 0);
    $resumo['total'] += $resumo['em_falta'];

    echo json_encode($resumo);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar dados: ' . $e->getMessage()]);
}
