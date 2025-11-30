<?php
session_start();
require_once '../modelo/crud.php';

// Verifica se é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../visao/login.php');
    exit;
}

// Instanciar CRUD
$crud = new crud();

// Buscar estudantes (ajuste seu método buscarEstudantes para incluir matricula, curso e ano_curricular)
$estudantes = $crud->buscarEstudantesComCurso();
?>

<?php include_once('head/Admin.php'); ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
body {
    background-color: #f5f7fa !important;
}
h2, h4 {
    font-weight: 600;
    color: #2c3e50;
}
.card-custom {
    background: #fff;
    border-radius: 12px;
    border: none;
    padding: 20px;
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
.alert {
    border-radius: 10px;
    font-weight: 500;
}
</style>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <div class="card-custom">
            <h4><i class="fas fa-user-graduate text-primary"></i>Estudantes Cadastrados no Sistema</h4>

            <!-- Mensagens -->
            <?php if (isset($_GET['status_estudante'])): ?>
                <?php if ($_GET['status_estudante'] === 'removido'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Estudante eliminado com sucesso.
                    </div>
                <?php elseif ($_GET['status_estudante'] === 'erro'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Erro ao eliminar estudante.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (count($estudantes) > 0): ?>
                <div class="table-responsive mt-3">
                    <table id="tabelaEstudantes" class="display table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Matrícula</th>
                                <th>Curso</th>
                                <th>Ano Curricular</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudantes as $estudante): ?>
                                <tr>
                                    <td><?= htmlspecialchars($estudante['nome']) ?></td>
                                    <td><?= htmlspecialchars($estudante['email']) ?></td>
                                    <td><?= htmlspecialchars($estudante['numero_matricula'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($estudante['curso_nome'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($estudante['ano_curricular'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <form action="../controlo/eliminar_estudante.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja eliminar este estudante?');">
                                            <input type="hidden" name="id" value="<?= $estudante['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle"></i> Nenhum estudante cadastrado.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelaEstudantes').DataTable({
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
