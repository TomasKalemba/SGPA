<?php
session_start();

require_once '../modelo/submissoes.php';
require_once '../modelo/crud.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Verifica autenticação
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    $_SESSION['mensagem'] = "Erro: Acesso não autorizado.";
    header("Location: ../visao/login.php");
    exit;
}

$estudanteId = $_SESSION['id'];
$projeto_id = $_POST['projeto_id'] ?? null;
$estatus = $_POST['estatus'] ?? '';
$feedback = $_POST['feedback'] ?? '';
$data_submissao = date('Y-m-d H:i:s');

// Validação básica dos campos obrigatórios
if (!$projeto_id || empty($_FILES['arquivo']['name'])) {
    $_SESSION['mensagem'] = "Erro: Preencha todos os campos obrigatórios.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

$arquivo_nome = $_FILES['arquivo']['name'];
$arquivo_tmp = $_FILES['arquivo']['tmp_name'];

// Conexão com o banco
$crud = new crud();
$conn = $crud->getConexao();

// Pega título do projeto direto do banco para evitar erro
$stmtProjeto = $conn->prepare("SELECT titulo, descricao, docente_id, prazo FROM projectos WHERE id = ?");
$stmtProjeto->execute([$projeto_id]);
$projeto = $stmtProjeto->fetch(PDO::FETCH_ASSOC);

if (!$projeto) {
    $_SESSION['mensagem'] = "Erro: Projeto inválido.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

$titulo = $projeto['titulo'];
$descricao = $projeto['descricao'];
$docente_id = $projeto['docente_id'];
$prazo = $projeto['prazo'];

// Verifica se o docente já tem um projeto com o mesmo título
$stmtVerificaTitulo = $conn->prepare("SELECT COUNT(*) FROM projectos WHERE docente_id = ? AND titulo = ?");
$stmtVerificaTitulo->execute([$docente_id, $titulo]);

if ($stmtVerificaTitulo->fetchColumn() > 0) {
    $_SESSION['mensagem'] = "Erro: O docente já enviou um projeto com o mesmo título.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// Verifica duplicidade de submissão do estudante
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

// Faz upload do arquivo
$arquivo_destino = "../uploads/" . time() . "_" . basename($arquivo_nome);
if (!move_uploaded_file($arquivo_tmp, $arquivo_destino)) {
    $_SESSION['mensagem'] = "Erro: Falha ao enviar o arquivo.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// Preenche submissão
$submissao->setProjecto_Id($projeto_id);
$submissao->setEstudante_Id($estudanteId);
$submissao->setTitulo($titulo);        // Usa título do BD
$submissao->setDescricao($descricao);  // Usa descrição do BD
$submissao->setArquivo($arquivo_destino);
$submissao->setestatus($estatus);
$submissao->setData_submissao($data_submissao);
$submissao->setFeedback($feedback);

// Insere submissão
if ($submissao->InserirSubmissao()) {
    try {
        // Busca nome do estudante
        $stmtNomeEstudante = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmtNomeEstudante->execute([$estudanteId]);
        $nome_estudante = $stmtNomeEstudante->fetchColumn();

        // Busca dados do docente (email, nome)
        $stmtDocente = $conn->prepare("SELECT email, nome FROM usuarios WHERE id = ?");
        $stmtDocente->execute([$docente_id]);
        $docente = $stmtDocente->fetch(PDO::FETCH_ASSOC);

        if ($docente) {
            $email_docente = $docente['email'];
            $nome_docente = $docente['nome'];

            $dataHora = date('Y-m-d H:i:s');
            $status = 'Não Lida';

            // Notificação para o DOCENTE
            $mensagemDocente = "O estudante {$nome_estudante} submeteu o projeto \"{$titulo}\".";
            $stmtNotifDocente = $conn->prepare("INSERT INTO notificacoes (docente_id, estudante_id, projeto_id, mensagem, data_envio, status) 
                                                VALUES (?, ?, ?, ?, ?, ?)");
            $stmtNotifDocente->execute([$docente_id, $estudanteId, $projeto_id, $mensagemDocente, $dataHora, $status]);

            // Notificação para o ESTUDANTE
            $mensagemEstudante = "Você foi atribuído a um novo projeto: <strong>Projeto:</strong> $titulo <strong>Prazo:</strong> $prazo <strong>Descrição:</strong> $descricao";

            $sqlNotifEstudante = "INSERT INTO notificacoes (mensagem, estudante_id, docente_id, data_envio) 
                                  VALUES (:mensagem, :estudante_id, :docente_id, NOW())";
            $stmtNotifEstudante = $conn->prepare($sqlNotifEstudante);
            $stmtNotifEstudante->bindParam(':mensagem', $mensagemEstudante);
            $stmtNotifEstudante->bindParam(':estudante_id', $estudanteId);
            $stmtNotifEstudante->bindParam(':docente_id', $docente_id);
            $stmtNotifEstudante->execute();

            // Envia e-mail
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tecnologia.tomas0@gmail.com';
            $mail->Password = 'rrgk fgay wfsu zwno'; // Sua senha de app do Gmail (recomendo usar variável ambiente)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tecnologia.tomas0@gmail.com', 'SGPA Notificações');
            $mail->addAddress($email_docente, $nome_docente);
            $mail->isHTML(true);
            $mail->Subject = 'Nova Submissão de Projeto';
            $mail->Body = "
                <p>Olá, <strong>{$nome_docente}</strong>,</p>
                <p>O estudante <strong>{$nome_estudante}</strong> submeteu o projeto: <strong>{$titulo}</strong>.</p>
                <p><strong>Descrição:</strong> {$descricao}</p>
                <p><strong>Data da Submissão:</strong> {$data_submissao}</p>
                <hr>
                <p>Acesse o SGPA para revisar esta submissão.</p>
            ";

            $mail->send();
        }
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail ao docente: " . $e->getMessage());
    }

    $_SESSION['mensagem'] = "✅ Projeto enviado com sucesso! Notificação enviada ao docente.";
} else {
    $_SESSION['mensagem'] = "Erro ao enviar o projeto.";
}

header("Location: ../visao/enviarProjecto.php");
exit;
