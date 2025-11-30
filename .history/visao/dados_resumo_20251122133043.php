<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([
        'em_andamento' => 0,
        'concluidos' => 0,
        'atrasados' => 0,
        'em_falta' => 0,
        'total' => 0
    ]);
    exit;
}

$docenteId = $_SESSION['id'];

// Conexão com o banco
$mysqli = new mysqli("localhost", "usuario", "senha", "nome_do_banco");
if ($mysqli->connect_errno) {
    echo json_encode([
        'em_andamento' => 0,
        'concluidos' => 0,
        'atrasados' => 0,
        'em_falta' => 0,
        'total' => 0
    ]);
    exit;
}

// Inicializa indicadores
$em_andamento = 0;
$concluidos = 0;
$atrasados = 0;
$em_falta = 0;
$total = 0;

// Exemplo de consulta (ajuste para a sua tabela)
$sql = "SELECT status, COUNT(*) as qtd FROM projetos WHERE docente_id = ? GROUP BY status";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $docenteId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $total += $row['qtd'];
    switch ($row['status']) {
        case 'Em Andamento': $em_andamento = $row['qtd']; break;
        case 'Concluido': $concluidos = $row['qtd']; break;
        case 'Atrasado': $atrasados = $row['qtd']; break;
        case 'Em Falta': $em_falta = $row['qtd']; break;
    }
}

echo json_encode([
    'em_andamento' => $em_andamento,
    'concluidos' => $concluidos,
    'atrasados' => $atrasados,
    'em_falta' => $em_falta,
    'total' => $total
]);
