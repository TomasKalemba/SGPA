<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/crud.php';
$crud = new crud();
$crud->verificarLoginPorCookie(); // <- verifica se existe cookie de lembrar-me

// Verifica se o usuário está autenticado como Estudante
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Estudante') {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $uri = $protocolo . $_SERVER['HTTP_HOST'] . '/SGPA/visao/login.php';
    header('Location: ' . $uri);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SGPA-Estudante</title>
    <link href="css/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/estiloChat.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

<style type="text/css">

    .modal-content {
  background-color: #ffffff;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
  z-index: 1055;
}
    
@media (max-width: 576px) {
    #searchForm {
        max-width: 100% !important;
        margin: 5px auto;
    }
}


</style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="indexEstudante.php">SGPA-<?= $_SESSION['tipo']?></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
<!-- Formulário de Pesquisa (no topo da página) -->
<form class="form-inline ms-auto me-0 my-2 my-md-0 flex-grow-1" id="searchForm" style="max-width: 300px;">

    <div class="input-group">
        <input class="form-control" type="text" placeholder="Procurar por..." id="searchQuery" aria-label="Campo de pesquisa" />
        <button class="btn btn-primary" id="btnNavbarSearch" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>
</form>

<!-- Modal de Resultados da Pesquisa -->
<div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="searchResultsModalLabel">Resultados da Pesquisa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingSpinner" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">A carregar...</span>
                    </div>
                    <p class="mt-2">A procurar resultados...</p>
                </div>
                <div id="resultsContainer" class="mt-3"></div>
            </div>
            <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
</div>
        </div>
    </div>
</div>


        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <!-- Notificações -->
            <li class="nav-item">
                <a class="nav-link" href="NotificacaoEstudante.php">
                    <i class="fas fa-bell text-warning"></i>
                    <?php
                    require_once '../modelo/crud.php';
                    $crud = new crud();
                    $notificacoes_nao_lidas = 0;
                    if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'Estudante') {
                        try {
                            $conn = $crud->getConexao();
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM notificacoes WHERE estudante_id = ? AND status = 'Não Lida'");
                            $stmt->execute([$_SESSION['id']]);
                            $notificacoes_nao_lidas = $stmt->fetchColumn();
                        } catch (PDOException $e) {
                            error_log("Erro ao contar notificações não lidas do estudante: " . $e->getMessage());
                        }
                    }
                    if ($notificacoes_nao_lidas > 0) {
                        echo '<span class="badge bg-danger">' . $notificacoes_nao_lidas . '</span>';
                    }
                    ?>
                </a>
            </li>

            <!-- Perfil -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw text-info"></i> <?= $_SESSION['nome'] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../visao/PerfilAluno.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a class="dropdown-item" href="../visao/Definicoes.php"><i class="fas fa-cogs"></i> Definições</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="../Controlo/login.php?sair=logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="indexEstudante.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-primary"></i></div>
                            Home
                        </a>
                        <div class="sb-sidenav-menu-heading"><?=$_SESSION['tipo']?></div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns text-success"></i></div>
                            Meus Projectos
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down text-warning"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="VerProjectos.php"><i class="fas fa-eye text-info"></i> Ver Projectos</a>
                                <a class="nav-link" href="EnviarProjecto.php"><i class="fas fa-upload text-success"></i> Enviar Projecto</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open text-primary"></i></div>
                            Documentos
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down text-danger"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link" href="historico_submissoes.php"><i class="fas fa-file-alt text-primary"></i> historico_submissoes</a>
                                <a class="nav-link" href="Avaliacao.php"><i class="fas fa-pencil-alt text-success"></i> Avaliações</a>
                                <a class="nav-link" href="Definicoes.php"><i class="fas fa-cogs text-info"></i> Definições</a>
                            </nav>
                            <a class="nav-link" href="Contacto.php"><i class="fas fa-phone-alt text-info"></i> Contacto</a>
                            <a class="nav-link" href="Sobre.php"><i class="fas fa-info-circle text-primary"></i> Sobre</a>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logado como: <a class="navbar-brand ps-3" href="index.php"><?= $_SESSION['nome']?></a></div>
                </div>
            </nav>
        </div>

<!-- Scripts essenciais -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-mQ93j8VLDjRjq6tr5Kk3sUUX0m5TYW9ijUYJSKkfUMnE9HXMciEk0v3EbbF5fpDk" crossorigin="anonymous"></script>

<!-- Script de Pesquisa -->
<script>
document.getElementById('searchForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const query = document.getElementById('searchQuery').value.trim();
    const button = document.getElementById('btnNavbarSearch');
    const resultsContainer = document.getElementById('resultsContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const modalElement = document.getElementById('searchResultsModal');
    const modal = new bootstrap.Modal(modalElement);

    if (!query) {
        resultsContainer.innerHTML = "<p class='text-danger'>Por favor, introduza um termo para pesquisar.</p>";
        modal.show();
        return;
    }

    button.disabled = true;
    resultsContainer.innerHTML = '';
    loadingSpinner.style.display = 'block';
    modal.show();

    fetch(`search.php?query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            loadingSpinner.style.display = 'none';
            resultsContainer.innerHTML = data;
        })
        .catch(error => {
            loadingSpinner.style.display = 'none';
            resultsContainer.innerHTML = `<p class='text-danger'>Erro ao carregar os resultados: ${error.message}</p>`;
        })
        .finally(() => {
            button.disabled = false;
        });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggle = document.getElementById("sidebarToggle");
        const body = document.body;
        const sidenav = document.getElementById("layoutSidenav_nav");

        sidebarToggle.addEventListener("click", function (e) {
            e.preventDefault();
            body.classList.toggle("sb-sidenav-toggled");

            // Salva preferência no localStorage (opcional)
            localStorage.setItem('sb|sidebar-toggle', body.classList.contains('sb-sidenav-toggled'));
        });

        // Restaura a preferência ao carregar
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            body.classList.add('sb-sidenav-toggled');
        }
    });
</script>

</body>
</html>
