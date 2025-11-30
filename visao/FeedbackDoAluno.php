<?php 

if (isset($_SESSION['tipo']) &&  $_SESSION['tipo'] == 'Admin') {
    include_once( 'head/Admin.php');
}else{
    include_once( 'head/headDocente.php');
}
?>        
            <div id="layoutSidenav_content">
                <br>
                    
                    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedback dos Estudantes</title>

    <!-- Já assumimos que o Bootstrap e FontAwesome estão integrados -->
    <style>
        body {
            padding-top: 20px;
            background-color: #f4f7fc;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .card {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 24px;
            font-weight: 500;
            border-radius: 8px 8px 0 0;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table th {
            background-color: #f8f9fa;
            color: #007bff;
            font-weight: 600;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f2f7fc;
        }
        .table-striped tbody tr:hover {
            background-color: #e9ecef;
        }
        .btn-group {
            margin-bottom: 20px;
        }
        .action-btns button {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            padding: 5px;
            margin-right: 5px;
        }
        .action-btns button i {
            font-size: 16px;
        }
        .file-upload-btn {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <i class="fas fa-comment-dots"></i> Feedback dos Estudantes
        </div>
        <div class="card-body">
            <!-- Filtro de Feedback -->
            <div class="btn-group mb-4" role="group">
                <button type="button" class="btn btn-secondary">Filtrar por Estudante</button>
                <button type="button" class="btn btn-secondary">Filtrar por Projeto</button>
                <button type="button" class="btn btn-secondary">Filtrar por Data</button>
            </div>
            
            <!-- Tabela de Feedback -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt"></i> Data</th>
                        <th><i class="fas fa-user"></i> Estudante</th>
                        <th><i class="fas fa-project-diagram"></i> Projeto</th>
                        <th><i class="fas fa-comment-alt"></i> Feedback</th>
                        <th><i class="fas fa-file-alt"></i> Arquivo</th>
                        <th><i class="fas fa-cogs"></i> Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Exemplo de Feedback 1 -->
                    <tr>
                        <td>12/11/2024</td>
                        <td>João da Silva</td>
                        <td>Inteligência Artificial no Ensino</td>
                        <td>Projeto muito bem desenvolvido, gostei da metodologia aplicada.</td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm" title="Ver Arquivo"><i class="fas fa-file-download"></i> Download</a>
                        </td>
                        <td class="action-btns">
                            <button class="btn btn-info" title="Ver Detalhes"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-warning" title="Responder Feedback"><i class="fas fa-reply"></i></button>
                        </td>
                    </tr>
                    <!-- Exemplo de Feedback 2 -->
                    <tr>
                        <td>11/11/2024</td>
                        <td>Maria Souza</td>
                        <td>Análise de Dados com Python</td>
                        <td>O projeto foi bem executado, mas poderia ter mais detalhes sobre os testes realizados.</td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm" title="Ver Arquivo"><i class="fas fa-file-download"></i> Download</a>
                        </td>
                        <td class="action-btns">
                            <button class="btn btn-info" title="Ver Detalhes"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-warning" title="Responder Feedback"><i class="fas fa-reply"></i></button>
                        </td>
                    </tr>
                    <!-- Mais feedbacks podem ser adicionados -->
                </tbody>
            </table>

            <!-- Formulário de Envio de Arquivos -->
            <div class="mt-4">
                <h5><i class="fas fa-upload"></i> Enviar Feedback e Arquivos</h5>
                <form action="/enviar-feedback" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="estudante">Estudante</label>
                        <select class="form-control" id="estudante" name="estudante" required>
                            <option value="EST001">João da Silva</option>
                            <option value="EST002">Maria Souza</option>
                            <option value="EST003">Carlos Pereira</option>
                            <!-- Adicione mais opções de estudantes conforme necessário -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="feedback">Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Escreva seu feedback aqui..." required></textarea>
                    </div>
                    <div class="form-group file-upload-btn">
                        <label for="arquivo">Enviar Arquivo</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf,.doc,.docx,.txt,.jpg,.png" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Enviar Feedback</button>
                </form>
            </div>
        </div>
    </div>
</div>
<footer>
<?php include_once( '../visao/Rodape.php') ?>
    
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
