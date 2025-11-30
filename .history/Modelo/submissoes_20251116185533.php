<?php
require_once 'crud.php';

class submissoes extends crud {
    private $Id;
    private $Id_projectos;
    private $Estudante_Id;
    private $Titulo;
    private $Descricao;
    private $Arquivo;
    private $estatus;
    private $Data_submissao;
    private $Docente_Id;
    private $Feedback;

    function __construct() {
        parent::__construct();
    }

    // Getters
    public function getId() { return $this->Id; }
    public function getProjecto_Id() { return $this->Id_projectos; }
    public function getEstudante_Id() { return $this->Estudante_Id; }
    public function getTitulo() { return $this->Titulo; }
    public function getDescricao() { return $this->Descricao; }
    public function getArquivo() { return $this->Arquivo; }
    public function getestatus() { return $this->estatus; }
    public function getData_submissao() { return $this->Data_submissao; }
    public function getDocente_Id() { return $this->Docente_Id; }
    public function getFeedback() { return $this->Feedback; }

    // Setters
    public function setId($Id) { $this->Id = $Id; }
    public function setProjecto_Id($Id_projectos) { $this->Id_projectos = $Id_projectos; }
    public function setEstudante_Id($Estudante_Id) { $this->Estudante_Id = $Estudante_Id; }
    public function setTitulo($Titulo) { $this->Titulo = $Titulo; }
    public function setDescricao($Descricao) { $this->Descricao = $Descricao; }
    public function setArquivo($Arquivo) { $this->Arquivo = $Arquivo; }
    public function setestatus($estatus) { $this->estatus = $estatus; }
    public function setData_submissao($Data_submissao) { $this->Data_submissao = $Data_submissao; }
    public function setDocente_Id($Docente_Id) { $this->Docente_Id = $Docente_Id; }
    public function setFeedback($Feedback) { $this->Feedback = $Feedback; }

    public function InserirSubmissao() {
        $Dados = [
            'Id_projectos' => $this->getProjecto_Id(),
            'estudante_id' => $this->getEstudante_Id(),
            'titulo' => $this->getTitulo(),
            'descricao' => $this->getDescricao(),
            'arquivo' => $this->getArquivo(),
            'estatus' => $this->getestatus(),
            'data_submissao' => $this->getData_submissao(),
            'docente_Id' => $this->getDocente_Id(),
            'feedback' => $this->getFeedback()
        ];
        return $this->Inserir('submisoes', $Dados) > 0;
    }

    public function EditarSubmissao() {
        $Dados = [
            'Id_projectos' => $this->getProjecto_Id(),
            'estudante_id' => $this->getEstudante_Id(),
            'titulo' => $this->getTitulo(),
            'descricao' => $this->getDescricao(),
            'arquivo' => $this->getArquivo(),
            'estatus' => $this->getestatus(),
            'data_submissao' => $this->getData_submissao(),
            'docente_Id' => $this->getDocente_Id(),
            'feedback' => $this->getFeedback()
        ];
        return $this->Editar('submisoes', $Dados, "Id='" . $this->getId() . "'") > 0;
    }

    public function EliminarSubmissao() {
        return $this->Eliminar('submisoes', "Id='" . $this->getId() . "'") > 0;
    }

    public function getSubmissoesRelacionadasAoGrupo($estudante_id) {
        try {
            $sql = "
                SELECT s.*, 
                       p.titulo, 
                       p.descricao, 
                       u.nome AS docente_nome,
                       ue.nome AS estudante_nome,
                       (
                           SELECT GROUP_CONCAT(DISTINCT u2.nome SEPARATOR ', ')
                           FROM grupo g2
                           INNER JOIN grupo_estudante ge2 ON ge2.grupo_id = g2.id
                           INNER JOIN usuarios u2 ON u2.id = ge2.estudante_id
                           WHERE g2.projeto_id = p.Id
                       ) AS estudantes
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                INNER JOIN usuarios u ON p.docente_id = u.Id
                INNER JOIN usuarios ue ON s.estudante_id = ue.id
                WHERE EXISTS (
                    SELECT 1
                    FROM grupo_estudante ge
                    INNER JOIN grupo g ON ge.grupo_id = g.id
                    WHERE g.projeto_id = s.Id_projectos
                      AND ge.estudante_id = :estudante_id
                )
                ORDER BY s.data_submissao DESC
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([':estudante_id' => $estudante_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões do grupo: " . $e->getMessage();
            return [];
        }
    }

    public function getSubmissoesParaDocente($docente_id) {
        try {
            $sql = "
                SELECT s.*, p.titulo, p.descricao,
                    u.nome AS estudante_nome,
                    (
                        SELECT GROUP_CONCAT(DISTINCT u2.nome SEPARATOR ', ')
                        FROM grupo g
                        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                        INNER JOIN usuarios u2 ON u2.id = ge.estudante_id
                        WHERE g.projeto_id = p.Id
                    ) AS estudantes
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                INNER JOIN usuarios u ON s.estudante_id = u.Id
                WHERE p.docente_id = ?
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$docente_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões para docente: " . $e->getMessage();
            return [];
        }
    }

    public function getSubmissoesParaAdmin() {
        try {
            $sql = "
    SELECT s.*, p.titulo AS titulo, p.descricao AS descricao,
           u.nome AS estudante_nome, s.feedback, s.arquivo,
           (
               SELECT GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ')
               FROM grupo g
               INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
               INNER JOIN usuarios ue ON ue.id = ge.estudante_id
               WHERE g.projeto_id = p.Id
           ) AS estudantes
    FROM submisoes s
    INNER JOIN projectos p ON s.Id_projectos = p.Id
    INNER JOIN usuarios u ON s.estudante_id = u.Id
";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões para admin: " . $e->getMessage();
            return [];
        }
    }

    public function getTodasSubmissoes() {
        try {
            $sql = "
    SELECT s.*, p.titulo AS titulo, p.descricao AS descricao,
           u.nome AS estudante_nome, s.feedback, s.arquivo,
           (
               SELECT GROUP_CONCAT(DISTINCT ue.nome SEPARATOR ', ')
               FROM grupo g
               INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
               INNER JOIN usuarios ue ON ue.id = ge.estudante_id
               WHERE g.projeto_id = p.Id
           ) AS estudantes
    FROM submisoes s
    INNER JOIN projectos p ON s.Id_projectos = p.Id
    INNER JOIN usuarios u ON s.estudante_id = u.Id
";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar todas as submissões: " . $e->getMessage();
            return [];
        }
    }

    public function jaExisteSubmissaoPorGrupo($estudante_id, $projeto_id) {
        try {
            $sqlGrupo = "
                SELECT g.id
                FROM grupo g
                INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
                WHERE ge.estudante_id = :estudante_id
                  AND g.projeto_id = :projeto_id
                LIMIT 1
            ";
            $stmtGrupo = $this->getConexao()->prepare($sqlGrupo);
            $stmtGrupo->execute([
                ':estudante_id' => $estudante_id,
                ':projeto_id' => $projeto_id
            ]);

            $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);
            if (!$grupo) return false;

            $grupo_id = $grupo['id'];

            $sqlCheck = "
                SELECT COUNT(*)
                FROM submisoes s
                INNER JOIN grupo_estudante ge ON s.estudante_id = ge.estudante_id
                WHERE ge.grupo_id = :grupo_id AND s.Id_projectos = :projeto_id
            ";
            $stmtCheck = $this->getConexao()->prepare($sqlCheck);
            $stmtCheck->execute([
                ':grupo_id' => $grupo_id,
                ':projeto_id' => $projeto_id
            ]);

            return $stmtCheck->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Erro ao verificar submissão do grupo: " . $e->getMessage();
            return true;
        }
    }
    public function salvarArquivo($projetoId, $arquivo, $usuarioId) {
        try {
            $sql = "INSERT INTO submissoes (Id_projectos, estudante_id, arquivo, data_submissao, estatus) 
                    VALUES (?, ?, ?, NOW(), 'emAndamento')";
            $stmt = $this->getConexao()->prepare($sql);
            return $stmt->execute([$projetoId, $usuarioId, $arquivo]);
        } catch (PDOException $e) {
            echo "Erro ao salvar arquivo: " . $e->getMessage();
            return false;
        }
    }

    public function jaExisteSubmissaoDoMesmoEstudante($estudante_id, $projeto_id) {
        try {
            $sql = "
                SELECT COUNT(*) FROM submisoes
                WHERE estudante_id = :estudante_id AND Id_projectos = :projeto_id
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->bindParam(':estudante_id', $estudante_id);
            $stmt->bindParam(':projeto_id', $projeto_id);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Erro ao verificar submissão do mesmo estudante: " . $e->getMessage();
            return true;
        }
    }
}
