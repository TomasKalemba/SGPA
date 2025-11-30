<?php
session_start();

// Carrega o model correto
require_once '../modelo/submissoes.php';

class SubmissoesController {

    public function exibirProjectos() {

        // Verificar sessão
        if (!isset($_SESSION['tipo']) || !isset($_SESSION['id'])) {
            header('Location: ../visao/login.php');
            exit;
        }

        $tipo = $_SESSION['tipo'];
        $id   = $_SESSION['id'];

        // Instância do model
        $submissoesModel = new VerSubmissoes();

        // Definir projetos conforme o tipo do utilizador
        switch ($tipo) {
            case 'Admin':
                $submissoes = $submissoesModel->getSubmissoesParaAdmin();
                break;

            case 'Docente':
                $submissoes = $submissoesModel->getSubmissoesPorDocente($id);
                break;

            default:
                $submissoes = [];
        }

        // Disponibiliza variáveis para a View
        $dados = [
            'submissoes' => $submissoes
        ];

        // Carregar a View
        require '../visao/submissoes.php';
    }
}
?>
