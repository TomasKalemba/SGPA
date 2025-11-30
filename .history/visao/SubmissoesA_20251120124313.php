<?php
ob_start();
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

require_once('../modelo/submissoes.php');
$submissoesModel = new Submissoes();

if ($_SESSION['tipo'] === 'Estudante') {
    include_once('head/Estudante.php');
    $submissoes = $submissoesModel->getSubmissoesPorEstudanteAtivas($_SESSION['id']);

} elseif ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesModel->getSubmissoesParaAdmin();
} else {
    header("Location: login.php");
    exit;
}
?>

<style>
    body {
        background-color: #f5f7fa !important;
    }

    .card-custom {
        background: #fff;
        border-radius: 12px;
        border: none;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    /* Estilo moderno da tabela */
    table.dataTable thead th {
        background: linear-gradient(to right, #4e73df, #224abe);
        color: white !important;
        font-size: 14px;
        padding: 10px !important;
    }

    table.dataTable tbody td {
        padding: 8px 12px !important;
        font-size: 14px;
    }

    table tbody tr:hover {
        background-color: #eef3ff !important;
    }

    .badge {
        padding: 6px 8px;
        font-size: 12px;
        border-radius: 6px;
    }

    .alert {
        border-radius: 10px;
        font-weight: 500;
    }
</style>

<div id="layoutSidenav_content">
<main class="container mt-4">

    <div class="card-custom">

        <h2 class="mb-4">
            <i class="fas fa-file-upload text-primary"></i> Projetos Submetidos
        </h2>

        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?>" role="alert">
                <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="tabelaSubmissoes" class="display table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Feedback</th>
                        <th>Arquivo</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (!empty($submissoes)) : $i = 1; ?>
                    <?php foreach ($submissoes as $sub): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($sub['titulo']) ?></td>
                            <td><?= htmlspecialchars($sub['descricao']) ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($sub['data_submissao'])) ?></td>

                            <td>
                                <span class="badge bg-success">
                                    <?= htmlspecialchars($sub['estatus']) ?>
                                </span>
                            </td>

                            <td>
                                <?= !empty($sub['feedback']) ? htmlspecialchars($sub['feedback']) : '<span class="text-muted">Nenhum</span>' ?>
                            </td>

                            <td>
                                <?php if (!empty($sub['arquivo'])): ?>
                                    <a href="uploads/<?= urlencode($sub['arquivo']) ?>" 
                                       class="btn btn-primary btn-sm" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Nenhum</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Nenhuma submissão encontrada.</td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>

</main>

<?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelaSubmissoes').DataTable({
        "pageLength": 5,
        "lengthMenu": [5, 10, 25, 50],
        "ordering": true,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "Nenhum dado encontrado",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(filtrado de _MAX_ registros no total)",
            "search": "Pesquisar:",
            "paginate": {
                "next": "Próximo",
                "previous": "Anterior"
            }
        }
    });
});
</script>

<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts e Estilos do DataTables -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">