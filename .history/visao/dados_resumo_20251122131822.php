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
            TRIM(s.estatus) AS estatus,
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
        $status = strtolower($row['estatus']); // padroniza para lowercase
        $quantidade = (int) $row['total'];

        // Verifica status usando strpos para evitar problemas de acentos ou espaços
        if (strpos($status, 'em andamento') !== false || strpos($status, 'emandamento') !== false) {
            $resumo['emAndamento'] += $quantidade;
        } elseif (strpos($status, 'concluido') !== false) {
            $resumo['concluidos'] += $quantidade;
        } elseif (strpos($status, 'atrasado') !== false) {
            $resumo['atrasados'] += $quantidade;
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
    $emFalta = (int)($stmtFalta->fetch(PDO::FETCH_ASSOC)['emFalta'] ?? 0);

    $resumo['emFalta'] = $emFalta;
    $resumo['total'] += $emFalta;

    echo json_encode($resumo);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar dados: ' . $e->getMessage()]);
}
