<?php
ob_start();
session_start();

require_once '../modelo/crud.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Inclui o cabeçalho correto
if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
} elseif ($_SESSION['tipo'] === 'Estudante') {
    include_once('head/Estudante.php');
} else {
    header("Location: login.php");
    exit;
}

// Buscar projetos com nome do docente (modificado para trazer nome docente)
$conexao = (new crud())->getConexao();
$projetos = [];

if ($_SESSION['tipo'] === 'Estudante') {
    $estudanteId = $_SESSION['id'];

    $sql = "
        SELECT p.Id, p.titulo, p.descricao, p.prazo, u.nome AS docente_nome
        FROM projectos p
        INNER JOIN grupo g ON g.projeto_id = p.Id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        INNER JOIN usuarios u ON u.id = p.docente_id
        WHERE ge.estudante_id = :estudante_id
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudanteId);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($_SESSION['tipo'] === 'Admin') {
    $sql = "
        SELECT p.Id, p.titulo, p.descricao, p.prazo, u.nome AS docente_nome
        FROM projectos p
        LEFT JOIN usuarios u ON u.id = p.docente_id
        ORDER BY p.titulo ASC
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div id="layoutSidenav_content">
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Enviar Projeto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <style>
        body {
            background: #f5f6f7;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background-color: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.12);
            max-width: 900px;
            margin: 40px auto;
        }

        label i {
            margin-right: 6px;
            color: #198754;
        }

        footer {
            max-width: 900px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center mx-auto mb-4 w-75" role="alert" style="max-width: 900px;">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="container shadow">
    <h3 class="text-center mb-4 text-success fw-bold">
        <i class="fas fa-paper-plane"></i> Enviar Projeto
    </h3>

    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data">

        <!-- Projeto para seleção -->
        <div class="mb-3">
            <label class="form-label" for="projetoSelect">
                <i class="fas fa-project-diagram"></i> Projeto
            </label>

            <select class="form-select" name="projeto_id" id="projetoSelect" required>
                <option value="">Selecione</option>

                <?php foreach ($projetos as $proj): ?>
                    <option 
                        value="<?= htmlspecialchars($proj['Id']) ?>"
                        data-descricao="<?= htmlspecialchars($proj['descricao']) ?>"
                        data-prazo="<?= htmlspecialchars($proj['prazo']) ?>"
                        data-docente="<?= htmlspecialchars($proj['docente_nome']) ?>"
                    >
                        <?= htmlspecialchars($proj['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nome do Docente -->
        <div class="mb-3">
            <label class="form-label" for="docenteNome">
                <i class="fas fa-user"></i> Nome do Docente (preenchido automaticamente)
            </label>
            <input type="text" class="form-control" id="docenteNome" name="docente_nome" required readonly>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label" for="descricaoProjeto">
                <i class="fas fa-pencil-alt"></i> Descrição do Projeto
            </label>
            <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="3" readonly></textarea>
        </div>

        <!-- Prazo -->
        <div class="mb-3">
            <label class="form-label" for="prazoProjeto">
                <i class="fas fa-calendar-alt"></i> Prazo do Projeto
            </label>
            <input type="date" class="form-control" id="prazoProjeto" name="data_submissao" readonly>
        </div>

        <!-- Arquivo -->
        <div class="mb-3">
            <label class="form-label" for="arquivo">
                <i class="fas fa-file-upload"></i> Arquivo do Projeto
            </label>
            <input type="file" class="form-control" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label" for="estatus">
                <i class="fas fa-tasks"></i> Status do Projeto
            </label>
            <select class="form-select" name="estatus" id="estatus" required>
                <option value="emAndamento">Em andamento</option>
                <option value="concluido">Concluído</option>
                <option value="atrasado">Atrasado</option>
            </select>
        </div>

        <!-- Feedback -->
        <div class="mb-3">
            <label class="form-label" for="feedback">
                <i class="fas fa-comments"></i> Comentários ou Observações
            </label>
            <textarea class="form-control" name="feedback" rows="3"></textarea>
        </div>

        <!-- Botão -->
        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
            <i class="fas fa-paper-plane"></i> Enviar Projeto
        </button>

    </form>
</div>

<footer>
    <?php include_once('Rodape.php'); ?>
</footer>

<script>
// Atualiza campos descrição, prazo e nome do docente automaticamente quando muda o projeto selecionado
document.getElementById('projetoSelect').addEventListener('change', function () {
    const op = this.options[this.selectedIndex];
    document.getElementById('descricaoProjeto').value = op.getAttribute('data-descricao') || '';
    document.getElementById('prazoProjeto').value = op.getAttribute('data-prazo') || '';
    document.getElementById('docenteNome').value = op.getAttribute('data-docente') || '';
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
