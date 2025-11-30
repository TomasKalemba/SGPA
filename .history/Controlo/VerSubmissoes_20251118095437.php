<?php
// Incluir o Model
include_once '../modelo/crud.php';
require_once '../modelo/submissoes.php';

class submissoesController {
    // Método para exibir os projetos enviados
    public function exibirProjectos() {
        session_start();

        $submissaoModel = new VerSubmissoes();
        $tipo = $_SESSION['tipo'] ?? null;
        $id = $_SESSION['id'] ?? null;

        if ($tipo === 'Admin') {
            // Admin vê todas as submissões ativas (estado = 1)
            $projectos = $submissaoModel->getTodasSubmissoesAtivas();
        } elseif ($tipo === 'Docente') {
            // Docente vê apenas submissões dos seus projetos ativas (estado = 1)
            $projectos = $submissaoModel->getSubmissoesPorDocenteAtivas($id);
        } else {
            // Outros tipos de usuários não têm permissão
            $projectos = [];
        }

        // Envia para a View
        require_once '../visao/submissoes.php';
    }

    // Futuramente você pode adicionar:
    // public function criarSubmissao() { ... }
    // public function editarSubmissao() { ... }
}
?>
