<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Impede cache no navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['id']) || empty($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}
?>
