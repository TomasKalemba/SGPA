<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/crud.php';
$crud = new crud();
$crud->verificarLoginPorCookie(); // verifica cookie "lembrar-me"

// Evitar cache e histórico para travar botão voltar
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

    <!-- CSS -->
    <link href="css/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/estiloChat.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
    .modal-content { background-color: #ffffff; box-shadow: 0 0 30px rgba(0,0,0,0.3); z-index:1055; }
    @media (max-width:576px){ #searchForm{ max-width:100%!important; margin:5px auto; } }
    </style>

    <script>
    // Travar botão voltar do navegador
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () {
        window.history.pushState(null, '', window.location.href);
    };
    </script>
</head>
<body class="sb-nav-fixed">
<!-- ... resto do body ... -->
