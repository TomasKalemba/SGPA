<?php
require_once("../modelo/crud.php");
$crud = new crud();

$tipo = $_GET['tipo'] ?? '';

/* ---------------------------
   CONSULTAS POR TIPO
---------------------------- */

if($tipo == "docentes"){

    $sql = "SELECT 
                p.titulo AS item,
                u.nome AS autor,
                p.data_criacao AS data
            FROM projectos p 
            JOIN usuarios u ON p.docente_id = u.id 
            ORDER BY p.data_criacao DESC";

}
elseif($tipo == "estudantes"){

    $sql = "SELECT 
                'Submissão' AS item,
                u.nome AS autor,
                s.data_submissao AS data
            FROM submisoes s 
            JOIN usuarios u ON s.estudante_id = u.id 
            ORDER BY s.data_submissao DESC";

}
else {  // total geral

    $sql = "
        SELECT 
            p.titulo AS item,
            u.nome AS autor,
            p.data_criacao AS data
        FROM projectos p 
        JOIN usuarios u ON p.docente_id = u.id 
        
        UNION ALL
        
        SELECT 
            'Submissão' AS item,
            u.nome AS autor,
            s.data_submissao AS data
        FROM submisoes s 
        JOIN usuarios u ON s.estudante_id = u.id 
        
        ORDER BY data DESC
    ";
}

$stmt = $crud->getConexao()->prepare($sql);
$stmt->execute();

/* ---------------------------
   GERAR LISTA HTML
---------------------------- */

echo "<ul class='list-group'>";

if($stmt->rowCount() > 0){

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $dataFormatada = date("d/m/Y H:i", strtotime($row["data"]));

        echo "
        <li class='list-group-item'>
            <strong>{$row['item']}</strong><br>
            <span class='text-primary'>{$row['autor']}</span><br>
            <small class='text-muted'>$dataFormatada</small>
        </li>";
    }

} else {

    echo "<li class='list-group-item'>Nenhum registo encontrado</li>";
}

echo "</ul>";
