<?php
session_start();
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

// Formata datas
$projecto['data_criacao'] = date('Y-m-d', strtotime($projecto['data_criacao']));
$projecto['prazo'] = date('Y-m-d', strtotime($projecto['prazo']));

if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/headDocente.php');
}
?>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h2 class="text-center mb-4"><i class="fas fa-edit"></i> Editar Projeto</h2>

        <?php if(isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_SESSION['mensagem']); ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <form action="../Controlo/EditarProjecto.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $projecto['Id'] ?>">

            <div class="mb-3">
                <label for="titulo">Título do Projeto</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($projecto['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= htmlspecialchars($projecto['descricao']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="prazo">Prazo</label>
                <input type="date" class="form-control" id="prazo" name="prazo" value="<?= $projecto['prazo'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="feedback">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="3"><?= htmlspecialchars($projecto['feedback']) ?></textarea>
            </div>

            <?php if($_SESSION['tipo'] === 'Admin'): ?>
            <div class="mb-3">
                <label for="estudantes">Estudantes do Projeto</label>
                <select id="estudantes" name="estudantes[]" class="form-control" multiple style="width:100%;">
                    <?php foreach($estudantes_vinculados as $est): ?>
                        <option value="<?= $est['id'] ?>" selected><?= htmlspecialchars($est['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Busque e selecione estudantes para adicionar ou remover.</small>
            </div>
            <?php else: ?>
            <div class="mb-3">
                <label>Estudantes do Projeto</label>
                <ul class="list-group">
                    <?php foreach($estudantes_vinculados as $est): ?>
                        <li class="list-group-item"><?= htmlspecialchars($est['nome']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?php if($_SESSION['tipo'] === 'Admin'): ?>
<script>
$(document).ready(function() {
    const existingStudents = <?php echo json_encode(array_map(function($e){ return ['id'=>$e['id'],'text'=>$e['nome']]; }, $estudantes_vinculados)); ?>;

    $('#estudantes').select2({
        placeholder: "Buscar estudantes...",
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

    // Adiciona os estudantes já vinculados ao Select2
    existingStudents.forEach(function(student){
        if(!$('#estudantes option[value="'+student.id+'"]').length){
            var newOption = new Option(student.text, student.id, true, true);
            $('#estudantes').append(newOption).trigger('change');
        }
    });
});
</script>
<?php endif; ?>

<?php include_once('../visao/Rodape.php'); ?>

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

