<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../modelo/VerProjectos.php');
require_once('../modelo/submissoes.php');

$projectoDAO = new VerProjectos();
$submissoesDAO = new submissoes();

// Admin
if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesDAO->getSubmissoesParaAdmin();
    $projectos = $projectoDAO->getTodosProjetos();

// Docente
} else {
    include_once('head/headDocente.php');
    $submissoes = $submissoesDAO->getSubmissoesParaDocente($_SESSION['id']);
    $projectos = $projectoDAO->getProjetosPorDocente($_SESSION['id']);
}

// Normalizar id
foreach ($projectos as &$p) {
    if (isset($p['Id']) && !isset($p['id'])) {
        $p['id'] = $p['Id'];
    }
}
?>

<div id="layoutSidenav_content">
<main class="container py-4">
<div class="card shadow">

    <div class="card-header bg-primary text-white text-center">
        <h5 class="mb-0">Projetos</h5>
    </div>

    <div class="card-body">

        <!-- Mensagens -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mensagem']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <!-- Abas -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#lista">Lista de Projetos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submetidos">Projetos Submetidos</button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- LISTA DE PROJETOS -->
            <div class="tab-pane fade show active" id="lista">
                <div class="table-responsive">
                    <table class="table table-bordered text-center tabela-projetos">
                        <thead class="table-dark">
                            <tr>
                                <th>N.º</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Grupo</th>
                                <th>Prazo</th>
                                <th>Arquivo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php $i=1; foreach ($projectos as $p): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($p['titulo']) ?></td>
                                <td><?= htmlspecialchars($p['descricao']) ?></td>
                                <td><?= htmlspecialchars($projectoDAO->getNomesDoGrupo($p['id'])) ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($p['prazo'])) ?></td>

                                <td>
                                    <?php if (!empty($p['arquivo'])): ?>
                                        <a href="download.php?file=<?= urlencode($p['arquivo']) ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Nenhum</span>
                                    <?php endif; ?>
                                </td>

                                <td class="d-flex justify-content-center gap-2">

                                    <a href="../visao/Editar.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="../controlo/EliminarProjecto.php?id=<?= $p['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Tem certeza que deseja eliminar este projeto?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PROJETOS SUBMETIDOS -->
            <div class="tab-pane fade" id="submetidos">
                <div class="table-responsive">
                    <table class="table table-bordered text-center tabela-projetos">
                        <thead class="table-dark">
                            <tr>
                                <th>N.º</th>
                                <th>Enviado por</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Grupo</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Arquivo</th>
                                <th>Comentário</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php $j=1; foreach ($submissoes as $s): ?>
                            <tr>
                                <td><?= $j++ ?></td>
                                <td><?= htmlspecialchars($s['estudante_nome'] ?? 'Desconhecido') ?></td>
                                <td><?= htmlspecialchars($s['titulo']) ?></td>
                                <td><?= htmlspecialchars($s['descricao']) ?></td>
                                <td><?= htmlspecialchars($s['estudantes'] ?? '-') ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($s['data_submissao'])) ?></td>

                                <td>
                                    <?php if ($s['estatus']=='concluido'): ?>
                                        <span class="badge bg-success">Concluído</span>
                                    <?php elseif ($s['estatus']=='emAndamento'): ?>
                                        <span class="badge bg-warning text-dark">Em andamento</span>
                                    <?php elseif ($s['estatus']=='atrasado'): ?>
                                        <span class="badge bg-danger">Atrasado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($s['arquivo'])): ?>
                                        <a href="../uploads/<?= rawurlencode($s['arquivo']) ?>" download class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Nenhum</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($s['feedback'] ?? 'Nenhum') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div> <!-- tab-content -->

    </div>
</div>
</main>

<?php include_once('../visao/Rodape.php'); ?>
</div>


<!-- JS E CSS ORGANIZADOS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script>
$(document).ready(function () {
    $('.tabela-projetos').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        language: {
            search: "Pesquisar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                next: "Próximo",
                previous: "Anterior"
            }
        }
    });
});
</script>
