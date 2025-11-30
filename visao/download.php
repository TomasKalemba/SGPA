<?php
session_start();

// Verifica se usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../visao/login.php");
    exit;
}

// Verifica se o parâmetro do arquivo foi passado
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Arquivo não especificado.");
}

$arquivo = basename($_GET['file']); // segurança contra path traversal
$caminho = "../uploads/" . $arquivo;

if (!file_exists($caminho)) {
    die("Arquivo não encontrado.");
}

// Força o download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $arquivo . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($caminho));
readfile($caminho);
exit;
