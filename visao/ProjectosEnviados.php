<?php
session_start();

if (!isset($_SESSION['tipo']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Cabeçalho dinâmico
switch ($_SESSION['tipo']) {
    case 'Admin':
        include_once('head/Admin.php');
        break;
    case 'Docente':
        include_once('head/headDocente.php');
        break;
    default:
        include_once('head/Estudante.php');
        break;
}

// Carregar modelos
require_once('../modelo/submissoes.php');
require_once('../modelo/VerProjectos.php');

$submissoesModel = new submissoes();
$projetosModel = new VerProjectos();

$submissoes = [];
$tipo = $_SESSION['tipo'];
$idUsuario = $_SESSION['id'];

// Obter submissões conforme o tipo de usuário
if ($tipo === 'Admin') {
    $submissoes = $submissoesModel->getSubmissoesParaAdmin();
} elseif ($tipo === 'Estudante') {
    $submissoes = $submissoesModel->getSubmissoesPorEstudante($idUsuario);
} elseif ($tipo === 'Docente') {
    $submissoes = $submissoesModel->getSubmissoesParaDocente($idUsuario);
}
?>
<div id="layoutSidenav_content">
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Meus Projetos Enviados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .container {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container mt-5">

    <!-- Mostrar botão apenas se for estudante -->
    <?php if ($tipo === 'Estudante') { ?>
        <div class="mb-4">
            <a href="EnviarProjecto.php" class="btn btn-custom">
                <i class="fas fa-plus-circle"></i> Adicionar Novo Projeto
            </a>
        </div>
    <?php } ?>

    <h2 class="text-center text-primary mb-4"><i class="fas fa-tasks"></i> Projetos Enviados</h2>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudante</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data de Envio</th>
                <th>Status</th>
                <th>Feedback</th>
                <th>Arquivo</th>
                <?php if ($tipo === 'Admin' || $tipo === 'Docente') { ?>
                    <th>Ações</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($submissoes)) { ?>
                <?php foreach ($submissoes as $projeto) { ?>
                    <tr>
                        <td><?= htmlspecialchars($projeto['id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['estudante_id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['titulo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['descricao'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['data_submissao'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['estatus'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($projeto['feedback'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($projeto['arquivo'])) { ?>
                                <a href="uploads/<?= htmlspecialchars($projeto['arquivo']) ?>" target="_blank">Baixar</a>
                            <?php } else { ?>
                                Nenhum
                            <?php } ?>
                        </td>
                        <?php if ($tipo === 'Admin' || $tipo === 'Docente') { ?>
                            <td>
                                <a href="Editar.php?id=<?= $projeto['Id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="ListaProjectos.php?id=<?= $projeto['Id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja eliminar este projeto?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="9" class="text-center">Nenhum projeto encontrado.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<footer>
    <?php include_once('../visao/Rodape.php'); ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
