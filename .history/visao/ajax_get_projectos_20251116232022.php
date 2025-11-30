<?php
session_start();

require_once('../Modelo/VerProjectos.php');

$projectoDAO = new VerProjectos();

if ($_SESSION['tipo'] === 'Admin') {
    $projectos = $projectoDAO->getTodosProjetos();
} else {
    $projectos = $projectoDAO->getProjetosPorDocente($_SESSION['id']);
}

// Normalizar os IDs para garantir a consistência
foreach ($projectos as &$p) {
    if (isset($p['Id']) && !isset($p['id'])) {
        $p['id'] = $p['Id'];
    }
}

echo json_encode($projectos);  // Retorna os dados em formato JSON
?>
