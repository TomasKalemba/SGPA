<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

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

    // Estudantes já vinculados ao projeto
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

// Datas
$projecto['data_criacao'] = date('Y-m-d', strtotime($projecto['data_criacao']));
$projecto['prazo'] = date('Y-m-d', strtotime($projecto['prazo']));

// Header
if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/headDocente.php');
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<style>
body, html { height:100%; margin:0; }
#layoutSidenav_content { min-height:100vh; display:flex; flex-direction:column; }
.container { flex:1; display:flex; justify-content:center; align-items:flex-start; padding:2rem 1rem; }
.form-container { width:100%; max-width:700px; background-color:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
footer { margin-top:auto; padding:1rem 0; background:#f8f9fa; text-align:center; }
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

            <form action="../Controlo/EditarProjecto.php" method="POST" class="mt-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($projecto['Id']) ?>">

                <div class="form-group mb-3">
                    <label for="titulo"><i class="fas fa-heading"></i> Título do Projeto</label>
                    <input type="text" class="form-control" id="titulo" name="titulo"
                           value="<?= htmlspecialchars($projecto['titulo']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="descricao"><i class="fas fa-align-left"></i> Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao"
                              rows="4" required><?= htmlspecialchars($projecto['descricao']) ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="prazo"><i class="fas fa-calendar-check"></i> Prazo de Entrega</label>
                    <input type="date" class="form-control" id="prazo" name="prazo"
                           value="<?= htmlspecialchars($projecto['prazo']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="feedback"><i class="fas fa-comment-alt"></i> Feedback</label>
                    <textarea class="form-control" id="feedback" name="feedback"
                              rows="3"><?= htmlspecialchars($projecto['feedback']) ?></textarea>
                </div>

                <?php if ($_SESSION['tipo'] === 'Admin'): ?>
                <div class="form-group mb-3">
                    <label><i class="fas fa-users"></i> Estudantes do Projeto</label>
                    <select id="estudantes_existentes" name="estudantes[]" class="form-control" multiple>
                        <?php foreach($estudantes_vinculados as $est): ?>
                            <option value="<?= $est['id'] ?>" selected><?= htmlspecialchars($est['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label><i class="fas fa-user-plus"></i> Adicionar Novo(s) Estudante(s)</label>
                    <select id="novos_estudantes" name="novos_estudantes[]" class="form-control" multiple></select>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
            </form>

        </div>
    </div>

    <?php include_once('../visao/Rodape.php'); ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function() {

<?php if ($_SESSION['tipo'] === 'Admin'): ?>

    $('#novos_estudantes').select2({
    placeholder: 'Buscar estudantes pelo nome...',
    dropdownParent: $('.form-container'),
    width: 'resolve',
    ajax: {
        url: '../ajax/busca_estudantes.php',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return { termo: params.term };
        },
        processResults: function(data) {
            return { results: data };
        },
        cache: true
    },
    minimumInputLength: 1,
    language: {
        inputTooShort: function () { return "Digite ao menos 1 caractere..."; },
        noResults: function () { return "Nenhum estudante encontrado"; },
        searching: function () { return "Buscando..."; }
    }
});

<?php endif; ?>

});
</script>


