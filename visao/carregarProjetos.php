<?php
session_start();

require_once('../Modelo/VerProjectos.php');

$projectoDAO = new VerProjectos();

// Verificar o tipo de usuário
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

if ($_SESSION['tipo'] === 'Admin') {
    $projectos = $projectoDAO->getTodosProjetos();
} else {
    $projectos = $projectoDAO->getProjetosPorDocente($_SESSION['id']);
}

// Normalizar os IDs para garantir consistência no código
foreach ($projectos as &$p) {
    if (isset($p['Id']) && !isset($p['id'])) {
        $p['id'] = $p['Id'];
    }
}

// Retornar os projetos como JSON
echo json_encode($projectos);
?>
