<?php 

if (isset($_SESSION['tipo']) &&  $_SESSION['tipo'] == 'Admin') {
    include_once( 'head/Admin.php');
}else{
    include_once( 'head/Estudante.php');
}
?>
<div id="layoutSidenav_content">
            
                    <!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Enviar Projeto</title>
    <!-- Link do Bootstrap 5.1.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome para ícones -->
    <style>
        /* Estilo para o container do formulário */
        .container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 700px;
        }

        /* Estilo personalizado para os botões */
        .btn-custom {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        /* Estilo para o formulário */
        .form-control, .form-select {
            border-radius: 0.375rem;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para os rótulos dos campos */
        .form-label {
            font-weight: bold;
            color: #333;
        }

        /* Estilo para o título */
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        /* Ícones nas labels */
        .form-label i {
            color: #28a745;
            margin-right: 10px;
        }

        /* Espaçamento dos ícones */
        .btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <!-- Título com ícone -->

        <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data">
            <!-- ID do Estudante -->
            <div class="mb-3">
                <label for="idEstudante" class="form-label">
                    <i class="fas fa-id-card"></i> ID do Estudante
                </label>
                <input type="text" class="form-control" id="Estudante_id" name="docente_id" placeholder="Digite seu ID de estudante" required>
            </div>

            <!-- Título do Projeto -->
            <div class="mb-3">
                <label for="tituloProjeto" class="form-label">
                    <i class="fas fa-heading"></i> Título do Projeto
                </label>
                <input type="text" class="form-control" name="titulo" placeholder="Digite o título do seu projeto" required>
            </div>

            <!-- Descrição do Projeto -->
            <div class="mb-3">
                <label for="descricaoProjeto" class="form-label">
                    <i class="fas fa-pencil-alt"></i> Descrição do Projeto
                </label>
                <textarea class="form-control" name="descricao" rows="3" placeholder="Digite a descrição do seu projeto" required></textarea>
            </div>

            <!-- Prazo do Projeto -->
            <div class="mb-3">
                <label for="prazoProjeto" class="form-label">
                    <i class="fas fa-calendar-alt"></i> Prazo do Projeto
                </label>
                <input type="date" class="form-control" name="data_submissao" required>
            </div>

            <!-- Arquivo do Projeto -->
            <div class="mb-3">
                <label for="arquivoProjeto" class="form-label">
                    <i class="fas fa-file-upload"></i> Arquivo do Projeto (PDF, DOCX, etc.)
                </label>
                <input type="file" class="form-control" name="arquivo" required>
            </div>

            <!-- Status do Projeto -->
            <div class="mb-3">
                <label for="statusProjeto" class="form-label">
                    <i class="fas fa-tasks"></i> Status do Projeto
                </label>
                <select class="form-select" name="estatus" required>
                    <option value="emAndamento">Em andamento</option>
                    <option value="concluido">Concluído</option>
                    <option value="atrasado">Atrasado</option>
                </select>
            </div>

            <!-- Comentários ou Observações -->
            <div class="mb-3">
                <label for="comentariosProjeto" class="form-label">
                    <i class="fas fa-comments"></i> Comentários ou Observações
                </label>
                <textarea class="form-control" name="feedback" rows="3" placeholder="Deixe algum comentário ou observação sobre o seu projeto..."></textarea>
            </div>

            <!-- Botão de Enviar -->
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-paper-plane"></i> Enviar Projeto
            </button>
        </form>
    </div><footer>
<?php include_once( '../visao/Rodape.php') ?>
    
    </footer>

    <!-- Link para o Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                   
   
 