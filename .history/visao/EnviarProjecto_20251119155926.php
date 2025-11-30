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

// Buscar projetos (código do seu antigo que funciona)
$conexao = (new crud())->getConexao();
$projetos = [];

if ($_SESSION['tipo'] === 'Estudante') {
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
    $sql = "SELECT Id, titulo, descricao, prazo FROM projectos ORDER BY titulo ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 900px;
            width: 100%;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            height: 38px;
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        textarea.form-control {
            min-height: 70px;
            max-height: 120px;
            resize: vertical;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .btn-submit {
            font-weight: 600;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 1rem;
        }

        /* Ícones junto ao texto dos labels */
        .form-label i {
            margin-right: 6px;
            color: #198754;
        }

        /* Ajusta o espaço entre as linhas do formulário */
        .row > .col-md-6, .row > .col-md-12 {
            margin-bottom: 15px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .form-container {
                padding: 20px 25px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center mx-auto mb-4 w-75" role="alert" style="max-width: 900px;">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="form-container shadow-sm">
    <h2 class="mb-4 text-center text-success fw-bold">Enviar Projeto</h2>
    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data" autocomplete="off">

        <div class="row">
            <!-- Projeto Atribuído -->
            <div class="col-md-6">
                <label class="form-label" for="projetoSelect"><i class="fas fa-project-diagram"></i> Projeto</label>
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
            <div class="col-md-6">
                <label class="form-label" for="docenteNome"><i class="fas fa-user"></i> Nome do Docente (digite corretamente)</label>
                <input type="text" id="docenteNome" class="form-control" name="docente_nome" required>
            </div>

            <!-- Descrição -->
            <div class="col-md-12">
                <label class="form-label" for="descricaoProjeto"><i class="fas fa-pencil-alt"></i> Descrição do Projeto</label>
                <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="3" readonly></textarea>
            </div>

            <!-- Prazo -->
            <div class="col-md-6">
                <label class="form-label" for="prazoProjeto"><i class="fas fa-calendar-alt"></i> Prazo do Projeto</label>
                <input type="date" class="form-control" id="prazoProjeto" name="data_submissao" readonly>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label" for="estatus"><i class="fas fa-tasks"></i> Status do Projeto</label>
                <select class="form-select" name="estatus" id="estatus" required>
                    <option value="emAndamento">Em andamento</option>
                    <option value="concluido">Concluído</option>
                    <option value="atrasado">Atrasado</option>
                </select>
            </div>

            <!-- Arquivo -->
            <div class="col-md-12">
                <label class="form-label" for="arquivo"><i class="fas fa-file-upload"></i> Arquivo do Projeto</label>
                <input type="file" class="form-control" id="arquivo" name="arquivo" required>
            </div>

            <!-- Feedback -->
            <div class="col-md-12">
                <label class="form-label" for="feedback"><i class="fas fa-comments"></i> Comentários ou Observações</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="3"></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn btn-success btn-submit" id="btnSubmit">
                <i class="fas fa-paper-plane"></i> Enviar Projeto
            </button>
        </div>
    </form>
</div>

<footer class="mt-5">
    <?php include_once('Rodape.php'); ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Preencher descrição e prazo automaticamente
    document.getElementById('projetoSelect').addEventListener('change', function () {
        const op = this.options[this.selectedIndex];
        document.getElementById('descricaoProjeto').value = op.getAttribute('data-descricao') || '';
        document.getElementById('prazoProjeto').value = op.getAttribute('data-prazo') || '';
    });
</script>

</body>
</html>
