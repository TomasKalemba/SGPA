<?php
include_once("crud.php");

class usuarios extends crud {
    private $Id;
    private $Nome;
    private $Tipo;
    private $Email;
    private $Senha;
    private $Ativo;

    function __construct() {
        parent::__construct();
    }

    // Getters
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

    public function getAtivo() {
        return $this->Ativo;
    }

    // Setters
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function setNome($Nome) {
        $this->Nome = $Nome;
    }

    public function setTipo($Tipo) {
        $this->Tipo = $Tipo;
    }

    public function setEmail($Email) {
        $this->Email = $Email;
    }

    public function setSenha($Senha) {
        $this->Senha = $Senha;
    }

    public function setAtivo($Ativo) {
        $this->Ativo = $Ativo;
    }

    // Inserir novo usuário
    public function InserirUsuario() {
        $Dados = [
            'nome'  => $this->getNome(),
            'tipo'  => $this->getTipo(),
            'email' => $this->getEmail(),
            'senha' => $this->getSenha(),
            'ativo' => $this->getAtivo()
        ];

        return $this->Inserir('usuarios', $Dados);
    }

    // Editar usuário
    public function EditarUsuario() {
        $Dados = [
            'nome'  => $this->getNome(),
            'tipo'  => $this->getTipo(),
            'email' => $this->getEmail(),
            'senha' => $this->getSenha(),
            'ativo' => $this->getAtivo()
        ];

        return $this->Editar('usuarios', $Dados, "id='" . $this->getId() . "'");
    }

    // Eliminar usuário
    public function EliminarUsuario() {
        return $this->Eliminar('usuarios', "id='" . $this->getId() . "'");
    }

    // Buscar docentes ativos
    public function buscarDocentesAtivos() {
        return $this->Selecionar("SELECT * FROM usuarios WHERE tipo = 'Docente' AND ativo = 1");
    }

    // Buscar estudantes ativos
    public function buscarEstudantesAtivos() {
        return $this->Selecionar("SELECT * FROM usuarios WHERE tipo = 'Estudante' AND ativo = 1");
    }

    // Ativar usuário
    public function ativarUsuario($id) {
        // Confirma que o método Atualizar existe no CRUD!
        return $this->Atualizar("usuarios", ['ativo' => 1], "id = '$id'");
    }
}
