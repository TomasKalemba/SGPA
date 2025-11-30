<?php
require_once("../modelo/crud.php");
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Função para envio de email
function enviarEmail($destinatario, $assunto, $mensagemHtml) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tomasferrazkalemba@gmail.com';
        $mail->Password = 'dmzt atyo jbii zmsk'; // senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tomasferrazkalemba@gmail.com', 'SGPA - Notificações');
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHtml;

        $mail->send();
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    }
}

// PROCESSA FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitização básica
    $titulo       = trim($_POST['titulo'] ?? '');
    $descricao    = trim($_POST['descricao'] ?? '');
    $docente_id   = intval($_POST['docente_id'] ?? 0);
    $data_criacao = $_POST['data_criacao'] ?? date('Y-m-d');
    $prazo        = $_POST['prazo'] ?? '';
    $estudantes   = $_POST['estudantes'] ?? [];

    // Função para gravar dados do formulário em caso de erro
    function gravarFormulario() {
        global $titulo, $descricao, $data_criacao, $prazo, $estudantes;
        $_SESSION['form_data'] = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'data_criacao' => $data_criacao,
            'prazo' => $prazo,
            'estudantes' => $estudantes
        ];
    }

    // === VALIDAÇÕES DE CAMPOS OBRIGATÓRIOS ===
    if (empty($titulo) || empty($descricao) || empty($docente_id) || empty($prazo) || empty($estudantes)) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ Preencha todos os campos obrigatórios.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE TÍTULO ===
    if (preg_match('/^\d+$/', $titulo)) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ O título do projeto não pode conter apenas números.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE DESCRIÇÃO ===
    if (preg_match('/^\d+$/', $descricao)) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ A descrição do projeto não pode conter apenas números.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE TAMANHO DE TEXTO ===
    if (mb_strlen($titulo) > 255) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ O título não pode ter mais de 255 caracteres.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    if (mb_strlen($descricao) < 10) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ A descrição deve ter pelo menos 10 caracteres.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE DATAS ===
    if (strtotime($prazo) < strtotime($data_criacao)) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ O prazo não pode ser anterior à data de criação.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VERIFICAÇÃO DE TÍTULO DUPLICADO ===
    $crud = new crud();
    $conn = $crud->getConexao();

    $stmtVerificaTitulo = $conn->prepare("SELECT COUNT(*) FROM projectos WHERE titulo = ?");
    $stmtVerificaTitulo->execute([$titulo]);
    if ($stmtVerificaTitulo->fetchColumn() > 0) {
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "⚠️ Já existe um projeto com o título '$titulo'. Escolha um título diferente.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE UPLOAD DE ARQUIVO ===
    $caminhoArquivo = null;
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $pastaUpload = "../uploads/";
        if (!file_exists($pastaUpload)) mkdir($pastaUpload, 0777, true);

        $nomeOriginal = $_FILES['arquivo']['name'];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $tamanhoArquivo = $_FILES['arquivo']['size'];

        // Tipos permitidos
        $tiposPermitidos = ['pdf', 'docx', 'pptx', 'jpg', 'png'];
        if (!in_array($extensao, $tiposPermitidos)) {
            gravarFormulario();
            $_SESSION['mensagem-novoprojecto'] = "⚠️ Tipo de arquivo inválido. Permitidos: PDF, DOCX, PPTX, JPG, PNG.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }

        // Limite de 10MB
        if ($tamanhoArquivo > 10 * 1024 * 1024) {
            gravarFormulario();
            $_SESSION['mensagem-novoprojecto'] = "⚠️ O arquivo não pode ultrapassar 10MB.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }

        // Nome único para evitar sobrescrita
        $nomeFinal = time() . '_' . uniqid() . '.' . $extensao;
        $caminhoFinal = $pastaUpload . $nomeFinal;

        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoFinal)) {
            $caminhoArquivo = $nomeFinal; // ⚡ salva apenas o nome do arquivo
        } else {
            gravarFormulario();
            $_SESSION['mensagem-novoprojecto'] = "⚠️ Falha ao salvar o arquivo.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }
    } else {
        gravarFormulario(); // garante que os outros campos ficam preenchidos
    }

    // === SALVAR NO BANCO ===
    try {
        $conn->beginTransaction();

        // Inserir projeto
        $stmt = $conn->prepare("
            INSERT INTO projectos (titulo, descricao, docente_id, data_criacao, prazo, arquivo) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$titulo, $descricao, $docente_id, $data_criacao, $prazo, $caminhoArquivo]);
        $projeto_id = $conn->lastInsertId();

        // Criar grupo
        $stmtGrupo = $conn->prepare("INSERT INTO grupo (projeto_id) VALUES (?)");
        $stmtGrupo->execute([$projeto_id]);
        $grupo_id = $conn->lastInsertId();

        // Preparar queries
        $stmtBuscarNomeEmail = $conn->prepare("SELECT nome, email FROM usuarios WHERE id = ? AND tipo = 'Estudante'");
        $stmtGrupoEstudante = $conn->prepare("INSERT INTO grupo_estudante (grupo_id, estudante_id, estudante_nome) VALUES (?, ?, ?)");
        $stmtNotificacao = $conn->prepare("
            INSERT INTO notificacoes 
            (docente_id, estudante_id, projeto_id, mensagem, data_envio, status)
            VALUES (?, ?, ?, ?, NOW(), 'Em Andamento')
        ");

        foreach ($estudantes as $estudante_id) {
            $stmtBuscarNomeEmail->execute([$estudante_id]);
            $row = $stmtBuscarNomeEmail->fetch(PDO::FETCH_ASSOC);

            $estudante_nome = $row['nome'] ?? 'Estudante';
            $email_estudante = $row['email'] ?? null;

            // Vincular ao grupo
            $stmtGrupoEstudante->execute([$grupo_id, $estudante_id, $estudante_nome]);

            // E-mail
            if ($email_estudante) {
                $mensagem_email = "
                    <h3>Olá, {$estudante_nome}!</h3>
                    <p>Você foi atribuído a um novo projeto: {$titulo}</p>
                    <p>Prazo: {$prazo}</p>
                    <p>Descrição:<br>{$descricao}</p>
                    <hr>
                    <p>Por favor, acesse o SGPA para mais detalhes.</p>
                ";
                enviarEmail($email_estudante, "Novo Projeto Atribuído - SGPA", $mensagem_email);
            }

            // Notificação interna
            $mensagem_sistema = "Você foi atribuído a um novo projeto:\n\n" . 
                                "Projeto: {$titulo}\n" . 
                                "Prazo: {$prazo}\n" . 
                                "Descrição: {$descricao}\n\n" . 
                                "Acesse o menu 'Ver Projetos' para mais detalhes.";

            $stmtNotificacao->execute([$docente_id, $estudante_id, $projeto_id, $mensagem_sistema]);
        }

        $conn->commit();
        unset($_SESSION['form_data']); // limpa dados do formulário
        $_SESSION['mensagem-novoprojecto'] = "✅ Projeto criado com sucesso! Notificações enviadas.";

    } catch (Exception $e) {
        $conn->rollBack();
        gravarFormulario();
        $_SESSION['mensagem-novoprojecto'] = "❌ Erro ao criar projeto: " . $e->getMessage();
    }

    header("Location: ../visao/novoprojecto.php");
    exit;
} else {
    $_SESSION['mensagem-novoprojecto'] = "Requisição inválida.";
    header("Location: ../visao/novoprojecto.php");
    exit;
}
