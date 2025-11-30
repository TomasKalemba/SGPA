<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../Modelo/VerProjectos.php');
require_once('../Modelo/submissoes.php');

$projectoDAO = new VerProjectos();
$submissoesDAO = new submissoes();

if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesDAO->getSubmissoesParaAdmin();

    // Buscar só projetos ativos para manter coerência com seu design anterior
    $projectos = $projectoDAO->getAllProjectsAtivos();
} else {
    include_once('head/headDocente.php');
    // Para usuários não admin, só listar seus projetos ativos
    $submissoes = $submissoesDAO->getSubmissoesParaDocente($_SESSION['id']);
    $projectos = $projectoDAO->getProjetosPorDocenteAtivos($_SESSION['id']);
}

// Normalizar IDs para usar sempre $p['id']
foreach ($projectos as &$p) {
    if (isset($p['Id']) && !isset($p['id'])) {
        $p['id'] = $p['Id'];
    }
}
?>

<!-- Estilos baseados no padrão da tabela de estudantes -->
<style>
body {
    background-color: #f5f7fa !important;
}
h5 {
    font-weight: 600;
    color: #2c3e50;
}
.card {
    background: #fff;
    border-radius: 12px;
    border: none;
    padding: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}
table.dataTable tbody td {
    padding: 6px 10px !important;
    font-size: 14px;
}
table.dataTable thead th {
    padding: 8px 10px !important;
    font-size: 14px;
    background: linear-gradient(to right, #4e73df, #224abe);
    color: #fff;
}
.table tbody tr:hover {
    background-color: #eef3ff;
}
.btn-danger {
    background-color: #e74a3b;
    border: none;
}
.btn-danger:hover {
    background-color: #c0392b;
}
.btn-warning {
    background-color: #f6c23e;
    border: none;
    color: #212529;
}
.btn-warning:hover {
    background-color: #dda20a;
    color: #212529;
}
.alert {
    border-radius: 10px;
    font-weight: 500;
}
</style>

<div id="layoutSidenav_content">
<main class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h5>Projetos</h5>
        </div>
        <div class="card-body">

            <!-- Mensagem de sucesso ou erro -->
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
            <?php endif; ?>

            <!-- Abas -->
            <ul class="nav nav-tabs mb-4" id="abasProjetos" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista" type="button" role="tab">Lista de Projetos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="submetidos-tab" data-bs-toggle="tab" data-bs-target="#submetidos" type="button" role="tab">Projetos Submetidos</button>
                </li>
            </ul>

            <div class="tab-content" id="conteudoAbas">

                <!-- TAB 1 - Lista de Projetos -->
                <div class="tab-pane fade show active" id="lista" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tabelaProjetos" class="display table table-hover align-middle tabela-projetos" style="width:100%">
                            <thead>
                                <tr>
                                    <th>N.º</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Grupo</th>
                                    <th>Prazo</th>
                                    <th>Arquivo</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($projectos as $p): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                                        <td><?= htmlspecialchars($p['descricao']) ?></td>
                                        <td><?= htmlspecialchars($projectoDAO->getNomesDoGrupo($p['id'] ?? $p['Id'] ?? null)) ?></td>
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
                                        <td class="text-center">
                                            <a href="../visao/Editar.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn btn-warning btn-sm" title="Editar">
                                              <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../controlo/EliminarProjecto.php?id=<?= $p['id'] ?? $p['Id'] ?? '' ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Tem certeza que deseja eliminar este projeto?');" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2 - Projetos Submetidos -->
                <div class="tab-pane fade" id="submetidos" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tabelaSubmissoes" class="display table table-hover align-middle tabela-projetos" style="width:100%">
                            <thead>
                                <tr>
                                    <th>N.º</th>
                                    <th>Enviado por:</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Grupo</th>
                                    <th>Data de Submissão</th>
                                    <th>Status</th>
                                    <th>Arquivo</th>
                                    <th>Comentário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $j = 1; foreach ($submissoes as $s): ?>
                                    <tr>
                                        <td><?= $j++ ?></td>
                                        <td><?= htmlspecialchars($s['estudante_nome'] ?? 'Desconhecido') ?></td>
                                        <td><?= htmlspecialchars($s['titulo']) ?></td>
                                        <td><?= htmlspecialchars($s['descricao']) ?></td>
                                        <td><?= !empty($s['estudantes']) ? htmlspecialchars($s['estudantes']) : '<span class="text-muted">-</span>' ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($s['data_submissao'])) ?></td>
                                        <td>
                                            <?php if ($s['estatus'] == 'concluido'): ?>
                                                <span class="badge bg-success">Concluído</span>
                                            <?php elseif ($s['estatus'] == 'emAndamento'): ?>
                                                <span class="badge bg-warning text-dark">Em andamento</span>
                                            <?php elseif ($s['estatus'] == 'atrasado'): ?>
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
                                        <td>
                                            <?= !empty($s['feedback']) ? htmlspecialchars($s['feedback']) : '<span class="text-muted">Nenhum</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- tab-content -->
        </div> <!-- card-body -->
    </div> <!-- card -->
</main>

<?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- CSS externo -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Scripts externos -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelaProjetos, #tabelaSubmissoes').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5',
            'print'
        ],
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "Nenhum dado encontrado",
            info: "Mostrando página _PAGE_ de _PAGES_",
            infoEmpty: "Nenhum registro disponível",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
            search: "Pesquisar:",
            paginate: {
                next: "Próximo",
                previous: "Anterior"
            }
        }
    });
});
</script>
