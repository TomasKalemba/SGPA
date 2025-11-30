<?php
include("crud.php");
class usuarios extends crud{
    private $Id;
    private $Nome;
    private $Tipo;
    private $Email;
    private $Senha;

    function __construct(){
       parent::__construct();
    }
    public function getId() {
    return $this->Id;
    }

    public function getNome() {
    return $this->Nome;
    }

    public function getTipo() {
    return $this->Tipo;
    }

    public function getEmail() {
    return $this->Email;
    }

    public function getSenha() {
    return $this->Senha;
    }
     
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function setNome($Nome){  
          $this->Nome =$Nome;
    }

    public function setTipo($Tipo){
        $this->Tipo =$Tipo;
    }

    public function setEmail($Email){
        $this->Email =$Email;
    }

    public function setSenha($Senha){
        $this->Senha =$Senha;
    }

    public function InserirUsuario(){
        $Dados['nome'] =$this->getNome();
        $Dados['tipo'] =$this->getTipo();
        $Dados['email'] =$this->getEmail();
        $Dados['senha'] =$this->getSenha();
        return $this->Inserir('usuarios',$Dados);
        
    }

    public function EditarUsuario(){

        $Dados['nome'] =$this->getNome();
        $Dados['tipo'] =$this->getTipo();
        $Dados['email'] =$this->getEmail();
        $Dados['senha'] =$this->getSenha();
        if($this->Editar('usuarios',$Dados,"Id='".$this->getId())>0){
            echo "Usuario Editado com Sucesso!!";
        }else{
            echo "Erro ao Editar Usuario";
        }
    }

    public function EliminarUsuario(){
        if($this->Eliminar('usuarios',"Id='".$this->getId())>0){
            echo "Usuario Eliminado com Sucesso!!";
        }else{
            echo "Erro ao Eliminar Usuario";
        }
    }
}