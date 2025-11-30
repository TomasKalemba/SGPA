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

// Buscar docentes inativos e ativos
$docentesInativos = $crud->buscarDocentesInativos();
$docentesAtivos = $crud->buscarDocentesAtivos(); // novo método que vamos adicionar
?>

<?php include_once('head/Admin.php'); ?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-user-clock"></i> Docentes Pendentes de Ativação</h2>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'sucesso'): ?>
                <div class="alert alert-success">Conta ativada com sucesso!</div>
            <?php elseif ($_GET['status'] == 'erro'): ?>
                <div class="alert alert-danger">Erro ao ativar conta.</div>
            <?php elseif ($_GET['status'] == 'removido'): ?>
                <div class="alert alert-warning">Conta removida com sucesso!</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Tabela de docentes pendentes -->
        <?php if (count($docentesInativos) > 0): ?>
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
                        <?php foreach ($docentesInativos as $docente): ?>
                            <tr>
                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                <td><?= htmlspecialchars($docente['email']) ?></td>
                                <td class="text-center">
                                    <form action="../controlo/ativar_docente.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja ativar este docente?');">
                                        <input type="hidden" name="id" value="<?= $docente['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle"></i> Ativar
                                        </button>
                                    </form>

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

        <hr class="my-4">
        <h4><i class="fas fa-user-check"></i> Docentes Já Ativos</h4>

        <?php if (count($docentesAtivos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docentesAtivos as $docente): ?>
                            <tr>
                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                <td><?= htmlspecialchars($docente['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">Nenhum docente ativo registrado.</p>
        <?php endif; ?>
    </main>

    <?php include_once('../visao/Rodape.php'); ?>
</div>
