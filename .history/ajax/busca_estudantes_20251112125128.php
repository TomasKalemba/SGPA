<?php
require_once("../modelo/crud.php");
session_start();

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';
$docente_id = $_SESSION['id']; 
$projeto_id = $_POST['projeto_id'] ?? null;

// Busca departamento do docente
$stmtDepto = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ?");
$stmtDepto->execute([$docente_id]);
$departamento_id = $stmtDepto->fetchColumn();

if (!$departamento_id) {
    echo json_encode([]);
    exit;
}

// Se for edição, exclui os já vinculados
$excluir_ids = [];
if ($projeto_id) {
    $stmt = $conn->prepare("
        SELECT ge.estudante_id 
        FROM grupo_estudante ge
        JOIN grupo g ON g.id = ge.grupo_id
        WHERE g.projeto_id = ?
    ");
    $stmt->execute([$projeto_id]);
    $excluir_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$sql = "
    SELECT id, nome AS text 
    FROM usuarios 
    WHERE tipo = 'Estudante' 
      AND departamento_id = ?
      AND nome LIKE ?
";

// Excluir estudantes já no projeto
if (!empty($excluir_ids)) {
    $sql .= " AND id NOT IN (" . implode(',', array_map('intval', $excluir_ids)) . ")";
}

$sql .= " ORDER BY nome ASC LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->execute([$departamento_id, "%$termo%"]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);

