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

// Buscar projetos
$conexao = (new crud())->getConexao();
$projetos = [];

if ($_SESSION['tipo'] === 'Estudante') {
    // Projetos atribuídos ao estudante
    $estudanteId = $_SESSION['id'];

    $sql = "
        SELECT p.Id, p.titulo, p.descricao, p.prazo
        FROM projectos p
        INNER JOIN grupo g ON g.projeto_id = p.Id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE ge.estudante_id = :estudante_id
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudanteId);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($_SESSION['tipo'] === 'Admin') {
    // Admin vê TODOS os projetos
    $sql = "SELECT Id, titulo, descricao, prazo FROM projectos ORDER BY titulo ASC";
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
        .container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 700px;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center m-4" role="alert">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data">

        <!-- Projeto para seleção -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-project-diagram"></i> Projeto
            </label>

            <select class="form-select" name="projeto_id" id="projetoSelect" required>
                <option value="">Selecione</option>

                <?php foreach ($projetos as $proj): ?>
                    <option 
                        value="<?= htmlspecialchars($proj['Id']) ?>"
                        data-descricao="<?= htmlspecialchars($proj['descricao']) ?>"
                        data-prazo="<?= htmlspecialchars($proj['prazo']) ?>"
                    >
                        <?= htmlspecialchars($proj['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nome do Docente -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-user"></i> Nome do Docente (digite corretamente)
            </label>
            <input type="text" class="form-control" name="docente_nome" required>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-pencil-alt"></i> Descrição do Projeto
            </label>
            <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="3" readonly></textarea>
        </div>

        <!-- Prazo -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-calendar-alt"></i> Prazo do Projeto
            </label>
            <input type="date" class="form-control" id="prazoProjeto" name="data_submissao" readonly>
        </div>

        <!-- Arquivo -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-file-upload"></i> Arquivo do Projeto
            </label>
            <input type="file" class="form-control" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-tasks"></i> Status do Projeto
            </label>
            <select class="form-select" name="estatus" required>
                <option value="emAndamento">Em andamento</option>
                <option value="concluido">Concluído</option>
                <option value="atrasado">Atrasado</option>
            </select>
        </div>

        <!-- Feedback -->
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-comments"></i> Comentários ou Observações
            </label>
            <textarea class="form-control" name="feedback" rows="3"></textarea>
        </div>

        <!-- Botão -->
        <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane"></i> Enviar Projeto
        </button>

    </form>
</div>

<footer>
    <?php include_once('Rodape.php'); ?>
</footer>

<script>
// Preenche descrição e prazo automaticamente
document.getElementById('projetoSelect').addEventListener('change', function () {
    const op = this.options[this.selectedIndex];
    document.getElementById('descricaoProjeto').value = op.getAttribute('data-descricao') || '';
    document.getElementById('prazoProjeto').value = op.getAttribute('data-prazo') || '';
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
