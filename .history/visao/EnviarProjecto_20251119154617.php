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
        /* Centraliza o corpo e dá um background leve */
        body, html {
            height: 100%;
            margin: 0;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Container do formulário */
        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            width: 800px;
            max-width: 95vw;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Labels com ícones */
        .form-label i {
            color: #198754;
            margin-right: 8px;
        }

        /* Campos do formulário */
        .form-control, .form-select {
            border-radius: 8px;
            font-size: 1rem;
            height: 44px;
            padding: 8px 12px;
            transition: border-color 0.3s ease;
        }

        textarea.form-control {
            min-height: 100px;
            max-height: 180px;
            resize: vertical;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        /* Botão */
        button.btn-success {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        button.btn-success:hover {
            background-color: #157347;
        }

        /* Espaçamento entre campos */
        .mb-3 {
            margin-bottom: 20px !important;
        }

        /* Scroll caso ultrapasse a altura */
        .container::-webkit-scrollbar {
            width: 8px;
        }

        .container::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        /* Responsividade */
        @media (max-width: 850px) {
            .container {
                width: 95vw;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center mx-auto mb-4 w-75" role="alert" style="max-width: 800px;">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="container mt-2">
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
                    >
                        <?= htmlspecialchars($proj['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nome do Docente -->
        <div class="mb-3">
            <label class="form-label" for="docente_nome">
                <i class="fas fa-user"></i> Nome do Docente (digite corretamente)
            </label>
            <input type="text" id="docente_nome" class="form-control" name="docente_nome" required>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label" for="descricaoProjeto">
                <i class="fas fa-pencil-alt"></i> Descrição do Projeto
            </label>
            <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="4" readonly></textarea>
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
            <input type="file" class="form-control" id="arquivo" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label" for="estatus">
                <i class="fas fa-tasks"></i> Status do Projeto
            </label>
            <select class="form-select" id="estatus" name="estatus" required>
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
            <textarea class="form-control" id="feedback" name="feedback" rows="4"></textarea>
        </div>

        <!-- Botão -->
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane"></i> Enviar Projeto
            </button>
        </div>
    </form>
</div>

<footer>
    <?php include_once('Rodape.php'); ?>
</footer>

<script>
// Preenche descrição e prazo automaticamente quando seleciona projeto
document.getElementById('projetoSelect').addEventListener('change', function () {
    const option = this.options[this.selectedIndex];
    document.getElementById('descricaoProjeto').value = option.getAttribute('data-descricao') || '';
    document.getElementById('prazoProjeto').value = option.getAttribute('data-prazo') || '';
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
