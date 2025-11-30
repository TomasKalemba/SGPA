<?php
session_start();
header('Content-Type: application/json');
require_once '../modelo/crud.php';

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    echo json_encode(['erro' => 'Usuário não autorizado']);
    exit;
}

$estudante_id = $_SESSION['id'];

try {
    $crud = new crud();
    $conexao = $crud->getConexao();

    // 1. Buscar status das submissões feitas pelo estudante
    $sql = "SELECT estatus, Id_projectos FROM submisoes WHERE estudante_id = :estudante_id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudante_id);
    $stmt->execute();
    $submissoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emAndamento = 0;
    $concluidos = 0;
    $atrasados = 0;
    $idsSubmetidos = [];

    foreach ($submissoes as $sub) {
        $status = strtolower(trim($sub['estatus']));
        $idProjeto = $sub['Id_projectos'];
        $idsSubmetidos[] = $idProjeto;

        if ($status === 'emandamento' || $status === 'em andamento') {
            $emAndamento++;
        } elseif ($status === 'concluido') {
            $concluidos++;
        } elseif ($status === 'atrasado') {
            $atrasados++;
        }
    }

    // 2. Buscar todos os projetos atribuídos ao estudante
    $sqlProjetos = "
        SELECT DISTINCT g.projeto_id
        FROM grupo_estudante ge
        INNER JOIN grupo g ON ge.grupo_id = g.id
        WHERE ge.estudante_id = :estudante_id
    ";
    $stmt2 = $conexao->prepare($sqlProjetos);
    $stmt2->bindParam(':estudante_id', $estudante_id);
    $stmt2->execute();
    $projetosAtribuidos = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // 3. Projetos em falta = atribuídos - submetidos
    $projetosEmFalta = array_diff($projetosAtribuidos, $idsSubmetidos);

    echo json_encode([
        'emAndamento' => $emAndamento,
        'concluidos' => $concluidos,
        'atrasados' => $atrasados,
        'emFalta' => count($projetosEmFalta),
        'totalProjetos' => count($projetosAtribuidos)
    ]);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao consultar dados: ' . $e->getMessage()]);
}
