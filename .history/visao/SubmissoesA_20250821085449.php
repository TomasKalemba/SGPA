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
    $submissoes = $submissoesModel->getSubmissoesPorEstudante($_SESSION['id']);
} elseif ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesModel->getSubmissoesParaAdmin(); // Certifique-se que esse método existe
} else {
    header("Location: login.php");
    exit;
}
?>

<div id="layoutSidenav_content">
<main class="container py-4">
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center m-4" role="alert">
            <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Projetos Submetidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center tabela-submissoes">
                    <thead class="table-dark">
                        <tr>
                            <th>N</th>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Data de Submissão</th>
                            <th>Status</th>
                            <th>Feedback</th>
                            <th>Arquivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($submissoes)) : $i = 1; ?>
                            <?php foreach ($submissoes as $sub) : ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($sub['titulo']) ?></td>
                                    <td><?= htmlspecialchars($sub['descricao']) ?></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($sub['data_submissao'])) ?></td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($sub['estatus']) ?></span></td>
                                    
                                    <td><?= !empty($sub['feedback']) ? htmlspecialchars($sub['feedback']) : '<span class="text-muted">Nenhum</span>' ?></td>
                                    <td>
                                        <?php if (!empty($sub['arquivo'])) : ?>
                                            <a href="uploads/<?= urlencode($sub['arquivo']) ?>" class="btn btn-success btn-sm" download>
                                                <i class="fas fa-download"></i> Baixar
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted">Nenhum</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Nenhuma submissão encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once('../visao/Rodape.php'); ?>
</div>



<script>
  $(document).ready(function () {
    $('.tabela-submissoes').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excelHtml5', title: 'Projetos Submetidos' },
        { extend: 'pdfHtml5', title: 'Projetos Submetidos', orientation: 'landscape', pageSize: 'A4' },
        { extend: 'print', title: 'Projetos Submetidos' }
      ],
      language: {
        search: "Pesquisar:",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "Nenhum registro encontrado",
        zeroRecords: "Nenhum registro correspondente encontrado",
        paginate: {
          first: "Primeiro",
          last: "Último",
          next: "Próximo",
          previous: "Anterior"
        }
      }
    });
  });
</script>
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