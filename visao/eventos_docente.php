<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

$docente_id = $_SESSION['id'];

require_once '../modelo/crud.php';

try {
    $conexao = (new crud())->getConexao();
    $eventos = [];

    // 🔹 1. Projetos COM submissões (status normal)
    $sqlSubmetidos = "
        SELECT 
            p.titulo,
            s.data_submissao,
            s.estatus,
            s.feedback
        FROM submisoes s
        INNER JOIN projectos p ON s.Id_projectos = p.id
        WHERE p.docente_id = :docente_id
    ";
    $stmt = $conexao->prepare($sqlSubmetidos);
    $stmt->bindParam(':docente_id', $docente_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_submissao = strtotime($row['data_submissao']);
        $hoje = strtotime(date('Y-m-d'));

        $diasRestantes = floor(($data_submissao - $hoje) / (60 * 60 * 24));
        $vencido = $diasRestantes < 0 && strtolower($row['estatus']) !== 'concluido';
        $alerta = $diasRestantes >= 0 && $diasRestantes <= 2 && strtolower($row['estatus']) !== 'concluido';

        // Cor por status
        if ($vencido) {
            $cor = '#dc3545'; // Vermelho
        } elseif (strtolower($row['estatus']) === 'concluido') {
            $cor = '#198754'; // Verde
        } elseif ($alerta) {
            $cor = '#fd7e14'; // Laranja
        } elseif (strtolower($row['estatus']) === 'emandamento') {
            $cor = '#ffc107'; // Amarelo
        } else {
            $cor = '#0d6efd'; // Azul padrão
        }

        $eventos[] = [
            'title' => $row['titulo'],
            'start' => date('Y-m-d', $data_submissao),
            'color' => $cor,
            'extendedProps' => [
                'status' => $row['estatus'],
                'feedback' => $row['feedback'],
                'vencido' => $vencido,
                'alerta' => $alerta,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    // 🔹 2. Projetos SEM submissões (Em Falta)
    $sqlEmFalta = "
        SELECT 
            p.titulo,
            p.prazo_entrega AS data_submissao
        FROM projectos p
        WHERE p.docente_id = :docente_id
        AND NOT EXISTS (
            SELECT 1 FROM submisoes s WHERE s.Id_projectos = p.id
        )
    ";
    $stmtFalta = $conexao->prepare($sqlEmFalta);
    $stmtFalta->bindParam(':docente_id', $docente_id);
    $stmtFalta->execute();

    while ($row = $stmtFalta->fetch(PDO::FETCH_ASSOC)) {
        $data_submissao = strtotime($row['data_submissao']);
        $hoje = strtotime(date('Y-m-d'));
        $diasRestantes = floor(($data_submissao - $hoje) / (60 * 60 * 24));
        $vencido = $diasRestantes < 0;

        $eventos[] = [
            'title' => $row['titulo'] . ' (não submetido)',
            'start' => date('Y-m-d', $data_submissao),
            'color' => '#6c757d', // Cinza escuro
            'extendedProps' => [
                'status' => 'Não Submetido',
                'feedback' => '',
                'vencido' => $vencido,
                'alerta' => !$vencido && $diasRestantes <= 2,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    echo json_encode($eventos);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar eventos: ' . $e->getMessage()]);
}
