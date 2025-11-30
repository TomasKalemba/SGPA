<?php
session_start();
require_once '../modelo/crud.php';

// Verifica se é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'Admin') {
    header('Location: ../index.php');
    exit;
}

// Criar a instância do CRUD
$crud = new crud();

// Buscar docentes inativos
$docentes = $crud->buscarDocentesInativos();
?>

<?php include_once('head/Admin.php'); ?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-user-clock"></i> Docentes Pendentes de Ativação</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
            <div class="alert alert-success">Conta ativada com sucesso!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'erro'): ?>
            <div class="alert alert-danger">Erro ao ativar conta.</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'removido'): ?>
            <div class="alert alert-warning">Conta removida com sucesso!</div>
        <?php endif; ?>

        <?php if (count($docentes) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docentes as $docente): ?>
                            <tr>
                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                <td><?= htmlspecialchars($docente['email']) ?></td>
                                <td class="text-center">
                                    <!-- Formulário de Ativação -->
                                    <form action="../controlo/ativar_docente.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja ativar este docente?');">
                                        <input type="hidden" name="id" value="<?= $docente['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle"></i> Ativar
                                        </button>
                                    </form>

                                    <!-- Formulário de Eliminação -->
                                    <form action="../controlo/eliminar_docente.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja eliminar esta conta?');">
                                        <input type="hidden" name="id" value="<?= $docente['id'] ?>">
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
            <div class="alert alert-info">Nenhum docente pendente de ativação no momento.</div>
        <?php endif; ?>
    </main>

    <?php include_once('../visao/Rodape.php'); ?>
</div>

