<?php include_once('head/headEstudante.php') ?>        
            <div id="layoutSidenav_content">
                <br>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><?=$_SESSION['tipo']?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"><?=$_SESSION['tipo']?></li>
                        </ol>
                    </div>
                     <center><h1>Documentos</h1></center> 
                     <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
    <title>Criar Novo Projeto</title>
    <link href="css/styles.css" rel="stylesheet" />
        <script src="js/all.js" crossorigin="anonymous"></script>
    <!-- Link para o Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Customizado -->
    <style>
        body {
            padding-top: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-container h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Criar Novo Projeto</h2>
        <form action="/submit" method="POST">
            <!-- Campo ID -->
            <div class="form-group">
                <label for="id">ID do Projeto</label>
                <input type="text" class="form-control" id="id" name="id" placeholder="Digite o ID do projeto" required>
            </div>
            
            <!-- Campo Título -->
            <div class="form-group">
                <label for="titulo">Título do Projeto</label>
                <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o título do projeto" required>
            </div>

            <!-- Campo Descrição -->
            <div class="form-group">
                <label for="descricao">Descrição do Projeto</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" placeholder="Descreva o projeto" required></textarea>
            </div>

            <!-- Campo Docente ID -->
            <div class="form-group">
                <label for="docente_id">ID do Docente</label>
                <input type="text" class="form-control" id="docente_id" name="docente_id" placeholder="Digite o ID do docente responsável" required>
            </div>

            <!-- Campo Data de Criação -->
            <div class="form-group">
                <label for="data_criacao">Data de Criação</label>
                <input type="date" class="form-control" id="data_criacao" name="data_criacao" required>
            </div>

            <!-- Campo Prazo -->
            <div class="form-group">
                <label for="prazo">Prazo</label>
                <input type="date" class="form-control" id="prazo" name="prazo" required>
            </div>

            <!-- Botão de Enviar -->
            <button type="submit" class="btn btn-primary btn-block">Criar Projeto</button>
        </form>
    </div>
</div>            
                       </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacidade Politicas</a>
                                &middot;
                                <a href="#">Termos &amp; Condições</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
