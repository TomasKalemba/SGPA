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
                n.id,
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
        error_log("Erro ao buscar notificações (docente): " . $e->getMessage());
        return [];
    }
}

public function listarNotificacoesEstudante($estudante_id)
{
    try {
        $conn = $this->getConexao();
        $stmt = $conn->prepare("
            SELECT 
                n.id,
                n.data_envio, 
                n.mensagem, 
                u.nome AS docente_nome,
                p.titulo AS titulo_projeto,
                n.status
            FROM notificacoes n
            JOIN usuarios u ON u.id = n.docente_id
            JOIN projectos p ON p.id = n.projeto_id
            WHERE n.estudante_id = ?
            ORDER BY n.data_envio DESC
        ");
        $stmt->execute([$estudante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar notificações (estudante): " . $e->getMessage());
        return [];
    }
}
