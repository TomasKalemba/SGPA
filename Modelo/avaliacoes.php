<?php
include("crud.php");
class avaliacoes extends crud{
    private $Id;
    private $Submissao_id;
    private $Nota;
    private $Comentarios;

    function __construct(){
       parent::__construct();
    }

    public function getId() {
    return $this->Id;
    }

    public function getSubmissao_Id() {
    return $this->Id;
    }

    public function getNota() {
    return $this->Nota;
    }

    public function getComentarios() {
    return $this->Comentarios;
    }

    public function setId($Id) {
        $this->Id = $Id;
     }

     public function setSubmissao_Id($Id) {
        $this->Id = $Id;
     }

     public function setNota($Nota) {
        $this->Id = $Nota;
     }

     public function setComentarios($Comentarios) {
        $this->Id = $Comentarios;
     }

     public function InserirAvaliacao(){
        $Dados['Submissao_Id'] =$this->getSubmissao_Id();
        $Dados['Nota'] =$this->getNota();
        $Dados['Comentarios'] =$this->getComentarios();
        if($this->Inserir('avaliacoes',$Dados)>0){
            echo "Projecto Avaliado com Sucesso";
        }else{
            echo "Erro ao Avaliar Projecto";
        }
    }

    public function EditarAvaliacaO(){
        $Dados['Submissao'] =$this->getSubmissao_Id();
        $Dados['Nota'] =$this->getNota();
        $Dados['Comentario'] =$this->getComentarios();
        if($this->Editar('avaliacoes',$Dados,"Id='".$this->getId())>0){
            echo "Projecto Editado com Sucesso";
        }else{
            echo "Erro ao Editar Avaliacao";
        }
    }

    public function EliminarAvaliacao(){
        if($this->Eliminar('avaliacoes',"Id='".$this->getId())>0){
            echo "Avaliacao Eliminado com Sucesso!!";
        }else{
            echo "Erro ao Eliminar Avaliacao";
        }
    }


}