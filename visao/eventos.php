<?php
session_start();
header('Content-Type: application/json');

require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode(['erro' => 'Usuário não autorizado']);
    exit;
}

$estudante_id = $_SESSION['id'];

try {
    $conexao = (new crud())->getConexao();

    $sql = "
        SELECT 
            p.titulo, 
            s.data_submissao, 
            s.estatus, 
            s.feedback 
        FROM submisoes s
        INNER JOIN projectos p ON s.Id_projectos = p.id
        WHERE s.estudante_id = :estudante_id
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudante_id);
    $stmt->execute();

    $eventos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_submissao = strtotime($row['data_submissao']);
        $hoje = strtotime(date('Y-m-d'));

        $diasRestantes = floor(($data_submissao - $hoje) / (60 * 60 * 24));
        $vencido = $diasRestantes < 0 && strtolower($row['estatus']) !== 'concluido';
        $alerta = $diasRestantes >= 0 && $diasRestantes <= 2 && strtolower($row['estatus']) !== 'concluido';

        // Define cor com base no status
        if ($vencido) {
            $cor = '#dc3545'; // Vermelho
        } elseif (strtolower($row['estatus']) === 'concluido') {
            $cor = '#198754'; // Verde
        } elseif ($alerta) {
            $cor = '#fd7e14'; // Laranja
        } elseif (strtolower($row['estatus']) === 'emandamento' || strtolower($row['estatus']) === 'em andamento') {
            $cor = '#ffc107'; // Amarelo
        } else {
            $cor = '#0d6efd'; // Azul padrão
        }

        $eventos[] = [
            'title' => $row['titulo'],
            'start' => date('Y-m-d', $data_submissao),
            'color' => $cor,
            'extendedProps' => [
                'status' => $vencido ? 'Atrasado (prazo vencido)' : $row['estatus'],
                'feedback' => $row['feedback'],
                'vencido' => $vencido,
                'alerta' => $alerta,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    echo json_encode($eventos);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao consultar eventos: ' . $e->getMessage()]);
}
