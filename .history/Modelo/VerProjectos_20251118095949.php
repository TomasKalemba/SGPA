<?php
// Incluir a classe de CRUD
require_once 'crud.php';

class VerProjectos extends crud {
    public function __construct() {
        parent::__construct();
    }

    // ... (outros métodos que você já tem) ...

    public function getAllProjectsAtivos() {
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
                WHERE p.estado = 1
                GROUP BY p.Id
                ORDER BY p.Id DESC
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar projetos ativos: " . $e->getMessage();
            return [];
        }
    }
    
    public function getProjetosPorDocenteAtivos($docenteId) {
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
                WHERE p.docente_id = ? AND p.estado = 1
                GROUP BY p.Id
                ORDER BY p.Id DESC
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$docenteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar projetos ativos do docente: " . $e->getMessage();
            return [];
        }
    }
    
    public function getProjetosPorEstudanteAtivos($estudanteId) {
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
                WHERE ge.estudante_id = ? AND p.estado = 1
                GROUP BY p.Id
                ORDER BY p.Id DESC
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$estudanteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar projetos ativos do estudante: " . $e->getMessage();
            return [];
        }
    }

    // ... (restante do seu código) ...
}
?>
