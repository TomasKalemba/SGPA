<?php
require_once('../Modelo/VerProjectos.php');

$projectoDAO = new VerProjectos();
$projectos = $projectoDAO->getTodosProjetos(); // Obtém todos os projetos

foreach ($projectos as $p) {
    echo "<tr>
            <td>{$p['id']}</td>
            <td>{$p['titulo']}</td>
            <td>{$p['descricao']}</td>
            <td>{$projectoDAO->getNomesDoGrupo($p['id'])}</td>
            <td>".date('d/m/Y H:i:s', strtotime($p['prazo']))."</td>
            <td>";
    if (!empty($p['arquivo'])) {
        echo "<a href='download.php?file={$p['arquivo']}' class='btn btn-success btn-sm'><i class='fas fa-download'></i> Download</a>";
    } else {
        echo "<span class='text-muted'>Nenhum</span>";
    }
    echo "</td>
            <td>
                <a href='Editar.php?id={$p['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                <a href='EliminarProjecto.php?id={$p['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja eliminar este projeto?\");'>
                    <i class='fas fa-trash-alt'></i>
                </a>
            </td>
        </tr>";
}
?>
