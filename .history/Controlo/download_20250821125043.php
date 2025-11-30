<?php
// controlo/download.php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo "Acesso negado.";
    exit;
}

$pdo = getPDO();

function saidaArquivo(string $nomeArquivoNoDisco) {
    $baseDir = realpath(__DIR__ . '/../uploads');
    if ($baseDir === false) {
        http_response_code(500);
        exit("Pasta de uploads inválida.");
    }

    // Caminho final (não permitir sair de uploads)
    $caminho = $baseDir . DIRECTORY_SEPARATOR . $nomeArquivoNoDisco;

    if (!is_file($caminho)) {
        http_response_code(404);
        exit("Arquivo não encontrado.");
    }

    // Mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($caminho) ?: 'application/octet-stream';

    // Cabeçalhos
    header("Content-Description: File Transfer");
    header("Content-Type: {$mime}");
    header("Content-Disposition: attachment; filename=\"" . basename($nomeArquivoNoDisco) . "\"");
    header("Content-Length: " . filesize($caminho));
    header("Cache-Control: private, must-revalidate");
    header("Pragma: public");
    header("Expires: 0");

    // Envio
    if (ob_get_level()) { ob_end_clean(); }
    readfile($caminho);
    exit;
}

try {
    if (isset($_GET['tipo'], $_GET['id']) && ctype_digit($_GET['id'])) {
        $tipo = $_GET['tipo'];
        $id   = (int) $_GET['id'];

        if ($tipo === 'projecto') {
            $q = $pdo->prepare("SELECT arquivo FROM projectos WHERE Id = :id AND arquivo IS NOT NULL");
            $q->execute([':id' => $id]);
            $row = $q->fetch();
            if (!$row) { throw new Exception("Arquivo não encontrado."); }
            saidaArquivo($row['arquivo']);

        } elseif ($tipo === 'submissao') {
            $q = $pdo->prepare("SELECT arquivo FROM submissoes WHERE id = :id AND arquivo IS NOT NULL");
            $q->execute([':id' => $id]);
            $row = $q->fetch();
            if (!$row) { throw new Exception("Arquivo não encontrado."); }
            // (Opcional) verificar se a submissão pertence ao grupo/estudante atual
            saidaArquivo($row['arquivo']);

        } else {
            throw new Exception("Tipo inválido.");
        }
    } elseif (isset($_GET['file'])) {
        // Modo legado: aceita ?file=nome.ext (apenas basename + allowlist)
        $nome = basename($_GET['file']);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $nome)) {
            throw new Exception("Nome de arquivo inválido.");
        }
        $ext = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
        $permitidas = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','7z','txt','jpg','jpeg','png'];
        if (!in_array($ext, $permitidas, true)) {
            throw new Exception("Extensão não permitida.");
        }
        saidaArquivo($nome);
    } else {
        throw new Exception("Parâmetros ausentes.");
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo "Erro: " . $e->getMessage();
}
