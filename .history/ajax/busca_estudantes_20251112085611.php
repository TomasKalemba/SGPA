<?php
require_once("../modelo/crud.php");
session_start();

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';
$docente_id = $_SESSION['id']; // docente logado
$projeto_id = $_POST['projeto_id'] ?? null;

// Busca departamento do docente
$stmtDepto = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ?");
$stmtDepto->execute([$docente_id]);
$departamento_id = $stmtDepto->fetchColumn();

if (!$departamento_id) {
    echo json_encode([]);
    exit;
}

// IDs de estudantes já vinculados ao projeto
$ids_vinculados = [];
if ($projeto_id) {
    $stmtGrupo = $conn->prepare("
        SELECT ge.estudante_id
        FROM grupo g
        JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE g.projeto_id = ?
    ");
    $stmtGrupo->execute([$projeto_id]);
    $ids_vinculados = $stmtGrupo->fetchAll(PDO::FETCH_COLUMN);
}

// Busca estudantes do mesmo departamento cujo nome corresponde ao termo
$stmt = $conn->prepare("
    SELECT id, nome AS text 
    FROM usuarios 
    WHERE tipo = 'Estudante' 
      AND departamento_id = ? 
      AND (nome LIKE ? OR id IN (" . (count($ids_vinculados) ? implode(',', $ids_vinculados) : '0') . "))
    ORDER BY nome ASC
    LIMIT 20
");
$stmt->execute([$departamento_id, "%$termo%"]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);

