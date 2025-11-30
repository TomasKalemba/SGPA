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

    // === VALIDAÇÕES DE CAMPOS OBRIGATÓRIOS ===
    if (empty($titulo) || empty($descricao) || empty($docente_id) || empty($prazo) || empty($estudantes)) {
        $_SESSION['mensagem'] = "⚠️ Preencha todos os campos obrigatórios.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE TÍTULO E DESCRIÇÃO ===
    if (preg_match('/^\d+$/', $titulo)) {
        $_SESSION['mensagem'] = "⚠️ O título do projeto não pode conter apenas números.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }
    if (preg_match('/^\d+$/', $descricao)) {
        $_SESSION['mensagem'] = "⚠️ A descrição do projeto não pode conter apenas números.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE TAMANHO DE TEXTO ===
    if (mb_strlen($titulo) > 255) {
        $_SESSION['mensagem'] = "⚠️ O título não pode ter mais de 255 caracteres.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }
    if (mb_strlen($descricao) < 10) {
        $_SESSION['mensagem'] = "⚠️ A descrição deve ter pelo menos 10 caracteres.";
        header("Location: ../visao/novoprojecto.php");
        exit;
    }

    // === VALIDAÇÃO DE DATAS ===
    if (strtotime($prazo) < strtotime($data_criacao)) {
        $_SESSION['mensagem'] = "⚠️ O prazo não pode ser anterior à data de criação.";
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
            $_SESSION['mensagem'] = "⚠️ Tipo de arquivo inválido. Permitidos: PDF, DOCX, PPTX, JPG, PNG.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }

        // Limite de 10MB
        if ($tamanhoArquivo > 10 * 1024 * 1024) {
            $_SESSION['mensagem'] = "⚠️ O arquivo não pode ultrapassar 10MB.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }

        // Nome único para evitar sobrescrita
        $nomeFinal = time() . '_' . uniqid() . '.' . $extensao;
        $caminhoFinal = $pastaUpload . $nomeFinal;

        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoFinal)) {
            $caminhoArquivo = $nomeFinal; // ⚡ salva apenas o nome do arquivo
        } else {
            $_SESSION['mensagem'] = "⚠️ Falha ao salvar o arquivo.";
            header("Location: ../visao/novoprojecto.php");
            exit;
        }
    }

    // === SALVAR NO BANCO ===
    $crud = new crud();
    $conn = $crud->getConexao();

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

        // === BUSCAR CURSO E DEPARTAMENTO DO DOCENTE ===
        $stmtDocente = $conn->prepare("SELECT fk_usuarios_cursos, fk_departamentos FROM usuarios WHERE id = ?");
        $stmtDocente->execute([$docente_id]);
        $docenteInfo = $stmtDocente->fetch(PDO::FETCH_ASSOC);

        $docenteCurso = $docenteInfo['fk_usuarios_cursos'];
        $docenteDepto = $docenteInfo['fk_departamentos'];

        // Preparar queries
        $stmtGrupoEstudante = $conn->prepare("INSERT INTO grupo_estudante (grupo_id, estudante_id, estudante_nome) VALUES (?, ?, ?)");
        $stmtNotificacao = $conn->prepare("
            INSERT INTO notificacoes 
            (docente_id, estudante_id, projeto_id, mensagem, data_envio, status)
            VALUES (?, ?, ?, ?, NOW(), 'Em Andamento')
        ");
        $stmtBuscarEstudante = $conn->prepare("
            SELECT id, nome, email 
            FROM usuarios 
            WHERE id = ? 
              AND tipo = 'Estudante'
              AND fk_usuarios_cursos = ? 
              AND fk_departamentos = ?
        ");

        foreach ($estudantes as $estudante_id) {
            // Verifica se estudante pertence ao curso e departamento do docente
            $stmtBuscarEstudante->execute([$estudante_id, $docenteCurso, $docenteDepto]);
            $row = $stmtBuscarEstudante->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $_SESSION['mensagem'] = "⚠️ Não pode adicionar o estudante ID $estudante_id — ele não pertence ao seu curso/departamento.";
                $conn->rollBack();
                header("Location: ../visao/novoprojecto.php");
                exit;
            }

            $estudante_nome = $row['nome'];
            $email_estudante = $row['email'];

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
        $_SESSION['mensagem'] = "✅ Projeto criado com sucesso! Notificações enviadas.";

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['mensagem'] = "❌ Erro ao criar projeto: " . $e->getMessage();
    }

    header("Location: ../visao/novoprojecto.php");
    exit;
} else {
    $_SESSION['mensagem'] = "Requisição inválida.";
    header("Location: ../visao/novoprojecto.php");
    exit;
}
