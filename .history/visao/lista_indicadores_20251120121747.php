<?php
require_once("../modelo/crud.php");
$crud = new crud();

$tipo = $_GET['tipo'] ?? '';

if($tipo == "docentes"){
    $sql = "SELECT p.titulo, u.nome, p.data_criacao 
            FROM projectos p 
            JOIN usuarios u ON p.docente_id = u.id 
            ORDER BY p.data_criacao DESC";
}
elseif($tipo == "estudantes"){
    $sql = "SELECT u.nome, s.data_submissao 
            FROM submisoes s 
            JOIN usuarios u ON s.estudante_id = u.id 
            ORDER BY s.data_submissao DESC";
}
else{ // total geral
    $sql = "
        (SELECT 'Projeto' AS tipo, p.titulo AS nome, p.data_criacao AS data
         FROM projectos p ORDER BY p.data_criacao DESC)
        UNION
        (SELECT 'Submissão', u.nome, s.data_submissao 
         FROM submisoes s 
         JOIN usuarios u ON s.estudante_id = u.id 
         ORDER BY s.data_submissao DESC)
    ";
}

$stmt = $crud->getConexao()->prepare($sql);
$stmt->execute();

echo "<ul class='list-group'>";
if($stmt->rowCount() > 0){
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data = isset($row['data_criacao']) ? $row['data_criacao'] : ($row['data_submissao'] ?? $row['data']);
        $dataFormatada = date("d/m/Y H:i", strtotime($data));

        echo "<li class='list-group-item'>
            <strong>".($row['titulo'] ?? $row['nome'])."</strong><br>
            <small class='text-muted'>$dataFormatada</small>
        </li>";
    }
} else {
    echo "<li class='list-group-item'>Nenhum registo encontrado</li>";
}
echo "</ul>";


