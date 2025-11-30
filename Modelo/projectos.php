<?php
require_once 'crud.php';


class projectos extends crud {
    private $Id;
    private $Titulo;
    private $Descricao;
    private $Docente_Id;
    private $Data_criacao;
    private $Prazo;

    function __construct() {
        parent::__construct();
    }

    // Getters
    public function getId() {
        return $this->Id;
    }

    public function getTitulo() {
        return $this->Titulo;
    }

    public function getDescricao() {
        return $this->Descricao;
    }

    public function getDocente_Id() {
        return $this->Docente_Id;
    }

    public function getData_criacao() {
        return $this->Data_criacao;
    }

    public function getPrazo() {
        return $this->Prazo;
    }

    // Setters
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function setTitulo($Titulo) {
        $this->Titulo = $Titulo;
    }

    public function setDescricao($Descricao) {
        $this->Descricao = $Descricao;
    }

    public function setDocente_Id($Docente_Id) {
        $this->Docente_Id = $Docente_Id;
    }

    public function setData_criacao($Data_criacao) {
        $this->Data_criacao = $Data_criacao;
    }

    public function setPrazo($Prazo) {
        $this->Prazo = $Prazo;
    }

    // Inserir projeto
    public function InserirProjecto() {
        $Dados['titulo']        = $this->getTitulo();
        $Dados['descricao']     = $this->getDescricao();
        $Dados['Docente_Id']    = $this->getDocente_Id();
        $Dados['Data_criacao']  = $this->getData_criacao();
        $Dados['Prazo']         = $this->getPrazo();

        if ($this->Inserir('projectos', $Dados) > 0) {
            echo "Projeto submetido com sucesso.";
        } else {
            echo "Erro ao submeter projeto.";
        }
    }

    // Editar projeto
    public function EditarProjecto() {
        $Dados['titulo']        = $this->getTitulo();
        $Dados['descricao']     = $this->getDescricao();
        $Dados['Docente_Id']    = $this->getDocente_Id();
        $Dados['Data_criacao']  = $this->getData_criacao();
        $Dados['Prazo']         = $this->getPrazo();

        if ($this->Editar('projectos', $Dados, "Id='" . $this->getId() . "'") > 0) {
            echo "Projeto editado com sucesso.";
        } else {
            echo "Erro ao editar projeto.";
        }
    }

    // Eliminar projeto
    public function EliminarProjecto() {
        if ($this->Eliminar('projectos', "Id='" . $this->getId() . "'") > 0) {
            echo "Projeto eliminado com sucesso!";
        } else {
            echo "Erro ao eliminar projeto.";
        }
    }
}
