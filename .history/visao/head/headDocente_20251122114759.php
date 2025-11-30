<?php  
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'protege_pagina.php';
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

// 🔹 Verificar se já existe sessão
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    if (isset($_COOKIE['lembrar_usuario']) && isset($_COOKIE['lembrar_token'])) {
        $usuario_id = $_COOKIE['lembrar_usuario'];
        $token = $_COOKIE['lembrar_token'];

        $stmt = $conn->prepare("SELECT id, nome, tipo FROM usuarios WHERE id = ? AND ativo = 1");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['tipo'] === 'Docente') {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = $usuario['tipo'];
        } else {
            redirecionarLogin();
        }
    } else {
        redirecionarLogin();
    }
}

function redirecionarLogin() {
    $uri = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $uri .= $_SERVER['HTTP_HOST'];
    header('Location: '.$uri.'/SGPA/Visao/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SGPA-Docente</title>
    <link href="css/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/estiloChat.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        /* Pesquisa */
        #searchResults {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            display: none;
            z-index: 1000;
            padding: 20px;
            overflow-y: auto;
        }
        #resultsContainer {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            max-width: 80%;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #closeSearchResults {
            background-color: #dc3545;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            display: block;
            margin: 20px auto 0 auto;
            cursor: pointer;
        }

        /* Cards indicadores */
        .indicador {
            cursor: pointer;
        }
        .indicador:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body class="sb-nav-fixed">

<!-- ====================== NAVBAR ====================== -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <a class="navbar-brand ps-3" href="indexDocente.php">SGPA-<?= $_SESSION['tipo']?></a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-3" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Botão Voltar -->
    <a href="indexDocente.php" class="btn btn-outline-light btn-sm me-3" title="Voltar ao Home">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Pesquisa -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-3 my-2 my-md-0" id="searchForm">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Procurar por..." id="searchQuery" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <div id="searchResults">
        <h3>Resultados da Pesquisa</h3>
        <div id="resultsContainer"></div>
        <button class="btn btn-secondary" id="closeSearchResults">Fechar</button>
    </div>

    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <!-- Notificações -->
        <li class="nav-item">
            <a class="nav-link" href="NotificacaoDocente.php">
                <i class="fas fa-bell text-warning"></i>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) total FROM notificacoes WHERE docente_id=? AND status='Não Lida'");
                $stmt->execute([$_SESSION['id']]);
                $n = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($n['total'] > 0) echo '<span class="badge bg-danger">'.$n['total'].'</span>';
                ?>
            </a>
        </li>

        <!-- Usuário -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user fa-fw text-info"></i> <?= $_SESSION['nome']?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="perfilDocente.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cogs"></i> Definições</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="../Controlo/login.php?sair=logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </li>
    </ul>
</nav>
<!-- ====================== FIM NAVBAR ====================== -->

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Principal</div>
                    <a class="nav-link" href="indexDocente.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-primary"></i></div>
                        Home
                    </a>

                    <div class="sb-sidenav-menu-heading">Docente</div>
                    <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns text-success"></i></div>
                        Projectos
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down text-warning"></i></div>
                    </a>
                    <div class="collapse" id="collapseLayouts">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="NovoProjecto.php"><i class="fas fa-plus-circle text-info"></i> Novo Projecto</a>
                            <a class="nav-link" href="ListaProjectos.php"><i class="fas fa-list text-primary"></i> Lista De Projectos</a>
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

    <!-- ====================== CONTEÚDO PRINCIPAL ====================== -->
    <div id="layoutSidenav_content" class="p-4">

        <h2>Indicadores de Projectos</h2>

        <div class="row">
            <div class="col-md-2">
                <div class="card bg-primary text-white mb-4 indicador" data-info="Em Andamento">
                    <div class="card-body">Em Andamento</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white mb-4 indicador" data-info="Concluídos">
                    <div class="card-body">Concluídos</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white mb-4 indicador" data-info="Atrasados">
                    <div class="card-body">Atrasados</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white mb-4 indicador" data-info="Em Falta">
                    <div class="card-body">Em Falta</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-dark text-white mb-4 indicador" data-info="Total">
                    <div class="card-body">Total</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ====================== MODAL ====================== -->
<div class="modal fade" id="modalIndicador" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="tituloIndicador"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="conteudoIndicador">
        Carregando...
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>

<!-- ====================== SCRIPTS ====================== -->
<script>
// Toggle Sidebar
document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const body = document.body;
    sidebarToggle.addEventListener("click", function () {
        body.classList.toggle("sb-sidenav-toggled");
        localStorage.setItem('sb|sidebar-toggle', body.classList.contains('sb-sidenav-toggled'));
    });
    if (localStorage.getItem('sb|sidebar-toggle') === 'true') body.classList.add('sb-sidenav-toggled');
});

// Pesquisa AJAX
document.getElementById('searchForm').addEventListener('submit', function(e){
    e.preventDefault();
    let q = document.getElementById('searchQuery').value;
    fetch("search.php?query="+encodeURIComponent(q))
        .then(r => r.text())
        .then(d => {
            document.getElementById('resultsContainer').innerHTML = d;
            document.getElementById('searchResults').style.display = 'block';
        });
});
document.getElementById('closeSearchResults').addEventListener('click', function(){
    document.getElementById('searchResults').style.display = 'none';
});

// Modal indicadores
document.querySelectorAll('.indicador').forEach(card => {
    card.addEventListener('click', function () {
        let tipo = this.getAttribute('data-info');
        document.getElementById('tituloIndicador').innerHTML = tipo;
        document.getElementById('conteudoIndicador').innerHTML = "<p>Carregando...</p>";

        fetch("modalDados.php?tipo="+encodeURIComponent(tipo))
            .then(r => r.text())
            .then(d => document.getElementById('conteudoIndicador').innerHTML = d)
            .catch(e => document.getElementById('conteudoIndicador').innerHTML = "<p>Erro ao carregar dados.</p>");

        var modal = new bootstrap.Modal(document.getElementById('modalIndicador'));
        modal.show();
    });
});
</script>

</body>
</html>
