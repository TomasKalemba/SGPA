<?php
// Incluir a classe de CRUD
require_once 'crud.php';

class VerProjectos extends crud {
    public function __construct() {
        parent::__construct();
    }

    // Criar um novo projeto
    public function createProject($titulo, $descricao, $estudantes, $docente_id, $data_criacao, $prazo, $arquivo) {
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

    public function getAllProjects() {
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
        ORDER BY p.Id DESC
    ";
    $stmt = $this->getConexao()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todos os projetos
}
    }

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

    public function getbuscaProjects($campo, $valor) {
        try {
            $camposPermitidos = ['Id', 'titulo', 'docente_id'];
            if (!in_array($campo, $camposPermitidos)) {
                throw new Exception("Campo inválido para busca.");
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
                    GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ') AS estudantes
                FROM projectos p
                LEFT JOIN usuarios u ON p.docente_id = u.id
                LEFT JOIN grupo g ON g.projeto_id = p.Id
                LEFT JOIN grupo_estudante ge ON ge.grupo_id = g.id
                LEFT JOIN usuarios ue ON ue.id = ge.estudante_id
                WHERE p.$campo = ?
                GROUP BY p.Id
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$valor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Erro ao buscar os projetos: " . $e->getMessage();
            return [];
        }
    }

    public function getProjetoDoEstudantePorTituloEDocente($estudanteId, $titulo, $docenteNome) {
        try {
            $sql = "
                SELECT p.Id
                FROM projectos p
                INNER JOIN usuarios u ON u.Id = p.docente_id
                INNER JOIN grupo g ON g.projeto_id = p.Id
                INNER JOIN grupo_estudante ge ON ge.grupo_id = g.Id
                WHERE ge.estudante_id = ?
                  AND p.titulo = ?
                  AND u.nome = ?
                LIMIT 1
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$estudanteId, $titulo, $docenteNome]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao verificar projeto do estudante: " . $e->getMessage();
            return false;
        }
    }
        // ✅ Buscar um único projeto pelo ID
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
            echo "Erro ao buscar projeto por ID: " . $e->getMessage();
            return false;
        }
    }


    public function getTodosProjetos() {
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
        ORDER BY p.Id ASC  -- Altere para ASC para ordenar do mais antigo ao mais recente
    ";
    $stmt = $this->getConexao()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
        $condicao = "Id = '$id'";
        return $this->Editar('projectos', $dados, $condicao);
    }

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
            return 'Erro';
        }
    }

    // ✅ Buscar projetos com filtro por tipo de usuário (Admin, Docente, Estudante)
    public function buscarProjetosComEstudantesPorUsuario($termo, $tipoUsuario, $usuarioId) {
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
                WHERE p.titulo LIKE :termo
            ";

            if ($tipoUsuario === 'Docente') {
                $sql .= " AND p.docente_id = :usuarioId";
            } elseif ($tipoUsuario === 'Estudante') {
                $sql .= " AND EXISTS (
                    SELECT 1 FROM grupo g
                    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                    WHERE g.projeto_id = p.Id AND ge.estudante_id = :usuarioId
                )";
            }

            $stmt = $this->getConexao()->prepare($sql);
            $search = '%' . $termo . '%';
            $stmt->bindParam(':termo', $search, PDO::PARAM_STR);
            if ($tipoUsuario !== 'Admin') {
                $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro ao buscar projetos filtrados: " . $e->getMessage();
            return [];
        }
    }
}
?>
