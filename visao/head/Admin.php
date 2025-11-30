<?php  
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Garante que só Admin acesse
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    header("Location: ../Visao/login.php");
    exit;
}
require_once 'protege_pagina.php';

require_once '../modelo/crud.php';

$crud = new crud();
$conexao = $crud->getConexao();

// Conta quantos docentes ainda estão pendentes (ativo = 0)
$sql = "SELECT COUNT(*) AS total FROM usuarios WHERE tipo = 'Docente' AND ativo = 0";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$notificacoes_nao_lidas = $resultado['total'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>SGPA-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="css/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/estiloChat.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>     
#searchResults {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(30, 30, 30, 0.6);
    display: none;
    z-index: 1000;
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease-in-out;
    padding: 0;
}

#resultsContainer {
    background: #fff;
    padding: 20px 25px;
    border-radius: 10px;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

#resultsContainer h3 {
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
}

.project {
    margin-bottom: 15px;
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    transition: background 0.2s ease-in-out;
}
.project:hover {
    background: #eef4ff;
}

#closeSearchResults {
    background: #dc3545;
    border: none;
    color: white;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    display: block;
    margin: 15px auto 0;
}
#closeSearchResults:hover {
    background: #b02a37;
}

@media (max-width: 576px) {
    #searchForm {
        max-width: 100% !important;
        margin: 5px auto;
    }
    #searchForm .input-group {
        flex-wrap: nowrap;
    }
}
    </style>
</head>

<body class="sb-nav-fixed">

    <!-- ================= NAVBAR ================= -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="indexAdmin.php">SGPA-<?= $_SESSION['tipo']?></a>

        <!-- Sidebar Toggle -->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Botão Voltar ao Home (ADICIONADO) -->
        <a href="indexAdmin.php" class="btn btn-outline-light btn-sm me-3" title="Voltar ao Home">
            <i class="bi bi-arrow-left-circle"></i>
        </a>

        <!-- Formulário de pesquisa -->
        <form class="form-inline ms-auto me-0 my-2 my-md-0" id="searchForm">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Procurar por..." id="searchQuery" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Container Results -->
        <div id="searchResults" style="display:none;">
            <h3>Resultados da Pesquisa</h3>
            <div id="resultsContainer"></div>
            <button class="btn btn-secondary" id="closeSearchResults">Fechar</button>
        </div>

        <!-- Navbar -->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">

            <!-- Notification -->
            <li class="nav-item">
                <a class="nav-link" href="NotificacaoAdmin.php">
                    <i class="fas fa-bell text-warning"></i>
                    <?php if ($notificacoes_nao_lidas > 0): ?>
                        <span class="badge bg-danger"><?= $notificacoes_nao_lidas ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <!-- User -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                   <i class="fas fa-user fa-fw text-info"></i> <?= $_SESSION['nome']?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="PerfilAdmin.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cogs"></i> Definições</a></li>      
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="../Controlo/login.php?sair=logout">
                        <i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </li>

        </ul>
    </nav>
    <!-- ================= FIM NAVBAR ================= -->


    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="indexAdmin.php?">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-primary"></i></div>
                            Home
                        </a>

                        <div class="sb-sidenav-menu-heading"><?=$_SESSION['tipo']?></div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                           data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns text-success"></i></div>
                            Projectos
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down text-warning"></i></div>
                        </a>

                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" 
                             data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="ActivarConta.php"><i class="fas fa-plus-circle text-info"></i> Activar Contas</a>
                                <a class="nav-link" href="EliminarEstudante.php"><i class="fas fa-plus-circle text-info"></i> Estudantes</a>
                                <a class="nav-link" href="NovoProjecto.php"><i class="fas fa-plus-circle text-info"></i> Novo Projecto</a>
                                <a class="nav-link" href="ListaProjectos.php"><i class="fas fa-list text-primary"></i> Lista De Projectos</a>
                                <a class="nav-link" href="VerProjectos.php"><i class="fas fa-eye text-info"></i> Ver Projectos</a>
                                <a class="nav-link" href="EnviarProjecto.php"><i class="fas fa-upload text-success"></i> Enviar Projecto</a>
                                <a class="nav-link" href="SubmissoesA.php"><i class="fas fa-paper-plane text-warning"></i> Projectos Enviados</a>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading">Informações</div>

                        <a class="nav-link" href="Contacto.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-phone-alt text-info"></i></div>
                            Contacto
                        </a>

                        <a class="nav-link" href="Sobre.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-info-circle text-primary"></i></div>
                            Sobre
                        </a>

                    </div>
                </div>

                <div class="sb-sidenav-footer">
                    <div class="small">Logado como: 
                        <a class="navbar-brand ps-3" href="index.php"><?= $_SESSION['nome']?></a>
                    </div>
                </div>

            </nav>
        </div>


<script>
// Pesquisa AJAX
document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var query = document.getElementById('searchQuery').value;

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?query=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var resultsContainer = document.getElementById('resultsContainer');
            var searchResultsDiv = document.getElementById('searchResults');
            resultsContainer.innerHTML = xhr.responseText;
            searchResultsDiv.style.display = 'block';
        }
    };
    xhr.send();
});

document.getElementById('closeSearchResults').addEventListener('click', function() {
    document.getElementById('searchResults').style.display = 'none';
});
</script>

<script>
// Sidebar Toggle
document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const body = document.body;

    sidebarToggle.addEventListener("click", function (e) {
        e.preventDefault();
        body.classList.toggle("sb-sidenav-toggled");
        localStorage.setItem('sb|sidebar-toggle', body.classList.contains('sb-sidenav-toggled'));
    });

    if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        body.classList.add('sb-sidenav-toggled');
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
