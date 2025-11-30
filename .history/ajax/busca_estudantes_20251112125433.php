<?php
require_once("../modelo/crud.php");
session_start();

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';
$projeto_id = $_POST['projeto_id'] ?? null;
$tipo = $_SESSION['tipo']; 
$docente_id = $_SESSION['id'];

// Se for edição, pegar IDs já vinculados
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

// Consultas diferentes para Admin e Docente
if ($tipo === "Admin") {
    // Admin vê TODOS os estudantes
    $sql = "
        SELECT id, nome AS text 
        FROM usuarios 
        WHERE tipo = 'Estudante'
          AND nome LIKE ?
    ";
    $params = ["%$termo%"];

} else {
    // Docente vê apenas estudantes do seu departamento
    $stmtDepto = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ?");
    $stmtDepto->execute([$docente_id]);
    $departamento_id = $stmtDepto->fetchColumn();

    if (!$departamento_id) {
        echo json_encode([]);
        exit;
    }

    $sql = "
        SELECT id, nome AS text 
        FROM usuarios 
        WHERE tipo = 'Estudante'
          AND departamento_id = ?
          AND nome LIKE ?
    ";
    $params = [$departamento_id, "%$termo%"];
}

// Excluir estudantes já vinculados ao projeto
if (!empty($excluir_ids)) {
    $sql .= " AND id NOT IN (" . implode(',', array_map('intval', $excluir_ids)) . ")";
}

$sql .= " ORDER BY nome ASC LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($resultados);
