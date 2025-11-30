<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../visao/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        
        $nomeOriginal = $_FILES['arquivo']['name'];
        $tmpName = $_FILES['arquivo']['tmp_name'];

        // Caminho da pasta uploads
        $pastaDestino = __DIR__ . '/../uploads/';
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0777, true);
        }

        // Nome único para o arquivo
        $novoNome = uniqid() . "_" . basename($nomeOriginal);
        $caminhoFinal = $pastaDestino . $novoNome;

        // Move o arquivo
        if (move_uploaded_file($tmpName, $caminhoFinal)) {
            
            // Salva no banco de dados
            require_once('../Modelo/submissoes.php');
            $subDAO = new submissoes();

            $projetoId = $_POST['projeto_id'] ?? null;
            $usuarioId = $_SESSION['id'];

            if ($projetoId) {
                $subDAO->salvarArquivo($projetoId, $novoNome, $usuarioId);
                $_SESSION['mensagem'] = "📂 Arquivo enviado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "⚠️ Projeto não informado!";
            }
        } else {
            $_SESSION['mensagem'] = "❌ Erro ao mover o arquivo para a pasta destino.";
        }
    } else {
        $_SESSION['mensagem'] = "⚠️ Nenhum arquivo enviado ou erro no upload!";
    }

    // Redireciona de volta
    header("Location: ../visao/projetos.php");
    exit;
}

