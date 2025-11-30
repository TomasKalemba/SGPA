<?php 
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../modelo/submissoes.php');
$submissoesModel = new submissoes();

$tipoUsuario = $_SESSION['tipo'];
$usuarioId = $_SESSION['id'];

// Carrega o cabeçalho correto
if ($tipoUsuario === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesModel->getTodasSubmissoes(); // Você precisará criar esse método
} elseif ($tipoUsuario === 'Docente') {
    include_once('head/headDocente.php');
    $submissoes = $submissoesModel->getSubmissoesParaDocente($usuarioId);
} else {
    // Tipo de usuário não permitido
    header("Location: ../visao/login.php");
    exit;
}
?>


<div id="layoutSidenav_content">
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Projetos Submetidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        .container {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            max-width: 1000px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead {
            background-color: #003366;
            color: white;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h4 class="text-center mb-4">Projetos Submetidos pelos Estudantes</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>

                <tr>
                    <th>ID Estudante</th>
                    <th>Nomes dos Estudantes do Grupo</th>
                    <th>ID do Projeto</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Data de Submissão</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Arquivo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($submissoes)) : ?>
                    <?php foreach ($submissoes as $pro) : ?>
                        <tr>
                            <td><?= htmlspecialchars($pro['estudante_id']) ?></td>
                            <td><?= htmlspecialchars($pro['estudantes']) ?></td>
                            <td><?= htmlspecialchars($pro['Id_projectos']) ?></td>
                            <td><?= htmlspecialchars($pro['titulo']) ?></td>
                            <td><?= htmlspecialchars($pro['descricao']) ?></td>
                            <td><?= htmlspecialchars($pro['data_submissao']) ?></td>
                            <td><?= htmlspecialchars($pro['estatus']) ?></td>
                            <td><?= htmlspecialchars($pro['feedback']) ?></td>
                            <td>
                                <?php if (!empty($pro['arquivo'])) : ?>
                                    <a href="uploads/<?= urlencode($pro['arquivo']) ?>" class="btn btn-success btn-sm" title="Baixar arquivo">
                                        <i class="fas fa-download"></i> Baixar
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted">Nenhum arquivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
    <a href="editarSubmissao.php?id=<?= urlencode($pro['Id']) ?>" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Editar
    </a>
    <a href="eliminarSubmissao.php?id=<?= urlencode($pro['Id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta submissão?');">
        <i class="fas fa-trash-alt"></i> Excluir
    </a>
</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted">Nenhuma submissão encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <?php include_once('../visao/Rodape.php'); ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
