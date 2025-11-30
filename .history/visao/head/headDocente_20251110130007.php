<?php  
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

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
            redirecionarLogin();
        }
    } else {
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
<title>SGPA-Docente</title>
<link href="css/style.min.css" rel="stylesheet" />
<link href="css/styles.css" rel="stylesheet" />
<link href="css/estiloChat.css" rel="stylesheet" />
<script src="js/all.js" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

<style> 
/* ======= SEU CSS ORIGINAL AQUI ======= */
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
}
#resultsContainer h3 { text-align:center; }
.project { margin-bottom: 20px; padding: 15px; background:#f9f9f9; border-radius:8px; }
#btnNavbarSearch { background:#007bff; border:none; }
#closeSearchResults { background:#dc3545; color:#fff; padding:10px 20px; border-radius:5px; margin:auto; display:block; }
</style>
</head>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
<a class="navbar-brand ps-3" href="indexDocente.php">SGPA-<?= $_SESSION['tipo']?></a>

<button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
<i class="fas fa-bars"></i></button>

<form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" id="searchForm">
<div class="input-group">
<input class="form-control" type="text" placeholder="Procurar por..." id="searchQuery"/>
<button class="btn btn-primary" id="btnNavbarSearch" type="submit">
<i class="fas fa-search"></i></button>
</div>
</form>

<div id="searchResults">
<h3>Resultados da Pesquisa</h3>
<div id="resultsContainer"></div>
<button class="btn btn-secondary" id="closeSearchResults">Fechar</button>
</div>

<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
<li class="nav-item"><a class="nav-link" href="NotificacaoDocente.php">
<i class="fas fa-bell text-warning"></i>
<?php
$crud = new crud();
if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'Docente') {
$conn = $crud->getConexao();
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notificacoes WHERE docente_id = ? AND status = 'Não Lida'");
$stmt->execute([$_SESSION['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['total'] > 0) echo '<span class="badge bg-danger">'.$row['total'].'</span>';
}
?>
</a></li>

<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" data-bs-toggle="dropdown">
<i class="fas fa-user fa-fw text-info"></i> <?= $_SESSION['nome']?></a>
<ul class="dropdown-menu dropdown-menu-end">
<li><a class="dropdown-item" href="perfilDocente.php"><i class="fas fa-user"></i> Perfil</a></li>
<li><a class="dropdown-item" href="#"><i class="fas fa-cogs"></i> Definições</a></li>
<li><hr class="dropdown-divider" /></li>
<li><a class="dropdown-item" href="../Controlo/login.php?sair=logout">
<i class="fas fa-sign-out-alt"></i> Sair</a></li>
</ul>
</li>
</ul>
</nav>

<!-- ✅ BOTÃO DE VOLTAR AQUI -->
<div class="container mt-3">
<a href="javascript:history.back()" class="btn btn-outline-primary">
<i class="fas fa-arrow-left"></i> Voltar
</a>
</div>
<!-- ✅ FIM DO BOTÃO -->

<div id="layoutSidenav">
<div id="layoutSidenav_nav">
<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
<div class="sb-sidenav-menu">
<div class="nav">
<div class="sb-sidenav-menu-heading">Principal</div>
<a class="nav-link" href="indexDocente.php">
<div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt text-primary"></i></div>
Home</a>

<div class="sb-sidenav-menu-heading"><?= $_SESSION['tipo']?></div>

<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts">
<div class="sb-nav-link-icon"><i class="fas fa-columns text-success"></i></div>
Projectos<i class="fas fa-angle-down text-warning"></i></a>

<div class="collapse" id="collapseLayouts">
<nav class="sb-sidenav-menu-nested nav">
<a class="nav-link" href="NovoProjecto.php"><i class="fas fa-plus-circle text-info"></i> Novo Projecto</a>
<a class="nav-link" href="ListaProjectos.php"><i class="fas fa-list text-primary"></i> Lista De Projectos</a>
</nav>
</div>

<div class="sb-sidenav-menu-heading">Informações</div>
<a class="nav-link" href="Contacto.php">
<div class="sb-nav-link-icon"><i class="fas fa-phone-alt text-info"></i></div> Contacto</a>
<a class="nav-link" href="Sobre.php">
<div class="sb-nav-link-icon"><i class="fas fa-info-circle text-primary"></i></div> Sobre</a>
</div>
</div>

<div class="sb-sidenav-footer">
<div class="small">Logado como:
<a class="navbar-brand ps-3" href="index.php"><?= $_SESSION['nome']?></a></div>
</div>
</nav>
</div>

<script>
// Pesquisa AJAX
document.getElementById('searchForm').addEventListener('submit', function(e){
    e.preventDefault();
    var q = document.getElementById('searchQuery').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?query='+encodeURIComponent(q), true);
    xhr.onload = function(){
        document.getElementById('resultsContainer').innerHTML = xhr.responseText;
        document.getElementById('searchResults').style.display = 'block';
    };
    xhr.send();
});
document.getElementById('closeSearchResults').addEventListener('click',()=>{
document.getElementById('searchResults').style.display='none';
});
</script>

<script>
document.addEventListener("DOMContentLoaded", ()=>{
const st = document.getElementById("sidebarToggle");
const body = document.body;
st.addEventListener("click", e=>{
e.preventDefault();
body.classList.toggle("sb-sidenav-toggled");
localStorage.setItem('sb|sidebar-toggle',body.classList.contains('sb-sidenav-toggled'));
});
if(localStorage.getItem('sb|sidebar-toggle')==='true'){ body.classList.add('sb-sidenav-toggled'); }
});
</script>

</body>
</html>

