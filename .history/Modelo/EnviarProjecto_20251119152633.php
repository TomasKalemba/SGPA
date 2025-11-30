<?php
require_once '../modelo/crud.php';

class EnviarProjectoModel {

    private $con;

    public function __construct() {
        $crud = new crud();
        $this->con = $crud->getConexao();
    }

    // Verificar se já existe um envio duplicado
    public function verificarDuplicado($docente, $projetoId) {
        $sql = "SELECT COUNT(*) FROM projectos_submetidos 
                WHERE docente_nome = :docente AND projeto_id = :projeto";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':docente', $docente);
        $stmt->bindParam(':projeto', $projetoId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Inserir novo envio
    public function inserirEnvio($dados, $arquivoBlob) {
        $sql = "INSERT INTO projectos_submetidos 
                (projeto_id, docente_nome, descricao, data_submissao, estatus, feedback, arquivo)
                VALUES (:p, :d, :desc, :data, :st, :fb, :arq)";

        $stmt = $this->con->prepare($sql);

        $stmt->bindParam(':p', $dados['projeto_id']);
        $stmt->bindParam(':d', $dados['docente_nome']);
        $stmt->bindParam(':desc', $dados['descricao']);
        $stmt->bindParam(':data', $dados['data_submissao']);
        $stmt->bindParam(':st', $dados['estatus']);
        $stmt->bindParam(':fb', $dados['feedback']);
        $stmt->bindParam(':arq', $arquivoBlob, PDO::PARAM_LOB);

        return $stmt->execute();
    }
}
