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
    $conexao = (new crud())->getConexao();

    $sql = "SELECT data_submissao FROM submisoes WHERE Estudante_Id = :estudante_id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudante_id);
    $stmt->execute();

    $datas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $resumo = [];

    foreach ($datas as $data) {
        $mesAno = date('M', strtotime($data));
        if (!isset($resumo[$mesAno])) {
            $resumo[$mesAno] = 0;
        }
        $resumo[$mesAno]++;
    }

    echo json_encode($resumo);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro ao consultar dados: ' . $e->getMessage()]);
}
