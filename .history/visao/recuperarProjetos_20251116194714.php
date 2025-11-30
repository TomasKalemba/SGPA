<?php
session_start();

require_once('../Modelo/VerProjectos.php');

$projectoDAO = new VerProjectos();

if ($_SESSION['tipo'] === 'Admin') {
    $projectos = $projectoDAO->getTodosProjetos();
} else {
    $projectos = $projectoDAO->getProjetosPorDocente($_SESSION['id']);
}

// Renderiza a tabela de projetos
foreach ($projectos as $i => $p) {
    echo "<tr>
            <td>".($i+1)."</td>
            <td>".htmlspecialchars($p['titulo'])."</td>
            <td>".htmlspecialchars($p['descricao'])."</td>
            <td>".htmlspecialchars($projectoDAO->getNomesDoGrupo($p['id'] ?? $p['Id'] ?? null))."</td>
            <td>".date('d/m/Y H:i:s', strtotime($p['prazo']))."</td>
            <td>
                ".(!empty($p['arquivo']) ? "<a href='download.php?file=".urlencode($p['arquivo'])."' class='btn btn-success btn-sm'><i class='fas fa-download'></i> Download</a>" : "<span class='text-muted'>Nenhum</span>")."
            </td>
            <td class='d-flex justify-content-center gap-2'>
                <a href='../visao/Editar.php?id=".htmlspecialchars($p['id'])."' class='btn btn-warning btn-sm'>
                  <i class='fas fa-edit'></i>
                </a>
                <a href='../controlo/EliminarProjecto.php?id=".($p['id'] ?? $p['Id'])."' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja eliminar este projeto?\");'>
                  <i class='fas fa-trash-alt'></i>
                </a>
            </td>
          </tr>";
}
?>
