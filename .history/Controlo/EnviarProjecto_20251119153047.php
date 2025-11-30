<?php
session_start();

require_once '../modelo/submissoes.php';
require_once '../modelo/crud.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// ---------------------
// 1. AUTENTICAÇÃO
// ---------------------
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    $_SESSION['mensagem'] = "Erro: Acesso não autorizado.";
    header("Location: ../visao/login.php");
    exit;
}

$estudanteId = $_SESSION['id'];

// ---------------------
// 2. RECEBE DADOS DO POST
// ---------------------
$projeto_id    = $_POST['projeto_id'] ?? null;
$titulo        = $_POST['titulo'] ?? '';
$descricao     = $_POST['descricao'] ?? '';
$estatus       = $_POST['estatus'] ?? '';
$feedback      = $_POST['feedback'] ?? '';
$docenteNome   = $_POST['docente_nome'] ?? '';
$data_submissao = date('Y-m-d H:i:s');

// ---------------------
// 3. VALIDAÇÃO DE ARQUIVO
// ---------------------
$arquivo_nome  = $_FILES['arquivo']['name'] ?? '';
$arquivo_tmp   = $_FILES['arquivo']['tmp_name'] ?? '';

if (!$projeto_id || !$arquivo_nome) {
    $_SESSION['mensagem'] = "Erro: Preencha todos os campos obrigatórios.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// ---------------------
// 4. VALIDAÇÃO DE DUPLICIDADE
// ---------------------
$submissao = new submissoes();

if ($submissao->jaExisteSubmissaoPorGrupo($estudanteId, $projeto_id)) {
    $_SESSION['mensagem'] = "Erro: Já existe uma submissão feita por um membro do grupo.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

if ($submissao->jaExisteSubmissaoDoMesmoEstudante($estudanteId, $projeto_id)) {
    $_SESSION['mensagem'] = "Erro: Você já enviou uma submissão para este projeto.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// ---------------------
// 5. UPLOAD DO ARQUIVO
// ---------------------
$arquivo_destino = "../uploads/" . time() . "_" . basename($arquivo_nome);

if (!move_uploaded_file($arquivo_tmp, $arquivo_destino)) {
    $_SESSION['mensagem'] = "Erro: Falha ao enviar o arquivo.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// ---------------------
// 6. GRAVAR SUBMISSÃO
// ---------------------
$submissao->setProjecto_Id($projeto_id);
$submissao->setEstudante_Id($estudanteId);
$submissao->setTitulo($titulo);
$submissao->setDescricao($descricao);
$submissao->setArquivo($arquivo_destino);
$submissao->setestatus($estatus);
$submissao->setData_submissao($data_submissao);
$submissao->setFeedback($feedback);

if (!$submissao->InserirSubmissao()) {
    $_SESSION['mensagem'] = "Erro ao enviar o projeto.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// ---------------------
// 7. NOTIFICAÇÕES + EMAIL
// ---------------------
try {
    $crud = new crud();
    $conn = $crud->getConexao();

    // Nome do estudante
    $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$estudanteId]);
    $nome_estudante = $stmt->fetchColumn();

    // Dados do docente
    $sql = "SELECT u.email, u.nome, p.docente_id
            FROM projectos p
            JOIN usuarios u ON u.id = p.docente_id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$projeto_id]);
    $docente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($docente) {
        $docente_id    = $docente['docente_id'];
        $email_docente = $docente['email'];
        $nome_docente  = $docente['nome'];

        $dataHora = date('Y-m-d H:i:s');
        $status   = "Não Lida";

        // ----------------------
        // Notificação para DOCENTE
        // ----------------------
        $mensagemDoc = "O estudante {$nome_estudante} submeteu o projeto \"{$titulo}\".";

        $stmtNot = $conn->prepare("
            INSERT INTO notificacoes (docente_id, estudante_id, projeto_id, mensagem, data_envio, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmtNot->execute([$docente_id, $estudanteId, $projeto_id, $mensagemDoc, $dataHora, $status]);

        // ----------------------
        // Buscar prazo do projeto p/ notificação do estudante
        // ----------------------
        $stmtPrazo = $conn->prepare("SELECT prazo FROM projectos WHERE id = ?");
        $stmtPrazo->execute([$projeto_id]);
        $prazo = $stmtPrazo->fetchColumn();

        // ----------------------
        // Notificação para ESTUDANTE
        // ----------------------
        $mensagemEst = "Você submeteu o projeto <strong>$titulo</strong>. Prazo: $prazo";

        $stmtEst = $conn->prepare("
            INSERT INTO notificacoes (mensagem, estudante_id, docente_id, data_envio)
            VALUES (:m, :est, :doc, NOW())
        ");
        $stmtEst->bindParam(':m', $mensagemEst);
        $stmtEst->bindParam(':est', $estudanteId);
        $stmtEst->bindParam(':doc', $docente_id);
        $stmtEst->execute();

        // ----------------------
        // 8. Enviar Email
        // ----------------------
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tecnologia.tomas0@gmail.com';
        $mail->Password = 'rrgk fgay wfsu zwno'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tecnologia.tomas0@gmail.com', 'SGPA Notificações');
        $mail->addAddress($email_docente, $nome_docente);
        $mail->isHTML(true);
        $mail->Subject = 'Nova Submissão de Projeto';
        $mail->Body =
            "<p>Olá, <strong>$nome_docente</strong>,</p>
             <p>O estudante <strong>{$nome_estudante}</strong> submeteu o projeto: <strong>{$titulo}</strong>.</p>
             <p><strong>Descrição:</strong> {$descricao}</p>
             <p><strong>Data:</strong> {$data_submissao}</p>
             <hr>
             <p>Acesse o SGPA para revisar a submissão.</p>";

        $mail->send();
    }

} catch (Exception $e) {
    error_log("Erro ao enviar e-mail ou notificações: " . $e->getMessage());
}

// ---------------------
// 9. FINALIZAÇÃO
// ---------------------
$_SESSION['mensagem'] = "✅ Projeto enviado com sucesso! Notificação enviada ao docente.";
header("Location: ../visao/enviarProjecto.php");
exit;
