<?php 
    
    require_once( '../Modelo/VerProjectos.php');
    $projecto = new VerProjectos();
    $projectos =  $projecto->getbuscaProjects('Id',$_GET['id']);
?>   
<?php include_once( 'head/headDocente.php') ?>        
            <div id="layoutSidenav_content">
                <br>
              
<div class="container">
    <div class="form-container">
        <h2>Criar Novo Projeto</h2>
        <?php if (!empty($projectos)) { ?>
            <?php foreach ($projectos as $projecto) { ?>
        <form action="../Controlo/EditarProjecto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" class="form-control" id="id" name="id" value="<?= $projecto['Id'] ?>" required>
    <!-- Campo Título -->
    <div class="form-group">
        <label for="titulo"><i class="fas fa-heading"></i> Título do Projeto</label>
        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $projecto['titulo'] ?>" required>
    </div>

    <!-- Campo Descrição -->
    <div class="form-group">
        <label for="descricao"><i class="fas fa-align-left"></i> Descrição do Projeto</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= $projecto['descricao'] ?></textarea>
    </div>

    <!-- Campo ID do Docente -->
    <div class="form-group">
        <label for="docente_id"><i class="fas fa-chalkboard-teacher"></i> ID do Docente</label>
        <input type="text" class="form-control" id="docente_id" name="docente_id" value="<?= $projecto['docente_id'] ?>" required>
    </div>

    <!-- Campo Data de Criação -->
    <div class="form-group">
        <label for="data_criacao"><i class="fas fa-calendar-alt"></i> Data de Criação</label>
        <input type="date" class="form-control" id="data_criacao" name="data_criacao" value="<?= $projecto['data_criacao'] ?>" required>
    </div>

    <!-- Campo Prazo -->
    <div class="form-group">
        <label for="prazo"><i class="fas fa-calendar-check"></i> Prazo</label>
        <input type="date" class="form-control" id="prazo" name="prazo" value="<?= $projecto['prazo'] ?>" required>
    </div>

    <!-- Campo Atribuir Estudantes -->
    <div class="form-group">
        <label for="estudantes"><i class="fas fa-users"></i> Atribuir Estudantes</label>
        <select class="form-control" id="estudantes" name="estudantes[]" multiple required>
        <option value="<?= $projecto['prazo'] ?>"> <?= $projecto['estudantes'] ?></option>
            <option value="1">Estudante 1</option>
            <option value="2">Estudante 2</option>
            <option value="3">Estudante 3</option>
            <option value="4">Estudante 4</option>
        </select>
    </div>

    <!-- Campo Prazo Individual para Estudantes -->
    <!--<div class="form-group">
        <label for="prazo_estudantes"><i class="fas fa-calendar-alt"></i> Definir Prazo para Estudantes</label>
        <input type="date" class="form-control" id="prazo_estudantes" name="prazo_estudantes"required><?= $projecto['prazo_estudantes'] ?>
    </div>-->

    <!-- Campo para Feedback -->
    <div class="form-group">
        <label for="feedback"><i class="fas fa-comment-alt"></i> Feedback (opcional)</label>
        <textarea class="form-control" id="feedback" name="feedback" rows="3" required><?= $projecto["feedback"] ?></textarea>
    </div>

    <!-- Campo para Upload de Arquivos -->
    <div class="form-group">
        <label for="arquivo"><i class="fas fa-file-upload"></i> Upload de Arquivo</label>
        <input type="file" class="form-control-file" id="arquivo" name="arquivo" accept=".pdf,.docx,.pptx,.jpg,.png" value="<?= $projecto['arquivo'] ?>">
        <small class="form-text text-muted">Você pode enviar arquivos PDF, DOCX, PPTX ou imagens.</small>
    </div>

    <!-- Botão de Enviar -->
    <button type="submit" class="btn btn-primary btn-block">
        <i class="fas fa-plus-circle"></i> Editar Projeto
    </button>
</form>
<?php } } ?>
    </div>
</div>

<!-- Rodapé -->
<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; Your Website 2023</div>
            <div>
                <a href="#">Privacy Policy</a> &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<!-- Link para o JavaScript do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

</body>
</html>
