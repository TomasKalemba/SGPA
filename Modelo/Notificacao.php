<?php
require_once 'crud.php';

class Notificacoes extends crud
{
    public function listarNotificacoesDocente($docente_id)
    {
        try {
            $conn = $this->getConexao();
            $stmt = $conn->prepare("
                SELECT 
                    n.data_envio, 
                    n.mensagem, 
                    u.nome AS estudante_nome,
                    p.titulo AS titulo_projeto,
                    n.status
                FROM notificacoes n
                JOIN usuarios u ON u.id = n.estudante_id
                JOIN projectos p ON p.id = n.projeto_id
                WHERE n.docente_id = ?
                ORDER BY n.data_envio DESC
            ");
            $stmt->execute([$docente_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar notificações: " . $e->getMessage());
            return [];
        }
    }
}
