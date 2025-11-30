<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode([]);
    exit;
}

$estudante_id = $_SESSION['id'];
$tipo = $_GET['tipo'] ?? '';
require_once '../modelo/crud.php';

try {
    $crud = new crud();
    $pdo = $crud->getConexao();
    $hoje = date('Y-m-d');

    $sql = "
        SELECT p.Id, p.titulo, p.prazo, s.Id AS submissao
        FROM projectos p
        LEFT JOIN submisoes s 
            ON p.Id = s.projeto_id AND s.estudante_id = :estudante_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estudante_id' => $estudante_id]);
    $projetos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = '';

        if ($row['submissao']) {
            $status = 'Concluído';
        } else {
            if ($row['prazo'] < $hoje) $status = 'Atrasado';
            elseif ($row['prazo'] >= $hoje) $status = 'Em Andamento';
            else $status = 'Em Falta';
        }

        // Filtrar pelo tipo selecionado
        switch ($tipo) {
            case 'em_andamento':
                if ($status !== 'Em Andamento') continue 2;
                break;
            case 'concluidos':
                if ($status !== 'Concluído') continue 2;
                break;
            case 'atrasados':
                if ($status !== 'Atrasado') continue 2;
                break;
            case 'em_falta':
                if ($status === 'Concluído') continue 2;
                break;
            case 'total':
            default:
                // Não filtra
                break;
        }

        $projetos[] = [
            'titulo' => $row['titulo'],
            'prazo' => $row['prazo'],
            'status' => $status
        ];
    }

    echo json_encode($projetos);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar projetos: ' . $e->getMessage()]);
}
