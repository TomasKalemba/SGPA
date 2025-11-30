<?php  
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

<?php
// 🔹 Contador de notificações não lidas
try {
    $stmtNaoLidas = $conn->prepare("
        SELECT COUNT(*) as total_nao_lidas
        FROM notificacoes
        WHERE docente_id = ?
          AND estudante_id IS NOT NULL
          AND (LOWER(status) = 'não lida' OR LOWER(status) = 'nao lida')
          AND mensagem LIKE '%submeteu o projeto%'
    ");
    $stmtNaoLidas->execute([$_SESSION['id']]);
    $totalNaoLidas = $stmtNaoLidas->fetch(PDO::FETCH_ASSOC)['total_nao_lidas'] ?? 0;
} catch (PDOException $e) {
    $totalNaoLidas = 0;
}
?>

<!-- Navbar-->
<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
    <!-- Notification Icon -->
    <li class="nav-item dropdown">
        <a class="nav-link position-relative" href="NotificacaoDocente.php" id="notificacoesDropdown" role="button">
            <i class="fas fa-bell text-warning"></i>
            <?php if ($totalNaoLidas > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $totalNaoLidas ?>
                    <span class="visually-hidden">novas notificações</span>
                </span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<style>
.nav-link .badge {
    font-size: 0.7rem;
    padding: 0.25em 0.4em;
}
</style>
// 🔹 Verificar se já existe sessão
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {

    // 🔹 Caso não tenha sessão, mas exista cookie de login
    if (isset($_COOKIE['lembrar_usuario']) && isset($_COOKIE['lembrar_token'])) {
        $usuario_id = $_COOKIE['lembrar_usuario'];
        $token = $_COOKIE['lembrar_token'];

        // Consulta usuário no banco
        $stmt = $conn->prepare("SELECT id, nome, tipo FROM usuarios WHERE id = ? AND ativo = 1");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['tipo'] === 'Docente') {
            // 🔹 Se tudo ok, cria sessão automaticamente
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = $usuario['tipo'];
        } else {
            // 🔹 Caso cookie inválido → redireciona para login
            redirecionarLogin();
        }
    } else {
        // 🔹 Se não tem sessão nem cookie → login
        redirecionarLogin();
    }
}

function redirecionarLogin() {
    if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
        $uri = 'https://'; 
    } else {
        $uri = 'http://';
    }
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
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>SGPA-Docente</title>
        <link href="css/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/estiloChat.css" rel="stylesheet" />
        <script src="js/all.js" crossorigin="anonymous"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style> 
        
/* Estilos para a div que contém os resultados */
#searchResults {
    position: fixed;  /* Fixa a div na tela */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Fundo escuro semitransparente */
    display: none;  /* Começa oculta */
    z-index: 1000;  /* Garante que fique acima de outros elementos */
    padding: 20px;
    box-sizing: border-box; /* Inclui o padding no tamanho total da div */
    overflow-y: auto;  /* Permite rolar se os resultados forem muitos */
} 

/* Centraliza o container dos resultados */
#resultsContainer {
    background-color: #fff;  /* Fundo branco */
    padding: 30px;
    border-radius: 8px;
    max-width: 80%;  /* Limita a largura máxima */
    margin: 0 auto;  /* Centraliza horizontalmente */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);  /* Sombra suave */
    overflow-y: auto;
}

/* Estilo para o título de resultados */
#resultsContainer h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #000;  /* Cor preta para o título */
}

/* Estilo para cada item de projeto */
.project {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #f9f9f9;
}

/* Cor preta para o título do projeto */
.project h5 {
    font-size: 1.2rem;
    color: #000;  /* Cor preta para o título do projeto */
    margin-bottom: 10px;
}

/* Cor preta para a descrição e outros campos */
.project p {
    margin: 5px 0;
    color: #000;  /* Cor preta para o texto da descrição, docente, estudantes, etc */
}

/* Estilo para campos como "docente", "estudantes" e outros, se necessário */
.project span {
    color: #000;  /* Cor preta para qualquer span usado dentro do projeto */
}

/* Estilo para o caso de não encontrar resultados */
#resultsContainer p {
    color: #ff0000;  /* Cor vermelha para erro */
    text-align: center;
    font-size: 1.1rem;
    font-weight: bold;
}

/* Estilo do botão de pesquisa */
#btnNavbarSearch {
    background-color: #007bff;
    border: none;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}

#btnNavbarSearch:hover {
    background-color: #0056b3;
}

/* Estilo do botão de fechar (fora da div de resultados) */
#closeSearchResults {
    background-color: #dc3545;  /* Cor vermelha */
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    display: block;
    margin: 20px auto 0 auto;  /* Centraliza e aplica uma margem superior */
}

#closeSearchResults:hover {
    background-color: #c82333;  /* Cor vermelha mais escura no hover */
}


</style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="indexDocente.php">SGPA-<?= $_SESSION['tipo']?></a>
            
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
         <!-- Formulário de pesquisa -->
<form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" id="searchForm">
    <div class="input-group">
        <input class="form-control" type="text" placeholder="Procurar por..." id="searchQuery" aria-label="Search for..." aria-describedby="btnNavbarSearch" />
        <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
    </div>
</form>

<!-- Container para mostrar os resultados da pesquisa -->
<div id="searchResults" style="display: none;">
    <h3>Resultados da Pesquisa</h3>
    <div id="resultsContainer"></div> <!-- Aqui será injetado o conteúdo da pesquisa -->
    <!-- Botão Fechar fora da div de resultados -->
    <button class="btn btn-secondary" id="closeSearchResults">Fechar</button>
</div>



            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <!-- Notification Icon -->
<li class="nav-item">
    <a class="nav-link" href="NotificacaoDocente.php">
        <i class="fas fa-bell text-warning"></i>
        <?php
        require_once '../modelo/crud.php';
        $crud = new crud();

        if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'Docente') {
            $docente_id = $_SESSION['id'];

            try {
                $conn = $crud->getConexao();
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notificacoes WHERE docente_id = ? AND status = 'Não Lida'");
                $stmt->execute([$docente_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $notificacoes_nao_lidas = $row['total'];

                if ($notificacoes_nao_lidas > 0) {
                    echo '<span class="badge bg-danger">' . $notificacoes_nao_lidas . '</span>';
                }
            } catch (PDOException $e) {
                // Em ambiente de produção, não mostrar erros ao usuário
                error_log("Erro ao buscar notificações não lidas: " . $e->getMessage());
            }
        }
        ?>
    </a>
</li>

                <!-- User Icon -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw text-info"></i> <?= $_SESSION['nome']?></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="perfilDocente.php"><i class="fas fa-user"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="#!"><i class="fas fa-cogs"></i> Definições</a></li>
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
                            <a class="nav-link" href="indexDocente.php?">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-primary"></i></div> <!-- Cor azul -->
                                Home
                            </a>
                            <div class="sb-sidenav-menu-heading"><?=$_SESSION['tipo']?></div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns text-success"></i></div> <!-- Cor verde -->
                                Projectos
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down text-warning"></i></div> <!-- Cor amarela -->
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="NovoProjecto.php"><i class="fas fa-plus-circle text-info"></i> Novo Projecto</a> <!-- Cor azul -->
                                    <a class="nav-link" href="ListaProjectos.php"><i class="fas fa-list text-primary"></i> Lista De Projectos</a> <!-- Cor azul -->
                                   
                                </nav>
                            </div>
                            
                            <div class="sb-sidenav-menu-heading">Informações</div>
                            <a class="nav-link" href="Contacto.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-phone-alt text-info"></i></div> <!-- Cor azul -->
                                Contacto
                            </a>
                            <a class="nav-link" href="Sobre.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-info-circle text-primary"></i></div> <!-- Cor azul -->
                                Sobre
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logado como:<a class="navbar-brand ps-3" href="index.php"><?= $_SESSION['nome']?></a></div>
                    </div>
                </nav>
            </div> 
            <script> 
               // Ao submeter o formulário de pesquisa
document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Evitar o envio do formulário tradicional

    var query = document.getElementById('searchQuery').value;

    // Enviar a pesquisa para o back-end via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?query=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Exibir os resultados da pesquisa
            var resultsContainer = document.getElementById('resultsContainer');
            var searchResultsDiv = document.getElementById('searchResults');
            resultsContainer.innerHTML = xhr.responseText;  // Inserir os resultados retornados

            // Mostrar a div com os resultados
            searchResultsDiv.style.display = 'block';
        } else {
            console.error('Erro na requisição: ' + xhr.status);
        }
    };
    xhr.send();
});

// Fechar os resultados ao clicar no botão de fechar
document.getElementById('closeSearchResults').addEventListener('click', function() {
    document.getElementById('searchResults').style.display = 'none';  // Esconde a div com os resultados
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
</script>
