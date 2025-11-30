<?php
session_start();
header('Content-Type: application/json');

// Verifica se é um docente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

$docente_id = $_SESSION['id'];

require_once '../modelo/crud.php';

try {
    $conexao = (new crud())->getConexao();

    // Inicializa os contadores
    $resumo = [
        'emAndamento' => 0,
        'concluidos' => 0,
        'atrasados' => 0,
        'emFalta' => 0,
        'total' => 0
    ];

    // 1. Conta os projetos por status a partir das submissões
    $sql = "
        SELECT 
            LOWER(TRIM(s.estatus)) AS estatus,
            COUNT(*) AS total
        FROM submisoes s
        INNER JOIN projectos p ON s.Id_projectos = p.id
        WHERE p.docente_id = :docente_id
        GROUP BY s.estatus
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['estatus'];
        $quantidade = (int) $row['total'];

        switch ($status) {
            case 'emandamento':
            case 'em andamento':
                $resumo['emAndamento'] += $quantidade;
                break;
            case 'concluido':
                $resumo['concluidos'] += $quantidade;
                break;
            case 'atrasado':
                $resumo['atrasados'] += $quantidade;
                break;
        }

        $resumo['total'] += $quantidade;
    }

    // 2. Conta os projetos criados pelo docente que ainda não têm submissões (Em Falta)
    $sqlFalta = "
        SELECT COUNT(*) AS emFalta
        FROM projectos p
        WHERE p.docente_id = :docente_id
        AND NOT EXISTS (
            SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id
        )
    ";
    $stmtFalta = $conexao->prepare($sqlFalta);
    $stmtFalta->bindParam(':docente_id', $docente_id, PDO::PARAM_INT);
    $stmtFalta->execute();
    $emFalta = $stmtFalta->fetch(PDO::FETCH_ASSOC)['emFalta'] ?? 0;

    $resumo['emFalta'] = (int) $emFalta;
    $resumo['total'] += (int) $emFalta; // total agora inclui também os em falta

    echo json_encode($resumo);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar dados: ' . $e->getMessage()]);
}
