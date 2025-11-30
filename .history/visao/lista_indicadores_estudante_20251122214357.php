<?php
session_start();
include_once('../modelo/conexao.php'); // ajustar caminho para conexão

if(!isset($_SESSION['id']) || $_SESSION['tipo'] != 'Estudante') {
    echo json_encode([]);
    exit;
}

$usuario_id = $_SESSION['id'];
$tipo = $_GET['tipo'] ?? 'total';

$where = " WHERE aluno_id = $usuario_id ";
switch($tipo){
    case 'em_andamento':
        $where .= " AND status = 'Em Andamento' ";
        break;
    case 'concluidos':
        $where .= " AND status = 'Concluído' ";
        break;
    case 'atrasados':
        $where .= " AND status = 'Atrasado' ";
        break;
    case 'em_falta':
        $where .= " AND status = 'Em Falta' ";
        break;
    case 'total':
        break;
    default:
        break;
}

$sql = "SELECT id, titulo, prazo, status FROM submisoes $where ORDER BY prazo ASC";
$result = $conn->query($sql);

$dados = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $dados[] = [
            'id' => $row['id'],
            'titulo' => $row['titulo'],
            'prazo' => $row['prazo'],
            'status' => $row['status']
        ];
    }
}

echo json_encode($dados);
