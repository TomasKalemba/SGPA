<?php
session_start();
require_once '../modelo/crud.php';
$crud = new crud();
$docentes = $crud->buscarDocentesInativos();

// Apenas administradores podem acessar
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'Admin') {
    header('Location: ../index.php');
    exit;
}

// Buscar docentes inativos
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE tipo = 'Docente' AND ativo = 0");
$stmt->execute();
$docentes = $stmt->fetchAll();
?>

<?php include_once('head/Admin.php'); ?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-user-clock"></i> Docentes Pendentes de Ativação</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
            <div class="alert alert-success">Conta ativada com sucesso!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'erro'): ?>
            <div class="alert alert-danger">Erro ao ativar conta.</div>
        <?php endif; ?>

        <?php if (count($docentes) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th class="text-center">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docentes as $docente): ?>
                            <tr>
                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                <td><?= htmlspecialchars($docente['email']) ?></td>
                                <td class="text-center">
                                    <form action="ativar_docente.php" method="POST" onsubmit="return confirm('Deseja ativar este docente?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $docente['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle"></i> Ativar Conta
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
