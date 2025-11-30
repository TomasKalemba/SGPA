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
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$estatus = $_POST['estatus'] ?? '';
$feedback = $_POST['feedback'] ?? '';
$data_submissao = date('Y-m-d H:i:s');

// Validação do arquivo
$arquivo_nome = $_FILES['arquivo']['name'] ?? '';
$arquivo_tmp = $_FILES['arquivo']['tmp_name'] ?? '';
if (!$projeto_id || !$arquivo_nome) {
    $_SESSION['mensagem'] = "Erro: Preencha todos os campos obrigatórios.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// Verifica duplicidade de submissão
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

// Faz upload
$arquivo_destino = "../uploads/" . time() . "_" . basename($arquivo_nome);
if (!move_uploaded_file($arquivo_tmp, $arquivo_destino)) {
    $_SESSION['mensagem'] = "Erro: Falha ao enviar o arquivo.";
    header("Location: ../visao/enviarProjecto.php");
    exit;
}

// Preenche submissão
$submissao->setProjecto_Id($projeto_id);
$submissao->setEstudante_Id($estudanteId);
$submissao->setTitulo($titulo);
$submissao->setDescricao($descricao);
$submissao->setArquivo($arquivo_destino);
$submissao->setestatus($estatus);
$submissao->setData_submissao($data_submissao);
$submissao->setFeedback($feedback);

// Insere submissão
if ($submissao->InserirSubmissao()) {
    try {
        $crud = new crud();
        $conn = $crud->getConexao();

        // Nome do estudante
        $stmtNomeEstudante = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmtNomeEstudante->execute([$estudanteId]);
        $nome_estudante = $stmtNomeEstudante->fetchColumn();

        // Busca o docente do projeto
        $stmtDocente = $conn->prepare("SELECT u.email, u.nome, p.docente_id 
                                       FROM projectos p 
                                       JOIN usuarios u ON u.id = p.docente_id 
                                       WHERE p.id = ?");
        $stmtDocente->execute([$projeto_id]);
        $docente = $stmtDocente->fetch(PDO::FETCH_ASSOC);

        if ($docente) {
            $docente_id = $docente['docente_id'];
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
// Recebe o nome do docente a partir do formulário
$nome_docente = $_POST['docente_nome']; // Certifique-se que o name do input seja 'docente_nome'

// Buscar prazo do projeto antes de montar a mensagem
$stmtPrazo = $conn->prepare("SELECT prazo FROM projectos WHERE id = ?");
$stmtPrazo->execute([$projeto_id]);
$prazo = $stmtPrazo->fetchColumn();

// Buscar o ID do docente com base no nome
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE nome = :nome AND tipo = 'Docente'");
$stmt->bindParam(':nome', $nome_docente);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $docente_id = $result['id'];

    // Monta a mensagem de notificação já com a variável $prazo definida
    $mensagem = "Você foi atribuído a um novo projeto:  <strong>Projeto:</strong> $titulo  <strong>Prazo:</strong> $prazo  <strong>Descrição:</strong> $descricao";

    // Insert da notificação
    $sql = "INSERT INTO notificacoes (mensagem, estudante_id, docente_id, data_envio) 
            VALUES (:mensagem, :estudante_id, :docente_id, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mensagem', $mensagem);
    $stmt->bindParam(':estudante_id', $estudanteId); // ou $_SESSION['id']
    $stmt->bindParam(':docente_id', $docente_id);
    $stmt->execute();
} else {
    // Caso o nome do docente não seja encontrado
    echo "Erro: Docente com nome '$nome_docente' não encontrado.";
    exit;
}




            // Envia e-mail
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tecnologia.tomas0@gmail.com';
            $mail->Password = 'rrgk fgay wfsu zwno'; // App password
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
