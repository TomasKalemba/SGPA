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

// Buscar docentes ativos e inativos
$docentesInativos = $crud->buscarDocentesInativos();
$docentesAtivos = $crud->buscarDocentesAtivos();
?>

<?php include_once('head/Admin.php'); ?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-user-clock"></i> Docentes Pendentes de Ativação</h2>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'sucesso'): ?>
                <div class="alert alert-success">Conta ativada com sucesso!</div>
            <?php elseif ($_GET['status'] === 'erro'): ?>
                <div class="alert alert-danger">Erro ao ativar a conta.</div>
            <?php elseif ($_GET['status'] === 'removido'): ?>
                <div class="alert alert-warning">Conta removida com sucesso.</div>
            <?php endif; ?>
        <?php endif; ?>

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
                                    <form action="../controlo/activar_docente.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja ativar este docente?');">
                                        <input type="hidden" name="id" value="<?= $docente['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle"></i> Ativar
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
            <style>
                .tabela-ativos {
                    border: 1px solid #dee2e6;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                .tabela-ativos thead {
                    background: linear-gradient(to right, #28a745, #218838);
                    color: white;
                }

                .tabela-ativos tbody tr:nth-child(odd) {
                    background-color: #f8f9fa;
                }

                .tabela-ativos tbody tr:hover {
                    background-color: #e2f5e9;
                }

                .tabela-ativos th, .tabela-ativos td {
                    vertical-align: middle !important;
                }
            </style>

            <div class="table-responsive tabela-ativos mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Nome</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docentesAtivos as $docente): ?>
                            <tr>
                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                <td><?= htmlspecialchars($docente['email']) ?></td>
                                <td class="text-center">
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
            <div class="alert alert-secondary">Nenhum docente ativo registrado.</div>
        <?php endif; ?>
    </main>

    <?php include_once('../visao/Rodape.php'); ?>
</div>
