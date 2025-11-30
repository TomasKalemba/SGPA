<?php
session_start();
require_once("../modelo/crud.php");

// Mostrar erros só enquanto debug (remover em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Verifica sessão
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

$docente_id = intval($_SESSION['id']);
$termo = trim($_POST['termo'] ?? '');

// Conexão
$crud = new crud();
$conn = $crud->getConexao();

try {
    // 1) Pegar departamento do docente (campo departamento_id na tabela usuarios)
    $stmt = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ? AND tipo = 'Docente' LIMIT 1");
    $stmt->execute([$docente_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc || empty($doc['departamento_id'])) {
        echo json_encode([]);
        exit;
    }

    $departamento_id = $doc['departamento_id'];

    // 2) Montar query para buscar estudantes do mesmo departamento
    //    se houver termo, filtra por nome LIKE, senão retorna vazio (para evitar lista gigante)
    if ($termo === '') {
        // Retorna vazio se não enviaram termo (comportamento do Select2)
        echo json_encode([]);
        exit;
    }

    $sql = "
        SELECT id, nome
        FROM usuarios
        WHERE tipo = 'Estudante'
          AND ativo = 1
          AND departamento_id = ?
          AND nome LIKE ?
        ORDER BY nome ASC
        LIMIT 50
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$departamento_id, "%{$termo}%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar para Select2: { id: X, text: 'Nome' }
    $results = [];
    foreach ($rows as $r) {
        $results[] = [
            'id' => $r['id'],
            'text' => $r['nome']
        ];
    }

    echo json_encode($results);
    exit;

} catch (PDOException $e) {
    // Em caso de erro devolve vazio (poderias logar o erro)
    error_log("busca_estudantes.php error: " . $e->getMessage());
    echo json_encode([]);
    exit;
}

