<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

$estudante_id = $_SESSION['id'];

require_once '../modelo/crud.php';

try {
    $pdo = (new crud())->getConexao();

    $tipo = $_GET['tipo'] ?? '';

    $res = [];

    switch($tipo) {
        case 'em_andamento':
            // Projetos que o estudante ainda não submeteu
            $sql = "
                SELECT p.titulo, p.prazo, 'Em Andamento' AS status
                FROM projectos p
                LEFT JOIN submisoes s ON s.projeto_id = p.Id AND s.estudante_id = :estudante_id
                WHERE s.Id IS NULL AND p.prazo >= CURDATE()
            ";
            break;
        case 'concluidos':
            // Projetos que o estudante submeteu
            $sql = "
                SELECT p.titulo, p.prazo, 'Concluído' AS status
                FROM projectos p
                INNER JOIN submisoes s ON s.projeto_id = p.Id
                WHERE s.estudante_id = :estudante_id
            ";
            break;
        case 'atrasados':
            // Projetos que o estudante não submeteu e o prazo já passou
            $sql = "
                SELECT p.titulo, p.prazo, 'Atrasado' AS status
                FROM projectos p
                LEFT JOIN submisoes s ON s.projeto_id = p.Id AND s.estudante_id = :estudante_id
                WHERE s.Id IS NULL AND p.prazo < CURDATE()
            ";
            break;
        case 'em_falta':
            // Projetos que o estudante ainda não submeteu (igual a em_andamento + atrasados)
            $sql = "
                SELECT p.titulo, p.prazo, IF(p.prazo >= CURDATE(),'Em Andamento','Atrasado') AS status
                FROM projectos p
                LEFT JOIN submisoes s ON s.projeto_id = p.Id AND s.estudante_id = :estudante_id
                WHERE s.Id IS NULL
            ";
            break;
        case 'total':
        default:
            $sql = "
                SELECT p.titulo, p.prazo, 
                IF(s.Id IS NULL, IF(p.prazo < CURDATE(),'Atrasado','Em Andamento'),'Concluído') AS status
                FROM projectos p
                LEFT JOIN submisoes s ON s.projeto_id = p.Id AND s.estudante_id = :estudante_id
            ";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estudante_id' => $estudante_id]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao buscar dados: '.$e->getMessage()]);
}
