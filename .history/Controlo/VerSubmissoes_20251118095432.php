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
            // Admin vê todas as submissões
            $projectos = $submissaoModel->getTodasSubmissoes();
        } elseif ($tipo === 'Docente') {
            // Docente vê apenas submissões dos seus projetos
            $projectos = $submissaoModel->getSubmissoesPorDocente($id);
        } else {
            // Outros tipos de usuários não têm permissão
            $projectos = [];
        }

        // Envia para a View
        require_once '../visao/submissoes.php';
    }

    // Aqui você pode adicionar futuramente:
    // public function criarSubmissao() { ... }
    // public function editarSubmissao() { ... }
}
?>
