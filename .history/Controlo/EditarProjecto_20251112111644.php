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

    // Novos arrays do formulário
    $estudantes_existentes = $_POST['estudantes'] ?? [];
    $novos_estudantes = $_POST['novos_estudantes'] ?? [];

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

        // Atualiza estudantes do grupo
        $stmtGrupo = $pdo->prepare("SELECT id FROM grupo WHERE projeto_id = ?");
        $stmtGrupo->execute([$id]);
        $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

        if ($grupo) {
            $grupo_id = $grupo['id'];

            // Recupera todos os estudantes atuais do grupo
            $stmtCurrent = $pdo->prepare("SELECT estudante_id FROM grupo_estudante WHERE grupo_id = ?");
            $stmtCurrent->execute([$grupo_id]);
            $current_students = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);

            // Todos os estudantes que devem permanecer: existentes + novos
            $all_selected = array_map('intval', array_merge($estudantes_existentes, $novos_estudantes));

            // Remove estudantes que não estão na nova lista
            $students_to_remove = array_diff($current_students, $all_selected);
            if (!empty($students_to_remove)) {
                $placeholders = implode(',', array_fill(0, count($students_to_remove), '?'));
                $stmtDelete = $pdo->prepare("DELETE FROM grupo_estudante WHERE grupo_id = ? AND estudante_id IN ($placeholders)");
                $stmtDelete->execute(array_merge([$grupo_id], $students_to_remove));
            }

            // Adiciona estudantes que ainda não estão vinculados
            $students_to_add = array_diff($all_selected, $current_students);
            if (!empty($students_to_add)) {
                $stmtInsert = $pdo->prepare("INSERT INTO grupo_estudante (grupo_id, estudante_id) VALUES (?, ?)");
                foreach ($students_to_add as $est_id) {
                    $stmtInsert->execute([$grupo_id, $est_id]);
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


