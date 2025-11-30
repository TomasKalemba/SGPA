<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
    $_SESSION['mensagem'] = "Acesso negado.";
    header("Location: ../visao/ListaProjectos.php");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $prazo = $_POST['prazo'];
    $feedback = $_POST['feedback'] ?? null;
    $estudantes_existentes = $_POST['estudantes'] ?? [];
    $novos_estudantes = $_POST['novos_estudantes'] ?? [];

    // Combina arrays e remove duplicados
    $todos_estudantes = array_unique(array_map('intval', array_merge($estudantes_existentes, $novos_estudantes)));

    // Validação básica
    if (empty($titulo) || empty($descricao) || empty($prazo)) {
        $_SESSION['mensagem'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../visao/editar.php?id=$id");
        exit;
    }

    try {
        // Atualiza dados do projeto
        $sql = "UPDATE projectos SET titulo=:titulo, descricao=:descricao, prazo=:prazo, feedback=:feedback WHERE Id=:Id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':prazo' => $prazo,
            ':feedback' => $feedback,
            ':Id' => $id
        ]);

        // Recupera grupo do projeto
        $stmtGrupo = $pdo->prepare("SELECT id FROM grupo WHERE projeto_id = ?");
        $stmtGrupo->execute([$id]);
        $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

        if ($grupo) {
            $grupo_id = $grupo['id'];

            // Remove estudantes que não estão na lista final
            $placeholders = count($todos_estudantes) ? implode(',', $todos_estudantes) : '0';
            $stmtDelete = $pdo->prepare("DELETE FROM grupo_estudante WHERE grupo_id = ? AND estudante_id NOT IN ($placeholders)");
            $stmtDelete->execute([$grupo_id]);

            // Insere estudantes que ainda não estão vinculados
            foreach ($todos_estudantes as $estudante_id) {
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM grupo_estudante WHERE grupo_id = ? AND estudante_id = ?");
                $stmtCheck->execute([$grupo_id, $estudante_id]);
                if ($stmtCheck->fetchColumn() == 0) {
                    $stmtInsert = $pdo->prepare("INSERT INTO grupo_estudante (grupo_id, estudante_id) VALUES (?, ?)");
                    $stmtInsert->execute([$grupo_id, $estudante_id]);
                }
            }
        }

        $_SESSION['mensagem'] = "Projeto e grupo atualizados com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
    }

    header("Location: ../visao/editar.php?id=$id");
    exit;
}
?>


