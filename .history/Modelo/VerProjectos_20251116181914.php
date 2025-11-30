<?php
require_once 'crud.php';

class VerProjectos extends crud {

    public function __construct() {
        parent::__construct();
    }

    // Criar projeto
    public function createProject($titulo, $descricao, $estudantes, $data_criacao, $prazo, $arquivo) {

        // Compatibilidade com sistema
        $docente_id = $_SESSION['id'] ?? ($_SESSION['usuario_id'] ?? null);

        if (!$docente_id) {
            return false;
        }

        $dados = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'docente_id' => $docente_id,
            'data_criacao' => $data_criacao,
            'prazo' => $prazo,
            'arquivo' => $arquivo
        ];

        return $this->Inserir('projectos', $dados);
    }

    // Todos os projetos
    public function getAllProjects() {
        try {
            $sql = "
                SELECT 
                    p.Id,
                    p.titulo,
                    p.descricao,
                    u.nome AS docente_nome,
                    p.data_criacao,
                    p.prazo,
                    p.arquivo,
                    p.feedback,
                    GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ') AS estudantes
                FROM projectos p
                INNER JOIN usuarios u ON u.id = p.docente_id
                LEFT JOIN grupo g ON g.projeto_id = p.Id
                LEFT JOIN grupo_estudante ge ON ge.grupo_id = g.id
                LEFT JOIN usuarios ue ON ue.id = ge.estudante_id
                GROUP BY p.Id
                ORDER BY p.Id DESC
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro ao buscar projetos: " . $e->getMessage();
            return [];
        }
    }

    // Projetos de um estudante
    public function getProjetosPorEstudante($estudanteId) {
        try {
            $sql = "
                SELECT 
                    p.Id,
                    p.titulo,
                    p.descricao,
                    u.nome AS docente_nome,
                    p.data_criacao,
                    p.prazo,
                    p.arquivo,
                    p.feedback,
                    (
                        SELECT GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ')
                        FROM grupo g2
                        INNER JOIN grupo_estudante ge2 ON ge2.grupo_id = g2.id
                        INNER JOIN usuarios ue ON ue.id = ge2.estudante_id
                        WHERE g2.projeto_id = p.Id
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

        } catch (PDOException $e) {
            echo "Erro ao buscar projetos do estudante: " . $e->getMessage();
            return [];
        }
    }

    // Projetos do docente
    public function getProjetosPorDocente($docenteId) {
        try {
            $sql = "
                SELECT 
                    p.Id,
                    p.titulo,
                    p.descricao,
                    u.nome AS docente_nome,
                    p.data_criacao,
                    p.prazo,
                    p.arquivo,
                    p.feedback,
                    (
                        SELECT GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ')
                        FROM grupo g2
                        INNER JOIN grupo_estudante ge2 ON ge2.grupo_id = g2.id
                        INNER JOIN usuarios ue ON ue.id = ge2.estudante_id
                        WHERE g2.projeto_id = p.Id
                    ) AS estudantes
                FROM projectos p
                INNER JOIN usuarios u ON u.id = p.docente_id
                WHERE p.docente_id = ?
                GROUP BY p.Id
                ORDER BY p.Id DESC
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$docenteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro ao buscar projetos do docente: " . $e->getMessage();
            return [];
        }
    }

    // Busca por campo
    public function getbuscaProjects($campo, $valor) {
        try {
            $permitidos = ['Id', 'titulo', 'docente_id'];

            if (!in_array($campo, $permitidos)) {
                throw new Exception("Campo inválido.");
            }

            $sql = "
                SELECT 
                    p.Id,
                    p.titulo,
                    p.descricao,
                    p.docente_id,
                    u.nome AS docente_nome,
                    p.data_criacao,
                    p.prazo,
                    p.arquivo,
                    p.feedback,
                    (
                        SELECT GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ')
                        FROM grupo g2
                        INNER JOIN grupo_estudante ge2 ON ge2.grupo_id = g2.id
                        INNER JOIN usuarios ue ON ue.id = ge2.estudante_id
                        WHERE g2.projeto_id = p.Id
                    ) AS estudantes
                FROM projectos p
                LEFT JOIN usuarios u ON u.id = p.docente_id
                WHERE p.$campo = ?
                GROUP BY p.Id
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$valor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            echo "Erro na busca: " . $e->getMessage();
            return [];
        }
    }

    // Projeto por ID
    public function getProjetoPorId($id) {
        try {
            $sql = "
                SELECT 
                    p.*,
                    (
                        SELECT GROUP_CONCAT(DISTINCT u.nome SEPARATOR ', ')
                        FROM grupo g
                        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                        INNER JOIN usuarios u ON u.id = ge.estudante_id
                        WHERE g.projeto_id = p.Id
                    ) AS estudantes
                FROM projectos p
                WHERE p.Id = ?
                LIMIT 1
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }

    // Editar projeto
    public function updateProject($id, $titulo, $descricao, $estudantes, $docente_id, $data_criacao, $prazo, $arquivo, $feedback = null) {
        
        $dados = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'docente_id' => $docente_id,
            'data_criacao' => $data_criacao,
            'prazo' => $prazo,
            'arquivo' => $arquivo,
            'feedback' => $feedback
        ];

        return $this->Editar('projectos', $dados, "Id = '$id'");
    }

    // Nomes do grupo
    public function getNomesDoGrupo($projeto_id) {
        try {
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

        } catch (PDOException $e) {
            return '-';
        }
    }

}
?>
