<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/crud.php'; // Ajuste se necessário

try {
    $crud = new crud();
    $pdo = $crud->getConexao();

    $docente_id = $_SESSION['id'];
    $tipo = $_GET['tipo'] ?? 'total';

    $sql = "SELECT id, titulo, prazo, estatus FROM projectos WHERE docente_id = :docente_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hoje = strtotime(date('Y-m-d'));
    $res = [];

    foreach ($projetos as $proj) {
        $prazo = strtotime($proj['prazo']);
        $vencido = $prazo < $hoje;
        $em_andamento = !$vencido && strtolower($proj['estatus']) !== 'concluido';
        $concluido = strtolower($proj['estatus']) === 'concluido';
        $atrasado = $vencido && !$concluido;
        $em_falta = strtolower($proj['estatus']) === 'em falta';

        $incluir = false;

        switch ($tipo) {
            case 'em_andamento': $incluir = $em_andamento; break;
            case 'concluidos': $incluir = $concluido; break;
            case 'atrasados': $incluir = $atrasado; break;
            case 'em_falta': $incluir = $em_falta; break;
            case 'total': $incluir = true; break;
        }

        if ($incluir) {
            $res[] = [
                'titulo' => $proj['titulo'],
                'data'   => $proj['prazo']
            ];
        }
    }

    echo json_encode($res);

} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao carregar dados: '.$e->getMessage()]);
}
