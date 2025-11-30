<?php
// protege_pagina.php
session_start();

// -------------------------
// 1️⃣ Verifica se o usuário está logado
// -------------------------
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    // Redireciona para login se não estiver logado
    header("Location: login.php");
    exit;
}

// -------------------------
// 2️⃣ Evita cache no navegador
// -------------------------
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema SGPA</title>
    <!-- Outros meta tags e CSS -->
</head>
<body>

<!-- -------------------------
3️⃣ Bloqueio do botão voltar via JavaScript
------------------------- -->
<script>
    // Adiciona estado atual no histórico
    history.pushState(null, null, location.href);

    // Quando o usuário tenta voltar, vai para frente
    window.onpopstate = function () {
        history.go(1);
    };
</script>
