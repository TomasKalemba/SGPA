<?php
// Incluir a classe de CRUD
require_once 'crud.php';

class VerSubmissoes extends crud {

    public function __construct() {
        parent::__construct();
    }

    // ✅ Obter TODAS as submissões (Admin ou uso geral)
    public function getTodasSubmissoes() {
        try {
            $stmt = $this->getConexao()->prepare("SELECT * FROM submisoes");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar as submissões: " . $e->getMessage();
            return [];
        }
    }

    // ✅ Obter submissões por campo específico
    public function buscarSubmissoesPorCampo($campo, $valor) {
        try {
            $stmt = $this->getConexao()->prepare("SELECT * FROM submisoes WHERE $campo = ?");
            $stmt->execute([$valor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar as submissões: " . $e->getMessage();
            return [];
        }
    }

    // ✅ Obter submissões apenas dos projetos atribuídos a um docente
    public function getSubmissoesPorDocente($docente_id) {
        try {
            $sql = "
                SELECT s.*, 
                       p.titulo, 
                       p.descricao, 
                       s.estudante_id, 
                       s.data_submissao, 
                       s.estatus, 
                       s.arquivo, 
                       s.feedback
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                WHERE p.docente_id = ?
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$docente_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões do docente: " . $e->getMessage();
            return [];
        }
    }

    // ✅ Obter submissões feitas por um estudante
    public function getSubmissoesPorEstudante($estudante_id) {
        try {
            $sql = "
                SELECT s.*, 
                       p.titulo, 
                       p.docente_id
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                INNER JOIN grupo g ON g.projeto_id = p.Id
                INNER JOIN grupo_estudante ge ON ge.grupo_id = g.Id
                WHERE ge.estudante_id = ?
            ";

            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$estudante_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões do estudante: " . $e->getMessage();
            return [];
        }
    }

    // ✅ Atualizar submissão
    public function updateSubmissao($id, $titulo, $descricao, $arquivo, $estatus, $feedback) {
        $dados = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'arquivo' => $arquivo,
            'estatus' => $estatus,
            'feedback' => $feedback
        ];

        $condicao = "Id = ?";
        return $this->Editar('submisoes', $dados, $condicao, [$id]);
    }

    // ✅ Deletar submissão
    public function deleteSubmissao($id) {
        return $this->Eliminar('submisoes', "Id = ?", [$id]);
    }
}
?>
