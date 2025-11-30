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
                // Buscar todos projetos ativos
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
                if (is_array($projectos)) {
                    foreach ($projectos as &$proj) {
                        // Verifique o nome correto do campo ID (Id, id, ID...)
                        $idProjeto = $proj['Id'] ?? $proj['id'] ?? null;
                        if ($idProjeto !== null) {
                            $proj['grupo'] = $this->model->getNomesDoGrupo($idProjeto);
                        } else {
                            $proj['grupo'] = '-';
                        }
                    }
                }
                
        include_once '../visao/VerProjectos.php';
    }
}

$controller = new VerProjectosController();
$controller->exibirProjectos();
