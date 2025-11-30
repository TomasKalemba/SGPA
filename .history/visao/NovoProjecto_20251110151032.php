<?php 
session_start(); 
include_once("../modelo/crud.php"); 
require_once '../controlo/email.php'; 

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) { 
    header("Location: login.php"); 
    exit; 
} 

// Carrega o cabeçalho de acordo com o tipo de usuário
if ($_SESSION['tipo'] === 'Admin') { 
    include_once('head/Admin.php'); 
} elseif ($_SESSION['tipo'] === 'Docente') { 
    include_once('head/headDocente.php'); 
} else { 
    header("Location: login.php"); 
    exit; 
} 

// Recupera dados do formulário em caso de erro
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Projeto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos -->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body { background-color: #fff; padding-top: 20px; }
        .form-container {
            max-width: 750px;
            margin: auto;
            padding: 30px;
            border: 2px solid #0d6efd;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0d6efd;
            font-weight: 600;
        }
        label.form-label i { color: #0d6efd; margin-right: 6px; }
        .btn-primary {
            background: linear-gradient(to right, #0d6efd, #0b5ed7);
            border: none;
            font-weight: 600;
            transition: background 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #0b5ed7, #084298);
        }
        .form-text { font-size: 0.875rem; color: #6c757d; }
        .alert-info { background-color: #e7f1ff; border-color: #b6daff; color: #084298; }
        .btn-close { background-color: #0d6efd; }
    </style>
</head>
<body>
<div id="layoutSidenav_content">
    <div class="container">
        <div class="form-container">
            <h2><i class="fas fa-folder-plus"></i> Criar Novo Projeto</h2>

            <?php if (!empty($_SESSION['mensagem'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
            <?php endif; ?>

            <form action="../controlo/novoprojecto.php" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="titulo" class="form-label"><i class="fas fa-heading"></i> Título do Projeto</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" 
                               placeholder="Digite o título do projeto" 
                               value="<?= htmlspecialchars($form_data['titulo'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="descricao" class="form-label"><i class="fas fa-align-left"></i> Descrição do Projeto</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" 
                                  placeholder="Descreva o projeto" required><?= htmlspecialchars($form_data['descricao'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label"><i class="fas fa-chalkboard-teacher"></i> Docente</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= htmlspecialchars($_SESSION['nome']) ?>" readonly required>
                        <input type="hidden" name="docente_id" value="<?= $_SESSION['id'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="data_criacao" class="form-label"><i class="fas fa-calendar-alt"></i> Data de Criação</label>
                        <input type="date" class="form-control" id="data_criacao" name="data_criacao" 
                               value="<?= htmlspecialchars($form_data['data_criacao'] ?? date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prazo" class="form-label"><i class="fas fa-calendar-check"></i> Prazo de Entrega</label>
                        <input type="date" class="form-control" id="prazo" name="prazo" 
                               value="<?= htmlspecialchars($form_data['prazo'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="arquivo" class="form-label"><i class="fas fa-file-upload"></i> Upload de Arquivo</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf,.docx,.pptx,.jpg,.png">
                        <small class="form-text text-muted">PDF, DOCX, PPTX, JPG ou PNG. (Será necessário reenviar em caso de erro)</small>
                    </div>
                </div>

                <!-- Select2 de Estudantes -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="estudantes" class="form-label"><i class="fas fa-users"></i> Atribuir Estudantes</label>
                        <select class="form-control" id="estudantes" name="estudantes[]" multiple="multiple" required style="width: 100%;">
                            <?php
                            // Pré-carrega estudantes selecionados em caso de erro
                            if (!empty($form_data['estudantes'])) {
                                foreach ($form_data['estudantes'] as $id => $nome) {
                                    echo "<option value='".htmlspecialchars($id)."' selected>".htmlspecialchars($nome)."</option>";
                                }
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">Digite o nome para buscar estudantes.</small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="fas fa-plus-circle"></i> Criar Projeto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include_once('../visao/Rodape.php') ?>  
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    var estudantesSelecionados = <?= json_encode(!empty($form_data['estudantes']) ? $form_data['estudantes'] : []) ?>;

    $('#estudantes').select2({
        placeholder: 'Buscar estudantes pelo nome...',
        dropdownParent: $('.form-container'),
        width: 'resolve',
        ajax: {
            url: '../ajax/busca_estudantes.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function(params) { return { termo: params.term }; },
            processResults: function(data) { return { results: data }; },
            cache: true
        },
        minimumInputLength: 1,
        language: {
            inputTooShort: function () { return "Digite ao menos 1 caractere..."; },
            noResults: function () { return "Nenhum estudante encontrado"; },
            searching: function () { return "Buscando..."; }
        }
    });

    // Pré-carrega os estudantes selecionados
    Object.entries(estudantesSelecionados).forEach(function([id, nome]) {
        if ($('#estudantes option[value="' + id + '"]').length === 0) {
            var newOption = new Option(nome, id, true, true);
            $('#estudantes').append(newOption).trigger('change');
        }
    });
});
</script>

</body>
</html>
