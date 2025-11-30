<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

include_once("../Modelo/projectos.php");

$host = 'localhost';
$db = 'sgpa';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['mensagem'] = 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
    header("Location: ../visao/ListaProjectos.php");
    exit;
}

// ✅ Se for GET, carrega dados para a página de edição
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM projectos WHERE Id = ?");
    $stmt->execute([$id]);
    $projecto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($projecto) {
        $projecto['data_criacao'] = date('Y-m-d', strtotime($projecto['data_criacao']));
        $projecto['prazo'] = date('Y-m-d', strtotime($projecto['prazo']));

        $_SESSION['projeto_editar'] = $projecto;
        header("Location: ../visao/editar.php?id=$id");
        exit;
    } else {
        $_SESSION['mensagem'] = "Projeto não encontrado.";
        header("Location: ../visao/ListaProjectos.php");
        exit;
    }
}

// ✅ Se for POST, edita projeto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $docente_id = $_POST['docente_id'];
    $data_criacao = $_POST['data_criacao'];
    $prazo = $_POST['prazo'];
    $feedback = $_POST['feedback'] ?? null;
    $estudantes = $_POST['estudantes'] ?? [];

    if (empty($titulo) || empty($descricao) || empty($docente_id) || empty($data_criacao) || empty($prazo)) {
        $_SESSION['mensagem'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../visao/editar.php?id=$id");
        exit;
    }

    // Verifica se foi feito upload de novo arquivo
    $stmtArquivo = $pdo->prepare("SELECT arquivo FROM projectos WHERE Id = ?");
    $stmtArquivo->execute([$id]);
    $arquivoAtual = $stmtArquivo->fetchColumn();
    $arquivo = $arquivoAtual;

    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $diretorio_upload = '../visao/uploads/';
        if (!is_dir($diretorio_upload)) {
            mkdir($diretorio_upload, 0777, true);
        }

        $arquivo_nome = uniqid() . '_' . basename($_FILES['arquivo']['name']);
        $caminho_arquivo = $diretorio_upload . $arquivo_nome;

        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho_arquivo)) {
            $arquivo = $arquivo_nome;
        } else {
            $_SESSION['mensagem'] = "Erro ao fazer upload do novo arquivo.";
            header("Location: ../visao/editar.php?id=$id");
            exit;
        }
    }

    try {
        // Atualizar projeto
        $sql = "UPDATE projectos SET titulo=:titulo, descricao=:descricao, docente_id=:docente_id, data_criacao=:data_criacao, prazo=:prazo, arquivo=:arquivo, feedback=:feedback WHERE Id=:Id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':docente_id', $docente_id);
        $stmt->bindParam(':data_criacao', $data_criacao);
        $stmt->bindParam(':prazo', $prazo);
        $stmt->bindParam(':arquivo', $arquivo);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->bindParam(':Id', $id);

        if ($stmt->execute()) {
            // Atualizar estudantes do grupo
            $stmtGrupo = $pdo->prepare("SELECT id FROM grupo WHERE projeto_id = ?");
            $stmtGrupo->execute([$id]);
            $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

            if ($grupo) {
                $grupo_id = $grupo['id'];

                // Limpa grupo_estudante
                $pdo->prepare("DELETE FROM grupo_estudante WHERE grupo_id = ?")->execute([$grupo_id]);

                // Insere os estudantes selecionados
                foreach ($estudantes as $estudante_id) {
                    $stmtInsert = $pdo->prepare("INSERT INTO grupo_estudante (grupo_id, estudante_id) VALUES (?, ?)");
                    $stmtInsert->execute([$grupo_id, $estudante_id]);
                }

                $_SESSION['mensagem'] = "Projeto editado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Grupo vinculado ao projeto não encontrado.";
            }
        } else {
            $_SESSION['mensagem'] = "Erro ao editar o projeto.";
        }

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
    }

    // Redireciona para a edição com mensagem (e não para a lista)
   header("Location: ../visao/ListaProjectos.php");
exit;
}
?>
