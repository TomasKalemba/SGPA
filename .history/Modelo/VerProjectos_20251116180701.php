<?php
require_once 'crud.php';

class VerProjectos extends crud {

    public function __construct() {
        parent::__construct();
    }

    // Criar projeto
    public function createProject($titulo, $descricao, $docente_id, $data_criacao, $prazo, $arquivo) {
        $dados = [
            'titulo'      => $titulo,
            'descricao'   => $descricao,
            'docente_id'  => $docente_id,  // 🔥 AGORA É OBRIGATÓRIO PARA FUNCIONAR
            'data_criacao'=> $data_criacao,
            'prazo'       => $prazo,
            'arquivo'     => $arquivo
        ];
        return $this->Inserir('projectos', $dados);
    }

    // Lista completa para ADMIN
    public function getTodosProjetos() {
        $sql = "
            SELECT 
                p.Id,
                p.titulo,
                p.descricao,
                u.nome AS docente_nome,
                p.data_criacao,
                p.prazo,
                p.arquivo,
                (
                    SELECT GROUP_CONCAT(DISTINCT est.nome SEPARATOR ', ')
                    FROM grupo g
                    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                    INNER JOIN usuarios est ON est.id = ge.estudante_id
                    WHERE g.projeto_id = p.Id
                ) AS estudantes
            FROM projectos p
            LEFT JOIN usuarios u ON u.id = p.docente_id
            ORDER BY p.Id DESC
        ";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Projetos do docente
    public function getProjetosPorDocente($docenteId) {
        $sql = "
            SELECT 
                p.Id,
                p.titulo,
                p.descricao,
                u.nome AS docente_nome,
                p.data_criacao,
                p.prazo,
                p.arquivo,
                (
                    SELECT GROUP_CONCAT(DISTINCT est.nome SEPARATOR ', ')
                    FROM grupo g
                    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                    INNER JOIN usuarios est ON est.id = ge.estudante_id
                    WHERE g.projeto_id = p.Id
                ) AS estudantes
            FROM projectos p
            INNER JOIN usuarios u ON u.id = p.docente_id
            WHERE p.docente_id = ?
            ORDER BY p.Id DESC
        ";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$docenteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Projetos do estudante
    public function getProjetosPorEstudante($estudanteId) {
        $sql = "
            SELECT 
                p.Id,
                p.titulo,
                p.descricao,
                p.data_criacao,
                p.prazo,
                p.arquivo,
                u.nome AS docente_nome,
                (
                    SELECT GROUP_CONCAT(DISTINCT est.nome SEPARATOR ', ')
                    FROM grupo g
                    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                    INNER JOIN usuarios est ON est.id = ge.estudante_id
                    WHERE g.projeto_id = p.Id
                ) AS estudantes
            FROM projectos p
            INNER JOIN usuarios u ON u.id = p.docente_id
            INNER JOIN grupo g ON g.projeto_id = p.Id
            INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
            WHERE ge.estudante_id = ?
            GROUP BY p.Id
            ORDER BY p.Id DESC
        ";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$estudanteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar 1 projeto
    public function getProjetoPorId($id) {
        $sql = "
            SELECT 
                p.*,
                (
                    SELECT GROUP_CONCAT(DISTINCT est.nome SEPARATOR ', ')
                    FROM grupo g
                    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                    INNER JOIN usuarios est ON est.id = ge.estudante_id
                    WHERE g.projeto_id = p.Id
                ) AS estudantes
            FROM projectos p
            WHERE p.Id = ?
        ";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Atualizar
    public function updateProject($id, $titulo, $descricao, $docente_id, $prazo, $arquivo) {
        $dados = [
            'titulo'     => $titulo,
            'descricao'  => $descricao,
            'docente_id' => $docente_id,
            'prazo'      => $prazo,
            'arquivo'    => $arquivo
        ];
        $condicao = "Id = '$id'";
        return $this->Editar('projectos', $dados, $condicao);
    }

    // Nome dos estudantes do grupo
    public function getNomesDoGrupo($projeto_id) {
        $sql = "
            SELECT GROUP_CONCAT(DISTINCT u.nome SEPARATOR ', ') AS nomes
            FROM grupo g
            INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
            INNER JOIN usuarios u ON u.id = ge.estudante_id
            WHERE g.projeto_id = ?
        ";
        $stmt = $this->getConexao()->prepare($sql);
        $stmt->execute([$projeto_id]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['nomes'] ?? '-';
    }
}
?>
