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
        INNER JOIN usuarios u ON u.id = p.docente_id
        ORDER BY titulo ASC
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <style>
        body {
            background: #f5f7fa;
            padding: 40px 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.15);
            max-width: 800px;
            margin: auto;
        }

        label i {
            margin-right: 6px;
            color: #198754;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> 
                text-center mx-auto mb-4 w-75" role="alert">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="form-container">

    <h3 class="text-center text-success fw-bold mb-4">
        <i class="fas fa-upload"></i> Enviar Projeto
    </h3>

    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data">

        <!-- Seleção do Projeto -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-project-diagram"></i> Projeto</label>
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

        <!-- Docente -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-user"></i> Docente</label>
            <input type="text" id="docenteNome" class="form-control" name="docente_nome" readonly required>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-pencil-alt"></i> Descrição</label>
            <textarea id="descricaoProjeto" class="form-control" name="descricao" rows="3" readonly required></textarea>
        </div>

        <!-- Prazo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-calendar-alt"></i> Prazo</label>
            <input type="date" id="prazoProjeto" class="form-control" name="data_submissao" readonly required>
        </div>

        <!-- Arquivo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-file-upload"></i> Arquivo</label>
            <input type="file" class="form-control" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-tasks"></i> Status</label>
            <select class="form-select" name="estatus" required>
                <option value="emAndamento">Em andamento</option>
                <option value="concluido">Concluído</option>
                <option value="atrasado">Atrasado</option>
            </select>
        </div>

        <!-- Feedback -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-comments"></i> Comentários</label>
            <textarea class="form-control" name="feedback" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100 fw-bold">
            <i class="fas fa-paper-plane"></i> Enviar Projeto
        </button>

    </form>
</div>

<footer class="mt-4">
    <?php include_once('Rodape.php'); ?>
</footer>

<script>
// Preenche descrição, prazo e docente automaticamente
document.getElementById('projetoSelect').addEventListener('change', function () {
    const op = this.options[this.selectedIndex];
    document.getElementById('descricaoProjeto').value = op.getAttribute('data-descricao') || '';
    document.getElementById('prazoProjeto').value = op.getAttribute('data-prazo') || '';
    document.getElementById('docenteNome').value = op.getAttribute('data-docente') || '';
});
</script>

</body>
</html>
