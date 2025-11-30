<?php
require_once('../Modelo/VerProjectos.php');
$projectoDAO = new VerProjectos();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID do projeto não foi fornecido.";
    exit;
}

$projectos = $projectoDAO->getbuscaProjects('Id', $_GET['id']);
$projecto = $projectos[0] ?? null;

if (!$projecto) {
    echo "Projeto não encontrado.";
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sgpa", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera os estudantes vinculados ao projeto
    $stmt = $pdo->prepare("
        SELECT u.id, u.nome
        FROM grupo g
        JOIN grupo_estudante ge ON ge.grupo_id = g.id
        JOIN usuarios u ON u.id = ge.estudante_id
        WHERE g.projeto_id = ?
    ");
    $stmt->execute([$projecto['Id']]);
    $estudantes_vinculados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $estudantes_vinculados = [];
}

// Formata datas para HTML
$projecto['data_criacao'] = date('Y-m-d', strtotime($projecto['data_criacao']));
$projecto['prazo'] = date('Y-m-d', strtotime($projecto['prazo']));

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/headDocente.php');
}
?>

<style>
    body, html {
        height: 100%;
        margin: 0;
    }
    #layoutSidenav_content {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 2rem 1rem;
    }
    .form-container {
        width: 100%;
        max-width: 700px;
        background-color: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    footer {
        margin-top: auto;
        padding: 1rem 0;
        background: #f8f9fa;
        text-align: center;
    }
</style>

<div id="layoutSidenav_content">
    <div class="container">
        <div class="form-container">
            <h2 class="text-center"><i class="fas fa-edit"></i> Editar Projeto</h2>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
            <?php endif; ?>

            <form action="../Controlo/EditarProjecto.php" method="POST" enctype="multipart/form-data" class="mt-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($projecto['Id']) ?>">

                <div class="form-group mb-3">
                    <label for="titulo"><i class="fas fa-heading"></i> Título do Projeto</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($projecto['titulo']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="descricao"><i class="fas fa-align-left"></i> Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= htmlspecialchars($projecto['descricao']) ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="docente_id"><i class="fas fa-chalkboard-teacher"></i> ID do Docente</label>
                    <input type="text" class="form-control" id="docente_id" name="docente_id" value="<?= htmlspecialchars($projecto['docente_id']) ?>" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="data_criacao"><i class="fas fa-calendar-alt"></i> Data de Criação</label>
                    <input type="date" class="form-control" id="data_criacao" name="data_criacao" value="<?= htmlspecialchars($projecto['data_criacao']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="prazo"><i class="fas fa-calendar-check"></i> Prazo de Entrega</label>
                    <input type="date" class="form-control" id="prazo" name="prazo" value="<?= htmlspecialchars($projecto['prazo']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="estudantes"><i class="fas fa-users"></i> Estudantes</label>
                    <select class="form-control" id="estudantes" name="estudantes[]" multiple required style="width: 100%;">
                        <?php foreach ($estudantes_vinculados as $estudante): ?>
                            <option value="<?= $estudante['id'] ?>" selected><?= htmlspecialchars($estudante['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Busque e selecione os estudantes vinculados.</small>
                </div>

                <div class="form-group mb-3">
                    <label for="feedback"><i class="fas fa-comment-alt"></i> Feedback</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="3"><?= htmlspecialchars($projecto['feedback']) ?></textarea>
                </div>

                <div class="form-group mb-4">
                    <label for="arquivo"><i class="fas fa-file-upload"></i> Upload de Arquivo</label>
                    <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf,.docx,.pptx,.jpg,.png">
                    <?php if (!empty($projecto['arquivo'])): ?>
                        <p class="mt-2">Arquivo atual: <a href="../uploads/<?= htmlspecialchars($projecto['arquivo']) ?>" target="_blank">baixar</a></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </form>
        </div>
    </div>
    <?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('#estudantes').select2({
        placeholder: 'Buscar estudantes...',
        ajax: {
            url: '../ajax/busca_estudantes.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: params => ({ termo: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        },
        minimumInputLength: 1
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

