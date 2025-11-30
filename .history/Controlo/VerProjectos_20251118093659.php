<?php
session_start();

require_once '../modelo/VerProjectos.php';

class VerProjectosController {
    private $model;

    public function __construct() {
        $this->model = new VerProjectos();
    }

    public function exibirProjectos() {
        $tipo = $_SESSION['tipo'] ?? null;
        $id = $_SESSION['id'] ?? null;

        if (!$tipo || !$id) {
            header('Location: login.php');
            exit;
        }

        switch ($tipo) {
            case 'Admin':
                // Pega todos os projetos ativos (estado = 1)
                $projectos = $this->model->getAllProjectsAtivos();
                break;

            case 'Docente':
                // Projetos ativos do docente
                $projectos = $this->model->getProjetosPorDocenteAtivos($id);
                break;

            case 'Estudante':
                // Projetos ativos do estudante
                $projectos = $this->model->getProjetosPorEstudanteAtivos($id);

                // Adiciona os nomes do grupo em cada projeto
                foreach ($projectos as &$proj) {
                    $proj['grupo'] = $this->model->getNomesDoGrupo($proj['Id']);
                }
                break;

            default:
                $projectos = [];
        }

        // Enviar para a View
        include_once '../visao/VerProjectos.php';
    }
}

// Executar
$controller = new VerProjectosController();
$controller->exibirProjectos();
