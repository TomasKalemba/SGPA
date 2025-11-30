<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../Modelo/VerProjectos.php');
require_once('../Modelo/submissoes.php');

$projectoDAO   = new VerProjectos();
$submissoesDAO = new submissoes();

if ($_SESSION['tipo'] === 'Estudante') {
    include_once('head/Estudante.php');
} else {
    header("Location: ../visao/login.php");
    exit;
}

$projectosPendentes = $projectoDAO->getProjetosPorEstudante($_SESSION['id']);
$submissoes         = $submissoesDAO->getSubmissoesRelacionadasAoGrupo($_SESSION['id']);
?>

<!-- CSS para layout e rodapé -->
<style>
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #layoutSidenav_content {
        flex: 1; /* ocupa o espaço antes do rodapé */
        display: flex;
        flex-direction: column;
    }

    main.page-main {
        flex: 1;
        display: flex;
        justify-content: center;   /* centraliza horizontalmente */
        align-items: flex-start;   /* alinha no topo */
        padding: 2rem;
        width: 100%;
    }

    footer {
        flex-shrink: 0;
        width: 100%;
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div id="layoutSidenav_content">
    <main class="page-main">
        <div class="card shadow-lg border-0 rounded-3 w-100" style="max-width: 1100px;">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <h2 class="text-primary fw-bold m-0">📂 Histórico de Submissões</h2>
                    <span class="text-muted small">Total: <?= count($submissoes) ?></span>
                </div>
                <div class="border-top mb-4"></div>

                <?php if (!empty($submissoes)): ?>
                    <div class="table-responsive">
                        <table id="tabelaSubmissoes" class="table table-striped table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th class="text-start">Descrição</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Feedback</th>
                                    <th>Grupo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($submissoes as $s): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['Id_projectos'] ?? $s['projecto_Id'] ?? '') ?></td>
                                        <td class="text-break"><?= htmlspecialchars($s['titulo'] ?? '') ?></td>
                                        <td class="text-start text-break"><?= nl2br(htmlspecialchars($s['descricao'] ?? '')) ?></td>
                                        <td><?= !empty($s['data_submissao']) ? date("d/m/Y H:i", strtotime($s['data_submissao'])) : '-' ?></td>
                                        <td>
                                            <?php
                                            $status = $s['estatus'] ?? '';
                                            if ($status === 'concluido') {
                                                echo '<span class="badge bg-success">✔️ Concluído</span>';
                                            } elseif ($status === 'atrasado') {
                                                echo '<span class="badge bg-danger">⏰ Atrasado</span>';
                                            } else {
                                                echo '<span class="badge bg-warning text-dark">⏳ Em Andamento</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-start"><?= !empty($s['feedback']) ? nl2br(htmlspecialchars($s['feedback'])) : '<em>Sem feedback</em>' ?></td>
                                        <td class="text-start"><?= !empty($s['estudantes']) ? htmlspecialchars($s['estudantes']) : '<em>Não definido</em>' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-1">Você ainda não submeteu nenhum projeto.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <!-- Rodapé fora do main -->
    <?php include_once("Rodape.php"); ?>
</div>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#tabelaSubmissoes').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json"
        },
        pageLength: 5,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'print', text: '🖨️ Imprimir' },
            { extend: 'pdfHtml5', text: '📄 Exportar PDF', orientation: 'landscape', pageSize: 'A4' }
        ]
    });
});
</script>
