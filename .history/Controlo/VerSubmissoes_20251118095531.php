<?php
// Incluir o Model
include_once '../modelo/crud.php';
require_once '../modelo/submissoes.php';

session_start();

class SubmissoesController {
    private $submissaoModel;

    public function __construct() {
        $this->submissaoModel = new VerSubmissoes();
    }

    // Método para exibir as submissões enviadas
    public function exibirSubmissoes() {
        $tipo = $_SESSION['tipo'] ?? null;
        $id = $_SESSION['id'] ?? null;

        if (!$tipo || !$id) {
            header('Location: login.php');
            exit;
        }

        if ($tipo === 'Admin') {
            // Admin vê todas as submissões (sem filtro de estado, pois seu model não tem estado)
            $submissoes = $this->submissaoModel->getTodasSubmissoes();
        } elseif ($tipo === 'Docente') {
            // Docente vê submissões dos seus projetos
            $submissoes = $this->submissaoModel->getSubmissoesPorDocente($id);
        } else {
            // Outros tipos não têm permissão (pode mudar depois se quiser)
            $submissoes = [];
        }

        // Enviar para a view
        require_once '../visao/submissoes.php';
    }
}

// Executar
$controller = new SubmissoesController();
$controller->exibirSubmissoes();
?>
