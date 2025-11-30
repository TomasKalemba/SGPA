<?php
session_start();
require_once("../modelo/crud.php");

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';

if (!empty($termo)) {
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE tipo = 'Estudante' AND nome LIKE ? LIMIT 20");
    $stmt->execute(['%' . $termo . '%']);
    $estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultados = [];
    foreach ($estudantes as $est) {
        $resultados[] = [
            'id' => $est['id'],
            'text' => $est['nome']
        ];
    }

    echo json_encode($resultados);
} else {
    echo json_encode([]);
}
