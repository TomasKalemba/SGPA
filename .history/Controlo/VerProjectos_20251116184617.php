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
                $projectos = $this->model->getAllProjects();
                break;

            case 'Docente':
                $projectos = $this->model->getProjetosPorDocente($id);
                break;

            case 'Estudante':
                $projectos = $this->model->getProjetosPorEstudante($id);

                // 🔽 Aqui adicionamos os nomes do grupo
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
