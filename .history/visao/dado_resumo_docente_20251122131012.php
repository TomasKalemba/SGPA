<?php
session_start();
require_once '../modelo/crud.php';

$crud = new crud();
$conn = $crud->getConexao();

$idDocente = $_SESSION['id'];

$sql = "SELECT
    SUM(CASE WHEN status='em_andamento' THEN 1 ELSE 0 END) AS em_andamento,
    SUM(CASE WHEN status='concluidos' THEN 1 ELSE 0 END) AS concluidos,
    SUM(CASE WHEN status='atrasados' THEN 1 ELSE 0 END) AS atrasados,
    SUM(CASE WHEN status='em_falta' THEN 1 ELSE 0 END) AS em_falta,
    COUNT(*) AS total
FROM projetos
WHERE docente_id=:docente_id";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':docente_id', $idDocente);
$stmt->execute();
$res = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($res);
