<?php
require_once 'crud.php';

class VerSubmissoes extends crud {

    public function __construct() {
        parent::__construct();
    }

    // Obter todas as submissões ativas (estado = 1)
    public function getTodasSubmissoesAtivas() {
        try {
            $stmt = $this->getConexao()->prepare("SELECT * FROM submisoes WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar as submissões: " . $e->getMessage();
            return [];
        }
    }

    // Buscar submissões por campo e valor, apenas ativas
    public function buscarSubmissoesPorCampoAtivas($campo, $valor) {
        try {
            $sql = "SELECT * FROM submisoes WHERE $campo = ? AND estado = 1";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$valor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar as submissões: " . $e->getMessage();
            return [];
        }
    }

    // Obter submissões ativas dos projetos atribuídos a um docente
    public function getSubmissoesPorDocenteAtivas($docente_id) {
        try {
            $sql = "
                SELECT s.*, p.titulo, p.descricao, s.estudante_id, s.data_submissao, s.estatus, s.arquivo, s.feedback
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                WHERE p.docente_id = ? AND s.estado = 1 AND p.estado = 1
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$docente_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões do docente: " . $e->getMessage();
            return [];
        }
    }

    // Obter submissões ativas feitas por um estudante
    public function getSubmissoesPorEstudanteAtivas($estudante_id) {
        try {
            $sql = "
                SELECT s.*, p.titulo, p.docente_id
                FROM submisoes s
                INNER JOIN projectos p ON s.Id_projectos = p.Id
                INNER JOIN grupo g ON g.projeto_id = p.Id
                INNER JOIN grupo_estudante ge ON ge.grupo_id = g.Id
                WHERE ge.estudante_id = ? AND s.estado = 1 AND p.estado = 1 AND g.estado = 1 AND ge.estado = 1
            ";
            $stmt = $this->getConexao()->prepare($sql);
            $stmt->execute([$estudante_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar submissões do estudante: " . $e->getMessage();
            return [];
        }
    }

    // Atualizar submissão (não altera estado, apenas dados)
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

    // Deletar submissão logicamente (marca estado = 0 e grava data_eliminacao)
    public function deleteSubmissaoLogico($id, $id_usuario_eliminou, $motivo = null) {
        $dados = [
            'estado' => 0,
            'data_eliminacao' => date('Y-m-d H:i:s'),
            'eliminado_por' => $id_usuario_eliminou,
            'motivo_eliminacao' => $motivo
        ];
        $condicao = "Id = ?";
        return $this->Editar('submisoes', $dados, $condicao, [$id]);
    }
}
?>
