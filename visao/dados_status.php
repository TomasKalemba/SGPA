<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

$docente_id = $_SESSION['id'];
$conn = new mysqli("localhost", "root", "", "sgpa");

if ($conn->connect_error) {
    die(json_encode(['erro' => 'Erro na conexão']));
}

// Total de projetos enviados pelo docente
$sql_total = "SELECT COUNT(*) FROM projectos WHERE docente_id = ?";
$stmt = $conn->prepare($sql_total);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

// Submissões concluídas
$sql_concluidos = "
    SELECT COUNT(*) FROM submisoes s
    INNER JOIN grupo g ON s.grupo_id = g.id
    INNER JOIN projectos p ON g.projeto_id = p.Id
    WHERE s.estatus = 'concluido' AND p.docente_id = ?
";
$stmt = $conn->prepare($sql_concluidos);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$stmt->bind_result($concluidos);
$stmt->fetch();
$stmt->close();

// Submissões atrasadas
$sql_atrasados = "
    SELECT COUNT(*) FROM submisoes s
    INNER JOIN grupo g ON s.grupo_id = g.id
    INNER JOIN projectos p ON g.projeto_id = p.Id
    WHERE s.estatus = 'atrasado' AND p.docente_id = ?
";
$stmt = $conn->prepare($sql_atrasados);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$stmt->bind_result($atrasados);
$stmt->fetch();
$stmt->close();

// Retorna os dados
echo json_encode([
    'total' => $total,
    'concluidos' => $concluidos,
    'atrasados' => $atrasados
]);

$conn->close();
?>
